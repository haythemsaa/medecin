<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Search Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900">Rechercher un médecin</h1>

        <!-- Main Search Bar with Autocomplete -->
        <div class="mt-4 relative">
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="handleSearchInput"
              @focus="showSuggestions = true"
              type="text"
              placeholder="Rechercher par nom, spécialité ou ville..."
              class="w-full px-4 py-3 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            />
            <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Autocomplete Suggestions -->
          <div
            v-if="showSuggestions && suggestions.length > 0"
            class="absolute z-10 w-full mt-2 bg-white rounded-lg shadow-lg max-h-96 overflow-auto"
          >
            <div
              v-for="(suggestion, index) in suggestions"
              :key="index"
              @click="selectSuggestion(suggestion)"
              class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-medium text-gray-900">{{ suggestion.label }}</p>
                  <p v-if="suggestion.subtitle" class="text-sm text-gray-500">{{ suggestion.subtitle }}</p>
                </div>
                <Badge :variant="suggestion.type === 'medecin' ? 'teal' : 'gray'">
                  {{ suggestion.type }}
                </Badge>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Filters -->
        <div class="mt-4 flex flex-wrap gap-2">
          <button
            @click="toggleFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filtres {{ Object.keys(activeFilters).length > 0 ? `(${Object.keys(activeFilters).length})` : '' }}
          </button>

          <Badge
            v-for="(value, key) in activeFilters"
            :key="key"
            variant="teal"
            class="cursor-pointer"
            @click="removeFilter(key)"
          >
            {{ getFilterLabel(key, value) }} ×
          </Badge>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Sidebar -->
        <div v-if="showFiltersPanel" class="lg:col-span-1">
          <Card class="sticky top-4">
            <div class="space-y-6">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Filtres</h2>
                <button
                  @click="clearAllFilters"
                  class="text-sm text-teal-600 hover:text-teal-700"
                >
                  Réinitialiser
                </button>
              </div>

              <!-- Specialité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Spécialité</label>
                <select
                  v-model="filters.specialite"
                  @change="applyFilters"
                  class="w-full border-gray-300 rounded-lg"
                >
                  <option value="">Toutes les spécialités</option>
                  <option v-for="spec in availableSpecialites" :key="spec.value" :value="spec.value">
                    {{ spec.label }}
                  </option>
                </select>
              </div>

              <!-- Localisation -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gouvernorat</label>
                <select
                  v-model="filters.governorate"
                  @change="applyFilters"
                  class="w-full border-gray-300 rounded-lg"
                >
                  <option value="">Tous les gouvernorats</option>
                  <option v-for="gov in availableGovernorates" :key="gov.value" :value="gov.value">
                    {{ gov.label }}
                  </option>
                </select>
              </div>

              <!-- Langues -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Langues</label>
                <div class="space-y-2">
                  <label v-for="lang in availableLanguages" :key="lang.value" class="flex items-center">
                    <input
                      type="checkbox"
                      :value="lang.value"
                      v-model="filters.languages"
                      @change="applyFilters"
                      class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                    />
                    <span class="ml-2 text-sm text-gray-700">{{ lang.label }}</span>
                  </label>
                </div>
              </div>

              <!-- Prix -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Prix ({{ filters.min_price || priceRange.min }} - {{ filters.max_price || priceRange.max }} TND)
                </label>
                <div class="space-y-3">
                  <input
                    v-model.number="filters.min_price"
                    @change="applyFilters"
                    type="range"
                    :min="priceRange.min"
                    :max="priceRange.max"
                    class="w-full"
                  />
                  <div class="grid grid-cols-2 gap-2">
                    <input
                      v-model.number="filters.min_price"
                      @change="applyFilters"
                      type="number"
                      placeholder="Min"
                      class="border-gray-300 rounded-lg text-sm"
                    />
                    <input
                      v-model.number="filters.max_price"
                      @change="applyFilters"
                      type="number"
                      placeholder="Max"
                      class="border-gray-300 rounded-lg text-sm"
                    />
                  </div>
                </div>
              </div>

              <!-- Note minimale -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Note minimale</label>
                <div class="flex items-center space-x-2">
                  <input
                    v-model.number="filters.min_rating"
                    @change="applyFilters"
                    type="number"
                    min="0"
                    max="5"
                    step="0.5"
                    class="w-20 border-gray-300 rounded-lg"
                  />
                  <span class="text-sm text-gray-500">⭐ et plus</span>
                </div>
              </div>

              <!-- Type de consultation -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de consultation</label>
                <select
                  v-model="filters.consultation_type"
                  @change="applyFilters"
                  class="w-full border-gray-300 rounded-lg"
                >
                  <option value="">Tous les types</option>
                  <option value="video">Consultation vidéo</option>
                  <option value="cabinet">Au cabinet</option>
                  <option value="both">Les deux</option>
                </select>
              </div>
            </div>
          </Card>
        </div>

        <!-- Results -->
        <div :class="showFiltersPanel ? 'lg:col-span-3' : 'lg:col-span-4'">
          <!-- Results Header -->
          <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-600">
              {{ pagination.total || 0 }} médecin(s) trouvé(s)
            </p>
            <select
              v-model="filters.sort_by"
              @change="applyFilters"
              class="border-gray-300 rounded-lg text-sm"
            >
              <option value="rating">Mieux notés</option>
              <option value="price">Prix croissant</option>
              <option value="experience">Plus d'expérience</option>
              <option value="availability">Plus disponibles</option>
            </select>
          </div>

          <!-- Loading State -->
          <LoadingSpinner v-if="loading" size="lg" text="Recherche en cours..." />

          <!-- Results Grid -->
          <div v-else-if="results.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <MedecinCard
              v-for="medecin in results"
              :key="medecin.id"
              :medecin="medecin"
              @click="$router.push(`/medecins/${medecin.id}`)"
            />
          </div>

          <!-- Empty State -->
          <EmptyState
            v-else
            icon="🔍"
            title="Aucun médecin trouvé"
            description="Essayez de modifier vos critères de recherche"
            action-text="Réinitialiser les filtres"
            @action="clearAllFilters"
          />

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="mt-8 flex justify-center">
            <nav class="inline-flex rounded-md shadow-sm">
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-4 py-2 border border-gray-300 rounded-l-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
              >
                Précédent
              </button>
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-4 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium',
                  page === pagination.current_page
                    ? 'bg-teal-600 text-white border-teal-600'
                    : 'text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="px-4 py-2 border border-gray-300 rounded-r-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
              >
                Suivant
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import EmptyState from '@/components/EmptyState.vue'
import MedecinCard from '@/components/MedecinCard.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const loading = ref(false)
const showFiltersPanel = ref(true)
const showSuggestions = ref(false)
const searchQuery = ref('')
const suggestions = ref<any[]>([])
const results = ref<any[]>([])
const pagination = ref({
  total: 0,
  per_page: 20,
  current_page: 1,
  last_page: 1,
})

