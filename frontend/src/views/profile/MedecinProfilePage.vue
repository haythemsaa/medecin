<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mon Profil Professionnel</h1>
        <p class="mt-2 text-gray-600">
          Gérez vos informations professionnelles et vos disponibilités
        </p>

        <div v-if="validationStatus" class="mt-4">
          <div
            :class="[
              'inline-flex items-center px-4 py-2 rounded-full text-sm font-medium',
              getStatusColor(validationStatus)
            ]"
          >
            <span class="mr-2">{{ getStatusIcon(validationStatus) }}</span>
            <span>{{ getStatusLabel(validationStatus, 'validation') }}</span>
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Personal Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informations Personnelles</h2>

            <form @submit.prevent="savePersonalInfo" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Prénom*</label>
                  <input
                    v-model="form.first_name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nom*</label>
                  <input
                    v-model="form.last_name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone*</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>
              </div>

              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </form>
          </div>

          <!-- Professional Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informations Professionnelles</h2>

            <form @submit.prevent="saveProfessionalInfo" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Spécialité*</label>
                  <select
                    v-model="form.speciality"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  >
                    <option value="">Sélectionner...</option>
                    <option v-for="spec in SPECIALTIES" :key="spec" :value="spec">
                      {{ spec }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Numéro Ordre des Médecins*</label>
                  <input
                    v-model="form.numero_ordre"
                    type="text"
                    required
                    placeholder="XXXX/YYYY"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Années d'Expérience</label>
                  <input
                    v-model.number="form.years_experience"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Prix Consultation (TND)*</label>
                  <input
                    v-model.number="form.consultation_price"
                    type="number"
                    min="30"
                    max="500"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Langues Parlées</label>
                <div class="flex flex-wrap gap-3 mt-2">
                  <label v-for="lang in LANGUAGES" :key="lang.code" class="flex items-center">
                    <input
                      type="checkbox"
                      :value="lang.code"
                      v-model="selectedLanguages"
                      class="mr-2 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                    />
                    <span>{{ lang.flag }} {{ lang.label }}</span>
                  </label>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio Professionnelle</label>
                <textarea
                  v-model="form.bio"
                  rows="4"
                  placeholder="Décrivez votre parcours, vos domaines d'expertise..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diplômes et Formations</label>
                <textarea
                  v-model="diplomasText"
                  rows="3"
                  placeholder="Un diplôme par ligne&#10;Ex: Doctorat en Médecine - Université de Tunis (2015)"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                ></textarea>
              </div>

              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </form>
          </div>

          <!-- Cabinet Information -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Cabinet Médical</h2>

            <form @submit.prevent="saveCabinetInfo" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du Cabinet</label>
                <input
                  v-model="form.cabinet_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse du Cabinet</label>
                <input
                  v-model="form.cabinet_address"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Gouvernorat</label>
                  <select
                    v-model="form.governorate"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  >
                    <option value="">Sélectionner...</option>
                    <option v-for="gov in GOVERNORATES" :key="gov" :value="gov">
                      {{ gov }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone Cabinet</label>
                  <input
                    v-model="form.cabinet_phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                  />
                </div>
              </div>

              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </form>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Statistics -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>

            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Consultations totales</span>
                <span class="text-lg font-bold text-gray-900">{{ stats.total_consultations }}</span>
              </div>

              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Note moyenne</span>
                <div class="flex items-center">
                  <span class="text-lg font-bold text-gray-900 mr-1">{{ stats.average_rating }}</span>
                  <span class="text-yellow-500">⭐</span>
                </div>
              </div>

              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Taux de réponse</span>
                <span class="text-lg font-bold text-gray-900">{{ stats.response_rate }}%</span>
              </div>
            </div>
          </div>

          <!-- Validation Status -->
          <div v-if="validationStatus !== 'validated'" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
              <span class="text-2xl mr-3">⚠️</span>
              <div>
                <h3 class="text-sm font-medium text-yellow-900">Validation en cours</h3>
                <p class="mt-1 text-xs text-yellow-700">
                  Votre profil est en cours de validation par notre équipe. Vous serez notifié par email dès que la validation sera terminée.
                </p>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>

            <div class="space-y-2">
              <router-link
                to="/appointments"
                class="block w-full px-4 py-2 bg-teal-600 text-white text-center rounded-md hover:bg-teal-700"
              >
                Mes Rendez-vous
              </router-link>

              <router-link
                to="/prescriptions"
                class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-md hover:bg-blue-700"
              >
                Ordonnances
              </router-link>

              <router-link
                to="/messages"
                class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-md hover:bg-purple-700"
              >
                Messages
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { GOVERNORATES, SPECIALTIES, LANGUAGES, getStatusColor, getStatusLabel, parseTextareaArray, joinTextareaArray } from '@/utils'

const authStore = useAuthStore()

const loading = ref(true)
const saving = ref(false)
const validationStatus = ref('')

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  speciality: '',
  numero_ordre: '',
  years_experience: 0,
  consultation_price: 75,
  bio: '',
  cabinet_name: '',
  cabinet_address: '',
  cabinet_phone: '',
  governorate: ''
})

