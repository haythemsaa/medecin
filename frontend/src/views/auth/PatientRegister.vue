<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <router-link to="/" class="inline-flex items-center space-x-2 mb-4">
          <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center">
            <span class="text-white font-bold text-2xl">S</span>
          </div>
          <span class="text-2xl font-bold text-primary-600">Seha Digital</span>
        </router-link>
        <h2 class="mt-4 text-3xl font-bold text-gray-900">
          Inscription Patient
        </h2>
        <p class="mt-2 text-gray-600">
          Créez votre compte pour accéder aux consultations
        </p>
      </div>

      <div class="bg-white shadow-lg rounded-xl p-8">
        <!-- Progress Steps -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div v-for="step in 3" :key="step" class="flex-1">
              <div class="flex items-center">
                <div
                  :class="[
                    'flex items-center justify-center w-10 h-10 rounded-full border-2',
                    currentStep >= step
                      ? 'bg-primary-600 border-primary-600 text-white'
                      : 'border-gray-300 text-gray-500'
                  ]"
                >
                  {{ step }}
                </div>
                <div
                  v-if="step < 3"
                  :class="[
                    'h-1 flex-1 mx-2',
                    currentStep > step ? 'bg-primary-600' : 'bg-gray-300'
                  ]"
                ></div>
              </div>
              <div class="mt-2 text-xs text-gray-600">
                {{ ['Identité', 'Contact', 'Médical'][step - 1] }}
              </div>
            </div>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="authStore.error" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
          {{ authStore.error }}
        </div>

        <form @submit.prevent="handleSubmit">
          <!-- Step 1: Identité -->
          <div v-show="currentStep === 1" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Prénom</label>
                <input v-model="form.first_name" type="text" required class="input mt-1" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nom</label>
                <input v-model="form.last_name" type="text" required class="input mt-1" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Prénom (Arabe)</label>
                <input v-model="form.first_name_ar" type="text" class="input mt-1" dir="rtl" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nom (Arabe)</label>
                <input v-model="form.last_name_ar" type="text" class="input mt-1" dir="rtl" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
              <input v-model="form.birth_date" type="date" required class="input mt-1" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">CIN (8 chiffres)</label>
              <input v-model="form.cin" type="text" pattern="[0-9]{8}" maxlength="8" required class="input mt-1" placeholder="12345678" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Sexe</label>
              <select v-model="form.gender" required class="input mt-1">
                <option value="">Sélectionner</option>
                <option value="male">Homme</option>
                <option value="female">Femme</option>
                <option value="other">Autre</option>
              </select>
            </div>
          </div>

          <!-- Step 2: Contact -->
          <div v-show="currentStep === 2" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input v-model="form.email" type="email" required class="input mt-1" placeholder="votre@email.com" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Téléphone mobile (+216)</label>
              <input v-model="form.phone" type="tel" pattern="[0-9]{8}" maxlength="8" required class="input mt-1" placeholder="12345678" />
              <p class="mt-1 text-sm text-gray-500">Format: 8 chiffres sans le +216</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Gouvernorat</label>
              <select v-model="form.governorate" class="input mt-1">
                <option value="">Sélectionner</option>
                <option value="Tunis">Tunis</option>
                <option value="Ariana">Ariana</option>
                <option value="Ben Arous">Ben Arous</option>
                <option value="Manouba">Manouba</option>
                <option value="Sfax">Sfax</option>
                <option value="Sousse">Sousse</option>
                <option value="Monastir">Monastir</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Adresse</label>
              <textarea v-model="form.address" rows="3" class="input mt-1"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
              <input v-model="form.password" type="password" required minlength="8" class="input mt-1" />
              <p class="mt-1 text-sm text-gray-500">Minimum 8 caractères</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
              <input v-model="form.password_confirmation" type="password" required class="input mt-1" />
            </div>
          </div>

          <!-- Step 3: Médical -->
          <div v-show="currentStep === 3" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Couverture santé</label>
              <select v-model="form.health_coverage" class="input mt-1">
                <option value="none">Aucune</option>
                <option value="cnam">CNAM</option>
                <option value="mutuelle">Mutuelle privée</option>
              </select>
            </div>

            <div v-if="form.health_coverage === 'cnam'">
              <label class="block text-sm font-medium text-gray-700">Numéro CNAM</label>
              <input v-model="form.cnam_number" type="text" class="input mt-1" />
            </div>

            <div v-if="form.health_coverage === 'mutuelle'">
              <label class="block text-sm font-medium text-gray-700">Nom de la mutuelle</label>
              <input v-model="form.mutuelle_name" type="text" class="input mt-1" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Allergies connues</label>
              <textarea
                v-model="allergiesText"
                rows="3"
                class="input mt-1"
                placeholder="Ex: Pénicilline, Aspirine (une par ligne)"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Maladies chroniques</label>
              <textarea
                v-model="diseasesText"
                rows="3"
                class="input mt-1"
                placeholder="Ex: Diabète, Hypertension (une par ligne)"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Traitements en cours</label>
              <textarea
                v-model="treatmentsText"
                rows="3"
                class="input mt-1"
                placeholder="Ex: Metformine 500mg (une par ligne)"
              ></textarea>
            </div>

            <div class="flex items-start">
              <input v-model="form.accepts_terms" id="terms" type="checkbox" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" />
              <label for="terms" class="ml-2 block text-sm text-gray-900">
                J'accepte les <a href="#" class="text-primary-600 hover:text-primary-500">conditions d'utilisation</a>
                et la <a href="#" class="text-primary-600 hover:text-primary-500">politique de confidentialité</a>
              </label>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-8 flex justify-between">
            <button
              v-if="currentStep > 1"
              type="button"
              @click="currentStep--"
              class="btn btn-secondary"
            >
              Précédent
            </button>
            <div v-else></div>

            <button
              v-if="currentStep < 3"
              type="button"
              @click="currentStep++"
              class="btn btn-primary"
            >
              Suivant
            </button>
            <button
              v-else
              type="submit"
              :disabled="authStore.loading"
              class="btn btn-primary"
            >
              {{ authStore.loading ? 'Inscription...' : "S'inscrire" }}
            </button>
          </div>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center text-sm text-gray-600">
          Vous avez déjà un compte ?
          <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
            Se connecter
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const currentStep = ref(1)

