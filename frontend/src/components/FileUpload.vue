<template>
  <div class="file-upload">
    <!-- Upload Area -->
    <div
      :class="[
        'border-2 border-dashed rounded-lg p-6 text-center transition-colors',
        isDragging ? 'border-teal-500 bg-teal-50' : 'border-gray-300 bg-white',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-teal-400'
      ]"
      @click="!disabled && triggerFileInput()"
      @dragover.prevent="handleDragOver"
      @dragleave.prevent="handleDragLeave"
      @drop.prevent="handleDrop"
    >
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        :multiple="multiple"
        :disabled="disabled"
        class="hidden"
        @change="handleFileSelect"
      />

      <div v-if="uploading" class="space-y-3">
        <LoadingSpinner size="lg" color="teal" />
        <p class="text-sm text-gray-600">Téléchargement en cours... {{ uploadProgress }}%</p>
      </div>

      <div v-else class="space-y-3">
        <div class="text-5xl">📁</div>
        <div>
          <p class="text-sm font-medium text-gray-700">
            {{ dragText || 'Glissez et déposez vos fichiers ici' }}
          </p>
          <p class="text-xs text-gray-500 mt-1">
            ou cliquez pour parcourir
          </p>
        </div>
        <div v-if="maxSize" class="text-xs text-gray-400">
          Taille maximale: {{ formatFileSize(maxSize) }}
        </div>
        <div v-if="accept" class="text-xs text-gray-400">
          Types acceptés: {{ accept }}
        </div>
      </div>
    </div>

    <!-- File List -->
    <div v-if="files.length > 0" class="mt-4 space-y-2">
      <div
        v-for="(file, index) in files"
        :key="index"
        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
      >
        <div class="flex items-center space-x-3 flex-1 min-w-0">
          <div class="flex-shrink-0">
            <span class="text-2xl">{{ getFileIcon(file.type) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ file.name }}
            </p>
            <p class="text-xs text-gray-500">
              {{ formatFileSize(file.size) }}
            </p>
          </div>
        </div>

        <button
          v-if="!uploading"
          @click="removeFile(index)"
          type="button"
          class="flex-shrink-0 ml-3 text-red-600 hover:text-red-800"
          :disabled="disabled"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div v-else class="flex-shrink-0 ml-3">
          <svg class="w-5 h-5 text-teal-600 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Error Messages -->
    <div v-if="errorMessage" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
      <p class="text-sm text-red-800">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import LoadingSpinner from './LoadingSpinner.vue'
import api from '@/services/api'

interface Props {
  accept?: string
  multiple?: boolean
  maxSize?: number // in bytes
  disabled?: boolean
  uploadUrl?: string
  dragText?: string
  autoUpload?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  accept: '*/*',
  multiple: false,
  maxSize: 10485760, // 10MB default
  disabled: false,
  uploadUrl: '/files/upload',
  autoUpload: false,
})

const emit = defineEmits<{
  'upload-success': [response: any]
  'upload-error': [error: any]
  'files-selected': [files: File[]]
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const files = ref<File[]>([])
const isDragging = ref(false)
const uploading = ref(false)
const uploadProgress = ref(0)
const errorMessage = ref('')

function triggerFileInput() {
  fileInput.value?.click()
}

function handleDragOver(e: DragEvent) {
  if (!props.disabled) {
    isDragging.value = true
  }
}

function handleDragLeave(e: DragEvent) {
  isDragging.value = false
}

function handleDrop(e: DragEvent) {
  isDragging.value = false

  if (props.disabled) return

  const droppedFiles = Array.from(e.dataTransfer?.files || [])
  processFiles(droppedFiles)
}

function handleFileSelect(e: Event) {
  const target = e.target as HTMLInputElement
  const selectedFiles = Array.from(target.files || [])
  processFiles(selectedFiles)
}

function processFiles(newFiles: File[]) {
  errorMessage.value = ''

  // Validate files
  const validFiles: File[] = []

  for (const file of newFiles) {
    // Check file size
    if (props.maxSize && file.size > props.maxSize) {
      errorMessage.value = `Le fichier "${file.name}" dépasse la taille maximale de ${formatFileSize(props.maxSize)}`
      continue
    }

    // Check file type if accept is specified
    if (props.accept && props.accept !== '*/*') {
      const acceptedTypes = props.accept.split(',').map(t => t.trim())
      const fileExtension = '.' + file.name.split('.').pop()?.toLowerCase()
      const mimeType = file.type

      const isAccepted = acceptedTypes.some(type => {
        if (type.startsWith('.')) {
          return fileExtension === type.toLowerCase()
        }
        if (type.endsWith('/*')) {
          return mimeType.startsWith(type.replace('/*', ''))
        }
        return mimeType === type
      })

      if (!isAccepted) {
        errorMessage.value = `Le type de fichier "${file.name}" n'est pas accepté`
        continue
      }
    }

    validFiles.push(file)
  }

  // Add or replace files
  if (props.multiple) {
    files.value = [...files.value, ...validFiles]
  } else {
    files.value = validFiles.slice(0, 1)
  }

  emit('files-selected', files.value)

  // Auto upload if enabled
  if (props.autoUpload && validFiles.length > 0) {
    uploadFiles()
  }
}

function removeFile(index: number) {
  files.value.splice(index, 1)
  emit('files-selected', files.value)
}

async function uploadFiles() {
  if (files.value.length === 0 || uploading.value) return

  uploading.value = true
  uploadProgress.value = 0
  errorMessage.value = ''

  try {
    const formData = new FormData()

    files.value.forEach((file, index) => {
      formData.append(props.multiple ? `files[${index}]` : 'file', file)
    })

    const response = await api.post(props.uploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        if (progressEvent.total) {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        }
      },
    })

    emit('upload-success', response.data)
    files.value = [] // Clear files after successful upload
  } catch (error: any) {
    console.error('Upload error:', error)
    errorMessage.value = error.response?.data?.message || 'Erreur lors du téléchargement'
    emit('upload-error', error)
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 B'

  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

function getFileIcon(type: string): string {
  if (type.startsWith('image/')) return '🖼️'
  if (type.startsWith('video/')) return '🎥'
  if (type.startsWith('audio/')) return '🎵'
  if (type.includes('pdf')) return '📄'
  if (type.includes('word') || type.includes('document')) return '📝'
  if (type.includes('excel') || type.includes('spreadsheet')) return '📊'
  if (type.includes('zip') || type.includes('rar') || type.includes('compressed')) return '📦'
  return '📄'
}

// Expose upload method for manual upload
defineExpose({
  uploadFiles,
  clearFiles: () => { files.value = [] },
  getFiles: () => files.value,
})
</script>
