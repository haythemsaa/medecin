<template>
  <teleport to="body">
    <div class="fixed top-4 right-4 z-50 space-y-3 w-96 max-w-full px-4">
      <transition-group name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'rounded-lg shadow-lg p-4 flex items-start',
            getToastClass(toast.type)
          ]"
        >
          <div class="flex-shrink-0 mr-3 text-2xl">
            {{ getToastIcon(toast.type) }}
          </div>

          <div class="flex-1 min-w-0">
            <p v-if="toast.title" class="text-sm font-semibold mb-1">
              {{ toast.title }}
            </p>
            <p class="text-sm">
              {{ toast.message }}
            </p>
          </div>

          <button
            @click="removeToast(toast.id)"
            class="flex-shrink-0 ml-3 text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue'

export interface Toast {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  title?: string
  message: string
  duration?: number
}

const toasts = ref<Toast[]>([])

function addToast(toast: Omit<Toast, 'id'>) {
  const id = `toast-${Date.now()}-${Math.random()}`
  const duration = toast.duration || 5000

  toasts.value.push({ ...toast, id })

  // Auto-remove after duration
  if (duration > 0) {
    setTimeout(() => {
      removeToast(id)
    }, duration)
  }
}

function removeToast(id: string) {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

function getToastClass(type: Toast['type']): string {
  const classes: Record<Toast['type'], string> = {
    success: 'bg-green-50 border-l-4 border-green-500 text-green-900',
    error: 'bg-red-50 border-l-4 border-red-500 text-red-900',
    warning: 'bg-yellow-50 border-l-4 border-yellow-500 text-yellow-900',
    info: 'bg-blue-50 border-l-4 border-blue-500 text-blue-900'
  }
  return classes[type]
}

function getToastIcon(type: Toast['type']): string {
  const icons: Record<Toast['type'], string> = {
    success: '✅',
    error: '❌',
    warning: '⚠️',
    info: 'ℹ️'
  }
  return icons[type]
}

// Expose methods for use in composable
defineExpose({
  addToast,
  removeToast
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>
