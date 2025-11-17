<template>
  <nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo and Main Nav -->
        <div class="flex">
          <!-- Logo -->
          <router-link to="/" class="flex items-center">
            <span class="text-2xl font-bold text-teal-600">Seha Digital</span>
          </router-link>

          <!-- Main Navigation -->
          <div class="hidden md:ml-8 md:flex md:space-x-4">
            <router-link
              to="/dashboard"
              class="nav-link"
            >
              🏠 Tableau de bord
            </router-link>

            <router-link
              v-if="authStore.isPatient"
              to="/medecins"
              class="nav-link"
            >
              👨‍⚕️ Médecins
            </router-link>

            <router-link
              to="/appointments"
              class="nav-link"
            >
              📅 Rendez-vous
            </router-link>

            <router-link
              v-if="authStore.isPatient"
              to="/prescriptions"
              class="nav-link"
            >
              📋 Ordonnances
            </router-link>

            <router-link
              v-if="authStore.isPatient"
              to="/medical-record"
              class="nav-link"
            >
              🏥 Dossier Médical
            </router-link>

            <router-link
              to="/messages"
              class="nav-link relative"
            >
              💬 Messages
              <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
              >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </router-link>

            <router-link
              v-if="authStore.isAdmin"
              to="/admin"
              class="nav-link"
            >
              ⚙️ Administration
            </router-link>
          </div>
        </div>

        <!-- Right Side Nav -->
        <div class="flex items-center">
          <!-- User Menu -->
          <div class="relative" v-if="authStore.isAuthenticated">
            <button
              @click="showUserMenu = !showUserMenu"
              class="flex items-center space-x-3 focus:outline-none"
            >
              <div class="flex items-center space-x-2">
                <div
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold',
                    getAvatarColor(authStore.user?.first_name || '')
                  ]"
                >
                  {{ getInitials(authStore.user?.first_name || '', authStore.user?.last_name || '') }}
                </div>
                <div class="hidden md:block text-left">
                  <p class="text-sm font-medium text-gray-900">
                    {{ getFullName(authStore.user?.first_name || '', authStore.user?.last_name || '', getRoleTitle()) }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ getRoleLabel() }}
                  </p>
                </div>
                <svg
                  :class="['w-4 h-4 text-gray-600 transition-transform', showUserMenu && 'rotate-180']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200"
              @click.stop
            >
              <router-link
                :to="getProfileRoute()"
                @click="showUserMenu = false"
                class="dropdown-item"
              >
                <span class="mr-2">👤</span>
                Mon Profil
              </router-link>

              <router-link
                v-if="authStore.isPatient"
                to="/medical-record"
                @click="showUserMenu = false"
                class="dropdown-item"
              >
                <span class="mr-2">🏥</span>
                Mon Dossier Médical
              </router-link>

              <router-link
                to="/appointments"
                @click="showUserMenu = false"
                class="dropdown-item"
              >
                <span class="mr-2">📅</span>
                Mes Rendez-vous
              </router-link>

              <router-link
                v-if="authStore.isPatient"
                to="/prescriptions"
                @click="showUserMenu = false"
                class="dropdown-item"
              >
                <span class="mr-2">📋</span>
                Mes Ordonnances
              </router-link>

              <hr class="my-1 border-gray-200" />

              <button
                @click="handleLogout"
                class="dropdown-item text-red-600 hover:bg-red-50 w-full text-left"
              >
                <span class="mr-2">🚪</span>
                Déconnexion
              </button>
            </div>
          </div>

          <!-- Login Button (when not authenticated) -->
          <router-link
            v-else
            to="/login"
            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
          >
            Se Connecter
          </router-link>

          <!-- Mobile Menu Button -->
          <button
            @click="showMobileMenu = !showMobileMenu"
            class="md:hidden ml-4 text-gray-600 hover:text-gray-900"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                v-if="!showMobileMenu"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="showMobileMenu" class="md:hidden py-4 space-y-2">
        <router-link
          to="/dashboard"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          🏠 Tableau de bord
        </router-link>

        <router-link
          v-if="authStore.isPatient"
          to="/medecins"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          👨‍⚕️ Médecins
        </router-link>

        <router-link
          to="/appointments"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          📅 Rendez-vous
        </router-link>

        <router-link
          v-if="authStore.isPatient"
          to="/prescriptions"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          📋 Ordonnances
        </router-link>

        <router-link
          v-if="authStore.isPatient"
          to="/medical-record"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          🏥 Dossier Médical
        </router-link>

        <router-link
          to="/messages"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          💬 Messages
          <span v-if="unreadCount > 0" class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">
            {{ unreadCount }}
          </span>
        </router-link>

        <router-link
          v-if="authStore.isAdmin"
          to="/admin"
          @click="showMobileMenu = false"
          class="mobile-nav-link"
        >
          ⚙️ Administration
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import { getInitials, getFullName, getAvatarColor } from '@/utils'

const router = useRouter()
const authStore = useAuthStore()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)
const unreadCount = ref(0)

let unreadInterval: NodeJS.Timeout | null = null

function getRoleTitle(): string {
  if (authStore.isMedecin) return 'Dr.'
  return ''
}

function getRoleLabel(): string {
  const labels: Record<string, string> = {
    patient: 'Patient',
    medecin: 'Médecin',
    admin: 'Administrateur'
  }
  return labels[authStore.user?.role || ''] || ''
}

function getProfileRoute(): string {
  if (authStore.isPatient) return '/profile/patient'
  if (authStore.isMedecin) return '/profile/medecin'
  return '/profile'
}

async function fetchUnreadCount() {
  if (!authStore.isAuthenticated) return

  try {
    const response = await api.get('/messages/unread-count')
    unreadCount.value = response.data.count
  } catch (error) {
    console.error('Error fetching unread count:', error)
  }
}

function handleLogout() {
  authStore.logout()
  showUserMenu.value = false
  router.push('/login')
}

// Close dropdown when clicking outside
function handleClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    fetchUnreadCount()
    // Poll for unread messages every 30 seconds
    unreadInterval = setInterval(fetchUnreadCount, 30000)
  }

  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  if (unreadInterval) {
    clearInterval(unreadInterval)
  }
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.nav-link {
  @apply inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-teal-600 hover:bg-gray-50 rounded-md transition-colors;
}

.nav-link.router-link-active {
  @apply text-teal-600 bg-teal-50;
}

.dropdown-item {
  @apply block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors;
}

.mobile-nav-link {
  @apply block px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md transition-colors;
}

.mobile-nav-link.router-link-active {
  @apply text-teal-600 bg-teal-50;
}
</style>
