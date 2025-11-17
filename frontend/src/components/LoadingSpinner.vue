<template>
  <div :class="['flex items-center justify-center', containerClass]">
    <div
      :class="[
        'animate-spin rounded-full border-b-2',
        sizeClasses[size],
        colorClasses[color]
      ]"
    ></div>
    <span v-if="text" :class="['ml-3 text-gray-600', textSizeClasses[size]]">
      {{ text }}
    </span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  size?: 'sm' | 'md' | 'lg' | 'xl'
  color?: 'teal' | 'blue' | 'green' | 'gray'
  text?: string
  containerClass?: string
}>()

const sizeClasses = {
  sm: 'w-4 h-4',
  md: 'w-8 h-8',
  lg: 'w-12 h-12',
  xl: 'w-16 h-16'
}

const colorClasses = {
  teal: 'border-teal-600',
  blue: 'border-blue-600',
  green: 'border-green-600',
  gray: 'border-gray-600'
}

const textSizeClasses = {
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base',
  xl: 'text-lg'
}
</script>
