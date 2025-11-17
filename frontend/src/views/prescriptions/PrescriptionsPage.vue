<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mes Ordonnances</h1>
        <p class="mt-2 text-gray-600">
          Consultez et téléchargez vos ordonnances médicales
        </p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher par médecin ou médicament..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
            />
          </div>

          <select
            v-model="filterStatus"
            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
          >
            <option value="">Tous les statuts</option>
            <option value="active">Actives</option>
            <option value="expired">Expirées</option>
          </select>

          <select
            v-model="sortBy"
            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
          >
            <option value="date_desc">Plus récentes</option>
            <option value="date_asc">Plus anciennes</option>
            <option value="medecin">Par médecin</option>
          </select>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
      </div>

      <!-- Prescriptions List -->
      <div v-else-if="filteredPrescriptions.length > 0" class="space-y-4">
        <div
          v-for="prescription in filteredPrescriptions"
          :key="prescription.id"
          class="bg-white rounded-lg shadow hover:shadow-md transition-shadow"
        >
          <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1">
                <div class="flex items-center mb-2">
                  <h3 class="text-lg font-semibold text-gray-900">
                    Ordonnance #{{ prescription.prescription_number }}
                  </h3>
                  <span
                    :class="[
                      'ml-3 px-3 py-1 rounded-full text-xs font-medium',
                      isExpired(prescription.valid_until)
                        ? 'bg-red-100 text-red-800'
                        : 'bg-green-100 text-green-800'
                    ]"
                  >
                    {{ isExpired(prescription.valid_until) ? 'Expirée' : 'Active' }}
                  </span>
                </div>

                <div class="flex items-center text-sm text-gray-600 space-x-4">
                  <div class="flex items-center">
                    <span class="mr-1">👨‍⚕️</span>
                    <span>
                      Dr. {{ prescription.consultation?.medecin?.first_name }}
                      {{ prescription.consultation?.medecin?.last_name }}
                    </span>
                  </div>
                  <div class="flex items-center">
                    <span class="mr-1">📅</span>
                    <span>{{ formatDate(prescription.created_at, 'medium') }}</span>
                  </div>
                  <div class="flex items-center">
                    <span class="mr-1">⏰</span>
                    <span>Valide jusqu'au {{ formatDate(prescription.valid_until, 'short') }}</span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 ml-4">
                <button
                  @click="viewPrescription(prescription)"
                  class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 flex items-center"
                >
                  <span class="mr-2">👁️</span>
                  Voir
                </button>
                <button
                  @click="downloadPrescription(prescription)"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center"
                >
                  <span class="mr-2">📥</span>
                  PDF
                </button>
              </div>
            </div>

            <!-- Medications List -->
            <div class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Médicaments prescrits</h4>
              <div class="space-y-3">
                <div
                  v-for="(medication, index) in prescription.medications"
                  :key="index"
                  class="flex items-start bg-gray-50 p-3 rounded-md"
                >
                  <span class="text-2xl mr-3">💊</span>
                  <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ medication.name }}</p>
                    <p class="text-sm text-gray-600 mt-1">
                      <span class="font-medium">Dosage:</span> {{ medication.dosage }}
                    </p>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Posologie:</span> {{ medication.frequency }}
                    </p>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Durée:</span> {{ medication.duration }}
                    </p>
                    <p v-if="medication.instructions" class="text-sm text-gray-500 mt-1 italic">
                      {{ medication.instructions }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="prescription.notes" class="mt-4 border-t pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Remarques du médecin</h4>
              <p class="text-sm text-gray-600 bg-yellow-50 p-3 rounded-md">
                {{ prescription.notes }}
              </p>
            </div>

            <!-- QR Code Info -->
            <div class="mt-4 border-t pt-4 flex items-center text-sm text-gray-500">
              <span class="mr-2">🔐</span>
              <span>Ordonnance sécurisée avec code QR de vérification</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <span class="text-6xl mb-4 block">📋</span>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune ordonnance</h3>
        <p class="text-gray-600">
          Vous n'avez pas encore d'ordonnances médicales
        </p>
      </div>
    </div>

    <!-- Prescription Detail Modal -->
    <div
      v-if="selectedPrescription"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="selectedPrescription = null"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex justify-between items-start mb-6">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">
                Ordonnance #{{ selectedPrescription.prescription_number }}
              </h2>
              <p class="text-gray-600 mt-1">
                Émise le {{ formatDate(selectedPrescription.created_at, 'long') }}
              </p>
            </div>
            <button
              @click="selectedPrescription = null"
              class="text-gray-400 hover:text-gray-600"
            >
              <span class="text-2xl">×</span>
            </button>
          </div>

          <!-- Doctor Info -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Médecin prescripteur</h3>
            <div class="space-y-1">
              <p class="text-gray-900">
                Dr. {{ selectedPrescription.consultation?.medecin?.first_name }}
                {{ selectedPrescription.consultation?.medecin?.last_name }}
              </p>
              <p class="text-sm text-gray-600">
                {{ selectedPrescription.consultation?.medecin?.speciality }}
              </p>
              <p class="text-sm text-gray-600">
                Ordre des Médecins: {{ selectedPrescription.consultation?.medecin?.numero_ordre }}
              </p>
            </div>
          </div>

          <!-- Patient Info -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Patient</h3>
            <div class="space-y-1">
              <p class="text-gray-900">
                {{ selectedPrescription.consultation?.patient?.first_name }}
                {{ selectedPrescription.consultation?.patient?.last_name }}
              </p>
              <p class="text-sm text-gray-600">
                Date de naissance: {{ formatDate(selectedPrescription.consultation?.patient?.date_of_birth, 'short') }}
              </p>
            </div>
          </div>

          <!-- Medications -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Médicaments prescrits</h3>
            <div class="space-y-4">
              <div
                v-for="(medication, index) in selectedPrescription.medications"
                :key="index"
                class="border-l-4 border-teal-500 bg-gray-50 p-4 rounded-r-lg"
              >
                <p class="font-semibold text-gray-900 text-lg">{{ medication.name }}</p>
                <div class="mt-2 space-y-1 text-sm">
                  <p class="text-gray-700">
                    <span class="font-medium">Dosage:</span> {{ medication.dosage }}
                  </p>
                  <p class="text-gray-700">
                    <span class="font-medium">Posologie:</span> {{ medication.frequency }}
                  </p>
                  <p class="text-gray-700">
                    <span class="font-medium">Durée du traitement:</span> {{ medication.duration }}
                  </p>
                  <p v-if="medication.instructions" class="text-gray-600 italic mt-2">
                    ℹ️ {{ medication.instructions }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="selectedPrescription.notes" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Remarques</h3>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
              <p class="text-gray-700">{{ selectedPrescription.notes }}</p>
            </div>
          </div>

          <!-- Validity -->
          <div class="mb-6">
            <div
              :class="[
                'p-4 rounded-lg flex items-center',
                isExpired(selectedPrescription.valid_until)
                  ? 'bg-red-50 border border-red-200'
                  : 'bg-green-50 border border-green-200'
              ]"
            >
              <span class="text-2xl mr-3">
                {{ isExpired(selectedPrescription.valid_until) ? '⚠️' : '✅' }}
              </span>
              <div>
                <p :class="isExpired(selectedPrescription.valid_until) ? 'text-red-900' : 'text-green-900'" class="font-medium">
                  {{ isExpired(selectedPrescription.valid_until) ? 'Ordonnance expirée' : 'Ordonnance valide' }}
                </p>
                <p :class="isExpired(selectedPrescription.valid_until) ? 'text-red-700' : 'text-green-700'" class="text-sm">
                  Valable jusqu'au {{ formatDate(selectedPrescription.valid_until, 'long') }}
                </p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button
              @click="downloadPrescription(selectedPrescription)"
              class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center justify-center"
            >
              <span class="mr-2">📥</span>
              Télécharger le PDF
            </button>
            <button
              @click="selectedPrescription = null"
              class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
            >
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { formatDate } from '@/utils'

const authStore = useAuthStore()

const loading = ref(true)
const prescriptions = ref<any[]>([])
const selectedPrescription = ref<any>(null)

const searchQuery = ref('')
const filterStatus = ref('')
const sortBy = ref('date_desc')

const filteredPrescriptions = computed(() => {
  let filtered = [...prescriptions.value]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p => {
      const doctorName = `${p.consultation?.medecin?.first_name} ${p.consultation?.medecin?.last_name}`.toLowerCase()
      const medications = p.medications.map((m: any) => m.name.toLowerCase()).join(' ')
      return doctorName.includes(query) || medications.includes(query)
    })
  }

  // Status filter
  if (filterStatus.value === 'active') {
    filtered = filtered.filter(p => !isExpired(p.valid_until))
  } else if (filterStatus.value === 'expired') {
    filtered = filtered.filter(p => isExpired(p.valid_until))
  }

  // Sorting
  if (sortBy.value === 'date_desc') {
    filtered.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
  } else if (sortBy.value === 'date_asc') {
    filtered.sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime())
  } else if (sortBy.value === 'medecin') {
    filtered.sort((a, b) => {
      const nameA = `${a.consultation?.medecin?.last_name}`
      const nameB = `${b.consultation?.medecin?.last_name}`
      return nameA.localeCompare(nameB)
    })
  }

  return filtered
})

async function loadPrescriptions() {
  try {
    const response = await api.get('/prescriptions/my-prescriptions')
    prescriptions.value = response.data
  } catch (error) {
    console.error('Error loading prescriptions:', error)
  } finally {
    loading.value = false
  }
}

function isExpired(validUntil: string): boolean {
  return new Date(validUntil) < new Date()
}

function viewPrescription(prescription: any) {
  selectedPrescription.value = prescription
}

async function downloadPrescription(prescription: any) {
  try {
    const response = await api.get(`/prescriptions/${prescription.id}/download`, {
      responseType: 'blob'
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `ordonnance_${prescription.prescription_number}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error downloading prescription:', error)
    alert('Erreur lors du téléchargement de l\'ordonnance')
  }
}

onMounted(() => {
  loadPrescriptions()
})
</script>
