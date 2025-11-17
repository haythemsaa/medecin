<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Préférences de notification</h1>
        <p class="mt-2 text-sm text-gray-600">
          Gérez vos notifications par email, SMS et push
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Settings Form -->
      <div v-else class="space-y-6">
        <!-- Notification Channels -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Canaux de notification</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Email</p>
                <p class="text-sm text-gray-500">Recevoir des notifications par email</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="preferences.email_enabled"
                  class="sr-only peer"
                  @change="savePreferences"
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
              </label>
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">SMS</p>
                <p class="text-sm text-gray-500">Recevoir des notifications par SMS</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="preferences.sms_enabled"
                  class="sr-only peer"
                  @change="savePreferences"
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
              </label>
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Notifications push</p>
                <p class="text-sm text-gray-500">Recevoir des notifications sur votre appareil</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="preferences.push_enabled"
                  class="sr-only peer"
                  @change="savePreferences"
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
              </label>
            </div>
          </div>
        </Card>

        <!-- Notification Types -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Types de notifications</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Rappels de rendez-vous</p>
                <p class="text-sm text-gray-500">Être rappelé avant vos rendez-vous</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.appointment_reminders"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Confirmations de rendez-vous</p>
                <p class="text-sm text-gray-500">Confirmation lors de la prise ou modification</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.appointment_confirmations"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Annulations de rendez-vous</p>
                <p class="text-sm text-gray-500">Être notifié en cas d'annulation</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.appointment_cancellations"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Nouveaux messages</p>
                <p class="text-sm text-gray-500">Notification pour les nouveaux messages</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.new_messages"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Prescriptions disponibles</p>
                <p class="text-sm text-gray-500">Notification quand une prescription est prête</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.prescription_ready"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Rappels d'avis</p>
                <p class="text-sm text-gray-500">Rappel pour laisser un avis après consultation</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.review_reminders"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Emails marketing</p>
                <p class="text-sm text-gray-500">Recevoir des offres et actualités</p>
              </div>
              <input
                type="checkbox"
                v-model="preferences.marketing_emails"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
            </div>
          </div>
        </Card>

        <!-- Reminder Timing -->
        <Card>
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Timing des rappels</h2>
          <p class="text-sm text-gray-600 mb-4">
            Choisissez quand recevoir les rappels avant vos rendez-vous
          </p>
          <div class="space-y-3">
            <label v-for="hours in [24, 12, 6, 3, 1]" :key="hours" class="flex items-center">
              <input
                type="checkbox"
                :value="hours"
                v-model="preferences.reminder_hours_before"
                @change="savePreferences"
                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
              <span class="ml-3 text-sm text-gray-700">
                {{ hours === 1 ? '1 heure' : `${hours} heures` }} avant
              </span>
            </label>
          </div>
        </Card>

        <!-- Test Notification -->
        <Card class="bg-teal-50 border-teal-200">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-sm font-medium text-teal-900 mb-2">Tester vos notifications</h3>
              <p class="text-sm text-teal-700 mb-4">
                Envoyez-vous une notification de test pour vérifier vos paramètres
              </p>
              <div class="flex space-x-2">
                <Button
                  variant="outline"
                  size="sm"
                  @click="testNotification('email')"
                  :disabled="!preferences.email_enabled || testing"
                >
                  Test Email
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  @click="testNotification('sms')"
                  :disabled="!preferences.sms_enabled || testing"
                >
                  Test SMS
                </Button>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Button from '@/components/Button.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const toast = useToast()
const loading = ref(true)
const testing = ref(false)

const preferences = ref({
  email_enabled: true,
  sms_enabled: false,
  push_enabled: true,
  appointment_reminders: true,
  appointment_confirmations: true,
  appointment_cancellations: true,
  new_messages: true,
  prescription_ready: true,
  review_reminders: true,
  marketing_emails: false,
  reminder_hours_before: [24, 1],
})

onMounted(async () => {
  await loadPreferences()
})

async function loadPreferences() {
  try {
    loading.value = true
    const response = await api.get('/notifications/preferences')
    preferences.value = response.data.preferences
  } catch (error: any) {
    console.error('Error loading preferences:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

async function savePreferences() {
  try {
    await api.put('/notifications/preferences', preferences.value)
    toast.success('Préférences enregistrées')
  } catch (error: any) {
    console.error('Error saving preferences:', error)
    toast.error('Erreur lors de l\'enregistrement')
  }
}

async function testNotification(channel: string) {
  try {
    testing.value = true
    await api.post('/notifications/test', { channel })
    toast.success(`Notification de test envoyée par ${channel}`)
  } catch (error: any) {
    console.error('Error sending test notification:', error)
    toast.error('Erreur lors de l\'envoi')
  } finally {
    testing.value = false
  }
}
</script>
