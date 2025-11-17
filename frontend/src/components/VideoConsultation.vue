<template>
  <div class="fixed inset-0 bg-gray-900 z-50 flex flex-col">
    <!-- Header -->
    <div class="bg-gray-800 text-white px-4 py-3 flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
          <span class="font-bold text-xl">S</span>
        </div>
        <div>
          <p class="font-semibold">Consultation en cours</p>
          <p class="text-sm text-gray-400">{{ participantName }}</p>
        </div>
      </div>

      <div class="flex items-center space-x-4">
        <div v-if="connectionQuality" :class="['flex items-center space-x-2', getQualityClass()]">
          <span class="w-2 h-2 rounded-full" :class="getQualityDotClass()"></span>
          <span class="text-sm">{{ getQualityLabel() }}</span>
        </div>
        <span class="text-sm">{{ formattedDuration }}</span>
      </div>
    </div>

    <!-- Video Area -->
    <div class="flex-1 relative bg-black">
      <!-- Remote Video (main) -->
      <video
        ref="remoteVideo"
        autoplay
        playsinline
        class="w-full h-full object-contain"
      ></video>

      <!-- Local Video (PiP) -->
      <div class="absolute top-4 right-4 w-48 h-36 bg-gray-800 rounded-lg overflow-hidden shadow-lg">
        <video
          ref="localVideo"
          autoplay
          playsinline
          muted
          class="w-full h-full object-cover"
        ></video>
      </div>

      <!-- Status Overlay -->
      <div v-if="status !== 'connected'" class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
        <div class="text-center text-white">
          <div v-if="status === 'connecting'" class="space-y-4">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto"></div>
            <p class="text-lg">Connexion en cours...</p>
          </div>
          <div v-else-if="status === 'failed'" class="space-y-4">
            <span class="text-6xl">⚠️</span>
            <p class="text-lg">Échec de connexion</p>
            <p class="text-sm text-gray-400">{{ errorMessage }}</p>
            <button @click="retry" class="btn btn-primary mt-4">Réessayer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Controls Bar -->
    <div class="bg-gray-800 px-4 py-4">
      <div class="flex items-center justify-center space-x-4">
        <!-- Microphone Toggle -->
        <button
          @click="toggleAudio"
          :class="[
            'w-12 h-12 rounded-full flex items-center justify-center transition-colors',
            audioEnabled ? 'bg-gray-700 hover:bg-gray-600' : 'bg-red-600 hover:bg-red-700'
          ]"
        >
          <span class="text-2xl">{{ audioEnabled ? '🎤' : '🔇' }}</span>
        </button>

        <!-- Camera Toggle -->
        <button
          @click="toggleVideo"
          :class="[
            'w-12 h-12 rounded-full flex items-center justify-center transition-colors',
            videoEnabled ? 'bg-gray-700 hover:bg-gray-600' : 'bg-red-600 hover:bg-red-700'
          ]"
        >
          <span class="text-2xl">{{ videoEnabled ? '📹' : '🚫' }}</span>
        </button>

        <!-- End Call -->
        <button
          @click="endCall"
          class="w-16 h-16 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center transition-colors"
        >
          <span class="text-3xl">📞</span>
        </button>

        <!-- Screen Share (optional) -->
        <button
          @click="toggleScreenShare"
          class="w-12 h-12 rounded-full bg-gray-700 hover:bg-gray-600 flex items-center justify-center transition-colors"
        >
          <span class="text-2xl">🖥️</span>
        </button>

        <!-- Settings -->
        <button
          @click="showSettings = !showSettings"
          class="w-12 h-12 rounded-full bg-gray-700 hover:bg-gray-600 flex items-center justify-center transition-colors"
        >
          <span class="text-2xl">⚙️</span>
        </button>
      </div>

      <!-- Settings Panel -->
      <div v-if="showSettings" class="mt-4 bg-gray-700 rounded-lg p-4 text-white">
        <h3 class="font-semibold mb-3">Paramètres</h3>
        <div class="space-y-3">
          <div>
            <label class="block text-sm mb-1">Microphone</label>
            <select v-model="selectedAudioDevice" class="w-full bg-gray-600 rounded px-3 py-2 text-sm">
              <option v-for="device in audioDevices" :key="device.deviceId" :value="device.deviceId">
                {{ device.label || 'Microphone' }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Caméra</label>
            <select v-model="selectedVideoDevice" class="w-full bg-gray-600 rounded px-3 py-2 text-sm">
              <option v-for="device in videoDevices" :key="device.deviceId" :value="device.deviceId">
                {{ device.label || 'Caméra' }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

interface Props {
  appointmentId: number
  participantName: string
  isMedecin?: boolean
}

const props = defineProps<Props>()
const router = useRouter()

const localVideo = ref<HTMLVideoElement>()
const remoteVideo = ref<HTMLVideoElement>()

const status = ref<'connecting' | 'connected' | 'failed'>('connecting')
const errorMessage = ref('')
const audioEnabled = ref(true)
const videoEnabled = ref(true)
const showSettings = ref(false)
const connectionQuality = ref<'excellent' | 'good' | 'fair' | 'poor'>('good')
const duration = ref(0)

const audioDevices = ref<MediaDeviceInfo[]>([])
const videoDevices = ref<MediaDeviceInfo[]>([])
const selectedAudioDevice = ref<string>('')
const selectedVideoDevice = ref<string>('')

let localStream: MediaStream | null = null
let peerConnection: RTCPeerConnection | null = null
let durationInterval: number | null = null

const formattedDuration = computed(() => {
  const minutes = Math.floor(duration.value / 60)
  const seconds = duration.value % 60
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

async function initializeCall() {
  try {
    // Get room configuration from backend
    const response = await api.post(`/consultations/appointments/${props.appointmentId}/room`)
    const { ice_servers, token, room_id } = response.data

    // Get user media
    localStream = await navigator.mediaDevices.getUserMedia({
      video: true,
      audio: true
    })

    if (localVideo.value) {
      localVideo.value.srcObject = localStream
    }

    // Enumerate devices
    await enumerateDevices()

    // Create peer connection
    peerConnection = new RTCPeerConnection({
      iceServers: ice_servers
    })

    // Add local tracks
    localStream.getTracks().forEach(track => {
      peerConnection?.addTrack(track, localStream!)
    })

    // Handle remote stream
    peerConnection.ontrack = (event) => {
      if (remoteVideo.value) {
        remoteVideo.value.srcObject = event.streams[0]
      }
      status.value = 'connected'
      startDurationTimer()
    }

    // Handle ICE candidates
    peerConnection.onicecandidate = (event) => {
      if (event.candidate) {
        // Send candidate to signaling server
        // TODO: Implement signaling server communication
      }
    }

    // Monitor connection quality
    monitorConnectionQuality()

    status.value = 'connected'
  } catch (error: any) {
    console.error('Error initializing call:', error)
    status.value = 'failed'
    errorMessage.value = error.message || 'Erreur de connexion'
  }
}

async function enumerateDevices() {
  try {
    const devices = await navigator.mediaDevices.enumerateDevices()
    audioDevices.value = devices.filter(d => d.kind === 'audioinput')
    videoDevices.value = devices.filter(d => d.kind === 'videoinput')

    if (audioDevices.value.length > 0) {
      selectedAudioDevice.value = audioDevices.value[0].deviceId
    }
    if (videoDevices.value.length > 0) {
      selectedVideoDevice.value = videoDevices.value[0].deviceId
    }
  } catch (error) {
    console.error('Error enumerating devices:', error)
  }
}

function toggleAudio() {
  if (localStream) {
    const audioTrack = localStream.getAudioTracks()[0]
    if (audioTrack) {
      audioTrack.enabled = !audioTrack.enabled
      audioEnabled.value = audioTrack.enabled
    }
  }
}

function toggleVideo() {
  if (localStream) {
    const videoTrack = localStream.getVideoTracks()[0]
    if (videoTrack) {
      videoTrack.enabled = !videoTrack.enabled
      videoEnabled.value = videoTrack.enabled
    }
  }
}

async function toggleScreenShare() {
  try {
    const screenStream = await navigator.mediaDevices.getDisplayMedia({
      video: true
    })

    const screenTrack = screenStream.getVideoTracks()[0]
    const sender = peerConnection?.getSenders().find(s => s.track?.kind === 'video')

    if (sender) {
      sender.replaceTrack(screenTrack)
    }

    screenTrack.onended = () => {
      // Revert to camera
      const videoTrack = localStream?.getVideoTracks()[0]
      if (videoTrack && sender) {
        sender.replaceTrack(videoTrack)
      }
    }
  } catch (error) {
    console.error('Error sharing screen:', error)
  }
}

async function endCall() {
  if (confirm('Êtes-vous sûr de vouloir terminer la consultation ?')) {
    cleanup()

    // End consultation on backend
    try {
      await api.post(`/consultations/appointments/${props.appointmentId}/end`)
    } catch (error) {
      console.error('Error ending consultation:', error)
    }

    router.push(`/appointments/${props.appointmentId}`)
  }
}

function retry() {
  status.value = 'connecting'
  initializeCall()
}

function startDurationTimer() {
  durationInterval = window.setInterval(() => {
    duration.value++
  }, 1000)
}

function monitorConnectionQuality() {
  // Monitor connection stats
  const interval = setInterval(async () => {
    if (!peerConnection) return

    const stats = await peerConnection.getStats()
    // Analyze stats to determine quality
    // This is simplified - actual implementation would analyze packet loss, latency, etc.
    connectionQuality.value = 'good'
  }, 2000)

  return () => clearInterval(interval)
}

function getQualityClass() {
  const classes = {
    excellent: 'text-green-400',
    good: 'text-green-400',
    fair: 'text-yellow-400',
    poor: 'text-red-400'
  }
  return classes[connectionQuality.value]
}

function getQualityDotClass() {
  const classes = {
    excellent: 'bg-green-400',
    good: 'bg-green-400',
    fair: 'bg-yellow-400',
    poor: 'bg-red-400'
  }
  return classes[connectionQuality.value]
}

function getQualityLabel() {
  const labels = {
    excellent: 'Excellente',
    good: 'Bonne',
    fair: 'Moyenne',
    poor: 'Faible'
  }
  return labels[connectionQuality.value]
}

function cleanup() {
  if (durationInterval) {
    clearInterval(durationInterval)
  }

  if (localStream) {
    localStream.getTracks().forEach(track => track.stop())
  }

  if (peerConnection) {
    peerConnection.close()
  }
}

onMounted(() => {
  initializeCall()
})

onUnmounted(() => {
  cleanup()
})
</script>
