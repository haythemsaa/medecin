<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Consultation Urgente</h1>
            <p class="text-sm text-gray-600 mt-1">Consultez un médecin dans l'heure</p>
          </div>
        </div>

        <!-- Info Banner -->
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-red-700">
              <p class="font-medium">Service de consultation urgente</p>
              <p class="mt-1">Pour les situations non vitales nécessitant un avis médical rapide. En cas d'urgence vitale, appelez le 190.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <Card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Spécialité
            </label>
            <select
              v-model="filters.specialty"
              @change="loadAvailableDoctors"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
            >
              <option value="">Toutes les spécialités</option>
              <option value="Médecine générale">Médecine générale</option>
              <option value="Cardiologie">Cardiologie</option>
              <option value="Pédiatrie">Pédiatrie</option>
              <option value="Dermatologie">Dermatologie</option>
              <option value="Psychiatrie">Psychiatrie</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Budget maximum
            </label>
            <input
              v-model.number="filters.max_price"
              @input="loadAvailableDoctors"
              type="number"
              min="0"
              step="10"
              placeholder="Aucune limite"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
            />
          </div>

          <div class="flex items-end">
            <Button
              @click="loadAvailableDoctors"
              :disabled="loading"
              class="w-full"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Actualiser
            </Button>
          </div>
        </div>
      </Card>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Recherche de médecins disponibles..." />

      <!-- Available Doctors -->
      <div v-else>
        <div v-if="doctors.length > 0" class="space-y-4">
          <div class="text-sm text-gray-600 mb-4">
            {{ doctors.length }} médecin(s) disponible(s) pour une consultation urgente
          </div>

          <Card
            v-for="doctor in doctors"
            :key="doctor.id"
            class="hover:shadow-lg transition-shadow cursor-pointer"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-4 flex-1">
                <!-- Photo -->
                <div class="flex-shrink-0">
                  <img
                    :src="doctor.user.photo || '/default-doctor.png'"
                    :alt="`Dr. ${doctor.user.nom}`"
                    class="w-20 h-20 rounded-full object-cover border-2 border-gray-200"
                  />
                </div>

                <!-- Info -->
                <div class="flex-1">
                  <div class="flex items-start justify-between">
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900">
                        Dr. {{ doctor.user.prenom }} {{ doctor.user.nom }}
                      </h3>
                      <p class="text-sm text-gray-600 mt-1">{{ doctor.specialite }}</p>
                    </div>
                  </div>

                  <!-- Availability Badge -->
                  <div class="mt-3 flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                      <span class="w-2 h-2 bg-green-600 rounded-full mr-2 animate-pulse"></span>
                      Disponible dans {{ doctor.available_in_minutes }} min
                    </span>

                    <div v-if="doctor.rating > 0" class="flex items-center text-sm text-gray-600">
                      <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      {{ doctor.rating.toFixed(1) }} ({{ doctor.total_reviews }})
                    </div>
                  </div>

                  <!-- Pricing -->
                  <div class="mt-3 flex items-center gap-4">
                    <div class="text-sm text-gray-500">
                      Tarif normal: <span class="font-medium">{{ doctor.tarif }} TND</span>
                    </div>
                    <div class="text-sm">
                      Tarif urgent: <span class="font-semibold text-red-600">{{ doctor.urgent_price }} TND</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Button -->
              <div class="ml-4">
                <Button
                  @click="selectDoctor(doctor)"
                  variant="primary"
                  class="bg-red-600 hover:bg-red-700"
                >
                  Consulter maintenant
                </Button>
              </div>
            </div>
          </Card>
        </div>

        <!-- Empty State -->
        <Card v-else class="text-center py-12">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun médecin disponible</h3>
          <p class="text-gray-600 mb-4">
            Aucun médecin n'est disponible pour le moment. Essayez de modifier vos filtres ou réessayez dans quelques minutes.
          </p>
          <Button @click="loadAvailableDoctors">Réessayer</Button>
        </Card>
      </div>

      <!-- Request Modal -->
      <Modal v-model:show="showRequestModal" title="Demande de consultation urgente" size="lg">
        <form @submit.prevent="submitRequest" class="space-y-4">
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
            <div class="flex">
              <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-yellow-700">
                <p class="font-medium">Consultation avec Dr. {{ selectedDoctor?.user.prenom }} {{ selectedDoctor?.user.nom }}</p>
                <p class="mt-1">Disponible dans {{ selectedDoctor?.available_in_minutes }} minutes</p>
                <p class="mt-1 font-semibold">Tarif: {{ selectedDoctor?.urgent_price }} TND</p>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Motif de consultation <span class="text-red-500">*</span>
            </label>
            <input
              v-model="requestForm.motif"
              type="text"
              required
              maxlength="500"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
              placeholder="Ex: Forte fièvre depuis ce matin"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Description des symptômes
            </label>
            <textarea
              v-model="requestForm.symptoms_description"
              rows="4"
              maxlength="1000"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
              placeholder="Décrivez vos symptômes en détail..."
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
              Ces informations aideront le médecin à se préparer pour votre consultation
            </p>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="text-sm text-blue-900">
                <p class="font-medium mb-1">Comment ça marche ?</p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                  <li>Votre demande sera envoyée immédiatement au médecin</li>
                  <li>Vous recevrez une confirmation dans les 5 minutes</li>
                  <li>La consultation se fera par vidéo</li>
                  <li>Le paiement se fait après la consultation</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <Button
              type="button"
              variant="outline"
              @click="showRequestModal = false"
            >
              Annuler
            </Button>
            <Button
              type="submit"
              :disabled="submitting"
              class="bg-red-600 hover:bg-red-700"
            >
              {{ submitting ? 'Envoi...' : 'Confirmer la demande' }}
            </Button>
          </div>
        </form>
      </Modal>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'
import Modal from '@/components/Modal.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const router = useRouter()
const toast = useToast()

const loading = ref(true)
const submitting = ref(false)
const showRequestModal = ref(false)
const doctors = ref<any[]>([])
const selectedDoctor = ref<any>(null)

const filters = ref({
  specialty: '',
  max_price: null as number | null,
})

const requestForm = ref({
  motif: '',
  symptoms_description: '',
})

onMounted(async () => {
  await loadAvailableDoctors()
})

async function loadAvailableDoctors() {
  try {
    loading.value = true
    const params: any = {}
    if (filters.value.specialty) params.specialty = filters.value.specialty
    if (filters.value.max_price) params.max_price = filters.value.max_price

    const response = await api.get('/urgent-consultations/available', { params })
    doctors.value = response.data.doctors
  } catch (error: any) {
    console.error('Error loading available doctors:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

function selectDoctor(doctor: any) {
  selectedDoctor.value = doctor
  showRequestModal.value = true
}

async function submitRequest() {
  try {
    submitting.value = true
    const response = await api.post('/urgent-consultations/request', {
      medecin_id: selectedDoctor.value.id,
      motif: requestForm.value.motif,
      symptoms_description: requestForm.value.symptoms_description,
    })

    toast.success('Demande envoyée ! Le médecin va confirmer dans les 5 minutes.')
    showRequestModal.value = false

    // Redirect to appointment details
    router.push(`/appointments/${response.data.appointment.id}`)
  } catch (error: any) {
    console.error('Error submitting request:', error)
    toast.error(error.response?.data?.message || 'Erreur lors de l\'envoi')
  } finally {
    submitting.value = false
  }
}
</script>
