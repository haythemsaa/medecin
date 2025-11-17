<template>
  <div
    :class="[
      'bg-white rounded-lg transition-all',
      shadow && 'shadow-md hover:shadow-lg',
      border && 'border border-gray-200',
      padding && paddingClasses[padding],
      clickable && 'cursor-pointer',
      customClass
    ]"
    @click="handleClick"
  >
    <!-- Card Header -->
    <div v-if="$slots.header || title" :class="['pb-4', headerClass]">
      <slot name="header">
        <div class="flex items-center justify-between">
          <h3 :class="['font-semibold', titleClass || 'text-lg text-gray-900']">
            {{ title }}
          </h3>
          <slot name="headerAction"></slot>
        </div>
        <p v-if="subtitle" class="text-sm text-gray-600 mt-1">
          {{ subtitle }}
        </p>
      </slot>
    </div>

    <!-- Card Body -->
    <div :class="bodyClass">
      <slot></slot>
    </div>

    <!-- Card Footer -->
    <div v-if="$slots.footer" :class="['pt-4 border-t border-gray-200', footerClass]">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  title?: string
  subtitle?: string
  shadow?: boolean
  border?: boolean
  padding?: 'none' | 'sm' | 'md' | 'lg'
  clickable?: boolean
  customClass?: string
  titleClass?: string
  headerClass?: string
  bodyClass?: string
  footerClass?: string
}>()

const emit = defineEmits<{
  'click': []
}>()

const paddingClasses = {
  none: '',
  sm: 'p-3',
  md: 'p-4',
  lg: 'p-6'
}

function handleClick() {
  if (props.clickable) {
    emit('click')
  }
}
</script>