const selectedLanguages = ref<string[]>([])
const diplomasText = ref('')

const stats = ref({
  total_consultations: 0,
  average_rating: 0,
  response_rate: 0
})

async function loadProfile() {
  try {
    const response = await api.get('/medecins/profile')
    const user = response.data.user
    const medecin = response.data

    validationStatus.value = medecin.validation_status

    form.value = {
      first_name: user.first_name,
      last_name: user.last_name,
      email: user.email,
      phone: user.phone,
      speciality: medecin.speciality,
      numero_ordre: medecin.numero_ordre,
      years_experience: medecin.years_experience || 0,
      consultation_price: medecin.consultation_price || 75,
      bio: medecin.bio || '',
      cabinet_name: medecin.cabinet_name || '',
      cabinet_address: medecin.cabinet_address || '',
      cabinet_phone: medecin.cabinet_phone || '',
      governorate: medecin.governorate || ''
    }

    selectedLanguages.value = medecin.languages_spoken || ['ar', 'fr']
    diplomasText.value = joinTextareaArray(medecin.diplomas || [])

    // Load stats
    stats.value = {
      total_consultations: medecin.total_consultations || 0,
      average_rating: medecin.rating || 0,
      response_rate: medecin.response_rate || 95
    }
  } catch (error) {
    console.error('Error loading profile:', error)
  } finally {
    loading.value = false
  }
}

async function savePersonalInfo() {
  saving.value = true
  try {
    await api.put('/medecins/profile', {
      first_name: form.value.first_name,
      last_name: form.value.last_name,
      email: form.value.email,
      phone: form.value.phone
    })
    alert('Informations personnelles mises à jour avec succès')
  } catch (error: any) {
    console.error('Error saving personal info:', error)
    alert(error.response?.data?.message || 'Erreur lors de la mise à jour')
  } finally {
    saving.value = false
  }
}

async function saveProfessionalInfo() {
  saving.value = true
  try {
    const diplomas = parseTextareaArray(diplomasText.value)

    await api.put('/medecins/profile', {
      speciality: form.value.speciality,
      numero_ordre: form.value.numero_ordre,
      years_experience: form.value.years_experience,
      consultation_price: form.value.consultation_price,
      bio: form.value.bio,
      languages_spoken: selectedLanguages.value,
      diplomas: diplomas
    })
    alert('Informations professionnelles mises à jour avec succès')
  } catch (error: any) {
    console.error('Error saving professional info:', error)
    alert(error.response?.data?.message || 'Erreur lors de la mise à jour')
  } finally {
    saving.value = false
  }
}

async function saveCabinetInfo() {
  saving.value = true
  try {
    await api.put('/medecins/profile', {
      cabinet_name: form.value.cabinet_name,
      cabinet_address: form.value.cabinet_address,
      cabinet_phone: form.value.cabinet_phone,
      governorate: form.value.governorate
    })
    alert('Informations du cabinet mises à jour avec succès')
  } catch (error: any) {
    console.error('Error saving cabinet info:', error)
    alert(error.response?.data?.message || 'Erreur lors de la mise à jour')
  } finally {
    saving.value = false
  }
}

function getStatusIcon(status: string): string {
  const icons: Record<string, string> = {
    pending: '⏳',
    incomplete: '📋',
    validated: '✅',
    rejected: '❌'
  }
  return icons[status] || '❓'
}

onMounted(() => {
  loadProfile()
})
</script>
