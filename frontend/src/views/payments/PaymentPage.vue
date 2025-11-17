<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Paiement</h1>
        <p class="mt-2 text-sm text-gray-600">
          Complétez le paiement pour confirmer votre rendez-vous
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Payment Form -->
      <div v-else-if="appointment && !paymentCompleted" class="space-y-6">
        <!-- Appointment Summary -->
        <Card>
          <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Détails du rendez-vous</h2>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-500">Médecin</p>
                <p class="font-medium">{{ medecinName }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Spécialité</p>
                <p class="font-medium">{{ appointment.medecin?.specialite }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="font-medium">{{ formatDate(appointment.date) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Heure</p>
                <p class="font-medium">{{ appointment.time }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Type</p>
                <Badge :variant="appointment.type === 'video' ? 'teal' : 'blue'">
                  {{ appointment.type === 'video' ? 'Vidéo' : 'Cabinet' }}
                </Badge>
              </div>
              <div>
                <p class="text-sm text-gray-500">Montant</p>
                <p class="text-xl font-bold text-teal-600">{{ appointment.montant }} TND</p>
              </div>
            </div>
          </div>
        </Card>

        <!-- Payment Method Selection -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Méthode de paiement</h2>

          <div class="space-y-3">
            <label
              v-for="method in paymentMethods"
              :key="method.value"
              :class="[
                'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors',
                selectedMethod === method.value
                  ? 'border-teal-500 bg-teal-50'
                  : 'border-gray-200 hover:border-gray-300'
              ]"
            >
              <input
                type="radio"
                :value="method.value"
                v-model="selectedMethod"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500"
              />
              <div class="ml-3 flex-1">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium text-gray-900">{{ method.label }}</p>
                    <p class="text-sm text-gray-500">{{ method.description }}</p>
                  </div>
                  <span class="text-2xl">{{ method.icon }}</span>
                </div>
              </div>
            </label>
          </div>
        </Card>

        <!-- Terms and Conditions -->
        <Card>
          <label class="flex items-start">
            <input
              type="checkbox"
              v-model="acceptedTerms"
              class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded mt-1"
            />
            <span class="ml-3 text-sm text-gray-600">
              J'accepte les
              <a href="#" class="text-teal-600 hover:text-teal-700">conditions générales</a>
              et la
              <a href="#" class="text-teal-600 hover:text-teal-700">politique d'annulation</a>
            </span>
          </label>
        </Card>

        <!-- Action Buttons -->
        <div class="flex space-x-4">
          <Button
            variant="outline"
            @click="$router.back()"
            class="flex-1"
          >
            Retour
          </Button>
          <Button
            variant="primary"
            @click="processPayment"
            :disabled="!selectedMethod || !acceptedTerms || processing"
            :loading="processing"
            class="flex-1"
          >
            {{ processing ? 'Traitement...' : `Payer ${appointment.montant} TND` }}
          </Button>
        </div>
      </div>

      <!-- Payment Completed -->
      <div v-else-if="paymentCompleted" class="text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Paiement réussi !</h2>
        <p class="text-gray-600 mb-6">
          Votre rendez-vous est confirmé. Vous recevrez un email de confirmation.
        </p>
        <div class="flex justify-center space-x-4">
          <Button variant="outline" @click="$router.push('/appointments')">
            Voir mes rendez-vous
          </Button>
          <Button variant="primary" @click="$router.push('/dashboard')">
            Retour au tableau de bord
          </Button>
        </div>
      </div>

      <!-- Payment Error -->
      <div v-if="errorMessage" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800">{{ errorMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Button from '@/components/Button.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const toast = useToast()

const loading = ref(true)
const processing = ref(false)
const paymentCompleted = ref(false)
const appointment = ref<any>(null)
const selectedMethod = ref('')
const acceptedTerms = ref(false)
const errorMessage = ref('')

const paymentMethods = [
  {
    value: 'card',
    label: 'Carte bancaire',
    description: 'Paiement sécurisé par carte Visa/Mastercard',
    icon: '💳',
  },
  {
    value: 'e-dinar',
    label: 'e-Dinar',
    description: 'Paiement via e-Dinar Tunisie',
    icon: '🏦',
  },
  {
    value: 'mobile_money',
    label: 'Mobile Money',
    description: 'Paiement via téléphone mobile',
    icon: '📱',
  },
  {
    value: 'cash',
    label: 'Espèces',
    description: 'Paiement en espèces lors du rendez-vous',
    icon: '💵',
  },
]

const medecinName = computed(() => {
  if (!appointment.value?.medecin?.user) return ''
  const { first_name, last_name } = appointment.value.medecin.user
  return `Dr. ${first_name} ${last_name}`
})

onMounted(async () => {
  await loadAppointment()
})

async function loadAppointment() {
  try {
    loading.value = true
    const appointmentId = route.params.appointmentId

    const response = await api.get(`/appointments/${appointmentId}`)
    appointment.value = response.data.appointment

    // Check if already paid
    if (appointment.value.payment_status === 'paid') {
      paymentCompleted.value = true
    }
  } catch (error: any) {
    console.error('Error loading appointment:', error)
    errorMessage.value = 'Impossible de charger les détails du rendez-vous'
    toast.error('Erreur de chargement')
  } finally {
    loading.value = false
  }
}

async function processPayment() {
  if (!selectedMethod.value || !acceptedTerms.value) return

  try {
    processing.value = true
    errorMessage.value = ''

    const response = await api.post('/payments/initiate', {
      appointment_id: appointment.value.id,
      payment_method: selectedMethod.value,
    })

    // Handle different payment methods
    if (selectedMethod.value === 'cash') {
      // Cash payment - just confirm
      toast.success('Paiement enregistré. Confirmez lors du rendez-vous.')
      paymentCompleted.value = true
    } else if (response.data.payment_url) {
      // Redirect to payment gateway
      window.location.href = response.data.payment_url
    } else {
      // Payment processed directly
      toast.success('Paiement effectué avec succès')
      paymentCompleted.value = true
    }
  } catch (error: any) {
    console.error('Payment error:', error)
    errorMessage.value = error.response?.data?.message || 'Erreur lors du paiement'
    toast.error('Échec du paiement')
  } finally {
    processing.value = false
  }
}

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('fr-FR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>
