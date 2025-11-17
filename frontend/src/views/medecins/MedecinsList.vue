<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation (reuse from DashboardPage or create a shared component) -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <router-link to="/" class="flex items-center space-x-2">
              <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl">S</span>
              </div>
              <span class="text-xl font-bold text-primary-600">Seha Digital</span>
            </router-link>
          </div>
          <div class="flex items-center space-x-4">
            <router-link v-if="authStore.isAuthenticated" to="/dashboard" class="btn btn-secondary text-sm">
              Tableau de bord
            </router-link>
            <router-link v-else to="/login" class="btn btn-primary text-sm">
              Connexion
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Trouver un médecin</h1>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Sidebar -->
        <aside class="lg:col-span-1">
          <div class="card sticky top-4">
            <h2 class="text-lg font-semibold mb-4">Filtres</h2>

            <!-- Speciality Filter -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Spécialité</label>
              <select v-model="filters.speciality" class="input">
                <option value="">Toutes les spécialités</option>
                <option value="Médecine Générale">Médecine Générale</option>
                <option value="Cardiologie">Cardiologie</option>
                <option value="Dermatologie">Dermatologie</option>
                <option value="Pédiatrie">Pédiatrie</option>
                <option value="Gynécologie">Gynécologie</option>
                <option value="Ophtalmologie">Ophtalmologie</option>
                <option value="ORL">ORL</option>
                <option value="Psychiatrie">Psychiatrie</option>
              </select>
            </div>

            <!-- Governorate Filter -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Gouvernorat</label>
              <select v-model="filters.governorate" class="input">
                <option value="">Tous</option>
                <option value="Tunis">Tunis</option>
                <option value="Ariana">Ariana</option>
                <option value="Ben Arous">Ben Arous</option>
                <option value="Sfax">Sfax</option>
                <option value="Sousse">Sousse</option>
              </select>
            </div>

            <!-- Language Filter -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Langue</label>
              <select v-model="filters.language" class="input">
                <option value="">Toutes</option>
                <option value="fr">Français</option>
                <option value="ar">Arabe</option>
                <option value="en">Anglais</option>
              </select>
            </div>

            <!-- Price Range -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Prix max: {{ filters.max_price }} TND
              </label>
              <input
                v-model.number="filters.max_price"
                type="range"
                min="0"
                max="200"
                step="10"
                class="w-full"
              />
            </div>

            <!-- Rating Filter -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Note minimale</label>
              <div class="flex items-center space-x-2">
                <button
                  v-for="rating in [3, 3.5, 4, 4.5]"
                  :key="rating"
                  @click="filters.min_rating = rating"
                  :class="[
                    'px-3 py-1 rounded-lg text-sm',
                    filters.min_rating === rating
                      ? 'bg-primary-600 text-white'
                      : 'bg-gray-200 text-gray-700'
                  ]"
                >
                  {{ rating }}+
                </button>
              </div>
            </div>

            <button @click="applyFilters" class="btn btn-primary w-full">
              Appliquer les filtres
            </button>
            <button @click="resetFilters" class="btn btn-secondary w-full mt-2">
              Réinitialiser
            </button>
          </div>
        </aside>

        <!-- Medecins List -->
        <div class="lg:col-span-3">
          <!-- Sort Options -->
          <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-600">
              {{ medecins.length }} médecin(s) trouvé(s)
            </p>
            <select v-model="sortBy" @change="applyFilters" class="input w-auto">
              <option value="rating_average">Mieux notés</option>
              <option value="consultation_price">Prix croissant</option>
              <option value="-consultation_price">Prix décroissant</option>
              <option value="years_of_experience">Plus d'expérience</option>
            </select>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Chargement...</p>
          </div>

          <!-- Medecins Grid -->
          <div v-else-if="medecins.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="medecin in medecins"
              :key="medecin.id"
              class="card hover:shadow-lg transition-shadow cursor-pointer"
              @click="router.push(`/medecins/${medecin.id}`)"
            >
              <div class="flex items-start space-x-4">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <img
                    v-if="medecin.photo"
                    :src="medecin.photo"
                    :alt="medecin.full_name"
                    class="w-16 h-16 rounded-full object-cover"
                  />
                  <span v-else class="text-2xl">👨‍⚕️</span>
                </div>

                <div class="flex-1">
                  <h3 class="font-semibold text-lg">
                    Dr. {{ medecin.first_name }} {{ medecin.last_name }}
                  </h3>
                  <p class="text-sm text-primary-600">{{ medecin.speciality }}</p>
                  <p class="text-sm text-gray-600 mt-1">
                    {{ medecin.years_of_experience }} ans d'expérience
                  </p>

                  <div class="flex items-center mt-2 space-x-4">
                    <div class="flex items-center text-sm">
                      <span class="text-yellow-500 mr-1">⭐</span>
                      <span class="font-medium">{{ medecin.rating_average?.toFixed(1) || 'N/A' }}</span>
                      <span class="text-gray-500 ml-1">({{ medecin.rating_count }})</span>
                    </div>
                    <div class="text-sm font-semibold text-primary-600">
                      {{ medecin.consultation_price }} TND
                    </div>
                  </div>

                  <div v-if="medecin.consultation_languages" class="flex items-center mt-2 space-x-1">
                    <span
                      v-for="lang in medecin.consultation_languages"
                      :key="lang"
                      class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded"
                    >
                      {{ lang.toUpperCase() }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="mt-4 pt-4 border-t border-gray-200">
                <button
                  @click.stop="router.push(`/medecins/${medecin.id}`)"
                  class="btn btn-primary w-full"
                >
                  Voir le profil et réserver
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="card text-center py-12">
            <span class="text-6xl mb-4 block">🔍</span>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun médecin trouvé</h3>
            <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche</p>
            <button @click="resetFilters" class="btn btn-primary">
              Réinitialiser les filtres
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import type { Medecin } from '@/types'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const medecins = ref<Medecin[]>([])

const filters = ref({
  speciality: '',
  governorate: '',
  language: '',
  min_rating: 0,
  max_price: 200
})

const sortBy = ref('rating_average')

async function applyFilters() {
  loading.value = true
  try {
    const params = {
      ...(filters.value.speciality && { speciality: filters.value.speciality }),
      ...(filters.value.governorate && { governorate: filters.value.governorate }),
      ...(filters.value.language && { language: filters.value.language }),
      ...(filters.value.min_rating && { min_rating: filters.value.min_rating }),
      ...(filters.value.max_price && { max_price: filters.value.max_price }),
      sort_by: sortBy.value,
      sort_order: sortBy.value.startsWith('-') ? 'desc' : 'asc'
    }

    const response = await api.get('/medecins/search', { params })
    medecins.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching medecins:', error)
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  filters.value = {
    speciality: '',
    governorate: '',
    language: '',
    min_rating: 0,
    max_price: 200
  }
  sortBy.value = 'rating_average'
  applyFilters()
}

onMounted(() => {
  applyFilters()
})
</script>
