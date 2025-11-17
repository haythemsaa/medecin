<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation (simplified for brevity, should be extracted to component) -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <router-link to="/dashboard" class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">S</span>
            </div>
            <span class="text-xl font-bold text-primary-600">Seha Digital</span>
          </router-link>
          <div class="flex items-center space-x-4">
            <router-link to="/dashboard" class="btn btn-secondary text-sm">
              Tableau de bord
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mes rendez-vous</h1>
        <p class="mt-2 text-gray-600">Gérez vos consultations passées et à venir</p>
      </div>

      <!-- Tabs -->
      <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="currentTab = tab.id"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              currentTab === tab.id
                ? 'border-primary-600 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span
              :class="[
                'ml-2 py-0.5 px-2 rounded-full text-xs',
                currentTab === tab.id
                  ? 'bg-primary-100 text-primary-600'
                  : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ getTabCount(tab.id) }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Chargement...</p>
      </div>

      <!-- Appointments List -->
      <div v-else class="space-y-4">
        <div
          v-for="appointment in filteredAppointments"
          :key="appointment.id"
          class="card hover:shadow-lg transition-shadow cursor-pointer"
          @click="router.push(`/appointments/${appointment.id}`)"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4 flex-1">
              <!-- Avatar -->
              <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-2xl">
                  {{ authStore.isPatient ? '👨‍⚕️' : '👤' }}
                </span>
              </div>

              <!-- Info -->
              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <div>
                    <h3 class="font-semibold text-lg">
                      <template v-if="authStore.isPatient">
                        Dr. {{ appointment.medecin?.first_name }} {{ appointment.medecin?.last_name }}
                      </template>
                      <template v-else>
                        {{ appointment.patient?.first_name }} {{ appointment.patient?.last_name }}
                      </template>
                    </h3>
                    <p v-if="authStore.isPatient" class="text-sm text-primary-600">
                      {{ appointment.medecin?.speciality }}
                    </p>
                  </div>

                  <!-- Status Badge -->
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium',
                      getStatusClass(appointment.status)
                    ]"
                  >
                    {{ getStatusLabel(appointment.status) }}
                  </span>
                </div>

                <!-- Details -->
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                  <div>
                    <p class="text-gray-500">Date & Heure</p>
                    <p class="font-medium">{{ formatDate(appointment.appointment_date) }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Type</p>
                    <p class="font-medium">
                      {{ appointment.type === 'video' ? '📹 Vidéo' : '📞 Téléphone' }}
                    </p>
                  </div>
                  <div>
                    <p class="text-gray-500">Durée</p>
                    <p class="font-medium">{{ appointment.duration }} min</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Prix</p>
                    <p class="font-medium">{{ appointment.price }} TND</p>
                  </div>
                </div>

                <!-- Reason -->
                <div class="mt-3">
                  <p class="text-sm text-gray-600">
                    <span class="font-medium">Motif:</span> {{ appointment.reason }}
                  </p>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex items-center space-x-3">
                  <button
                    v-if="canJoin(appointment)"
                    @click.stop="joinConsultation(appointment.id)"
                    class="btn btn-success text-sm"
                  >
                    Rejoindre la consultation
                  </button>

                  <button
                    v-if="canCancel(appointment)"
                    @click.stop="cancelAppointment(appointment)"
                    class="btn btn-secondary text-sm"
                  >
                    Annuler
                  </button>

                  <router-link
                    :to="`/appointments/${appointment.id}`"
                    class="btn btn-secondary text-sm"
                    @click.stop
                  >
                    Voir détails
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredAppointments.length === 0" class="card text-center py-12">
          <span class="text-6xl mb-4 block">📅</span>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">
            Aucun rendez-vous {{ currentTab === 'upcoming' ? 'à venir' : currentTab === 'past' ? 'passé' : '' }}
          </h3>
          <p class="text-gray-600 mb-6">
            {{ authStore.isPatient ? 'Trouvez un médecin et réservez votre prochaine consultation' : 'Vous n\'avez pas encore de rendez-vous' }}
          </p>
          <router-link v-if="authStore.isPatient" to="/medecins" class="btn btn-primary">
            Trouver un médecin
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import type { Appointment } from '@/types'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const appointments = ref<Appointment[]>([])
const currentTab = ref('upcoming')

const tabs = [
  { id: 'upcoming', label: 'À venir' },
  { id: 'past', label: 'Passés' },
  { id: 'cancelled', label: 'Annulés' }
]

const filteredAppointments = computed(() => {
  const now = new Date()

  switch (currentTab.value) {
    case 'upcoming':
      return appointments.value.filter(a =>
        a.status === 'confirmed' && new Date(a.appointment_date) > now
      )
    case 'past':
      return appointments.value.filter(a =>
        a.status === 'completed' || (a.status === 'confirmed' && new Date(a.appointment_date) < now)
      )
    case 'cancelled':
      return appointments.value.filter(a => a.status === 'cancelled')
    default:
      return appointments.value
  }
})

function getTabCount(tabId: string): number {
  const now = new Date()
  switch (tabId) {
    case 'upcoming':
      return appointments.value.filter(a =>
        a.status === 'confirmed' && new Date(a.appointment_date) > now
      ).length
    case 'past':
      return appointments.value.filter(a =>
        a.status === 'completed' || (a.status === 'confirmed' && new Date(a.appointment_date) < now)
      ).length
    case 'cancelled':
      return appointments.value.filter(a => a.status === 'cancelled').length
    default:
      return 0
  }
}

function getStatusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    no_show: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    pending: 'En attente',
    confirmed: 'Confirmé',
    completed: 'Terminé',
    cancelled: 'Annulé',
    no_show: 'Absent'
  }
  return labels[status] || status
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(date)
}

function canJoin(appointment: Appointment): boolean {
  if (appointment.status !== 'confirmed') return false

  const appointmentTime = new Date(appointment.appointment_date)
  const now = new Date()
  const diffMinutes = (appointmentTime.getTime() - now.getTime()) / 1000 / 60

  // Can join 10 minutes before until end of consultation
  return diffMinutes <= 10 && diffMinutes >= -appointment.duration
}

function canCancel(appointment: Appointment): boolean {
  if (appointment.status !== 'confirmed') return false

  const appointmentTime = new Date(appointment.appointment_date)
  const now = new Date()

  return appointmentTime > now
}

function joinConsultation(appointmentId: number) {
  router.push(`/consultation/${appointmentId}`)
}

async function cancelAppointment(appointment: Appointment) {
  const reason = prompt('Raison de l\'annulation (optionnelle):')
  if (reason === null) return // User cancelled

  try {
    await api.post(`/appointments/${appointment.id}/cancel`, { reason })
    // Refresh appointments
    await fetchAppointments()
    alert('Rendez-vous annulé avec succès')
  } catch (error: any) {
    alert(error.response?.data?.message || 'Erreur lors de l\'annulation')
  }
}

async function fetchAppointments() {
  loading.value = true
  try {
    const response = await api.get('/appointments')
    appointments.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching appointments:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchAppointments()
})
</script>
