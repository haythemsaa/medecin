<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
      <select
        v-if="showPeriodFilter"
        v-model="selectedPeriod"
        @change="$emit('period-change', selectedPeriod)"
        class="text-sm border-gray-300 rounded-md focus:ring-teal-500"
      >
        <option value="7d">7 derniers jours</option>
        <option value="30d">30 derniers jours</option>
        <option value="3m">3 derniers mois</option>
        <option value="1y">12 derniers mois</option>
      </select>
    </div>

    <!-- Simple Bar Chart -->
    <div v-if="type === 'bar'" class="space-y-3">
      <div v-for="(item, index) in chartData" :key="index" class="flex items-center">
        <div class="w-32 text-sm text-gray-600 truncate">{{ item.label }}</div>
        <div class="flex-1 mx-4">
          <div class="relative pt-1">
            <div class="overflow-hidden h-8 text-xs flex rounded bg-gray-200">
              <div
                :style="{ width: getPercentage(item.value) + '%' }"
                :class="[
                  'shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center transition-all duration-500',
                  getBarColor(index)
                ]"
              ></div>
            </div>
          </div>
        </div>
        <div class="w-16 text-right text-sm font-semibold text-gray-900">
          {{ formatValue(item.value) }}
        </div>
      </div>
    </div>

    <!-- Simple Line Chart -->
    <div v-else-if="type === 'line'" class="relative h-64">
      <svg class="w-full h-full" viewBox="0 0 600 200" preserveAspectRatio="none">
        <!-- Grid lines -->
        <line v-for="i in 5" :key="'grid-' + i"
          :x1="0"
          :y1="(i * 200) / 5"
          :x2="600"
          :y2="(i * 200) / 5"
          class="stroke-gray-200"
          stroke-width="1"
        />

        <!-- Line path -->
        <polyline
          :points="getLinePoints()"
          fill="none"
          :class="lineColor"
          stroke-width="3"
        />

        <!-- Points -->
        <circle
          v-for="(point, index) in getLinePoints().split(' ')"
          :key="'point-' + index"
          :cx="point.split(',')[0]"
          :cy="point.split(',')[1]"
          r="4"
          :class="pointColor"
        />
      </svg>

      <!-- X-axis labels -->
      <div class="flex justify-between mt-2 text-xs text-gray-600">
        <span v-for="(item, index) in chartData" :key="'label-' + index">
          {{ item.label }}
        </span>
      </div>
    </div>

    <!-- Pie Chart -->
    <div v-else-if="type === 'pie'" class="flex items-center justify-center">
      <div class="relative w-48 h-48">
        <svg viewBox="0 0 100 100" class="transform -rotate-90">
          <circle
            v-for="(slice, index) in getPieSlices()"
            :key="'slice-' + index"
            cx="50"
            cy="50"
            r="25"
            :class="getPieColor(index)"
            fill="transparent"
            :stroke-dasharray="`${slice.percentage} ${100 - slice.percentage}`"
            :stroke-dashoffset="-slice.offset"
            stroke-width="50"
          />
        </svg>
      </div>

      <div class="ml-8 space-y-2">
        <div v-for="(item, index) in chartData" :key="'legend-' + index" class="flex items-center">
          <div :class="['w-3 h-3 rounded-full mr-2', getLegendColor(index)]"></div>
          <span class="text-sm text-gray-700">{{ item.label }}</span>
          <span class="ml-auto text-sm font-semibold text-gray-900 pl-4">
            {{ formatValue(item.value) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div v-else-if="type === 'stats'" class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="(item, index) in chartData" :key="index" class="text-center p-4 bg-gray-50 rounded-lg">
        <div :class="['text-3xl font-bold mb-1', getStatColor(index)]">
          {{ formatValue(item.value) }}
        </div>
        <div class="text-sm text-gray-600">{{ item.label }}</div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!chartData || chartData.length === 0" class="text-center py-12 text-gray-500">
      <span class="text-4xl block mb-2">📊</span>
      <p>Aucune donnée disponible</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface ChartData {
  label: string
  value: number
}

const props = defineProps<{
  title: string
  type: 'bar' | 'line' | 'pie' | 'stats'
  chartData: ChartData[]
  showPeriodFilter?: boolean
  valueFormat?: 'number' | 'currency' | 'percentage'
}>()

const emit = defineEmits<{
  'period-change': [period: string]
}>()

const selectedPeriod = ref('30d')

const maxValue = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return 0
  return Math.max(...props.chartData.map(d => d.value))
})

function getPercentage(value: number): number {
  if (maxValue.value === 0) return 0
  return (value / maxValue.value) * 100
}

function formatValue(value: number): string {
  if (props.valueFormat === 'currency') {
    return `${value.toFixed(0)} TND`
  } else if (props.valueFormat === 'percentage') {
    return `${value.toFixed(1)}%`
  }
  return value.toString()
}

function getBarColor(index: number): string {
  const colors = [
    'bg-teal-500',
    'bg-blue-500',
    'bg-green-500',
    'bg-yellow-500',
    'bg-purple-500',
    'bg-pink-500',
  ]
  return colors[index % colors.length]
}

const lineColor = 'stroke-teal-500'
const pointColor = 'fill-teal-500'

function getLinePoints(): string {
  if (!props.chartData || props.chartData.length === 0) return ''

  const width = 600
  const height = 200
  const pointSpacing = width / (props.chartData.length - 1 || 1)

  return props.chartData
    .map((item, index) => {
      const x = index * pointSpacing
      const y = height - (item.value / maxValue.value) * height
      return `${x},${y}`
    })
    .join(' ')
}

function getPieSlices() {
  if (!props.chartData || props.chartData.length === 0) return []

  const total = props.chartData.reduce((sum, item) => sum + item.value, 0)
  let currentOffset = 0

  return props.chartData.map(item => {
    const percentage = (item.value / total) * 100
    const slice = {
      percentage,
      offset: currentOffset,
    }
    currentOffset += percentage
    return slice
  })
}

function getPieColor(index: number): string {
  const colors = [
    'stroke-teal-500',
    'stroke-blue-500',
    'stroke-green-500',
    'stroke-yellow-500',
    'stroke-purple-500',
    'stroke-pink-500',
  ]
  return colors[index % colors.length]
}

function getLegendColor(index: number): string {
  const colors = [
    'bg-teal-500',
    'bg-blue-500',
    'bg-green-500',
    'bg-yellow-500',
    'bg-purple-500',
    'bg-pink-500',
  ]
  return colors[index % colors.length]
}

function getStatColor(index: number): string {
  const colors = [
    'text-teal-600',
    'text-blue-600',
    'text-green-600',
    'text-yellow-600',
  ]
  return colors[index % colors.length]
}
</script>
