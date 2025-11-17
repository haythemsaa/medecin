<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-2xl font-bold text-gray-900">Trouver un médecin près de chez vous</h1>
        </div>

        <!-- Search Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Ville
            </label>
            <input
              v-model="filters.city"
              type="text"
              placeholder="Ex: Tunis"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Spécialité
            </label>
            <select
              v-model="filters.specialty"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
            >
              <option value="">Toutes</option>
              <option value="Médecine générale">Médecine générale</option>
              <option value="Cardiologie">Cardiologie</option>
              <option value="Pédiatrie">Pédiatrie</option>
              <option value="Dermatologie">Dermatologie</option>
              <option value="Psychiatrie">Psychiatrie</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Rayon (km)
            </label>
            <select
              v-model.number="filters.radius"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
            >
              <option :value="5">5 km</option>
              <option :value="10">10 km</option>
              <option :value="20">20 km</option>
              <option :value="50">50 km</option>
            </select>
          </div>

          <div class="flex items-end gap-2">
            <Button @click="getCurrentLocation" :disabled="loading" class="flex-1">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Ma position
            </Button>
            <Button @click="search" :disabled="loading">
              Rechercher
            </Button>
          </div>
        </div>
      </div>
    </div>

    <div class="flex h-[calc(100vh-200px)]">
      <!-- Results Sidebar -->
      <div class="w-1/3 bg-white border-r border-gray-200 overflow-y-auto">
        <div class="p-4">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            {{ doctors.length }} médecin(s) trouvé(s)
          </h2>

          <!-- Loading State -->
          <div v-if="loading" class="flex justify-center py-8">
            <LoadingSpinner size="md" text="Recherche en cours..." />
          </div>

          <!-- Doctors List -->
          <div v-else-if="doctors.length > 0" class="space-y-4">
            <div
              v-for="doctor in doctors"
              :key="doctor.id"
              @click="selectDoctor(doctor)"
              :class="[
                'p-4 rounded-lg border-2 transition-all cursor-pointer',
                selectedDoctor?.id === doctor.id
                  ? 'border-teal-500 bg-teal-50'
                  : 'border-gray-200 hover:border-teal-300'
              ]"
            >
              <div class="flex items-start gap-3">
                <img
                  :src="doctor.user.photo || '/default-doctor.png'"
                  :alt="`Dr. ${doctor.user.nom}`"
                  class="w-12 h-12 rounded-full object-cover"
                />

                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-gray-900 truncate">
                    Dr. {{ doctor.user.prenom }} {{ doctor.user.nom }}
                  </h3>
                  <p class="text-sm text-gray-600">{{ doctor.specialite }}</p>

                  <div class="mt-2 flex items-center gap-3 text-sm">
                    <div v-if="doctor.distance" class="flex items-center text-gray-600">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      </svg>
                      {{ doctor.distance }} km
                    </div>

                    <div v-if="doctor.rating > 0" class="flex items-center text-gray-600">
                      <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      {{ doctor.rating.toFixed(1) }}
                    </div>
                  </div>

                  <p class="text-sm text-gray-600 mt-2">{{ doctor.tarif }} TND</p>

                  <div v-if="doctor.accepts_urgent" class="mt-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      Consultation urgente
                    </span>
                  </div>
                </div>
              </div>

              <div class="mt-3 flex gap-2">
                <Button
                  size="sm"
                  class="flex-1"
                  @click.stop="bookAppointment(doctor)"
                >
                  Prendre RDV
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  @click.stop="viewDetails(doctor)"
                >
                  Détails
                </Button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun médecin trouvé</h3>
            <p class="text-gray-600 mb-4">Essayez d'élargir votre zone de recherche</p>
          </div>
        </div>
      </div>

      <!-- Map -->
      <div class="flex-1 relative">
        <div id="map" class="w-full h-full"></div>

        <!-- Map placeholder (replace with actual map integration) -->
        <div class="absolute inset-0 bg-gray-100 flex items-center justify-center">
          <div class="text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <p class="text-lg text-gray-600 mb-2">Carte interactive</p>
            <p class="text-sm text-gray-500">
              Intégration Leaflet/OpenStreetMap ou Google Maps<br/>
              Les marqueurs afficheront les médecins sur la carte
            </p>

            <!-- Map mockup markers -->
            <div v-if="doctors.length > 0" class="mt-6 text-sm text-gray-600">
              <p>📍 {{ doctors.length }} médecin(s) seraient affichés sur la carte</p>
              <p class="mt-2 text-xs">Centre: {{ mapCenter.latitude }}, {{ mapCenter.longitude }}</p>
              <p class="text-xs">Rayon: {{ filters.radius }} km</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Button from '@/components/Button.vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const router = useRouter()
