<template>
  <div :class="['flex flex-col items-center justify-center text-center', padding && paddingClasses[padding], customClass]">
    <div :class="['mb-4', iconSizeClasses[size]]">
      <slot name="icon">
        <span class="text-6xl">{{ icon || '📭' }}</span>
      </slot>
    </div>

    <h3 :class="['font-semibold text-gray-900 mb-2', titleSizeClasses[size]]">
      <slot name="title">{{ title || 'Aucune donnée' }}</slot>
    </h3>

    <p :class="['text-gray-600 mb-6', descriptionSizeClasses[size], maxWidth && 'max-w-md']">
      <slot name="description">{{ description || 'Aucun élément à afficher pour le moment' }}</slot>
    </p>

    <div v-if="$slots.action || actionText">
      <slot name="action">
        <button
          v-if="actionText"
          @click="$emit('action')"
          class="px-6 py-3 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition-colors"
        >
          {{ actionText }}
        </button>
      </slot>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  icon?: string
  title?: string
  description?: string
  actionText?: string
  size?: 'sm' | 'md' | 'lg'
  padding?: 'none' | 'sm' | 'md' | 'lg' | 'xl'
  maxWidth?: boolean
  customClass?: string
}>()

defineEmits<{
  'action': []
}>()

const paddingClasses = {
  none: '',
  sm: 'py-8',
  md: 'py-12',
  lg: 'py-16',
  xl: 'py-24'
}

const iconSizeClasses = {
  sm: 'text-4xl',
  md: 'text-6xl',
  lg: 'text-8xl'
}

const titleSizeClasses = {
  sm: 'text-base',
  md: 'text-lg',
  lg: 'text-2xl'
}

const descriptionSizeClasses = {
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base'
}
</script>
