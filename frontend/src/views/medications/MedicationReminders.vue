<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Rappels de médicaments</h1>
          <p class="mt-2 text-sm text-gray-600">
            Gérez vos rappels de prise de médicaments et suivez votre adhérence
          </p>
        </div>
        <Button @click="showAddModal = true">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nouveau rappel
        </Button>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Reminders List -->
      <div v-else class="space-y-4">
        <Card v-for="reminder in reminders" :key="reminder.id" class="hover:shadow-md transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                  </div>
                </div>

                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900">{{ reminder.medication_name }}</h3>
                  <p class="text-sm text-gray-600 mt-1">{{ reminder.dosage }}</p>

                  <div class="mt-3 flex flex-wrap gap-2">
                    <span
                      v-for="time in reminder.reminder_times"
                      :key="time"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ time }}
                    </span>
                  </div>

                  <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
                    <span>Début: {{ formatDate(reminder.start_date) }}</span>
                    <span v-if="reminder.end_date">Fin: {{ formatDate(reminder.end_date) }}</span>
                    <span v-else class="text-teal-600">En cours</span>
                  </div>

                  <p v-if="reminder.notes" class="mt-2 text-sm text-gray-600 italic">{{ reminder.notes }}</p>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2 ml-4">
              <Button
                variant="outline"
                size="sm"
                @click="recordIntake(reminder.id)"
              >
                Prendre
              </Button>
              <Button
                variant="outline"
                size="sm"
                @click="viewStats(reminder)"
              >
                Stats
              </Button>
              <button
                @click="deleteReminder(reminder.id)"
                class="p-2 text-red-600 hover:bg-red-50 rounded-md"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </Card>

        <!-- Empty State -->
        <Card v-if="reminders.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rappel configuré</h3>
          <p class="text-gray-600 mb-4">Créez votre premier rappel de médicament</p>
          <Button @click="showAddModal = true">Créer un rappel</Button>
        </Card>
      </div>

      <!-- Add/Edit Reminder Modal -->
      <Modal v-model:show="showAddModal" title="Nouveau rappel de médicament">
        <form @submit.prevent="saveReminder" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nom du médicament <span class="text-red-500">*</span>
            </label>
            <input
              v-model="newReminder.medication_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              placeholder="Ex: Paracétamol"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Dosage <span class="text-red-500">*</span>
            </label>
            <input
              v-model="newReminder.dosage"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              placeholder="Ex: 500mg"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fréquence <span class="text-red-500">*</span>
            </label>
            <select
              v-model="newReminder.frequency"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              @change="updateReminderTimes"
            >
              <option value="">Sélectionner</option>
              <option value="once_daily">Une fois par jour</option>
              <option value="twice_daily">Deux fois par jour</option>
              <option value="three_times_daily">Trois fois par jour</option>
              <option value="four_times_daily">Quatre fois par jour</option>
              <option value="custom">Personnalisé</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Heures de prise <span class="text-red-500">*</span>
            </label>
            <div class="space-y-2">
              <div v-for="(time, index) in newReminder.reminder_times" :key="index" class="flex gap-2">
                <input
                  v-model="newReminder.reminder_times[index]"
                  type="time"
                  required
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                />
                <button
                  v-if="newReminder.frequency === 'custom'"
                  type="button"
                  @click="newReminder.reminder_times.splice(index, 1)"
                  class="px-3 py-2 text-red-600 hover:text-red-700"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <button
                v-if="newReminder.frequency === 'custom'"
                type="button"
                @click="newReminder.reminder_times.push('')"
                class="text-sm text-teal-600 hover:text-teal-700"
              >
                + Ajouter une heure
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Date de début <span class="text-red-500">*</span>
              </label>
              <input
                v-model="newReminder.start_date"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Durée (jours)
              </label>
              <input
                v-model.number="newReminder.duration_days"
                type="number"
                min="1"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                placeholder="Ex: 7"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Notes
            </label>
            <textarea
              v-model="newReminder.notes"
              rows="3"
              maxlength="500"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
              placeholder="Instructions supplémentaires..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <Button
              type="button"
              variant="outline"
              @click="showAddModal = false"
            >
              Annuler
            </Button>
            <Button type="submit" :disabled="saving">
              {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
            </Button>
          </div>
        </form>
      </Modal>

      <!-- Stats Modal -->
      <Modal v-model:show="showStatsModal" title="Statistiques d'adhérence">
        <div v-if="selectedReminder" class="space-y-6">
          <div class="text-center">
            <div class="text-5xl font-bold text-teal-600 mb-2">
              {{ stats.adherence_rate }}%
            </div>
            <p class="text-gray-600">Taux d'adhérence</p>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-semibold text-gray-900">{{ stats.total_recorded }}</div>
              <p class="text-sm text-gray-600 mt-1">Prises enregistrées</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-semibold text-gray-900">{{ stats.last_7_days }}</div>
              <p class="text-sm text-gray-600 mt-1">7 derniers jours</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-semibold text-gray-900">{{ stats.last_30_days }}</div>
              <p class="text-sm text-gray-600 mt-1">30 derniers jours</p>
            </div>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1">
                <p class="text-sm text-blue-900">
                  {{ stats.total_expected }} prises attendues au total
                </p>
              </div>
            </div>
          </div>
        </div>
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
const saving = ref(false)
const showAddModal = ref(false)
const showStatsModal = ref(false)
const reminders = ref<any[]>([])
const selectedReminder = ref<any>(null)
const stats = ref({
  adherence_rate: 0,
  total_expected: 0,
  total_recorded: 0,
  last_7_days: 0,
  last_30_days: 0,
})

const newReminder = ref({
  medication_name: '',
  dosage: '',
  frequency: '',
  reminder_times: ['08:00'],
  start_date: new Date().toISOString().split('T')[0],
  duration_days: null as number | null,
  notes: '',
})

onMounted(async () => {
  await loadReminders()
})

async function loadReminders() {
  try {
    loading.value = true
    const response = await api.get('/medication-reminders')
    reminders.value = response.data.reminders
  } catch (error: any) {
    console.error('Error loading reminders:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

function updateReminderTimes() {
  const frequency = newReminder.value.frequency
  switch (frequency) {
    case 'once_daily':
      newReminder.value.reminder_times = ['08:00']
      break
    case 'twice_daily':
      newReminder.value.reminder_times = ['08:00', '20:00']
      break
    case 'three_times_daily':
      newReminder.value.reminder_times = ['08:00', '14:00', '20:00']
      break
    case 'four_times_daily':
      newReminder.value.reminder_times = ['08:00', '12:00', '17:00', '21:00']
      break
    case 'custom':
      newReminder.value.reminder_times = ['08:00']
      break
  }
}

async function saveReminder() {
  try {
    saving.value = true
    await api.post('/medication-reminders', newReminder.value)
    toast.success('Rappel créé avec succès')
    showAddModal.value = false
    resetForm()
    await loadReminders()
  } catch (error: any) {
    console.error('Error saving reminder:', error)
    toast.error(error.response?.data?.message || 'Erreur lors de l\'enregistrement')
  } finally {
    saving.value = false
  }
}

async function recordIntake(reminderId: number) {
  try {
    await api.post('/medication-reminders/intake/record', {
      reminder_id: reminderId,
      taken_at: new Date().toISOString(),
      skipped: false,
    })
    toast.success('Prise enregistrée')
  } catch (error: any) {
    console.error('Error recording intake:', error)
    toast.error('Erreur lors de l\'enregistrement')
  }
}

async function viewStats(reminder: any) {
  try {
    selectedReminder.value = reminder
    const response = await api.get(`/medication-reminders/${reminder.id}/adherence-stats`)
    stats.value = response.data
    showStatsModal.value = true
  } catch (error: any) {
    console.error('Error loading stats:', error)
    toast.error('Erreur lors du chargement des statistiques')
  }
}

async function deleteReminder(id: number) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer ce rappel ?')) return

  try {
    await api.delete(`/medication-reminders/${id}`)
    toast.success('Rappel supprimé')
    await loadReminders()
  } catch (error: any) {
    console.error('Error deleting reminder:', error)
    toast.error('Erreur lors de la suppression')
  }
}

function resetForm() {
  newReminder.value = {
    medication_name: '',
    dosage: '',
    frequency: '',
    reminder_times: ['08:00'],
    start_date: new Date().toISOString().split('T')[0],
    duration_days: null,
    notes: '',
  }
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('fr-FR')
}
</script>
