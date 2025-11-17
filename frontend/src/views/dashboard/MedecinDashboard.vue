<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Welcome Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Bonjour Dr. {{ user?.last_name }} 👨‍⚕️
        </h1>
        <p class="mt-2 text-sm text-gray-600">
          Vue d'ensemble de votre journée - {{ formatToday() }}
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Dashboard Content -->
      <div v-else class="space-y-6">
        <!-- Today's Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card class="bg-gradient-to-br from-teal-500 to-teal-600 text-white">
            <div class="space-y-2">
              <p class="text-sm opacity-90">Consultations aujourd'hui</p>
              <p class="text-3xl font-bold">{{ todayStats.total_appointments }}</p>
              <p class="text-sm opacity-90">{{ todayStats.completed }} complétées</p>
            </div>
          </Card>

          <Card>
            <div class="space-y-2">
              <p class="text-sm text-gray-500">En attente</p>
              <p class="text-3xl font-bold text-yellow-600">{{ todayStats.pending }}</p>
              <p class="text-sm text-gray-500">À confirmer</p>
            </div>
          </Card>

          <Card>
            <div class="space-y-2">
              <p class="text-sm text-gray-500">Note moyenne</p>
              <p class="text-3xl font-bold text-gray-900">
                {{ stats.average_rating?.toFixed(1) || '0.0' }}
                <span class="text-yellow-400">⭐</span>
              </p>
              <p class="text-sm text-gray-500">{{ stats.total_reviews }} avis</p>
            </div>
          </Card>

          <Card>
            <div class="space-y-2">
              <p class="text-sm text-gray-500">Revenus ce mois</p>
              <p class="text-3xl font-bold text-green-600">{{ stats.monthly_revenue }} TND</p>
              <p class="text-sm text-gray-500">{{ stats.monthly_consultations }} consultations</p>
            </div>
          </Card>
        </div>

        <!-- Quick Actions -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h2>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button
              @click="$router.push('/medecin/availability')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Disponibilités</span>
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
              <span class="text-sm font-medium text-gray-900">Rendez-vous</span>
            </button>

            <button
              @click="$router.push('/medecin/analytics')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Statistiques</span>
            </button>

            <button
              @click="$router.push('/profile/medecin')"
              class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <span class="text-sm font-medium text-gray-900">Mon profil</span>
            </button>
          </div>
        </Card>

        <!-- Today's Schedule -->
        <Card>
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Programme du jour</h2>
            <Badge variant="teal">{{ todayAppointments.length }} RDV</Badge>
          </div>

          <div v-if="todayAppointments.length === 0" class="text-center py-8">
            <p class="text-gray-500">Aucun rendez-vous prévu aujourd'hui</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="appointment in todayAppointments"
              :key="appointment.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors"
              @click="$router.push(`/appointments/${appointment.id}`)"
            >
              <div class="flex items-center space-x-4">
                <div class="text-center">
                  <p class="text-2xl font-bold text-teal-600">{{ appointment.time }}</p>
                  <p class="text-xs text-gray-500">{{ getDuration(appointment) }} min</p>
                </div>
                <div class="h-12 w-px bg-gray-300"></div>
                <div>
                  <p class="font-medium text-gray-900">
                    {{ appointment.patient?.user?.first_name }} {{ appointment.patient?.user?.last_name }}
                  </p>
                  <p class="text-sm text-gray-500">{{ appointment.motif || 'Consultation générale' }}</p>
                  <div class="flex items-center mt-1 space-x-2">
                    <Badge :variant="appointment.type === 'video' ? 'blue' : 'gray'" size="sm">
                      {{ appointment.type === 'video' ? '📹 Vidéo' : '🏥 Cabinet' }}
                    </Badge>
                    <Badge :variant="getStatusVariant(appointment.status)" size="sm">
                      {{ getStatusLabel(appointment.status) }}
                    </Badge>
                  </div>
                </div>
              </div>
              <Button
                v-if="appointment.status === 'confirmed' && appointment.type === 'video'"
                variant="primary"
                size="sm"
                @click.stop="startVideoConsultation(appointment)"
              >
                Démarrer
              </Button>
            </div>
          </div>
        </Card>

        <!-- Recent Reviews -->
        <Card v-if="recentReviews.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Derniers avis</h2>
            <Button variant="outline" size="sm" @click="viewAllReviews">
              Voir tout
            </Button>
          </div>
          <div class="space-y-3">
            <div
              v-for="review in recentReviews.slice(0, 3)"
              :key="review.id"
              class="p-4 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="font-medium text-gray-900">
                  {{ review.patient?.user?.first_name }} {{ review.patient?.user?.last_name?.charAt(0) }}.
                </p>
                <div class="flex items-center">
                  <span class="text-yellow-400 mr-1">⭐</span>
                  <span class="font-semibold">{{ review.overall_rating }}</span>
                </div>
              </div>
              <p class="text-sm text-gray-600">{{ review.comment }}</p>
              <p class="text-xs text-gray-400 mt-2">{{ formatDate(review.created_at) }}</p>
            </div>
          </div>
        </Card>

        <!-- Performance Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Consultations cette semaine</h3>
            <div class="space-y-3">
              <div v-for="day in weeklyStats" :key="day.day" class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ day.day }}</span>
                <div class="flex items-center flex-1 mx-4">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-teal-600 h-2 rounded-full"
                      :style="{ width: `${(day.count / maxDailyCount) * 100}%` }"
                    ></div>
                  </div>
                  <span class="text-sm font-medium text-gray-900 ml-3 w-8 text-right">{{ day.count }}</span>
                </div>
              </div>
            </div>
          </Card>

          <Card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de satisfaction</h3>
            <div class="space-y-3">
              <div v-for="(value, criterion) in satisfactionBreakdown" :key="criterion">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm text-gray-600">{{ getCriterionLabel(criterion) }}</span>
                  <span class="text-sm font-medium">{{ value }}/5</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-yellow-400 h-2 rounded-full"
                    :style="{ width: `${(value / 5) * 100}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </Card>
        </div>
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
const todayStats = ref({
  total_appointments: 0,
  completed: 0,
  pending: 0,
})
const stats = ref({
  average_rating: 0,
  total_reviews: 0,
  monthly_revenue: 0,
  monthly_consultations: 0,
})
const todayAppointments = ref<any[]>([])
const recentReviews = ref<any[]>([])
const weeklyStats = ref<any[]>([])
const satisfactionBreakdown = ref<any>({})

