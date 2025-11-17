<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
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
            <button @click="router.back()" class="text-gray-600 hover:text-gray-900">
              ← Retour
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-5xl mx-auto px-4 py-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Chargement...</p>
    </div>

    <!-- Main Content -->
    <main v-else-if="appointment" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="card mb-6">
        <div class="flex items-start justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Rendez-vous du {{ formatDate(appointment.appointment_date) }}</h1>
            <span
              :class="['inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium', getStatusClass(appointment.status)]"
            >
              {{ getStatusLabel(appointment.status) }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex items-center space-x-3">
            <button
              v-if="canJoin(appointment)"
              @click="joinConsultation"
              class="btn btn-success"
            >
              Rejoindre la consultation
            </button>
            <button
              v-if="canCancel(appointment)"
              @click="cancelAppointment"
              class="btn btn-secondary"
            >
              Annuler le RDV
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Participants -->
          <div class="card">
            <h2 class="text-lg font-semibold mb-4">
              {{ authStore.isPatient ? 'Médecin' : 'Patient' }}
            </h2>
            <div v-if="authStore.isPatient && appointment.medecin" class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-2xl">👨‍⚕️</span>
              </div>
              <div>
                <p class="font-semibold text-lg">
                  Dr. {{ appointment.medecin.first_name }} {{ appointment.medecin.last_name }}
                </p>
                <p class="text-sm text-primary-600">{{ appointment.medecin.speciality }}</p>
                <p class="text-sm text-gray-600">{{ appointment.medecin.years_of_experience }} ans d'expérience</p>
              </div>
            </div>
            <div v-else-if="authStore.isMedecin && appointment.patient" class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-2xl">👤</span>
              </div>
              <div>
                <p class="font-semibold text-lg">
                  {{ appointment.patient.first_name }} {{ appointment.patient.last_name }}
                </p>
                <p class="text-sm text-gray-600">{{ calculateAge(appointment.patient.birth_date) }} ans</p>
                <p class="text-sm text-gray-600">{{ appointment.patient.gender === 'male' ? 'Homme' : 'Femme' }}</p>
              </div>
            </div>
          </div>

          <!-- Appointment Details -->
          <div class="card">
            <h2 class="text-lg font-semibold mb-4">Détails de la consultation</h2>
            <div class="space-y-3">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Date et heure</p>
                  <p class="font-medium">{{ formatDate(appointment.appointment_date) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Durée</p>
                  <p class="font-medium">{{ appointment.duration }} minutes</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Type</p>
                  <p class="font-medium">
                    {{ appointment.type === 'video' ? '📹 Visioconférence' : '📞 Téléphone' }}
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Urgence</p>
                  <p class="font-medium">
                    {{ appointment.is_urgent ? '🚨 Urgente' : '✅ Standard' }}
                  </p>
                </div>
              </div>

              <div>
                <p class="text-sm text-gray-500">Motif de consultation</p>
                <p class="font-medium">{{ appointment.reason }}</p>
              </div>

              <div v-if="appointment.symptoms && appointment.symptoms.length">
                <p class="text-sm text-gray-500">Symptômes</p>
                <div class="flex flex-wrap gap-2 mt-1">
                  <span
                    v-for="symptom in appointment.symptoms"
                    :key="symptom"
                    class="px-3 py-1 bg-red-50 text-red-700 text-sm rounded-lg"
                  >
                    {{ symptom }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Consultation Notes (for completed) -->
          <div v-if="appointment.consultation" class="card">
            <h2 class="text-lg font-semibold mb-4">Notes de consultation</h2>
            <div class="space-y-3">
              <div v-if="appointment.consultation.diagnosis">
                <p class="text-sm text-gray-500">Diagnostic</p>
                <p class="font-medium">{{ appointment.consultation.diagnosis }}</p>
              </div>
              <div v-if="appointment.consultation.notes">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-gray-700">{{ appointment.consultation.notes }}</p>
              </div>
              <div v-if="appointment.consultation.recommendations">
                <p class="text-sm text-gray-500">Recommandations</p>
                <p class="text-gray-700">{{ appointment.consultation.recommendations }}</p>
              </div>
            </div>
          </div>

          <!-- Review (for patients on completed appointments) -->
          <div v-if="authStore.isPatient && appointment.status === 'completed' && !appointment.review" class="card">
            <h2 class="text-lg font-semibold mb-4">Évaluer cette consultation</h2>
            <p class="text-sm text-gray-600 mb-4">Partagez votre expérience pour aider d'autres patients</p>
            <router-link to="#" class="btn btn-primary">
              Laisser un avis
            </router-link>
          </div>

          <div v-else-if="appointment.review" class="card">
            <h2 class="text-lg font-semibold mb-4">Votre évaluation</h2>
            <div class="flex items-center text-yellow-500 mb-2">
              <span v-for="i in 5" :key="i">
                {{ i <= appointment.review.overall_rating ? '⭐' : '☆' }}
              </span>
            </div>
            <p class="text-gray-700">{{ appointment.review.comment }}</p>
          </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Payment Info -->
          <div class="card">
            <h2 class="text-lg font-semibold mb-4">Paiement</h2>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Montant</span>
                <span class="font-semibold">{{ appointment.price }} TND</span>
              </div>
              <div v-if="appointment.payment" class="flex justify-between">
                <span class="text-gray-600">Statut</span>
                <span
                  :class="[
                    'text-sm font-medium',
                    appointment.payment.status === 'completed' ? 'text-green-600' :
                    appointment.payment.status === 'refunded' ? 'text-orange-600' :
                    'text-gray-600'
                  ]"
                >
                  {{ appointment.payment.status === 'completed' ? 'Payé' :
                     appointment.payment.status === 'refunded' ? 'Remboursé' :
                     'En attente' }}
                </span>
              </div>
              <div v-if="appointment.payment" class="flex justify-between">
                <span class="text-gray-600">Méthode</span>
                <span class="text-sm">
                  {{ getPaymentMethodLabel(appointment.payment.payment_method) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Cancellation Policy -->
          <div v-if="appointment.status === 'confirmed'" class="card bg-blue-50 border border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-2">Politique d'annulation</h3>
            <ul class="text-sm text-blue-800 space-y-1">
              <li>• >24h: Remboursement 100%</li>
              <li>• 2h-24h: Remboursement 50%</li>
              <li>• <2h: Pas de remboursement</li>
            </ul>
          </div>

          <!-- Documents -->
          <div v-if="appointment.status === 'completed'" class="card">
            <h2 class="text-lg font-semibold mb-4">Documents</h2>
            <div class="space-y-2">
              <button class="btn btn-secondary w-full text-left flex items-center justify-between">
                <span>📄 Ordonnance</span>
                <span class="text-xs">PDF</span>
              </button>
              <button class="btn btn-secondary w-full text-left flex items-center justify-between">
                <span>🧾 Facture</span>
                <span class="text-xs">PDF</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import type { Appointment } from '@/types'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const appointment = ref<Appointment | null>(null)

async function fetchAppointment() {
  try {
    const response = await api.get(`/appointments/${route.params.id}`)
    appointment.value = response.data
  } catch (error) {
    console.error('Error fetching appointment:', error)
  } finally {
    loading.value = false
  }
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'long',
    timeStyle: 'short'
  }).format(date)
}

function calculateAge(birthDate: string): number {
  const today = new Date()
  const birth = new Date(birthDate)
  let age = today.getFullYear() - birth.getFullYear()
  const monthDiff = today.getMonth() - birth.getMonth()
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
    age--
  }
  return age
}

function getStatusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    pending: 'En attente',
    confirmed: 'Confirmé',
    completed: 'Terminé',
    cancelled: 'Annulé'
  }
  return labels[status] || status
}

