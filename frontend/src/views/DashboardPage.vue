<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <router-link to="/" class="flex items-center space-x-2">
              <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl">S</span>
              </div>
              <span class="text-xl font-bold text-primary-600">Seha Digital</span>
            </router-link>

            <div class="hidden md:flex space-x-4">
              <router-link
                to="/dashboard"
                class="px-3 py-2 rounded-lg text-sm font-medium text-gray-900 hover:bg-gray-100"
              >
                Tableau de bord
              </router-link>
              <router-link
                to="/appointments"
                class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"
              >
                Rendez-vous
              </router-link>
              <router-link
                v-if="authStore.isPatient"
                to="/medecins"
                class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"
              >
                Trouver un médecin
              </router-link>
              <router-link
                v-if="authStore.isPatient"
                to="/medical-record"
                class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"
              >
                Dossier médical
              </router-link>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">
              {{ authStore.user?.patient?.first_name || authStore.user?.medecin?.first_name }}
            </span>
            <button @click="handleLogout" class="btn btn-secondary text-sm">
              Déconnexion
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Patient Dashboard -->
      <div v-if="authStore.isPatient">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
          Bonjour {{ authStore.user?.patient?.first_name }} 👋
        </h1>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Rendez-vous à venir</p>
                <p class="text-3xl font-bold text-primary-600">{{ upcomingAppointments.length }}</p>
              </div>
              <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">📅</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Consultations totales</p>
                <p class="text-3xl font-bold text-success-600">{{ completedAppointments.length }}</p>
              </div>
              <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">✅</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Prochain RDV</p>
                <p class="text-lg font-semibold text-gray-900">
                  {{ nextAppointment ? formatDate(nextAppointment.appointment_date) : 'Aucun' }}
                </p>
              </div>
              <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">⏰</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-8">
          <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <router-link to="/medecins" class="btn btn-primary text-center">
              Réserver une consultation
            </router-link>
            <router-link to="/medical-record" class="btn btn-secondary text-center">
              Mon dossier médical
            </router-link>
            <router-link to="/appointments" class="btn btn-secondary text-center">
              Mes rendez-vous
            </router-link>
          </div>
        </div>

        <!-- Upcoming Appointments -->
        <div v-if="upcomingAppointments.length > 0" class="card">
          <h2 class="text-xl font-semibold mb-4">Prochains rendez-vous</h2>
          <div class="space-y-4">
            <div
              v-for="appointment in upcomingAppointments.slice(0, 3)"
              :key="appointment.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                  <span class="text-xl">👨‍⚕️</span>
                </div>
                <div>
                  <p class="font-semibold">Dr. {{ appointment.medecin?.first_name }} {{ appointment.medecin?.last_name }}</p>
                  <p class="text-sm text-gray-600">{{ appointment.medecin?.speciality }}</p>
                  <p class="text-sm text-gray-500">{{ formatDate(appointment.appointment_date) }}</p>
                </div>
              </div>
              <router-link
                :to="`/appointments/${appointment.id}`"
                class="btn btn-secondary text-sm"
              >
                Voir détails
              </router-link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="card text-center py-12">
          <span class="text-6xl mb-4 block">📅</span>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun rendez-vous à venir</h3>
          <p class="text-gray-600 mb-6">Réservez votre première consultation en quelques clics</p>
          <router-link to="/medecins" class="btn btn-primary">
            Trouver un médecin
          </router-link>
        </div>
      </div>

      <!-- Medecin Dashboard -->
      <div v-else-if="authStore.isMedecin">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
          Bonjour Dr. {{ authStore.user?.medecin?.last_name }} 👨‍⚕️
        </h1>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">RDV aujourd'hui</p>
                <p class="text-3xl font-bold text-primary-600">{{ todayAppointments.length }}</p>
              </div>
              <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">📅</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Consultations totales</p>
                <p class="text-3xl font-bold text-success-600">{{ authStore.user?.medecin?.consultation_count || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">✅</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Note moyenne</p>
                <p class="text-3xl font-bold text-yellow-600">
                  {{ authStore.user?.medecin?.rating_average?.toFixed(1) || '0.0' }} ⭐
                </p>
              </div>
              <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">⭐</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Revenus ce mois</p>
                <p class="text-3xl font-bold text-green-600">0 TND</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="text-2xl">💰</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Validation Status Alert -->
        <div v-if="authStore.user?.medecin?.validation_status !== 'validated'" class="card bg-yellow-50 border border-yellow-200 mb-8">
          <div class="flex items-start">
            <span class="text-2xl mr-3">⏳</span>
            <div>
              <h3 class="font-semibold text-yellow-900">Compte en cours de validation</h3>
              <p class="text-sm text-yellow-800 mt-1">
                Votre dossier est en cours d'examen. Vous recevrez un email dès qu'il sera validé (sous 72h).
              </p>
            </div>
          </div>
        </div>

        <!-- Today's Appointments -->
        <div class="card">
          <h2 class="text-xl font-semibold mb-4">Rendez-vous du jour</h2>
          <div v-if="todayAppointments.length > 0" class="space-y-4">
            <div
              v-for="appointment in todayAppointments"
              :key="appointment.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                  <span class="text-xl">👤</span>
                </div>
                <div>
                  <p class="font-semibold">{{ appointment.patient?.first_name }} {{ appointment.patient?.last_name }}</p>
                  <p class="text-sm text-gray-600">{{ appointment.reason }}</p>
                  <p class="text-sm text-gray-500">{{ formatTime(appointment.appointment_date) }}</p>
                </div>
              </div>
              <router-link
                :to="`/appointments/${appointment.id}`"
                class="btn btn-primary text-sm"
              >
                Commencer
              </router-link>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            Aucun rendez-vous prévu pour aujourd'hui
          </div>
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

const appointments = ref<Appointment[]>([])

const upcomingAppointments = computed(() =>
  appointments.value.filter(a =>
    a.status === 'confirmed' && new Date(a.appointment_date) > new Date()
  ).sort((a, b) =>
    new Date(a.appointment_date).getTime() - new Date(b.appointment_date).getTime()
  )
)

const completedAppointments = computed(() =>
  appointments.value.filter(a => a.status === 'completed')
)

const todayAppointments = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const tomorrow = new Date(today)
  tomorrow.setDate(tomorrow.getDate() + 1)

  return appointments.value.filter(a => {
    const appointmentDate = new Date(a.appointment_date)
    return appointmentDate >= today && appointmentDate < tomorrow && a.status === 'confirmed'
  })
})

const nextAppointment = computed(() => upcomingAppointments.value[0] || null)

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'short',
    timeStyle: 'short'
  }).format(date)
}

function formatTime(dateString: string): string {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    timeStyle: 'short'
  }).format(date)
}

async function fetchAppointments() {
  try {
    const response = await api.get('/appointments')
    appointments.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching appointments:', error)
  }
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'Login' })
}

onMounted(() => {
  fetchAppointments()
})
</script>
