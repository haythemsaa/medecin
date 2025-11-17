<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Intégration calendrier</h1>
        <p class="mt-2 text-sm text-gray-600">
          Synchronisez vos rendez-vous avec Google Calendar ou Outlook
        </p>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" size="lg" text="Chargement..." />

      <!-- Calendar Integrations -->
      <div v-else class="space-y-6">
        <!-- Auto-sync Toggle -->
        <Card>
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Synchronisation automatique</h3>
              <p class="text-sm text-gray-600 mt-1">
                Synchroniser automatiquement tous les nouveaux rendez-vous avec vos calendriers
              </p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                v-model="autoSyncEnabled"
                @change="toggleAutoSync"
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
            </label>
          </div>
        </Card>

        <!-- Google Calendar -->
        <Card>
          <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                  <svg class="w-8 h-8" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                  </svg>
                </div>
              </div>

              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Google Calendar</h3>
                <p class="text-sm text-gray-600 mt-1">
                  Synchronisez vos rendez-vous avec Google Calendar
                </p>

                <div v-if="googleConnected" class="mt-3 flex items-center text-sm text-green-600">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Connecté
                </div>
                <div v-else class="mt-3 flex items-center text-sm text-gray-500">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Non connecté
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2 ml-4">
              <Button
                v-if="!googleConnected"
                @click="connectGoogle"
                :disabled="connecting"
              >
                Connecter
              </Button>
              <Button
                v-else
                variant="outline"
                @click="disconnectGoogle"
                :disabled="connecting"
              >
                Déconnecter
              </Button>
            </div>
          </div>
        </Card>

        <!-- Outlook Calendar -->
        <Card>
          <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                  <svg class="w-8 h-8" viewBox="0 0 24 24">
                    <path fill="#0078D4" d="M22 3H9a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm0 16H9V5h13v14zM2 7h4v2H2zm0 4h4v2H2zm0 4h4v2H2z"/>
                  </svg>
                </div>
              </div>

              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Outlook Calendar</h3>
                <p class="text-sm text-gray-600 mt-1">
                  Synchronisez vos rendez-vous avec Outlook Calendar
                </p>

                <div v-if="outlookConnected" class="mt-3 flex items-center text-sm text-green-600">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Connecté
                </div>
                <div v-else class="mt-3 flex items-center text-sm text-gray-500">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Non connecté
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2 ml-4">
              <Button
                v-if="!outlookConnected"
                @click="connectOutlook"
                :disabled="connecting"
              >
                Connecter
              </Button>
              <Button
                v-else
                variant="outline"
                @click="disconnectOutlook"
                :disabled="connecting"
              >
                Déconnecter
              </Button>
            </div>
          </div>
        </Card>

        <!-- Info Card -->
        <Card class="bg-blue-50 border-blue-200">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-900">À propos de la synchronisation</h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Les rendez-vous seront automatiquement ajoutés à votre calendrier</li>
                  <li>Les annulations seront également synchronisées</li>
                  <li>Vos données de calendrier restent privées et sécurisées</li>
                  <li>Vous pouvez vous déconnecter à tout moment</li>
                </ul>
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
const connecting = ref(false)
const googleConnected = ref(false)
const outlookConnected = ref(false)
const autoSyncEnabled = ref(false)

onMounted(async () => {
  await loadStatus()
  handleOAuthCallback()
})

async function loadStatus() {
  try {
    loading.value = true
    const response = await api.get('/calendar/status')
    googleConnected.value = response.data.google_connected
    outlookConnected.value = response.data.outlook_connected
    autoSyncEnabled.value = response.data.auto_sync_enabled
  } catch (error: any) {
    console.error('Error loading calendar status:', error)
    toast.error('Erreur lors du chargement')
  } finally {
    loading.value = false
  }
}

async function connectGoogle() {
  try {
    connecting.value = true
    const response = await api.get('/calendar/google/auth-url')
    window.location.href = response.data.auth_url
  } catch (error: any) {
    console.error('Error connecting Google:', error)
    toast.error('Erreur lors de la connexion')
    connecting.value = false
  }
}

async function connectOutlook() {
  try {
    connecting.value = true
    const response = await api.get('/calendar/outlook/auth-url')
    window.location.href = response.data.auth_url
  } catch (error: any) {
    console.error('Error connecting Outlook:', error)
    toast.error('Erreur lors de la connexion')
    connecting.value = false
  }
}

async function disconnectGoogle() {
  if (!confirm('Êtes-vous sûr de vouloir déconnecter Google Calendar ?')) return

  try {
    connecting.value = true
    await api.post('/calendar/google/disconnect')
    googleConnected.value = false
    toast.success('Google Calendar déconnecté')
  } catch (error: any) {
    console.error('Error disconnecting Google:', error)
    toast.error('Erreur lors de la déconnexion')
  } finally {
    connecting.value = false
  }
}

async function disconnectOutlook() {
  if (!confirm('Êtes-vous sûr de vouloir déconnecter Outlook Calendar ?')) return

  try {
    connecting.value = true
    await api.post('/calendar/outlook/disconnect')
    outlookConnected.value = false
    toast.success('Outlook Calendar déconnecté')
  } catch (error: any) {
    console.error('Error disconnecting Outlook:', error)
    toast.error('Erreur lors de la déconnexion')
  } finally {
    connecting.value = false
  }
}

async function toggleAutoSync() {
  try {
    await api.post('/calendar/auto-sync', {
      enabled: autoSyncEnabled.value
    })
    toast.success('Paramètre mis à jour')
  } catch (error: any) {
    console.error('Error toggling auto-sync:', error)
    toast.error('Erreur lors de la mise à jour')
    // Revert the toggle
    autoSyncEnabled.value = !autoSyncEnabled.value
  }
}

function handleOAuthCallback() {
  const urlParams = new URLSearchParams(window.location.search)
  const code = urlParams.get('code')
  const provider = urlParams.get('provider')

  if (code && provider) {
    if (provider === 'google') {
      handleGoogleCallback(code)
    } else if (provider === 'outlook') {
      handleOutlookCallback(code)
    }
  }
}

async function handleGoogleCallback(code: string) {
  try {
    await api.post('/calendar/google/callback', { code })
    toast.success('Google Calendar connecté avec succès')
    await loadStatus()
    // Clean URL
    window.history.replaceState({}, document.title, window.location.pathname)
  } catch (error: any) {
    console.error('Error handling Google callback:', error)
    toast.error('Erreur lors de la connexion')
  }
}

async function handleOutlookCallback(code: string) {
  try {
    await api.post('/calendar/outlook/callback', { code })
    toast.success('Outlook Calendar connecté avec succès')
    await loadStatus()
    // Clean URL
    window.history.replaceState({}, document.title, window.location.pathname)
  } catch (error: any) {
    console.error('Error handling Outlook callback:', error)
    toast.error('Erreur lors de la connexion')
  }
}
</script>
