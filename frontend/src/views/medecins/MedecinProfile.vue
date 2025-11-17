<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
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
            <button @click="router.back()" class="text-gray-600 hover:text-gray-900">
              ← Retour
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Chargement...</p>
    </div>

    <!-- Main Content -->
    <main v-else-if="medecin" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Doctor Info -->
        <div class="lg:col-span-2">
          <!-- Profile Header -->
          <div class="card mb-6">
            <div class="flex items-start space-x-6">
              <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                <img
                  v-if="medecin.photo"
                  :src="medecin.photo"
                  :alt="medecin.full_name"
                  class="w-24 h-24 rounded-full object-cover"
                />
                <span v-else class="text-4xl">👨‍⚕️</span>
              </div>

              <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900">
                  Dr. {{ medecin.first_name }} {{ medecin.last_name }}
                </h1>
                <p class="text-lg text-primary-600 mt-1">{{ medecin.speciality }}</p>

                <div class="flex items-center mt-3 space-x-4">
                  <div class="flex items-center">
                    <span class="text-yellow-500 text-lg mr-1">⭐</span>
                    <span class="font-semibold">{{ medecin.rating_average?.toFixed(1) || 'N/A' }}</span>
                    <span class="text-gray-500 ml-1">({{ medecin.rating_count }} avis)</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <span class="mr-1">💼</span>
                    <span>{{ medecin.years_of_experience }} ans d'expérience</span>
                  </div>
                  <div class="flex items-center text-gray-600">
                    <span class="mr-1">✅</span>
                    <span>{{ medecin.consultation_count }} consultations</span>
                  </div>
                </div>

                <div class="flex items-center mt-3 space-x-2">
                  <span
                    v-for="lang in medecin.consultation_languages"
                    :key="lang"
                    class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg"
                  >
                    {{ lang.toUpperCase() }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Biography -->
          <div v-if="medecin.bio" class="card mb-6">
            <h2 class="text-xl font-semibold mb-3">À propos</h2>
            <p class="text-gray-700 leading-relaxed">{{ medecin.bio }}</p>
          </div>

          <!-- Sub-specialities -->
          <div v-if="medecin.sub_specialities && medecin.sub_specialities.length" class="card mb-6">
            <h2 class="text-xl font-semibold mb-3">Sous-spécialités</h2>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="sub in medecin.sub_specialities"
                :key="sub"
                class="px-3 py-2 bg-primary-50 text-primary-700 text-sm rounded-lg"
              >
                {{ sub }}
              </span>
            </div>
          </div>

          <!-- Reviews -->
          <div v-if="reviews.length > 0" class="card">
            <h2 class="text-xl font-semibold mb-4">Avis patients ({{ medecin.rating_count }})</h2>

            <div class="space-y-4">
              <div
                v-for="review in reviews"
                :key="review.id"
                class="pb-4 border-b border-gray-200 last:border-0"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center">
                    <div class="flex items-center text-yellow-500">
                      <span v-for="i in 5" :key="i">
                        {{ i <= review.overall_rating ? '⭐' : '☆' }}
                      </span>
                    </div>
                    <span class="ml-2 text-sm text-gray-600">
                      {{ formatDate(review.created_at) }}
                    </span>
                  </div>
                  <span v-if="review.is_verified" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                    ✓ Vérifié
                  </span>
                </div>

                <p v-if="review.comment" class="text-gray-700 mb-2">{{ review.comment }}</p>

                <div class="grid grid-cols-5 gap-2 text-xs text-gray-600">
                  <div>Qualité: {{ review.quality_rating }}/5</div>
                  <div>Écoute: {{ review.listening_rating }}/5</div>
                  <div>Clarté: {{ review.clarity_rating }}/5</div>
                  <div>Ponctualité: {{ review.punctuality_rating }}/5</div>
                  <div>Rapport Q/P: {{ review.value_rating }}/5</div>
                </div>

                <!-- Doctor Response -->
                <div v-if="review.medecin_response" class="mt-3 pl-4 border-l-2 border-primary-200 bg-primary-50 p-3 rounded">
                  <p class="text-sm text-gray-700">
                    <span class="font-semibold">Réponse du médecin:</span>
                    {{ review.medecin_response }}
                  </p>
                </div>
              </div>
            </div>

            <button v-if="reviews.length < medecin.rating_count" class="btn btn-secondary w-full mt-4">
              Voir plus d'avis
            </button>
          </div>
        </div>

        <!-- Right Column - Booking Card -->
        <div class="lg:col-span-1">
          <div class="card sticky top-4">
            <h3 class="text-lg font-semibold mb-4">Réserver une consultation</h3>

            <!-- Pricing -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600">Consultation standard</span>
                <span class="text-2xl font-bold text-gray-900">{{ medecin.consultation_price }} TND</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600 text-sm">Consultation urgente</span>
                <span class="text-lg font-semibold text-gray-700">{{ medecin.urgent_consultation_price }} TND</span>
              </div>
            </div>

            <!-- Consultation Type -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Type de consultation</label>
              <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                  <input v-model="bookingForm.type" type="radio" value="video" class="mr-3" />
                  <div class="flex-1">
                    <div class="font-medium">Vidéo</div>
                    <div class="text-sm text-gray-500">Consultation par visioconférence</div>
                  </div>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                  <input v-model="bookingForm.type" type="radio" value="phone" class="mr-3" />
                  <div class="flex-1">
                    <div class="font-medium">Téléphone</div>
                    <div class="text-sm text-gray-500">Consultation par appel téléphonique</div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Urgent Checkbox -->
            <div class="mb-4">
              <label class="flex items-center p-3 border border-orange-200 rounded-lg cursor-pointer hover:bg-orange-50">
                <input v-model="bookingForm.is_urgent" type="checkbox" class="mr-3" />
                <div class="flex-1">
                  <div class="font-medium text-orange-700">Consultation urgente</div>
                  <div class="text-sm text-gray-600">+ {{ (medecin.urgent_consultation_price! - medecin.consultation_price).toFixed(2) }} TND</div>
                </div>
              </label>
            </div>

            <!-- Next Available -->
            <div class="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex items-center text-sm text-green-800">
                <span class="mr-2">🟢</span>
                <span>Disponible aujourd'hui</span>
              </div>
            </div>

            <!-- CTA Button -->
            <button
              v-if="authStore.isAuthenticated"
              @click="proceedToBooking"
              class="btn btn-primary w-full text-lg py-3"
            >
              Choisir un créneau
            </button>
            <router-link
              v-else
              to="/login"
              class="btn btn-primary w-full text-center text-lg py-3"
            >
              Connexion pour réserver
            </router-link>

            <!-- Info -->
            <div class="mt-4 text-sm text-gray-600 space-y-2">
              <div class="flex items-center">
                <span class="mr-2">✓</span>
                <span>Annulation gratuite jusqu'à 24h avant</span>
              </div>
              <div class="flex items-center">
                <span class="mr-2">✓</span>
                <span>Ordonnance numérique immédiate</span>
              </div>
              <div class="flex items-center">
                <span class="mr-2">✓</span>
                <span>Paiement sécurisé</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import type { Medecin } from '@/types'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const medecin = ref<Medecin | null>(null)
const reviews = ref<any[]>([])

const bookingForm = ref({
  type: 'video',
  is_urgent: false
})

async function fetchMedecin() {
  try {
    const response = await api.get(`/medecins/${route.params.id}`)
    medecin.value = response.data
    // Mock reviews for now
    reviews.value = medecin.value?.reviews || []
  } catch (error) {
    console.error('Error fetching medecin:', error)
  } finally {
    loading.value = false
  }
}

function proceedToBooking() {
  router.push({
    name: 'BookAppointment',
    params: { medecinId: route.params.id },
    query: {
      type: bookingForm.value.type,
      is_urgent: bookingForm.value.is_urgent ? '1' : '0'
    }
  })
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('fr-FR', { dateStyle: 'medium' }).format(date)
}

onMounted(() => {
  fetchMedecin()
})
</script>
