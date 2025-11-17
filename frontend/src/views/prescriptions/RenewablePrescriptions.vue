<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Ordonnances renouvelables</h1>
        <p class="mt-2 text-sm text-gray-600">
          Renouvelez vos ordonnances sans nouvelle consultation
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Prescriptions List -->
      <div v-else class="space-y-6">
        <div v-if="prescriptions.length > 0">
          <Card
            v-for="prescription in prescriptions"
            :key="prescription.id"
            class="hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-start gap-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                    </div>
                  </div>

                  <div class="flex-1">
                    <div class="flex items-start justify-between mb-3">
                      <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                          Dr. {{ prescription.medecin.prenom }} {{ prescription.medecin.nom }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ prescription.medecin.specialite }}</p>
                      </div>
                    </div>

                    <!-- Medications -->
                    <div class="mb-3">
                      <h4 class="text-sm font-medium text-gray-700 mb-2">Médicaments :</h4>
                      <ul class="list-disc list-inside space-y-1">
                        <li
                          v-for="(med, index) in prescription.medicaments"
                          :key="index"
                          class="text-sm text-gray-600"
                        >
                          {{ med }}
                        </li>
                      </ul>
                    </div>

                    <!-- Instructions -->
                    <p v-if="prescription.instructions" class="text-sm text-gray-600 mb-3">
                      <span class="font-medium">Instructions :</span> {{ prescription.instructions }}
                    </p>

                    <!-- Renewals Info -->
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        {{ prescription.renewals_remaining }} renouvellement(s) restant(s)
                      </span>

                      <span v-if="prescription.valid_until" class="text-gray-600">
                        Valide jusqu'au {{ formatDate(prescription.valid_until) }}
                      </span>

                      <span class="text-gray-500">
                        Créée le {{ formatDate(prescription.created_at) }}
                      </span>
                    </div>

                    <!-- Renewal Conditions -->
                    <div v-if="prescription.renewal_conditions" class="mt-3 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r">
                      <p class="text-sm text-yellow-800">
                        <span class="font-medium">Conditions de renouvellement :</span><br>
                        {{ prescription.renewal_conditions }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Button -->
              <div class="ml-4">
                <Button
                  @click="requestRenewal(prescription)"
                  :disabled="requesting"
                >
                  Demander renouvellement
                </Button>
              </div>
            </div>
          </Card>
        </div>

        <!-- Empty State -->
        <Card v-else class="text-center py-12">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune ordonnance renouvelable</h3>
          <p class="text-gray-600">Vous n'avez pas d'ordonnance éligible au renouvellement pour le moment.</p>
        </Card>

        <!-- Renewal History -->
        <div class="mt-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Historique des renouvellements</h2>

          <Card v-if="history.length > 0">
            <div class="divide-y divide-gray-200">
              <div
                v-for="renewal in history"
                :key="renewal.id"
                class="py-4 first:pt-0 last:pb-0"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                      <span
                        :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          renewal.status === 'approved' ? 'bg-green-100 text-green-800' :
                          renewal.status === 'rejected' ? 'bg-red-100 text-red-800' :
                          'bg-yellow-100 text-yellow-800'
                        ]"
                      >
                        {{ getStatusLabel(renewal.status) }}
                      </span>
                      <span class="text-sm text-gray-600">
                        Demandé le {{ formatDateTime(renewal.requested_at) }}
                      </span>
                    </div>

                    <p v-if="renewal.patient_notes" class="text-sm text-gray-700 mb-2">
                      <span class="font-medium">Vos notes :</span> {{ renewal.patient_notes }}
                    </p>

                    <p v-if="renewal.medecin_notes" class="text-sm text-gray-700">
                      <span class="font-medium">Réponse du médecin :</span> {{ renewal.medecin_notes }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </Card>

          <Card v-else class="text-center py-8">
            <p class="text-gray-600">Aucun historique de renouvellement</p>
          </Card>
        </div>
      </div>

      <!-- Renewal Request Modal -->
      <Modal v-model:show="showRenewalModal" title="Demander un renouvellement" size="lg">
        <form @submit.prevent="submitRenewal" class="space-y-4">
          <div v-if="selectedPrescription" class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-4">
            <p class="text-sm text-blue-900 font-medium mb-2">Ordonnance à renouveler :</p>
            <ul class="list-disc list-inside space-y-1 text-sm text-blue-800">
              <li v-for="(med, index) in selectedPrescription.medicaments" :key="index">
                {{ med }}
              </li>
            </ul>
            <p class="text-sm text-blue-900 mt-2">
              {{ selectedPrescription.renewals_remaining }} renouvellement(s) restant(s)
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Informations complémentaires (optionnel)
            </label>
            <textarea
              v-model="renewalForm.patient_notes"
              rows="4"
              maxlength="1000"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              placeholder="Décrivez votre état actuel, l'efficacité du traitement, etc..."
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
              Ces informations aideront le médecin à valider le renouvellement
            </p>
          </div>

          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-yellow-800">
                <p class="font-medium mb-1">À savoir :</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>Le médecin validera votre demande sous 24-48h</li>
                  <li>Vous recevrez une notification à la validation</li>
                  <li>Aucun frais supplémentaire pour le renouvellement</li>
                  <li>Si refusé, une consultation sera nécessaire</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <Button
              type="button"
              variant="outline"
              @click="showRenewalModal = false"
            >
              Annuler
            </Button>
            <Button
              type="submit"
              :disabled="requesting"
            >
              {{ requesting ? 'Envoi...' : 'Envoyer la demande' }}
            </Button>
          </div>
        </form>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'
import Modal from '@/components/Modal.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const toast = useToast()

const loading = ref(true)
const requesting = ref(false)
const showRenewalModal = ref(false)
const prescriptions = ref<any[]>([])
const history = ref<any[]>([])
const selectedPrescription = ref<any>(null)

const renewalForm = ref({
  patient_notes: '',
})

onMounted(async () => {
  await loadPrescriptions()
  await loadHistory()
})

async function loadPrescriptions() {
  try {
    loading.value = true
    const response = await api.get('/prescription-renewals/renewable')
    prescriptions.value = response.data.prescriptions
  } catch (error: any) {
    console.error('Error loading prescriptions:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

async function loadHistory() {
  try {
    const response = await api.get('/prescription-renewals/history')
    history.value = response.data.renewals
  } catch (error: any) {
    console.error('Error loading history:', error)
  }
}

function requestRenewal(prescription: any) {
  selectedPrescription.value = prescription
  renewalForm.value.patient_notes = ''
  showRenewalModal.value = true
}

async function submitRenewal() {
  try {
    requesting.value = true
    await api.post('/prescription-renewals/request', {
      prescription_id: selectedPrescription.value.id,
      patient_notes: renewalForm.value.patient_notes,
    })

    toast.success('Demande de renouvellement envoyée')
    showRenewalModal.value = false
    await loadPrescriptions()
    await loadHistory()
  } catch (error: any) {
    console.error('Error submitting renewal:', error)
    toast.error(error.response?.data?.message || 'Erreur lors de l\'envoi')
  } finally {
    requesting.value = false
  }
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('fr-FR')
}

function formatDateTime(date: string) {
  return new Date(date).toLocaleString('fr-FR')
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    pending: 'En attente',
    approved: 'Approuvé',
    rejected: 'Refusé',
  }
  return labels[status] || status
}
</script>
