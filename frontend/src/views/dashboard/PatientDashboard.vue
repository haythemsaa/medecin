<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Welcome Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Bonjour {{ user?.first_name }} 👋
        </h1>
        <p class="mt-2 text-sm text-gray-600">
          Voici un aperçu de votre santé
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Dashboard Content -->
      <div v-else class="space-y-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card class="bg-gradient-to-br from-teal-500 to-teal-600 text-white">
            <div class="space-y-2">
              <p class="text-sm opacity-90">Prochain rendez-vous</p>
              <p class="text-2xl font-bold">
                {{ upcomingAppointment ? formatDate(upcomingAppointment.date) : 'Aucun' }}
              </p>
              <p v-if="upcomingAppointment" class="text-sm opacity-90">
                {{ upcomingAppointment.time }} - Dr. {{ upcomingAppointment.medecin?.user?.last_name }}
              </p>
            </div>
          </Card>

          <Card>
            <div class="space-y-2">
              <p class="text-sm text-gray-500">Consultations</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total_consultations }}</p>
              <p class="text-sm text-green-600">✓ Complétées</p>
            </div>
          </Card>

          <Card>
            <div class="space-y-2">
              <p class="text-sm text-gray-500">Prescriptions actives</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.active_prescriptions }}</p>
              <p class="text-sm text-gray-500">En cours</p>
            </div>
          </Card>

          <Card>
            <div class="space-y-2">
              <p class="text-sm text-gray-500">Médecins suivis</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.favorite_doctors }}</p>
              <p class="text-sm text-gray-500">Favoris</p>
            </div>
          </Card>
        </div>

        <!-- Quick Actions -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button
              @click="$router.push('/medecins')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Trouver un médecin</span>
            </button>

            <button
              @click="$router.push('/appointments')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Mes rendez-vous</span>
            </button>

            <button
              @click="$router.push('/prescriptions')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Mes prescriptions</span>
            </button>

            <button
              @click="$router.push('/medical-record')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Dossier médical</span>
            </button>
          </div>
        </Card>

        <!-- Upcoming Appointments -->
        <Card v-if="upcomingAppointments.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Rendez-vous à venir</h2>
            <Button variant="outline" size="sm" @click="$router.push('/appointments')">
              Voir tout
            </Button>
          </div>
          <div class="space-y-3">
            <div
              v-for="appointment in upcomingAppointments.slice(0, 3)"
              :key="appointment.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors"
              @click="$router.push(`/appointments/${appointment.id}`)"
            >
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                  <span class="text-lg font-bold text-teal-600">
                    {{ appointment.medecin?.user?.first_name?.charAt(0) }}{{ appointment.medecin?.user?.last_name?.charAt(0) }}
                  </span>
                </div>
                <div>
                  <p class="font-medium text-gray-900">
                    Dr. {{ appointment.medecin?.user?.first_name }} {{ appointment.medecin?.user?.last_name }}
                  </p>
                  <p class="text-sm text-gray-500">{{ appointment.medecin?.specialite }}</p>
                  <p class="text-sm text-gray-600 mt-1">
                    {{ formatDate(appointment.date) }} à {{ appointment.time }}
                  </p>
                </div>
              </div>
              <Badge :variant="getAppointmentBadgeVariant(appointment.status)">
                {{ getStatusLabel(appointment.status) }}
              </Badge>
            </div>
          </div>
        </Card>

        <!-- Recent Prescriptions -->
        <Card v-if="recentPrescriptions.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Prescriptions récentes</h2>
            <Button variant="outline" size="sm" @click="$router.push('/prescriptions')">
              Voir tout
            </Button>
          </div>
          <div class="space-y-3">
            <div
              v-for="prescription in recentPrescriptions.slice(0, 3)"
              :key="prescription.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
            >
              <div>
                <p class="font-medium text-gray-900">
                  Prescription du {{ formatDate(prescription.created_at) }}
                </p>
                <p class="text-sm text-gray-500">
                  Dr. {{ prescription.medecin?.user?.last_name }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                  {{ prescription.medications?.length || 0 }} médicament(s)
                </p>
              </div>
              <Button variant="outline" size="sm" @click="downloadPrescription(prescription)">
                Télécharger
              </Button>
            </div>
          </div>
        </Card>

        <!-- Health Tips -->
        <Card class="bg-gradient-to-br from-blue-50 to-teal-50 border-teal-200">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Conseil santé du jour</h3>
              <p class="text-gray-700">
                N'oubliez pas de boire au moins 1,5L d'eau par jour pour rester hydraté et maintenir une bonne santé.
              </p>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Button from '@/components/Button.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const loading = ref(true)
const stats = ref({
  total_consultations: 0,
  active_prescriptions: 0,
  favorite_doctors: 0,
})
const upcomingAppointments = ref<any[]>([])
const recentPrescriptions = ref<any[]>([])

const user = computed(() => authStore.user)
const upcomingAppointment = computed(() => upcomingAppointments.value[0])

onMounted(async () => {
  await loadDashboard()
})

async function loadDashboard() {
  try {
    loading.value = true

    const [appointmentsRes, prescriptionsRes] = await Promise.all([
      api.get('/appointments', { params: { status: 'confirmed,pending', upcoming: true } }),
      api.get('/prescriptions/my-prescriptions', { params: { limit: 5 } }),
    ])

    upcomingAppointments.value = appointmentsRes.data.appointments || []
    recentPrescriptions.value = prescriptionsRes.data.prescriptions || []

    // Calculate stats
    stats.value.total_consultations = appointmentsRes.data.total_completed || 0
    stats.value.active_prescriptions = recentPrescriptions.value.filter(
      (p: any) => new Date(p.valid_until) > new Date()
    ).length
    stats.value.favorite_doctors = 0 // TODO: Implement favorites

  } catch (error: any) {
    console.error('Error loading dashboard:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

async function downloadPrescription(prescription: any) {
  try {
    const response = await api.get(`/prescriptions/${prescription.id}/download`, {
      responseType: 'blob',
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `prescription_${prescription.id}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()

    toast.success('Prescription téléchargée')
  } catch (error) {
    toast.error('Erreur lors du téléchargement')
  }
}

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    pending: 'En attente',
    confirmed: 'Confirmé',
    completed: 'Complété',
    cancelled: 'Annulé',
  }
  return labels[status] || status
}

function getAppointmentBadgeVariant(status: string): string {
  const variants: Record<string, string> = {
    pending: 'warning',
    confirmed: 'teal',
    completed: 'success',
    cancelled: 'danger',
  }
  return variants[status] || 'gray'
}
</script>
