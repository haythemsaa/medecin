<template>
  <Card class="cursor-pointer hover:shadow-lg transition-shadow">
    <div class="space-y-4">
      <!-- Header with Photo -->
      <div class="flex items-start space-x-4">
        <div class="flex-shrink-0">
          <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center text-2xl font-bold text-teal-600">
            {{ initials }}
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-gray-900 truncate">
            Dr. {{ medecin.user?.first_name }} {{ medecin.user?.last_name }}
          </h3>
          <p class="text-sm text-gray-600">{{ medecin.specialite }}</p>
          <div v-if="medecin.rating_average" class="flex items-center mt-1">
            <span class="text-yellow-400 mr-1">⭐</span>
            <span class="text-sm font-medium text-gray-700">{{ medecin.rating_average?.toFixed(1) }}</span>
            <span v-if="medecin.reviews_count" class="text-sm text-gray-500 ml-1">
              ({{ medecin.reviews_count }} avis)
            </span>
          </div>
        </div>
      </div>

      <!-- Info -->
      <div class="space-y-2 text-sm">
        <div v-if="medecin.annees_experience" class="flex items-center text-gray-600">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          {{ medecin.annees_experience }} ans d'expérience
        </div>

        <div v-if="medecin.ville" class="flex items-center text-gray-600">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{ medecin.ville }}, {{ medecin.governorate }}
        </div>

        <div v-if="medecin.languages_spoken" class="flex items-center text-gray-600">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
          </svg>
          {{ formatLanguages(medecin.languages_spoken) }}
        </div>
      </div>

      <!-- Price -->
      <div class="pt-4 border-t border-gray-200 flex items-center justify-between">
        <div>
          <p class="text-2xl font-bold text-teal-600">{{ medecin.tarif_consultation }} TND</p>
          <p class="text-xs text-gray-500">par consultation</p>
        </div>
        <Button variant="primary" size="sm">
          Voir le profil
        </Button>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Card from './Card.vue'
import Button from './Button.vue'

interface Props {
  medecin: any
}

const props = defineProps<Props>()

const initials = computed(() => {
  const firstName = props.medecin.user?.first_name || ''
  const lastName = props.medecin.user?.last_name || ''
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
})

function formatLanguages(languages: string[]): string {
  if (!Array.isArray(languages)) return ''

  const languageNames: Record<string, string> = {
    ar: 'Arabe',
    fr: 'Français',
    en: 'Anglais',
  }

  return languages.map(lang => languageNames[lang] || lang).join(', ')
}
</script>