const form = ref({
  // Step 1: Identité
  first_name: '',
  last_name: '',
  first_name_ar: '',
  last_name_ar: '',
  birth_date: '',
  cin: '',
  gender: '',

  // Step 2: Contact
  email: '',
  phone: '',
  governorate: '',
  delegation: '',
  address: '',
  password: '',
  password_confirmation: '',
  preferred_language: 'fr',

  // Step 3: Médical
  health_coverage: 'none',
  cnam_number: '',
  mutuelle_name: '',
  accepts_terms: false
})

const allergiesText = ref('')
const diseasesText = ref('')
const treatmentsText = ref('')

async function handleSubmit() {
  if (form.value.password !== form.value.password_confirmation) {
    authStore.error = 'Les mots de passe ne correspondent pas'
    return
  }

  // Convert text areas to arrays
  const allergies = allergiesText.value.split('\n').filter(a => a.trim())
  const chronic_diseases = diseasesText.value.split('\n').filter(d => d.trim())
  const current_treatments = treatmentsText.value.split('\n').filter(t => t.trim())

  const registrationData = {
    ...form.value,
    phone: '+216' + form.value.phone,
    allergies,
    chronic_diseases,
    current_treatments
  }

  try {
    await authStore.register(registrationData, 'patient')
    router.push({ name: 'Dashboard' })
  } catch (error) {
    console.error('Registration error:', error)
  }
}
</script>
