<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord analytique</h1>
        <p class="mt-2 text-sm text-gray-600">
          Visualisez vos performances et statistiques
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement des données..." />

      <!-- Analytics Content -->
      <div v-else class="space-y-6">
        <!-- Overview Stats -->
        <AnalyticsChart
          title="Vue d'ensemble"
          type="stats"
          :chart-data="overviewStats"
          :show-period-filter="false"
        />

        <!-- Revenue Chart -->
        <AnalyticsChart
          title="Revenus"
          type="line"
          :chart-data="revenueData"
          :show-period-filter="true"
          value-format="currency"
          @period-change="handleRevenuePerio dChange"
        />

        <!-- Consultations by Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <AnalyticsChart
            title="Consultations par statut"
            type="pie"
            :chart-data="consultationsByStatus"
            :show-period-filter="false"
          />

          <AnalyticsChart
            title="Consultations par mois"
            type="bar"
            :chart-data="consultationsByMonth"
            :show-period-filter="false"
          />
        </div>

        <!-- Patient Demographics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <AnalyticsChart
            title="Patients par tranche d'âge"
            type="bar"
            :chart-data="patientsByAge"
            :show-period-filter="false"
          />

          <AnalyticsChart
            title="Taux de satisfaction"
            type="stats"
            :chart-data="satisfactionStats"
            :show-period-filter="false"
          />
        </div>

        <!-- Appointment Trends -->
        <AnalyticsChart
          title="Évolution des rendez-vous"
          type="line"
          :chart-data="appointmentTrends"
          :show-period-filter="true"
          @period-change="handleAppointmentPeriodChange"
        />

        <!-- Top Diagnoses -->
        <AnalyticsChart
          title="Diagnostics les plus fréquents"
          type="bar"
          :chart-data="topDiagnoses"
          :show-period-filter="false"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AnalyticsChart from '@/components/AnalyticsChart.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const router = useRouter()
const toast = useToast()
const loading = ref(true)

// Overview statistics
const overviewStats = ref([
  { label: 'Total Consultations', value: 0 },
  { label: 'Patients Actifs', value: 0 },
  { label: 'Revenus ce mois', value: 0 },
  { label: 'Taux de satisfaction', value: 0 },
])

// Revenue data
const revenueData = ref<Array<{ label: string; value: number }>>([])
const revenuePeriod = ref('30d')

// Consultations by status
const consultationsByStatus = ref<Array<{ label: string; value: number }>>([])

// Consultations by month
const consultationsByMonth = ref<Array<{ label: string; value: number }>>([])

// Patients by age
const patientsByAge = ref<Array<{ label: string; value: number }>>([])

// Satisfaction stats
const satisfactionStats = ref([
  { label: 'Note moyenne', value: 0 },
  { label: 'Avis positifs', value: 0 },
  { label: 'Ponctualité', value: 0 },
  { label: 'Écoute', value: 0 },
])

// Appointment trends
const appointmentTrends = ref<Array<{ label: string; value: number }>>([])
const appointmentPeriod = ref('30d')

// Top diagnoses
const topDiagnoses = ref<Array<{ label: string; value: number }>>([])

onMounted(async () => {
  await loadAnalytics()
})

async function loadAnalytics() {
  try {
    loading.value = true

    // Fetch all analytics data
    const [
      overviewRes,
      revenueRes,
      statusRes,
      monthlyRes,
      ageRes,
      satisfactionRes,
      trendsRes,
      diagnosesRes,
    ] = await Promise.all([
      api.get('/analytics/overview'),
      api.get(`/analytics/revenue?period=${revenuePeriod.value}`),
      api.get('/analytics/consultations-by-status'),
      api.get('/analytics/consultations-by-month'),
      api.get('/analytics/patients-by-age'),
      api.get('/analytics/satisfaction'),
      api.get(`/analytics/appointment-trends?period=${appointmentPeriod.value}`),
      api.get('/analytics/top-diagnoses'),
    ])

    // Update overview stats
    overviewStats.value = [
      { label: 'Total Consultations', value: overviewRes.data.total_consultations },
      { label: 'Patients Actifs', value: overviewRes.data.active_patients },
      { label: 'Revenus ce mois', value: overviewRes.data.monthly_revenue },
      { label: 'Taux de satisfaction', value: overviewRes.data.satisfaction_rate },
    ]

    // Update revenue data
    revenueData.value = revenueRes.data.data

    // Update consultations by status
    consultationsByStatus.value = statusRes.data.data

    // Update consultations by month
    consultationsByMonth.value = monthlyRes.data.data

    // Update patients by age
    patientsByAge.value = ageRes.data.data

    // Update satisfaction stats
    const satisfaction = satisfactionRes.data
    satisfactionStats.value = [
      { label: 'Note moyenne', value: parseFloat(satisfaction.average_rating.toFixed(1)) },
      { label: 'Avis positifs', value: satisfaction.positive_reviews_percentage },
      { label: 'Ponctualité', value: parseFloat(satisfaction.average_punctuality.toFixed(1)) },
      { label: 'Écoute', value: parseFloat(satisfaction.average_listening.toFixed(1)) },
    ]

    // Update appointment trends
    appointmentTrends.value = trendsRes.data.data

    // Update top diagnoses
    topDiagnoses.value = diagnosesRes.data.data

  } catch (error: any) {
    console.error('Error loading analytics:', error)
    toast.error('Erreur lors du chargement des statistiques')
  } finally {
    loading.value = false
  }
}

async function handleRevenuePeriodChange(period: string) {
  revenuePeriod.value = period
  try {
    const response = await api.get(`/analytics/revenue?period=${period}`)
    revenueData.value = response.data.data
  } catch (error) {
    toast.error('Erreur lors du chargement des revenus')
  }
}

async function handleAppointmentPeriodChange(period: string) {
  appointmentPeriod.value = period
  try {
    const response = await api.get(`/analytics/appointment-trends?period=${period}`)
    appointmentTrends.value = response.data.data
  } catch (error) {
    toast.error('Erreur lors du chargement des tendances')
  }
}
</script>
