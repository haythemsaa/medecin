<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des Disponibilités</h1>
        <p class="mt-2 text-gray-600">
          Configurez vos horaires de consultation pour la semaine
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar Section -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-xl font-semibold text-gray-900">Créneaux Disponibles</h2>
              <button
                @click="showAddSlotModal = true"
                class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
              >
                + Ajouter un créneau
              </button>
            </div>

            <!-- Days of Week -->
            <div class="space-y-4">
              <div
                v-for="day in daysOfWeek"
                :key="day.value"
                class="border border-gray-200 rounded-lg p-4"
              >
                <div class="flex items-center justify-between mb-3">
                  <h3 class="font-semibold text-gray-900">{{ day.label }}</h3>
                  <button
                    @click="addSlotForDay(day.value)"
                    class="text-sm text-teal-600 hover:text-teal-700"
                  >
                    + Ajouter
                  </button>
                </div>

                <div v-if="getSlotsByDay(day.value).length > 0" class="space-y-2">
                  <div
                    v-for="slot in getSlotsByDay(day.value)"
                    :key="slot.id"
                    class="flex items-center justify-between bg-gray-50 p-3 rounded-md"
                  >
                    <div class="flex items-center space-x-4">
                      <span class="text-sm font-medium text-gray-900">
                        {{ slot.start_time }} - {{ slot.end_time }}
                      </span>
                      <span
                        :class="[
                          'px-2 py-1 text-xs rounded-full',
                          slot.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                        ]"
                      >
                        {{ slot.is_active ? 'Actif' : 'Inactif' }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <button
                        @click="toggleSlot(slot)"
                        class="text-sm text-blue-600 hover:text-blue-700"
                      >
                        {{ slot.is_active ? 'Désactiver' : 'Activer' }}
                      </button>
                      <button
                        @click="deleteSlot(slot.id)"
                        class="text-sm text-red-600 hover:text-red-700"
                      >
                        Supprimer
                      </button>
                    </div>
                  </div>
                </div>

                <p v-else class="text-sm text-gray-500 italic">
                  Aucun créneau configuré pour ce jour
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Stats -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Créneaux actifs</span>
                <span class="text-lg font-bold text-teal-600">{{ activeSlots }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Heures/semaine</span>
                <span class="text-lg font-bold text-gray-900">{{ totalHoursPerWeek }}h</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Jours travaillés</span>
                <span class="text-lg font-bold text-gray-900">{{ workingDays }}</span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="space-y-2">
              <button
                @click="applyTemplateWeekdays"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
              >
                Appliquer modèle (Lun-Ven)
              </button>
              <button
                @click="clearAllSlots"
                class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm"
              >
                Tout effacer
              </button>
              <button
                @click="duplicateLastWeek"
                class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm"
              >
                Dupliquer semaine précédente
              </button>
            </div>
          </div>

          <!-- Tips -->
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex">
              <span class="text-2xl mr-3">💡</span>
              <div>
                <h4 class="text-sm font-medium text-blue-900 mb-2">Conseils</h4>
                <ul class="text-xs text-blue-700 space-y-1">
                  <li>• Créneaux de 30 min recommandés</li>
                  <li>• Laissez des pauses entre consultations</li>
                  <li>• Mettez à jour chaque semaine</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Slot Modal -->
    <Modal v-model="showAddSlotModal" title="Ajouter un Créneau" size="md" :show-close="true">
      <form @submit.prevent="addSlot" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jour de la semaine</label>
          <select
            v-model="newSlot.day_of_week"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
          >
            <option value="">Sélectionner...</option>
            <option v-for="day in daysOfWeek" :key="day.value" :value="day.value">
              {{ day.label }}
            </option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Heure de début</label>
            <input
              v-model="newSlot.start_time"
              type="time"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin</label>
            <input
              v-model="newSlot.end_time"
              type="time"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500"
            />
          </div>
        </div>

        <div class="flex items-center">
          <input
            v-model="newSlot.is_active"
            type="checkbox"
            class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
          />
          <label class="ml-2 text-sm text-gray-700">Activer immédiatement</label>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="showAddSlotModal = false"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
          >
            Annuler
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
          >
            Ajouter
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import Modal from '@/components/Modal.vue'

const showAddSlotModal = ref(false)
const availabilities = ref<any[]>([])

const daysOfWeek = [
  { value: 0, label: 'Dimanche' },
  { value: 1, label: 'Lundi' },
  { value: 2, label: 'Mardi' },
  { value: 3, label: 'Mercredi' },
  { value: 4, label: 'Jeudi' },
  { value: 5, label: 'Vendredi' },
  { value: 6, label: 'Samedi' },
]

const newSlot = ref({
  day_of_week: '',
  start_time: '09:00',
  end_time: '17:00',
  is_active: true,
})

const activeSlots = computed(() => {
  return availabilities.value.filter(a => a.is_active).length
})

const totalHoursPerWeek = computed(() => {
  return availabilities.value
    .filter(a => a.is_active)
    .reduce((total, slot) => {
      const start = new Date(`2000-01-01T${slot.start_time}`)
      const end = new Date(`2000-01-01T${slot.end_time}`)
      const hours = (end.getTime() - start.getTime()) / (1000 * 60 * 60)
      return total + hours
    }, 0)
})

const workingDays = computed(() => {
  const days = new Set(availabilities.value.filter(a => a.is_active).map(a => a.day_of_week))
  return days.size
})

function getSlotsByDay(day: number) {
  return availabilities.value
    .filter(a => a.day_of_week === day)
    .sort((a, b) => a.start_time.localeCompare(b.start_time))
}

async function loadAvailabilities() {
  try {
    const response = await api.get('/medecins/availabilities')
    availabilities.value = response.data
  } catch (error) {
    console.error('Error loading availabilities:', error)
  }
}

async function addSlot() {
  try {
    const response = await api.post('/medecins/availabilities', newSlot.value)
    availabilities.value.push(response.data)

    newSlot.value = {
      day_of_week: '',
      start_time: '09:00',
      end_time: '17:00',
      is_active: true,
    }

    showAddSlotModal.value = false
  } catch (error: any) {
    console.error('Error adding slot:', error)
    alert(error.response?.data?.message || 'Erreur lors de l\'ajout du créneau')
  }
}

function addSlotForDay(day: number) {
  newSlot.value.day_of_week = day.toString()
  showAddSlotModal.value = true
}

async function toggleSlot(slot: any) {
  try {
    const response = await api.put(`/medecins/availabilities/${slot.id}`, {
      is_active: !slot.is_active
    })
    slot.is_active = response.data.is_active
  } catch (error) {
    console.error('Error toggling slot:', error)
  }
}

async function deleteSlot(id: number) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) return

  try {
    await api.delete(`/medecins/availabilities/${id}`)
    availabilities.value = availabilities.value.filter(a => a.id !== id)
  } catch (error) {
    console.error('Error deleting slot:', error)
  }
}

