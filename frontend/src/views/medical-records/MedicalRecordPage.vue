<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mon Dossier Médical</h1>
        <p class="mt-2 text-gray-600">
          Gérez vos informations médicales et contrôlez l'accès à votre dossier
        </p>
      </div>

      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
      </div>

      <div v-else-if="medicalRecord" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Personal Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Informations Personnelles</h2>
              <button
                @click="editMode.personal = !editMode.personal"
                class="text-teal-600 hover:text-teal-700 font-medium"
              >
                {{ editMode.personal ? 'Annuler' : 'Modifier' }}
              </button>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Groupe Sanguin</label>
                <input
                  v-if="editMode.personal"
                  v-model="editForm.blood_type"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                />
                <p v-else class="text-gray-900">{{ medicalRecord.blood_type || 'Non renseigné' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poids (kg)</label>
                <input
                  v-if="editMode.personal"
                  v-model.number="editForm.weight"
                  type="number"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                />
                <p v-else class="text-gray-900">{{ medicalRecord.weight || 'Non renseigné' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Taille (cm)</label>
                <input
                  v-if="editMode.personal"
                  v-model.number="editForm.height"
                  type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md"
                />
                <p v-else class="text-gray-900">{{ medicalRecord.height || 'Non renseigné' }}</p>
              </div>

              <div v-if="medicalRecord.weight && medicalRecord.height">
                <label class="block text-sm font-medium text-gray-700 mb-1">IMC</label>
                <p class="text-gray-900 font-medium">{{ calculateBMI() }}</p>
              </div>
            </div>

            <div v-if="editMode.personal" class="mt-4">
              <button
                @click="savePersonalInfo"
                class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
              >
                Enregistrer
              </button>
            </div>
          </div>

          <!-- Allergies -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Allergies</h2>
              <button
                @click="editMode.allergies = !editMode.allergies"
                class="text-teal-600 hover:text-teal-700 font-medium"
              >
                {{ editMode.allergies ? 'Annuler' : 'Modifier' }}
              </button>
            </div>

            <div v-if="editMode.allergies">
              <textarea
                v-model="allergiesText"
                rows="4"
                placeholder="Une allergie par ligne&#10;Ex: Pénicilline&#10;Arachides"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              ></textarea>
              <button
                @click="saveAllergies"
                class="mt-2 px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
              >
                Enregistrer
              </button>
            </div>

            <div v-else-if="medicalRecord.allergies && medicalRecord.allergies.length > 0">
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="allergy in medicalRecord.allergies"
                  :key="allergy"
                  class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm"
                >
                  ⚠️ {{ allergy }}
                </span>
              </div>
            </div>

            <p v-else class="text-gray-500 italic">Aucune allergie déclarée</p>
          </div>

          <!-- Chronic Diseases -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Maladies Chroniques</h2>
              <button
                @click="editMode.diseases = !editMode.diseases"
                class="text-teal-600 hover:text-teal-700 font-medium"
              >
                {{ editMode.diseases ? 'Annuler' : 'Modifier' }}
              </button>
            </div>

            <div v-if="editMode.diseases">
              <textarea
                v-model="diseasesText"
                rows="4"
                placeholder="Une maladie par ligne&#10;Ex: Diabète type 2&#10;Hypertension"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              ></textarea>
              <button
                @click="saveDiseases"
                class="mt-2 px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
              >
                Enregistrer
              </button>
            </div>

            <div v-else-if="medicalRecord.chronic_diseases && medicalRecord.chronic_diseases.length > 0">
              <div class="space-y-2">
                <div
                  v-for="disease in medicalRecord.chronic_diseases"
                  :key="disease"
                  class="flex items-start"
                >
                  <span class="text-orange-500 mr-2">🏥</span>
                  <span class="text-gray-900">{{ disease }}</span>
                </div>
              </div>
            </div>

            <p v-else class="text-gray-500 italic">Aucune maladie chronique déclarée</p>
          </div>

          <!-- Current Treatments -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Traitements en Cours</h2>
              <button
                @click="editMode.treatments = !editMode.treatments"
                class="text-teal-600 hover:text-teal-700 font-medium"
              >
                {{ editMode.treatments ? 'Annuler' : 'Modifier' }}
              </button>
            </div>

            <div v-if="editMode.treatments">
              <textarea
                v-model="treatmentsText"
                rows="4"
                placeholder="Un traitement par ligne&#10;Ex: Metformine 500mg - 2x/jour&#10;Aspirine 100mg - 1x/jour"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              ></textarea>
              <button
                @click="saveTreatments"
                class="mt-2 px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
              >
                Enregistrer
              </button>
            </div>

            <div v-else-if="medicalRecord.current_treatments && medicalRecord.current_treatments.length > 0">
              <div class="space-y-2">
                <div
                  v-for="treatment in medicalRecord.current_treatments"
                  :key="treatment"
                  class="flex items-start"
                >
                  <span class="text-blue-500 mr-2">💊</span>
                  <span class="text-gray-900">{{ treatment }}</span>
                </div>
              </div>
            </div>

            <p v-else class="text-gray-500 italic">Aucun traitement en cours</p>
          </div>

          <!-- Vaccinations -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Vaccinations</h2>
              <button
                @click="editMode.vaccinations = !editMode.vaccinations"
                class="text-teal-600 hover:text-teal-700 font-medium"
              >
                {{ editMode.vaccinations ? 'Annuler' : 'Modifier' }}
              </button>
            </div>

            <div v-if="editMode.vaccinations">
              <textarea
                v-model="vaccinationsText"
                rows="4"
                placeholder="Une vaccination par ligne&#10;Ex: COVID-19 (3 doses - 2023)&#10;Grippe saisonnière (2024)"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              ></textarea>
              <button
                @click="saveVaccinations"
                class="mt-2 px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
              >
                Enregistrer
              </button>
            </div>

            <div v-else-if="medicalRecord.vaccinations && medicalRecord.vaccinations.length > 0">
              <div class="space-y-2">
                <div
                  v-for="vaccination in medicalRecord.vaccinations"
                  :key="vaccination"
                  class="flex items-start"
                >
                  <span class="text-green-500 mr-2">💉</span>
                  <span class="text-gray-900">{{ vaccination }}</span>
                </div>
              </div>
            </div>

            <p v-else class="text-gray-500 italic">Aucune vaccination enregistrée</p>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Access Control -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Contrôle d'Accès</h2>

            <div class="space-y-4">
              <div class="flex items-start">
                <span class="text-2xl mr-3">🔒</span>
                <div>
                  <h3 class="font-medium text-gray-900">Consentement actif</h3>
                  <p class="text-sm text-gray-600">
                    Les médecins peuvent accéder à votre dossier uniquement avec votre consentement
                  </p>
                </div>
              </div>

              <div v-if="activeConsents.length > 0" class="mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Accès autorisés ({{ activeConsents.length }})</h4>
                <div class="space-y-2">
                  <div
                    v-for="consent in activeConsents"
                    :key="consent.id"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-md"
                  >
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900">
                        Dr. {{ consent.medecin?.first_name }} {{ consent.medecin?.last_name }}
                      </p>
                      <p class="text-xs text-gray-500">
                        Expire le {{ formatDate(consent.expires_at) }}
                      </p>
                    </div>
                    <button
                      @click="revokeConsent(consent.id)"
                      class="ml-2 text-red-600 hover:text-red-700 text-sm"
                    >
                      Révoquer
                    </button>
                  </div>
                </div>
              </div>

              <p v-else class="text-sm text-gray-500 italic">
                Aucun accès actif
              </p>
            </div>
          </div>

          <!-- Access History -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Historique d'Accès</h2>

            <div v-if="accessHistory.length > 0" class="space-y-3">
              <div
                v-for="access in accessHistory"
                :key="access.id"
                class="border-l-2 border-teal-500 pl-3"
              >
                <p class="text-sm font-medium text-gray-900">
                  Dr. {{ access.medecin?.first_name }} {{ access.medecin?.last_name }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ formatDateTime(access.accessed_at) }}
                </p>
              </div>
            </div>

            <p v-else class="text-sm text-gray-500 italic">
              Aucun accès récent
            </p>
          </div>

          <!-- Info Box -->
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex">
              <span class="text-2xl mr-3">ℹ️</span>
              <div>
                <h3 class="text-sm font-medium text-blue-900">Conservation des données</h3>
                <p class="mt-1 text-xs text-blue-700">
                  Vos données médicales sont conservées de manière sécurisée pendant 10 ans conformément
                  à la réglementation tunisienne (INPDP).
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { formatDate, formatDateTime, parseTextareaArray, joinTextareaArray } from '@/utils'

const authStore = useAuthStore()

const loading = ref(true)
const medicalRecord = ref<any>(null)
const activeConsents = ref<any[]>([])
const accessHistory = ref<any[]>([])

const editMode = ref({
  personal: false,
  allergies: false,
  diseases: false,
  treatments: false,
  vaccinations: false
})

const editForm = ref({
  blood_type: '',
  weight: null,
  height: null
})

const allergiesText = ref('')
const diseasesText = ref('')
const treatmentsText = ref('')
const vaccinationsText = ref('')

async function loadMedicalRecord() {
  try {
    const response = await api.get('/medical-records/my-record')
    medicalRecord.value = response.data

    // Initialize edit form
    editForm.value = {
      blood_type: medicalRecord.value.blood_type || '',
      weight: medicalRecord.value.weight || null,
      height: medicalRecord.value.height || null
    }

    // Initialize textarea values
    allergiesText.value = joinTextareaArray(medicalRecord.value.allergies || [])
    diseasesText.value = joinTextareaArray(medicalRecord.value.chronic_diseases || [])
    treatmentsText.value = joinTextareaArray(medicalRecord.value.current_treatments || [])
    vaccinationsText.value = joinTextareaArray(medicalRecord.value.vaccinations || [])
  } catch (error) {
    console.error('Error loading medical record:', error)
  } finally {
    loading.value = false
  }
}

async function loadConsents() {
  try {
    const response = await api.get('/medical-records/consents')
    activeConsents.value = response.data.filter((c: any) => !c.revoked_at && new Date(c.expires_at) > new Date())
  } catch (error) {
    console.error('Error loading consents:', error)
  }
}

async function loadAccessHistory() {
  try {
    const response = await api.get('/medical-records/access-history')
    accessHistory.value = response.data.slice(0, 10) // Last 10 accesses
  } catch (error) {
    console.error('Error loading access history:', error)
  }
}

async function savePersonalInfo() {
  try {
    await api.put('/medical-records/my-record', editForm.value)
    await loadMedicalRecord()
    editMode.value.personal = false
  } catch (error) {
    console.error('Error saving personal info:', error)
  }
}

async function saveAllergies() {
  try {
    const allergies = parseTextareaArray(allergiesText.value)
    await api.put('/medical-records/my-record', { allergies })
    await loadMedicalRecord()
    editMode.value.allergies = false
  } catch (error) {
    console.error('Error saving allergies:', error)
  }
}

async function saveDiseases() {
  try {
    const chronic_diseases = parseTextareaArray(diseasesText.value)
    await api.put('/medical-records/my-record', { chronic_diseases })
    await loadMedicalRecord()
    editMode.value.diseases = false
  } catch (error) {
    console.error('Error saving diseases:', error)
  }
}

async function saveTreatments() {
  try {
    const current_treatments = parseTextareaArray(treatmentsText.value)
    await api.put('/medical-records/my-record', { current_treatments })
    await loadMedicalRecord()
    editMode.value.treatments = false
  } catch (error) {
    console.error('Error saving treatments:', error)
  }
}

async function saveVaccinations() {
  try {
    const vaccinations = parseTextareaArray(vaccinationsText.value)
    await api.put('/medical-records/my-record', { vaccinations })
    await loadMedicalRecord()
    editMode.value.vaccinations = false
  } catch (error) {
    console.error('Error saving vaccinations:', error)
  }
}

async function revokeConsent(consentId: number) {
  if (!confirm('Êtes-vous sûr de vouloir révoquer cet accès ?')) return

  try {
    await api.delete(`/medical-records/consents/${consentId}`)
    await loadConsents()
  } catch (error) {
    console.error('Error revoking consent:', error)
  }
}

function calculateBMI(): string {
  if (!medicalRecord.value.weight || !medicalRecord.value.height) return 'N/A'

  const heightInMeters = medicalRecord.value.height / 100
  const bmi = medicalRecord.value.weight / (heightInMeters * heightInMeters)

  let category = ''
  if (bmi < 18.5) category = 'Insuffisance pondérale'
  else if (bmi < 25) category = 'Normal'
  else if (bmi < 30) category = 'Surpoids'
  else category = 'Obésité'

  return `${bmi.toFixed(1)} (${category})`
}

onMounted(() => {
  loadMedicalRecord()
  loadConsents()
  loadAccessHistory()
})
</script>
