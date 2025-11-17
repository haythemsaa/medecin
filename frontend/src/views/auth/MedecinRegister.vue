<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <router-link to="/" class="inline-flex items-center space-x-2 mb-4">
          <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center">
            <span class="text-white font-bold text-2xl">S</span>
          </div>
          <span class="text-2xl font-bold text-primary-600">Seha Digital</span>
        </router-link>
        <h2 class="mt-4 text-3xl font-bold text-gray-900">
          Inscription Médecin
        </h2>
        <p class="mt-2 text-gray-600">
          Rejoignez notre réseau de médecins et élargissez votre patientèle
        </p>
      </div>

      <div class="bg-white shadow-lg rounded-xl p-8">
        <!-- Alert: Validation Process -->
        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
          <p class="font-medium">Processus de validation</p>
          <p class="text-sm mt-1">
            Votre dossier sera examiné par notre équipe sous 72 heures. Vous recevrez un email une fois validé.
          </p>
        </div>

        <!-- Error Alert -->
        <div v-if="authStore.error" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
          {{ authStore.error }}
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-8">
          <!-- Informations personnelles -->
          <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Prénom *</label>
                <input v-model="form.first_name" type="text" required class="input mt-1" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nom *</label>
                <input v-model="form.last_name" type="text" required class="input mt-1" />
              </div>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700">CIN (8 chiffres) *</label>
              <input v-model="form.cin" type="text" pattern="[0-9]{8}" maxlength="8" required class="input mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Email *</label>
                <input v-model="form.email" type="email" required class="input mt-1" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Téléphone *</label>
                <input v-model="form.phone" type="tel" pattern="[0-9]{8}" required class="input mt-1" placeholder="12345678" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Mot de passe *</label>
                <input v-model="form.password" type="password" required minlength="8" class="input mt-1" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Confirmer mot de passe *</label>
                <input v-model="form.password_confirmation" type="password" required class="input mt-1" />
              </div>
            </div>
          </section>

          <!-- Informations professionnelles -->
          <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations professionnelles</h3>

            <div>
              <label class="block text-sm font-medium text-gray-700">Numéro d'Ordre des Médecins *</label>
              <input v-model="form.ordre_number" type="text" required class="input mt-1" placeholder="Ex: 123456" />
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700">Spécialité *</label>
              <select v-model="form.speciality" required class="input mt-1">
                <option value="">Sélectionner une spécialité</option>
                <option value="Médecine Générale">Médecine Générale</option>
                <option value="Cardiologie">Cardiologie</option>
                <option value="Dermatologie">Dermatologie</option>
                <option value="Pédiatrie">Pédiatrie</option>
                <option value="Gynécologie">Gynécologie</option>
                <option value="Ophtalmologie">Ophtalmologie</option>
                <option value="ORL">ORL</option>
                <option value="Psychiatrie">Psychiatrie</option>
                <option value="Radiologie">Radiologie</option>
                <option value="Chirurgie Générale">Chirurgie Générale</option>
              </select>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700">Années d'expérience *</label>
              <input v-model.number="form.years_of_experience" type="number" min="0" required class="input mt-1" />
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700">Biographie professionnelle</label>
              <textarea v-model="form.bio" rows="4" class="input mt-1" placeholder="Décrivez votre parcours et vos domaines d'expertise..."></textarea>
              <p class="mt-1 text-sm text-gray-500">Maximum 1000 caractères</p>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700">Langues de consultation *</label>
              <div class="mt-2 space-y-2">
                <label class="inline-flex items-center mr-4">
                  <input v-model="languages" type="checkbox" value="fr" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                  <span class="ml-2">Français</span>
                </label>
                <label class="inline-flex items-center mr-4">
                  <input v-model="languages" type="checkbox" value="ar" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                  <span class="ml-2">Arabe</span>
                </label>
                <label class="inline-flex items-center">
                  <input v-model="languages" type="checkbox" value="en" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                  <span class="ml-2">Anglais</span>
                </label>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tarif consultation standard (TND) *</label>
                <input v-model.number="form.consultation_price" type="number" min="0" step="0.01" required class="input mt-1" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tarif consultation urgente (TND)</label>
                <input v-model.number="form.urgent_consultation_price" type="number" min="0" step="0.01" class="input mt-1" />
                <p class="mt-1 text-sm text-gray-500">Laisser vide pour +30% du tarif standard</p>
              </div>
            </div>
          </section>

          <!-- Documents -->
          <section>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents requis</h3>
            <p class="text-sm text-gray-600 mb-4">
              Formats acceptés: JPG, PNG, PDF (Max 5 MB par fichier)
            </p>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">CIN Recto *</label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'cin_file_recto')"
                  accept="image/*,application/pdf"
                  required
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">CIN Verso *</label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'cin_file_verso')"
                  accept="image/*,application/pdf"
                  required
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Certificat Ordre des Médecins (&lt;3 mois) *</label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'ordre_certificate')"
                  accept="application/pdf"
                  required
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Diplôme de médecine *</label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'diploma_file')"
                  accept="application/pdf"
                  required
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Attestation RCP (Assurance) *</label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'rcp_attestation')"
                  accept="application/pdf"
                  required
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Photo professionnelle</label>
                <input
                  type="file"
                  @change="handleFileUpload($event, 'photo')"
                  accept="image/*"
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                />
              </div>
            </div>
          </section>

          <!-- Terms -->
          <div class="flex items-start">
            <input v-model="form.accepts_terms" id="terms" type="checkbox" required class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              J'accepte les <a href="#" class="text-primary-600 hover:text-primary-500">conditions d'utilisation</a>
              et confirme que toutes les informations fournies sont exactes
            </label>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end space-x-4">
            <router-link to="/" class="btn btn-secondary">
              Annuler
            </router-link>
            <button
              type="submit"
              :disabled="authStore.loading"
              class="btn btn-primary"
            >
              {{ authStore.loading ? 'Envoi en cours...' : 'Soumettre ma demande' }}
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  first_name: '',
  last_name: '',
  cin: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  ordre_number: '',
  speciality: '',
  years_of_experience: 0,
  bio: '',
  consultation_price: 60,
  urgent_consultation_price: null,
  accepts_terms: false,
  preferred_language: 'fr'
})

const languages = ref(['fr'])
const files = ref<Record<string, File>>({})

function handleFileUpload(event: Event, fieldName: string) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    files.value[fieldName] = target.files[0]
  }
}

async function handleSubmit() {
  if (form.value.password !== form.value.password_confirmation) {
    authStore.error = 'Les mots de passe ne correspondent pas'
    return
  }

  // Create FormData for file upload
  const formData = new FormData()

  // Add all form fields
  Object.entries(form.value).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      formData.append(key, value.toString())
    }
  })

  // Add languages
  formData.append('consultation_languages', JSON.stringify(languages.value))
  formData.append('phone', '+216' + form.value.phone)

  // Add files
  Object.entries(files.value).forEach(([key, file]) => {
    formData.append(key, file)
  })

  try {
    await authStore.register(formData, 'medecin')

    // Show success message
    alert('Votre demande a été soumise avec succès ! Vous recevrez un email dans les 72 heures.')
    router.push({ name: 'Home' })
  } catch (error) {
    console.error('Registration error:', error)
  }
}
</script>