const user = computed(() => authStore.user)
const maxDailyCount = computed(() => Math.max(...weeklyStats.value.map(d => d.count), 1))

onMounted(async () => {
  await loadDashboard()
})

async function loadDashboard() {
  try {
    loading.value = true

    const today = new Date().toISOString().split('T')[0]

    const [appointmentsRes, analyticsRes, reviewsRes] = await Promise.all([
      api.get('/appointments', { params: { date: today } }),
      api.get('/analytics/overview'),
      api.get(`/reviews/medecins/${authStore.user?.medecin?.id}`, { params: { limit: 5 } }),
    ])

    todayAppointments.value = appointmentsRes.data.appointments || []
    todayStats.value = {
      total_appointments: todayAppointments.value.length,
      completed: todayAppointments.value.filter((a: any) => a.status === 'completed').length,
      pending: todayAppointments.value.filter((a: any) => a.status === 'pending').length,
    }

    stats.value = {
      average_rating: analyticsRes.data.satisfaction_rate || 0,
      total_reviews: analyticsRes.data.total_reviews || 0,
      monthly_revenue: analyticsRes.data.monthly_revenue || 0,
      monthly_consultations: analyticsRes.data.total_consultations || 0,
    }

    recentReviews.value = reviewsRes.data.reviews || []

    // Mock weekly stats
    weeklyStats.value = [
      { day: 'Lun', count: 8 },
      { day: 'Mar', count: 12 },
      { day: 'Mer', count: 10 },
      { day: 'Jeu', count: 15 },
      { day: 'Ven', count: 11 },
      { day: 'Sam', count: 5 },
      { day: 'Dim', count: 0 },
    ]

    satisfactionBreakdown.value = {
      professionalism: 4.8,
      listening: 4.7,
      explanation: 4.6,
      punctuality: 4.5,
      effectiveness: 4.7,
    }

  } catch (error: any) {
    console.error('Error loading dashboard:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

function formatToday(): string {
  return new Date().toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('fr-FR')
}

function getDuration(appointment: any): number {
  return appointment.type === 'video' ? 30 : 20
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

function getStatusVariant(status: string): string {
  const variants: Record<string, string> = {
    pending: 'warning',
    confirmed: 'teal',
    completed: 'success',
    cancelled: 'danger',
  }
  return variants[status] || 'gray'
}

function getCriterionLabel(criterion: string): string {
  const labels: Record<string, string> = {
    professionalism: 'Professionnalisme',
    listening: 'Écoute',
    explanation: 'Explications',
    punctuality: 'Ponctualité',
    effectiveness: 'Efficacité',
  }
  return labels[criterion] || criterion
}

function startVideoConsultation(appointment: any) {
  router.push(`/consultations/${appointment.id}`)
}

function viewAllReviews() {
  // TODO: Navigate to reviews page
  toast.info('Fonctionnalité à venir')
}
</script>
