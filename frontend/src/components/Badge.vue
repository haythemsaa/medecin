<template>
  <span
    :class="[
      'inline-flex items-center rounded-full font-medium',
      sizeClasses[size],
      variantClasses[variant],
      customClass
    ]"
  >
    <span v-if="icon" class="mr-1">{{ icon }}</span>
    <slot>{{ text }}</slot>
    <button
      v-if="removable"
      @click.stop="$emit('remove')"
      class="ml-1 hover:opacity-70 transition-opacity"
    >
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path
          fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
  </span>
</template>

<script setup lang="ts">
defineProps<{
  text?: string
  icon?: string
  size?: 'xs' | 'sm' | 'md' | 'lg'
  variant?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'gray'
  removable?: boolean
  customClass?: string
}>()

defineEmits<{
  'remove': []
}>()

const sizeClasses = {
  xs: 'px-2 py-0.5 text-xs',
  sm: 'px-2.5 py-0.5 text-xs',
  md: 'px-3 py-1 text-sm',
  lg: 'px-4 py-1.5 text-base'
}

const variantClasses = {
  primary: 'bg-teal-100 text-teal-800',
  success: 'bg-green-100 text-green-800',
  warning: 'bg-yellow-100 text-yellow-800',
  danger: 'bg-red-100 text-red-800',
  info: 'bg-blue-100 text-blue-800',
  gray: 'bg-gray-100 text-gray-800'
}
</script>
