<template>
  <div v-if="loading" class="min-h-screen bg-gray-900 flex items-center justify-center">
    <div class="text-center text-white">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
      <p class="text-lg">Chargement de la consultation...</p>
    </div>
  </div>

  <div v-else-if="error" class="min-h-screen bg-gray-900 flex items-center justify-center">
    <div class="text-center text-white">
      <span class="text-6xl mb-4 block">⚠️</span>
      <p class="text-lg mb-4">{{ error }}</p>
      <router-link to="/appointments" class="btn btn-primary">
        Retour aux rendez-vous
      </router-link>
    </div>
  </div>

  <VideoConsultation
    v-else-if="appointment"
    :appointment-id="appointment.id"
    :participant-name="participantName"
    :is-medecin="authStore.isMedecin"
  />
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import VideoConsultation from '@/components/VideoConsultation.vue'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const error = ref('')
const appointment = ref<any>(null)

const participantName = computed(() => {
  if (!appointment.value) return ''

  if (authStore.isMedecin) {
    return `${appointment.value.patient.first_name} ${appointment.value.patient.last_name}`
  } else {
    return `Dr. ${appointment.value.medecin.first_name} ${appointment.value.medecin.last_name}`
  }
})

async function loadAppointment() {
  try {
    const appointmentId = route.params.appointmentId
    const response = await api.get(`/appointments/${appointmentId}`)
    appointment.value = response.data

    // Verify appointment status and timing
    const now = new Date()
    const appointmentDate = new Date(appointment.value.appointment_date)
    const diffMinutes = (appointmentDate.getTime() - now.getTime()) / 1000 / 60

    if (appointment.value.status !== 'confirmed') {
      error.value = 'Ce rendez-vous n\'est pas confirmé'
      return
    }

    // Can join 10 minutes before until appointment end
    if (diffMinutes > 10) {
      error.value = 'La consultation n\'est pas encore disponible'
      return
    }

    if (diffMinutes < -appointment.value.duration) {
      error.value = 'Cette consultation est terminée'
      return
    }

  } catch (err: any) {
    console.error('Error loading appointment:', err)
    error.value = err.response?.data?.message || 'Erreur lors du chargement du rendez-vous'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAppointment()
})
</script>
