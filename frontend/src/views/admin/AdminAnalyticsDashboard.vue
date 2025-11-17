<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord administrateur</h1>
        <p class="mt-2 text-sm text-gray-600">
          Vue d'ensemble de la plateforme Seha Digital
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement des statistiques..." />

      <!-- Dashboard Content -->
      <div v-else class="space-y-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Utilisateurs</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.total_users }}</p>
              </div>
            </div>
          </Card>

          <Card>
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-teal-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Médecins Actifs</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.active_medecins }}</p>
              </div>
            </div>
          </Card>

          <Card>
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">RDV ce mois</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.appointments_this_month }}</p>
              </div>
            </div>
          </Card>

          <Card>
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Revenus ce mois</p>
                <p class="text-2xl font-semibold text-gray-900">{{ stats.revenue_this_month }} TND</p>
              </div>
            </div>
          </Card>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rendez-vous par statut</h3>
            <div class="space-y-3">
              <div v-for="(value, status) in stats.appointments_by_status" :key="status" class="flex items-center justify-between">
                <span class="text-sm text-gray-600 capitalize">{{ getStatusLabel(status) }}</span>
                <div class="flex items-center">
                  <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                    <div
                      :class="getStatusColor(status)"
                      class="h-2 rounded-full"
                      :style="{ width: `${(value / stats.total_appointments) * 100}%` }"
                    ></div>
                  </div>
                  <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ value }}</span>
                </div>
              </div>
            </div>
          </Card>

          <Card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisateurs par rôle</h3>
            <div class="space-y-3">
              <div v-for="(value, role) in stats.users_by_role" :key="role" class="flex items-center justify-between">
                <span class="text-sm text-gray-600 capitalize">{{ getRoleLabel(role) }}</span>
                <div class="flex items-center">
                  <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                    <div
                      class="bg-teal-600 h-2 rounded-full"
                      :style="{ width: `${(value / stats.total_users) * 100}%` }"
                    ></div>
                  </div>
                  <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ value }}</span>
                </div>
              </div>
            </div>
          </Card>
        </div>

        <!-- Pending Validations -->
        <Card v-if="pendingValidations.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Validations en attente</h3>
            <Badge variant="warning">{{ pendingValidations.length }}</Badge>
          </div>
          <div class="space-y-3">
            <div
              v-for="medecin in pendingValidations"
              :key="medecin.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div>
                <p class="font-medium text-gray-900">
                  Dr. {{ medecin.user.first_name }} {{ medecin.user.last_name }}
                </p>
                <p class="text-sm text-gray-500">{{ medecin.specialite }}</p>
              </div>
              <Button
                variant="primary"
                size="sm"
                @click="$router.push(`/admin/medecins/${medecin.id}`)"
              >
                Examiner
              </Button>
            </div>
          </div>
        </Card>

        <!-- Recent Activity -->
        <Card>
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Activité récente</h3>
          <div class="space-y-4">
            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3">
              <div class="flex-shrink-0 w-2 h-2 bg-teal-600 rounded-full mt-2"></div>
              <div class="flex-1">
                <p class="text-sm text-gray-900">{{ activity.description }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ formatDate(activity.created_at) }}</p>
              </div>
            </div>
          </div>
        </Card>

        <!-- Export Section -->
        <Card>
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Exporter les données</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <Button variant="outline" @click="exportData('appointments')">
              Rendez-vous
            </Button>
            <Button variant="outline" @click="exportData('consultations')">
              Consultations
            </Button>
            <Button variant="outline" @click="exportData('payments')">
              Paiements
            </Button>
            <Button variant="outline" @click="exportData('reviews')">
              Avis
            </Button>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Button from '@/components/Button.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const toast = useToast()
const loading = ref(true)
const stats = ref<any>({})
const pendingValidations = ref<any[]>([])
const recentActivity = ref<any[]>([])

onMounted(async () => {
  await loadDashboard()
})

async function loadDashboard() {
  try {
    loading.value = true

    const [statsRes, validationsRes] = await Promise.all([
      api.get('/admin/statistics'),
      api.get('/admin/medecins/pending'),
    ])

    stats.value = statsRes.data
    pendingValidations.value = validationsRes.data.medecins || []

    // Mock recent activity (would come from API)
    recentActivity.value = [
      { id: 1, description: 'Nouveau patient inscrit', created_at: new Date().toISOString() },
      { id: 2, description: 'Rendez-vous confirmé', created_at: new Date(Date.now() - 3600000).toISOString() },
      { id: 3, description: 'Paiement reçu', created_at: new Date(Date.now() - 7200000).toISOString() },
    ]
  } catch (error: any) {
    console.error('Error loading dashboard:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

async function exportData(type: string) {
  try {
    const response = await api.get(`/export/${type}`, {
      params: { format: 'csv' },
      responseType: 'blob',
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `${type}_${new Date().toISOString().split('T')[0]}.csv`)
    document.body.appendChild(link)
    link.click()
    link.remove()

    toast.success('Export réussi')
  } catch (error) {
    toast.error('Erreur lors de l\'export')
  }
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    pending: 'En attente',
    confirmed: 'Confirmé',
    completed: 'Complété',
    cancelled: 'Annulé',
    no_show: 'Absent',
  }
  return labels[status] || status
}

function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-500',
    confirmed: 'bg-blue-500',
    completed: 'bg-green-500',
    cancelled: 'bg-red-500',
    no_show: 'bg-gray-500',
  }
  return colors[status] || 'bg-gray-500'
}

function getRoleLabel(role: string): string {
  const labels: Record<string, string> = {
    patient: 'Patients',
    medecin: 'Médecins',
    admin: 'Administrateurs',
  }
  return labels[role] || role
}

function formatDate(date: string): string {
  return new Date(date).toLocaleString('fr-FR')
}
</script>