const filters = ref({
  query: '',
  specialite: '',
  governorate: '',
  ville: '',
  languages: [] as string[],
  min_price: null as number | null,
  max_price: null as number | null,
  min_rating: null as number | null,
  consultation_type: '',
  sort_by: 'rating',
  sort_order: 'desc',
})

const availableSpecialites = ref<any[]>([])
const availableGovernorates = ref<any[]>([])
const availableLanguages = ref([
  { value: 'ar', label: 'Arabe' },
  { value: 'fr', label: 'Français' },
  { value: 'en', label: 'Anglais' },
])
const priceRange = ref({ min: 0, max: 200 })

const activeFilters = computed(() => {
  const active: Record<string, any> = {}
  if (filters.value.specialite) active.specialite = filters.value.specialite
  if (filters.value.governorate) active.governorate = filters.value.governorate
  if (filters.value.languages.length > 0) active.languages = filters.value.languages.join(', ')
  if (filters.value.min_rating) active.min_rating = filters.value.min_rating
  return active
})

const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const total = pagination.value.last_page
  const pages = []

  for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
    pages.push(i)
  }

  return pages
})

let autocompleteTimeout: any = null

onMounted(async () => {
  await loadFiltersMetadata()
  await performSearch()
})

async function loadFiltersMetadata() {
  try {
    const response = await api.get('/search/filters')
    availableSpecialites.value = response.data.specialites || []
    availableGovernorates.value = response.data.governorates || []
    priceRange.value = response.data.price_range || { min: 0, max: 200 }
  } catch (error) {
    console.error('Error loading filters:', error)
  }
}

function handleSearchInput() {
  clearTimeout(autocompleteTimeout)

  if (searchQuery.value.length < 2) {
    suggestions.value = []
    return
  }

  autocompleteTimeout = setTimeout(async () => {
    try {
      const response = await api.get('/search/autocomplete', {
        params: { query: searchQuery.value },
      })
      suggestions.value = response.data.suggestions || []
    } catch (error) {
      console.error('Autocomplete error:', error)
    }
  }, 300)
}

function selectSuggestion(suggestion: any) {
  if (suggestion.type === 'medecin') {
    router.push(`/medecins/${suggestion.id}`)
  } else if (suggestion.type === 'specialite') {
    filters.value.specialite = suggestion.value
    searchQuery.value = suggestion.label
    applyFilters()
  } else if (suggestion.type === 'location') {
    filters.value.ville = suggestion.value
    searchQuery.value = suggestion.label
    applyFilters()
  }

  showSuggestions.value = false
}

async function performSearch() {
  try {
    loading.value = true

    const params = {
      ...filters.value,
      query: searchQuery.value,
      page: pagination.value.current_page,
    }

    // Remove empty values
    Object.keys(params).forEach(key => {
      if (params[key] === '' || params[key] === null || (Array.isArray(params[key]) && params[key].length === 0)) {
        delete params[key]
      }
    })

    const response = await api.get('/search/medecins', { params })

    results.value = response.data.data || []
    pagination.value = response.data.pagination || pagination.value
  } catch (error: any) {
    console.error('Search error:', error)
    toast.error('Erreur lors de la recherche')
  } finally {
    loading.value = false
  }
}

function applyFilters() {
  pagination.value.current_page = 1
  performSearch()
}

function toggleFilters() {
  showFiltersPanel.value = !showFiltersPanel.value
}

function removeFilter(key: string) {
  if (key === 'languages') {
    filters.value.languages = []
  } else {
    ;(filters.value as any)[key] = key.includes('price') || key.includes('rating') ? null : ''
  }
  applyFilters()
}

function clearAllFilters() {
  filters.value = {
    query: '',
    specialite: '',
    governorate: '',
    ville: '',
    languages: [],
    min_price: null,
    max_price: null,
    min_rating: null,
    consultation_type: '',
    sort_by: 'rating',
    sort_order: 'desc',
  }
  searchQuery.value = ''
  applyFilters()
}

function goToPage(page: number) {
  if (page >= 1 && page <= pagination.value.last_page) {
    pagination.value.current_page = page
    performSearch()
  }
}

function getFilterLabel(key: string, value: any): string {
  const labels: Record<string, string> = {
    specialite: `Spécialité: ${value}`,
    governorate: `Gouvernorat: ${value}`,
    languages: `Langues: ${value}`,
    min_rating: `Note: ${value}⭐+`,
  }
  return labels[key] || `${key}: ${value}`
}
</script>
