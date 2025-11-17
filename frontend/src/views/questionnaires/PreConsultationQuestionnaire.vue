<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <router-link
          :to="`/appointments/${appointmentId}`"
          class="inline-flex items-center text-teal-600 hover:text-teal-700 mb-4"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour au rendez-vous
        </router-link>
        <h1 class="text-3xl font-bold text-gray-900">Questionnaire pré-consultation</h1>
        <p class="mt-2 text-sm text-gray-600">
          Veuillez remplir ce questionnaire avant votre consultation pour aider votre médecin à mieux vous prendre en charge.
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Questionnaire Form -->
      <form v-else @submit.prevent="submitQuestionnaire" class="space-y-6">
        <!-- Chief Complaint -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Motif de consultation</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Décrivez votre problème principal <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.chief_complaint"
                rows="3"
                required
                maxlength="500"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                placeholder="Ex: Douleur abdominale depuis 2 jours..."
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Durée des symptômes
                </label>
                <input
                  v-model="form.symptom_duration"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                  placeholder="Ex: 2 jours, 1 semaine..."
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Intensité (1-10)
                </label>
                <input
                  v-model.number="form.symptom_intensity"
                  type="range"
                  min="1"
                  max="10"
                  class="w-full"
                />
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Faible</span>
                  <span class="font-semibold text-teal-600">{{ form.symptom_intensity }}</span>
                  <span>Intense</span>
                </div>
              </div>
            </div>
          </div>
        </Card>

        <!-- Medical History -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Antécédents médicaux</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Médicaments actuels
              </label>
              <div class="space-y-2">
                <div v-for="(med, index) in form.current_medications" :key="index" class="flex gap-2">
                  <input
                    v-model="form.current_medications[index]"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Nom du médicament et dosage"
                  />
                  <button
                    type="button"
                    @click="removeMedication(index)"
                    class="px-3 py-2 text-red-600 hover:text-red-700"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
                <button
                  type="button"
                  @click="addMedication"
                  class="text-sm text-teal-600 hover:text-teal-700"
                >
                  + Ajouter un médicament
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Allergies connues
              </label>
              <div class="space-y-2">
                <div v-for="(allergy, index) in form.allergies" :key="index" class="flex gap-2">
                  <input
                    v-model="form.allergies[index]"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Type d'allergie"
                  />
                  <button
                    type="button"
                    @click="removeAllergy(index)"
                    class="px-3 py-2 text-red-600 hover:text-red-700"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
                <button
                  type="button"
                  @click="addAllergy"
                  class="text-sm text-teal-600 hover:text-teal-700"
                >
                  + Ajouter une allergie
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Antécédents de santé
              </label>
              <div class="space-y-2">
                <div v-for="(condition, index) in form.previous_conditions" :key="index" class="flex gap-2">
                  <input
                    v-model="form.previous_conditions[index]"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Ex: Diabète, hypertension..."
                  />
                  <button
                    type="button"
                    @click="removeCondition(index)"
                    class="px-3 py-2 text-red-600 hover:text-red-700"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
                <button
                  type="button"
                  @click="addCondition"
                  class="text-sm text-teal-600 hover:text-teal-700"
                >
                  + Ajouter un antécédent
                </button>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Antécédents familiaux
              </label>
              <textarea
                v-model="form.family_history"
                rows="2"
                maxlength="1000"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                placeholder="Maladies présentes dans votre famille..."
              ></textarea>
            </div>
          </div>
        </Card>

        <!-- Lifestyle -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Hygiène de vie</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Fumez-vous ?
              </label>
              <select
                v-model="form.lifestyle.smoking"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              >
                <option value="">Sélectionner</option>
                <option value="non">Non</option>
                <option value="oui">Oui</option>
                <option value="anciennement">Anciennement</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Consommation d'alcool
              </label>
              <select
                v-model="form.lifestyle.alcohol"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              >
                <option value="">Sélectionner</option>
                <option value="jamais">Jamais</option>
                <option value="occasionnellement">Occasionnellement</option>
                <option value="régulièrement">Régulièrement</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Activité physique
              </label>
              <select
                v-model="form.lifestyle.exercise"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              >
                <option value="">Sélectionner</option>
                <option value="non">Aucune</option>
                <option value="peu">Peu fréquente</option>
                <option value="régulière">Régulière</option>
                <option value="intensive">Intensive</option>
              </select>
            </div>
          </div>
        </Card>

        <!-- Template Questions -->
        <Card v-if="template && template.questions">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ template.title }}</h2>
          <div class="space-y-4">
            <div v-for="question in template.questions" :key="question.id">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ question.question }}
              </label>

              <!-- Radio buttons -->
              <div v-if="question.type === 'radio'" class="space-y-2">
                <label
                  v-for="option in question.options"
                  :key="option"
                  class="flex items-center"
                >
                  <input
                    v-model="form.answers[question.id]"
                    type="radio"
                    :value="option"
                    class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300"
                  />
                  <span class="ml-3 text-sm text-gray-700">{{ option }}</span>
                </label>
              </div>

              <!-- Text input -->
              <input
                v-else-if="question.type === 'text'"
                v-model="form.answers[question.id]"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              />

              <!-- Number input -->
              <input
                v-else-if="question.type === 'number'"
                v-model.number="form.answers[question.id]"
                type="number"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              />
            </div>
          </div>
        </Card>

        <!-- Additional Notes -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes additionnelles</h2>
          <textarea
            v-model="form.additional_notes"
            rows="4"
            maxlength="1000"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
            placeholder="Toute information supplémentaire que vous souhaitez partager avec votre médecin..."
          ></textarea>
        </Card>

        <!-- Submit Button -->
        <div class="flex justify-end gap-4">
          <Button
            type="button"
            variant="outline"
            @click="$router.back()"
          >
            Annuler
          </Button>
          <Button
            type="submit"
            :disabled="submitting"
          >
            {{ submitting ? 'Envoi en cours...' : 'Soumettre le questionnaire' }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const appointmentId = ref(Number(route.params.id))
const loading = ref(true)
const submitting = ref(false)
const template = ref<any>(null)

const form = ref({
  appointment_id: appointmentId.value,
  chief_complaint: '',
  symptom_duration: '',
  symptom_intensity: 5,
  current_medications: [''],
  allergies: [''],
  previous_conditions: [''],
  family_history: '',
  lifestyle: {
    smoking: '',
    alcohol: '',
    exercise: '',
  },
  additional_notes: '',
  answers: {} as Record<string, any>,
})

onMounted(async () => {
  await loadQuestionnaire()
})

async function loadQuestionnaire() {
  try {
    loading.value = true
    const response = await api.get(`/questionnaires/appointments/${appointmentId.value}`)

    if (response.data.questionnaire) {
      // Load existing questionnaire
      const q = response.data.questionnaire
      form.value = {
        appointment_id: appointmentId.value,
        chief_complaint: q.chief_complaint || '',
        symptom_duration: q.symptom_duration || '',
        symptom_intensity: q.symptom_intensity || 5,
        current_medications: q.current_medications || [''],
        allergies: q.allergies || [''],
        previous_conditions: q.previous_conditions || [''],
        family_history: q.family_history || '',
        lifestyle: q.lifestyle || { smoking: '', alcohol: '', exercise: '' },
        additional_notes: q.additional_notes || '',
        answers: q.answers || {},
      }
    }

    if (response.data.template) {
      template.value = response.data.template
    }
  } catch (error: any) {
    console.error('Error loading questionnaire:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

function addMedication() {
  form.value.current_medications.push('')
}

function removeMedication(index: number) {
  form.value.current_medications.splice(index, 1)
}

function addAllergy() {
  form.value.allergies.push('')
}

function removeAllergy(index: number) {
  form.value.allergies.splice(index, 1)
}

function addCondition() {
  form.value.previous_conditions.push('')
}

function removeCondition(index: number) {
  form.value.previous_conditions.splice(index, 1)
}

async function submitQuestionnaire() {
  try {
    submitting.value = true

    // Filter out empty strings
    const data = {
      ...form.value,
      current_medications: form.value.current_medications.filter(m => m.trim()),
      allergies: form.value.allergies.filter(a => a.trim()),
      previous_conditions: form.value.previous_conditions.filter(c => c.trim()),
    }

    await api.post('/questionnaires/submit', data)
    toast.success('Questionnaire soumis avec succès')
    router.push(`/appointments/${appointmentId.value}`)
  } catch (error: any) {
    console.error('Error submitting questionnaire:', error)
    toast.error(error.response?.data?.message || 'Erreur lors de la soumission')
  } finally {
    submitting.value = false
  }
}
</script>