function getPaymentMethodLabel(method: string): string {
  const labels: Record<string, string> = {
    card: 'Carte bancaire',
    e_dinar: 'E-Dinar',
    mobile_money: 'Mobile Money',
    postal_mandate: 'Mandat postal'
  }
  return labels[method] || method
}

function canJoin(appt: Appointment): boolean {
  if (appt.status !== 'confirmed') return false
  const appointmentTime = new Date(appt.appointment_date)
  const now = new Date()
  const diffMinutes = (appointmentTime.getTime() - now.getTime()) / 1000 / 60
  return diffMinutes <= 10 && diffMinutes >= -appt.duration
}

function canCancel(appt: Appointment): boolean {
  return appt.status === 'confirmed' && new Date(appt.appointment_date) > new Date()
}

function joinConsultation() {
  router.push(`/consultation/${appointment.value?.id}`)
}

async function cancelAppointment() {
  const reason = prompt('Raison de l\'annulation (optionnelle):')
  if (reason === null) return

  try {
    await api.post(`/appointments/${appointment.value?.id}/cancel`, { reason })
    alert('Rendez-vous annulé avec succès')
    await fetchAppointment()
  } catch (error: any) {
    alert(error.response?.data?.message || 'Erreur lors de l\'annulation')
  }
}

onMounted(() => {
  fetchAppointment()
})
</script>