const toast = useToast()

const loading = ref(false)
const doctors = ref<any[]>([])
const selectedDoctor = ref<any>(null)

const mapCenter = ref({
  latitude: 36.8065, // Tunis center
  longitude: 10.1815
})

const filters = ref({
  city: '',
  specialty: '',
  radius: 10,
})

const currentPosition = ref<GeolocationPosition | null>(null)

onMounted(() => {
  // Initialize map here (Leaflet, Google Maps, etc.)
  // For now, we'll use a placeholder
})

function getCurrentLocation() {
  if (!navigator.geolocation) {
    toast.error('La géolocalisation n\'est pas supportée par votre navigateur')
    return
  }

  loading.value = true
  navigator.geolocation.getCurrentPosition(
    (position) => {
      currentPosition.value = position
      mapCenter.value = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude
      }
      searchNearby()
    },
    (error) => {
      console.error('Geolocation error:', error)
      toast.error('Impossible d\'obtenir votre position')
      loading.value = false
    }
  )
}

async function search() {
  if (filters.value.city) {
    await searchByCity()
  } else if (currentPosition.value) {
    await searchNearby()
  } else {
    toast.warning('Veuillez entrer une ville ou utiliser votre position actuelle')
  }
}

async function searchNearby() {
  if (!currentPosition.value) {
    toast.error('Position non disponible')
    return
  }

  try {
    loading.value = true
    const params: any = {
      latitude: currentPosition.value.coords.latitude,
      longitude: currentPosition.value.coords.longitude,
      radius: filters.value.radius,
    }

    if (filters.value.specialty) {
      params.specialty = filters.value.specialty
    }

    const response = await api.get('/geolocation/nearby', { params })
    doctors.value = response.data.doctors
    mapCenter.value = response.data.center
  } catch (error: any) {
    console.error('Error searching nearby:', error)
    toast.error('Erreur lors de la recherche')
  } finally {
    loading.value = false
  }
}

async function searchByCity() {
  try {
    loading.value = true
    const params: any = {
      ville: filters.value.city,
    }

    if (filters.value.specialty) {
      params.specialty = filters.value.specialty
    }

    const response = await api.get('/geolocation/by-city', { params })
    doctors.value = response.data.doctors

    // Update map center to first doctor or use geocoding
    if (doctors.value.length > 0 && doctors.value[0].latitude) {
      mapCenter.value = {
        latitude: doctors.value[0].latitude,
        longitude: doctors.value[0].longitude
      }
    }
  } catch (error: any) {
    console.error('Error searching by city:', error)
    toast.error('Erreur lors de la recherche')
  } finally {
    loading.value = false
  }
}

function selectDoctor(doctor: any) {
  selectedDoctor.value = doctor
  // Center map on selected doctor
  if (doctor.latitude && doctor.longitude) {
    mapCenter.value = {
      latitude: doctor.latitude,
      longitude: doctor.longitude
    }
  }
}

function bookAppointment(doctor: any) {
  router.push(`/medecins/${doctor.id}/book`)
}

function viewDetails(doctor: any) {
  router.push(`/medecins/${doctor.id}`)
}
</script>
