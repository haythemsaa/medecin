<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mon Profil</h1>
        <p class="mt-2 text-gray-600">
          Gérez vos informations personnelles et vos préférences
        </p>
      </div>

      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-600"></div>
      </div>

      <div v-else class="space-y-6">
        <!-- Personal Information Card -->
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

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de Naissance*</label>
                <input
                  v-model="form.date_of_birth"
                  type="date"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sexe*</label>
                <select
                  v-model="form.gender"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                >
                  <option value="male">Homme</option>
                  <option value="female">Femme</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CIN*</label>
                <input
                  v-model="form.cin"
                  type="text"
                  required
                  maxlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>
            </div>

            <div class="pt-4">
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer les modifications' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Address Information -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Adresse</h2>

          <form @submit.prevent="saveAddress" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Adresse Complète</label>
              <input
                v-model="form.address"
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Code Postal</label>
                <input
                  v-model="form.postal_code"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                />
              </div>
            </div>

            <div class="pt-4">
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer l\'adresse' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Health Coverage -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Couverture Santé</h2>

          <form @submit.prevent="saveHealthCoverage" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type de Couverture</label>
              <select
                v-model="form.health_coverage_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
              >
                <option value="none">Aucune</option>
                <option value="cnam">CNAM</option>
                <option value="mutuelle">Mutuelle privée</option>
              </select>
            </div>

            <div v-if="form.health_coverage_type === 'cnam'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Numéro CNAM</label>
              <input
                v-model="form.health_coverage_number"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
                placeholder="13 chiffres"
              />
            </div>

            <div v-if="form.health_coverage_type === 'mutuelle'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la Mutuelle</label>
              <input
                v-model="form.mutuelle_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
              />
            </div>

            <div class="pt-4">
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer la couverture santé' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Changer le Mot de Passe</h2>

          <form @submit.prevent="changePassword" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mot de Passe Actuel*</label>
              <input
                v-model="passwordForm.current_password"
                type="password"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau Mot de Passe*</label>
              <input
                v-model="passwordForm.new_password"
                type="password"
                required
                minlength="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
              />
              <p class="mt-1 text-xs text-gray-500">
                Minimum 8 caractères, incluant majuscule, minuscule et chiffre
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le Nouveau Mot de Passe*</label>
              <input
                v-model="passwordForm.new_password_confirmation"
                type="password"
                required
                minlength="8"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
              />
            </div>

            <div v-if="passwordError" class="text-red-600 text-sm">
              {{ passwordError }}
            </div>

            <div class="pt-4">
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:bg-gray-400"
              >
                {{ saving ? 'Modification...' : 'Changer le mot de passe' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Account Actions -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">Actions du Compte</h2>

          <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b">
              <div>
                <h3 class="font-medium text-gray-900">Télécharger mes données</h3>
                <p class="text-sm text-gray-600">Exportez toutes vos données médicales</p>
              </div>
              <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Télécharger
              </button>
            </div>

            <div class="flex items-center justify-between py-3">
              <div>
                <h3 class="font-medium text-red-600">Supprimer mon compte</h3>
                <p class="text-sm text-gray-600">
                  Cette action est irréversible. Toutes vos données seront supprimées.
                </p>
              </div>
              <button
                @click="confirmDeleteAccount"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
              >
                Supprimer
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
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { GOVERNORATES } from '@/utils'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const saving = ref(false)
const passwordError = ref('')

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  gender: 'male',
  cin: '',
  address: '',
  governorate: '',
  postal_code: '',
  health_coverage_type: 'none',
  health_coverage_number: '',
  mutuelle_name: ''
})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

async function loadProfile() {
  try {
    const response = await api.get('/patients/profile')
    const user = response.data.user
    const patient = response.data

    form.value = {
      first_name: user.first_name,
      last_name: user.last_name,
      email: user.email,
      phone: user.phone,
      date_of_birth: patient.date_of_birth,
      gender: patient.gender,
      cin: patient.cin,
      address: patient.address || '',
      governorate: patient.governorate || '',
      postal_code: patient.postal_code || '',
      health_coverage_type: patient.health_coverage_type || 'none',
      health_coverage_number: patient.health_coverage_number || '',
      mutuelle_name: patient.mutuelle_name || ''
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
    await api.put('/patients/profile', {
      first_name: form.value.first_name,
      last_name: form.value.last_name,
      email: form.value.email,
      phone: form.value.phone,
      date_of_birth: form.value.date_of_birth,
      gender: form.value.gender,
      cin: form.value.cin
    })
    alert('Informations personnelles mises à jour avec succès')
  } catch (error: any) {
    console.error('Error saving personal info:', error)
    alert(error.response?.data?.message || 'Erreur lors de la mise à jour')
  } finally {
    saving.value = false
  }
}

async function saveAddress() {
  saving.value = true
  try {
    await api.put('/patients/profile', {
      address: form.value.address,
      governorate: form.value.governorate,
      postal_code: form.value.postal_code
    })
    alert('Adresse mise à jour avec succès')
  } catch (error: any) {
    console.error('Error saving address:', error)
    alert(error.response?.data?.message || 'Erreur lors de la mise à jour')
  } finally {
    saving.value = false
  }
}

async function saveHealthCoverage() {
  saving.value = true
  try {
    await api.put('/patients/profile', {
      health_coverage_type: form.value.health_coverage_type,
      health_coverage_number: form.value.health_coverage_number,
      mutuelle_name: form.value.mutuelle_name
    })
    alert('Couverture santé mise à jour avec succès')
  } catch (error: any) {
    console.error('Error saving health coverage:', error)
    alert(error.response?.data?.message || 'Erreur lors de la mise à jour')
  } finally {
    saving.value = false
  }
}

async function changePassword() {
  passwordError.value = ''

  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    passwordError.value = 'Les mots de passe ne correspondent pas'
    return
  }

  saving.value = true
  try {
    await api.post('/auth/change-password', {
      current_password: passwordForm.value.current_password,
      new_password: passwordForm.value.new_password
    })

    passwordForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    }

    alert('Mot de passe modifié avec succès')
  } catch (error: any) {
    console.error('Error changing password:', error)
    passwordError.value = error.response?.data?.message || 'Erreur lors de la modification'
  } finally {
    saving.value = false
  }
}

function confirmDeleteAccount() {
  const confirmed = confirm(
    'Êtes-vous absolument sûr de vouloir supprimer votre compte ? ' +
    'Cette action est irréversible et toutes vos données seront supprimées.'
  )

  if (confirmed) {
    const doubleConfirmed = confirm(
      'Dernière confirmation : Voulez-vous vraiment supprimer votre compte ?'
    )

    if (doubleConfirmed) {
      deleteAccount()
    }
  }
}

async function deleteAccount() {
  try {
    await api.delete('/patients/profile')
    authStore.logout()
    router.push('/')
  } catch (error: any) {
    console.error('Error deleting account:', error)
    alert(error.response?.data?.message || 'Erreur lors de la suppression')
  }
}

onMounted(() => {
  loadProfile()
})
</script>
