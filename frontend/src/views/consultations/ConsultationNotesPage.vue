<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
      </div>

      <div v-else-if="consultation" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Header -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Notes de Consultation</h1>
                <p class="text-gray-600 mt-1">
                  {{ formatDateTime(consultation.created_at) }}
                </p>
              </div>
              <router-link
                :to="`/appointments/${consultation.appointment_id}`"
                class="text-teal-600 hover:text-teal-700"
              >
                ← Retour
              </router-link>
            </div>
          </div>

          <!-- Patient Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations Patient</h2>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Nom complet</p>
                <p class="font-medium text-gray-900">
                  {{ consultation.patient?.first_name }} {{ consultation.patient?.last_name }}
                </p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Âge</p>
                <p class="font-medium text-gray-900">
                  {{ getAge(consultation.patient?.date_of_birth) }} ans
                </p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Sexe</p>
                <p class="font-medium text-gray-900">
                  {{ consultation.patient?.gender === 'male' ? 'Homme' : 'Femme' }}
                </p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Téléphone</p>
                <p class="font-medium text-gray-900">
                  {{ formatPhoneNumber(consultation.patient?.phone) }}
                </p>
              </div>
            </div>

            <button
              @click="showMedicalHistory = !showMedicalHistory"
              class="mt-4 text-teal-600 hover:text-teal-700 text-sm font-medium"
            >
              {{ showMedicalHistory ? 'Masquer' : 'Afficher' }} l'historique médical
            </button>

            <div v-if="showMedicalHistory && medicalRecord" class="mt-4 p-4 bg-gray-50 rounded-lg">
              <div class="space-y-3">
                <div v-if="medicalRecord.allergies && medicalRecord.allergies.length > 0">
                  <p class="text-sm font-medium text-red-700">⚠️ Allergies:</p>
                  <p class="text-sm text-gray-700">{{ medicalRecord.allergies.join(', ') }}</p>
                </div>

                <div v-if="medicalRecord.chronic_diseases && medicalRecord.chronic_diseases.length > 0">
                  <p class="text-sm font-medium text-orange-700">🏥 Maladies chroniques:</p>
                  <p class="text-sm text-gray-700">{{ medicalRecord.chronic_diseases.join(', ') }}</p>
                </div>

                <div v-if="medicalRecord.current_treatments && medicalRecord.current_treatments.length > 0">
                  <p class="text-sm font-medium text-blue-700">💊 Traitements en cours:</p>
                  <p class="text-sm text-gray-700">{{ medicalRecord.current_treatments.join(', ') }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Vital Signs -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Signes Vitaux</h2>

            <form @submit.prevent="saveVitalSigns" class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">TA Systolique</label>
                <input
                  v-model.number="vitalSigns.blood_pressure_systolic"
                  type="number"
                  placeholder="mmHg"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">TA Diastolique</label>
                <input
                  v-model.number="vitalSigns.blood_pressure_diastolic"
                  type="number"
                  placeholder="mmHg"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence Cardiaque</label>
                <input
                  v-model.number="vitalSigns.heart_rate"
                  type="number"
                  placeholder="bpm"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Température</label>
                <input
                  v-model.number="vitalSigns.temperature"
                  type="number"
                  step="0.1"
                  placeholder="°C"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Saturation O2</label>
                <input
                  v-model.number="vitalSigns.oxygen_saturation"
                  type="number"
                  placeholder="%"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poids</label>
                <input
                  v-model.number="vitalSigns.weight"
                  type="number"
                  step="0.1"
                  placeholder="kg"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div class="col-span-2">
                <button
                  type="submit"
                  class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                  Enregistrer les signes vitaux
                </button>
              </div>
            </form>
          </div>

          <!-- Consultation Notes -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes de Consultation</h2>

            <form @submit.prevent="saveNotes" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif de Consultation</label>
                <textarea
                  v-model="notesForm.chief_complaint"
                  rows="2"
                  placeholder="Raison de la visite du patient..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Examen Clinique</label>
                <textarea
                  v-model="notesForm.examination"
                  rows="4"
                  placeholder="Observations de l'examen physique..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnostic</label>
                <textarea
                  v-model="notesForm.diagnosis"
                  rows="3"
                  placeholder="Diagnostic médical..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan de Traitement</label>
                <textarea
                  v-model="notesForm.treatment_plan"
                  rows="4"
                  placeholder="Prescriptions, recommandations, suivi..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes Additionnelles</label>
                <textarea
                  v-model="notesForm.additional_notes"
                  rows="3"
                  placeholder="Autres observations..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <button
                type="submit"
                :disabled="saving"
                class="w-full px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer les notes' }}
              </button>
            </form>
          </div>

          <!-- Create Prescription -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Créer une Ordonnance</h2>

            <div class="space-y-4">
              <div
                v-for="(medication, index) in medications"
                :key="index"
                class="p-4 border border-gray-200 rounded-lg"
              >
                <div class="flex justify-between items-start mb-3">
                  <h3 class="font-medium text-gray-900">Médicament {{ index + 1 }}</h3>
                  <button
                    v-if="medications.length > 1"
                    @click="removeMedication(index)"
                    class="text-red-600 hover:text-red-700 text-sm"
                  >
                    Supprimer
                  </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du médicament*</label>
                    <input
                      v-model="medication.name"
                      type="text"
                      required
                      placeholder="Ex: Paracétamol"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage*</label>
                    <input
                      v-model="medication.dosage"
                      type="text"
                      required
                      placeholder="Ex: 500mg"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Posologie*</label>
                    <input
                      v-model="medication.frequency"
                      type="text"
                      required
                      placeholder="Ex: 3 fois par jour"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée*</label>
                    <input
                      v-model="medication.duration"
                      type="text"
                      required
                      placeholder="Ex: 7 jours"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                    <input
                      v-model="medication.instructions"
                      type="text"
                      placeholder="Ex: Prendre après les repas"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                    />
                  </div>
                </div>
              </div>

              <button
                @click="addMedication"
                type="button"
                class="w-full px-4 py-2 border-2 border-dashed border-gray-300 text-gray-700 rounded-md hover:border-teal-500 hover:text-teal-600"
              >
                + Ajouter un médicament
              </button>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes pour le pharmacien</label>
                <textarea
                  v-model="prescriptionNotes"
                  rows="2"
                  placeholder="Instructions spéciales..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <button
                @click="createPrescription"
                :disabled="saving || medications.length === 0"
                class="w-full px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Création...' : 'Créer l\'ordonnance' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Appointment Info -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rendez-vous</h3>

            <div class="space-y-3">
              <div>
                <p class="text-sm text-gray-600">Date & Heure</p>
                <p class="font-medium text-gray-900">
                  {{ formatDateTime(consultation.appointment?.appointment_date) }}
                </p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="font-medium text-gray-900">
                  {{ consultation.appointment?.appointment_type === 'video' ? '📹 Visioconférence' : '📞 Téléphone' }}
                </p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Durée</p>
                <p class="font-medium text-gray-900">
                  {{ consultation.appointment?.duration }} minutes
                </p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Statut</p>
                <span
                  :class="[
                    'inline-block px-2 py-1 rounded-full text-xs font-medium',
                    getStatusColor(consultation.appointment?.status, 'appointment')
                  ]"
                >
                  {{ getStatusLabel(consultation.appointment?.status, 'appointment') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Existing Prescriptions -->
          <div v-if="existingPrescriptions.length > 0" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ordonnances Créées</h3>

            <div class="space-y-3">
              <div
                v-for="prescription in existingPrescriptions"
                :key="prescription.id"
                class="p-3 bg-green-50 rounded-lg"
              >
                <p class="font-medium text-green-900">
                  #{{ prescription.prescription_number }}
                </p>
                <p class="text-sm text-green-700 mt-1">
                  {{ prescription.medications.length }} médicament(s)
                </p>
                <button
                  @click="downloadPrescription(prescription.id)"
                  class="mt-2 text-sm text-green-600 hover:text-green-700"
                >
                  📥 Télécharger PDF
                </button>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>

            <div class="space-y-2">
              <button
                @click="generateMedicalCertificate"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
              >
                📄 Certificat Médical
              </button>

              <button
                @click="scheduleFollowUp"
                class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm"
              >
                📅 Programmer Suivi
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { formatDateTime, getAge, formatPhoneNumber, getStatusColor, getStatusLabel } from '@/utils'

const route = useRoute()

const loading = ref(true)
const saving = ref(false)
const showMedicalHistory = ref(false)

const consultation = ref<any>(null)
const medicalRecord = ref<any>(null)
const existingPrescriptions = ref<any[]>([])

const vitalSigns = ref({
  blood_pressure_systolic: null,
  blood_pressure_diastolic: null,
  heart_rate: null,
  temperature: null,
  oxygen_saturation: null,
  weight: null
})

const notesForm = ref({
  chief_complaint: '',
  examination: '',
  diagnosis: '',
  treatment_plan: '',
  additional_notes: ''
})

const medications = ref([
  { name: '', dosage: '', frequency: '', duration: '', instructions: '' }
])

const prescriptionNotes = ref('')

async function loadConsultation() {
  try {
    const consultationId = route.params.id
    const response = await api.get(`/consultations/${consultationId}`)
    consultation.value = response.data

    // Initialize forms with existing data
    if (consultation.value.vital_signs) {
      vitalSigns.value = { ...consultation.value.vital_signs }
    }

    notesForm.value = {
      chief_complaint: consultation.value.chief_complaint || '',
      examination: consultation.value.examination || '',
      diagnosis: consultation.value.diagnosis || '',
      treatment_plan: consultation.value.treatment_plan || '',
      additional_notes: consultation.value.additional_notes || ''
    }

    // Load medical record if patient consent exists
    if (consultation.value.patient) {
      try {
        const recordResponse = await api.get(`/medical-records/patients/${consultation.value.patient.id}`)
        medicalRecord.value = recordResponse.data
      } catch (error) {
        console.log('No access to medical record')
      }
    }

    // Load existing prescriptions for this consultation
    loadPrescriptions()
  } catch (error) {
    console.error('Error loading consultation:', error)
  } finally {
    loading.value = false
  }
}

async function loadPrescriptions() {
  try {
    const response = await api.get(`/consultations/${consultation.value.id}/prescriptions`)
    existingPrescriptions.value = response.data
  } catch (error) {
    console.error('Error loading prescriptions:', error)
  }
}

async function saveVitalSigns() {
  saving.value = true
  try {
    await api.put(`/consultations/${consultation.value.id}/notes`, {
      vital_signs: vitalSigns.value
    })
    alert('Signes vitaux enregistrés avec succès')
  } catch (error: any) {
    console.error('Error saving vital signs:', error)
    alert(error.response?.data?.message || 'Erreur lors de l\'enregistrement')
  } finally {
    saving.value = false
  }
}

async function saveNotes() {
  saving.value = true
  try {
    await api.put(`/consultations/${consultation.value.id}/notes`, notesForm.value)
    alert('Notes enregistrées avec succès')
  } catch (error: any) {
    console.error('Error saving notes:', error)
    alert(error.response?.data?.message || 'Erreur lors de l\'enregistrement')
  } finally {
    saving.value = false
  }
}

function addMedication() {
  medications.value.push({
    name: '',
    dosage: '',
    frequency: '',
    duration: '',
    instructions: ''
  })
}

function removeMedication(index: number) {
  medications.value.splice(index, 1)
}

async function createPrescription() {
  // Validate medications
  const isValid = medications.value.every(m =>
    m.name && m.dosage && m.frequency && m.duration
  )

  if (!isValid) {
    alert('Veuillez remplir tous les champs obligatoires pour chaque médicament')
    return
  }

  saving.value = true
  try {
    await api.post(`/consultations/${consultation.value.id}/prescriptions`, {
      medications: medications.value,
      notes: prescriptionNotes.value
    })

    alert('Ordonnance créée avec succès')

    // Reset form
    medications.value = [
      { name: '', dosage: '', frequency: '', duration: '', instructions: '' }
    ]
    prescriptionNotes.value = ''

    // Reload prescriptions
    loadPrescriptions()
  } catch (error: any) {
    console.error('Error creating prescription:', error)
    alert(error.response?.data?.message || 'Erreur lors de la création de l\'ordonnance')
  } finally {
    saving.value = false
  }
}

async function downloadPrescription(prescriptionId: number) {
  try {
    const response = await api.get(`/prescriptions/${prescriptionId}/download`, {
      responseType: 'blob'
    })

    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `ordonnance_${prescriptionId}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error downloading prescription:', error)
    alert('Erreur lors du téléchargement de l\'ordonnance')
  }
}

function generateMedicalCertificate() {
  alert('Fonctionnalité à venir : Génération de certificat médical')
}

function scheduleFollowUp() {
  alert('Fonctionnalité à venir : Programmation de rendez-vous de suivi')
}

onMounted(() => {
  loadConsultation()
})
</script>