async function applyTemplateWeekdays() {
  if (!confirm('Cela va créer des créneaux 9h-12h et 14h-18h du lundi au vendredi. Continuer ?')) return

  const template = [
    { day: 1, start: '09:00', end: '12:00' },
    { day: 1, start: '14:00', end: '18:00' },
    { day: 2, start: '09:00', end: '12:00' },
    { day: 2, start: '14:00', end: '18:00' },
    { day: 3, start: '09:00', end: '12:00' },
    { day: 3, start: '14:00', end: '18:00' },
    { day: 4, start: '09:00', end: '12:00' },
    { day: 4, start: '14:00', end: '18:00' },
    { day: 5, start: '09:00', end: '12:00' },
    { day: 5, start: '14:00', end: '18:00' },
  ]

  try {
    for (const slot of template) {
      const response = await api.post('/medecins/availabilities', {
        day_of_week: slot.day,
        start_time: slot.start,
        end_time: slot.end,
        is_active: true,
      })
      availabilities.value.push(response.data)
    }
  } catch (error) {
    console.error('Error applying template:', error)
  }
}

async function clearAllSlots() {
  if (!confirm('Êtes-vous sûr de vouloir supprimer tous les créneaux ?')) return

  try {
    await api.delete('/medecins/availabilities/all')
    availabilities.value = []
  } catch (error) {
    console.error('Error clearing slots:', error)
  }
}

function duplicateLastWeek() {
  alert('Fonctionnalité à venir : duplication de la semaine précédente')
}

onMounted(() => {
  loadAvailabilities()
})
</script>
