<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="w-16 h-16 bg-primary-600 rounded-xl flex items-center justify-center">
          <span class="text-white font-bold text-3xl">S</span>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
        Connexion à Seha Digital
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/register/patient" class="font-medium text-primary-600 hover:text-primary-500">
          créer un compte patient
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-lg sm:rounded-xl sm:px-10">
        <!-- Error Alert -->
        <div v-if="authStore.error" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
          {{ authStore.error }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                type="email"
                autocomplete="email"
                required
                class="input"
                placeholder="votre@email.com"
              />
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="current-password"
                required
                class="input"
                placeholder="••••••••"
              />
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="form.remember"
                type="checkbox"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Se souvenir de moi
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-primary-600 hover:text-primary-500">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="authStore.loading"
              class="w-full btn btn-primary flex justify-center py-3"
            >
              <span v-if="!authStore.loading">Se connecter</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Connexion...
              </span>
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Nouveau sur Seha Digital ?</span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-2 gap-3">
            <router-link to="/register/patient" class="btn btn-secondary text-center">
              Inscription Patient
            </router-link>
            <router-link to="/register/medecin" class="btn btn-secondary text-center">
              Inscription Médecin
            </router-link>
          </div>
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
  email: '',
  password: '',
  remember: false
})

async function handleLogin() {
  try {
    const result = await authStore.login(form.value.email, form.value.password)

    if (result.requires_2fa) {
      router.push({ name: 'Verify2FA', params: { userId: result.user_id } })
    } else {
      router.push({ name: 'Dashboard' })
    }
  } catch (error) {
    console.error('Login error:', error)
  }
}
</script>
