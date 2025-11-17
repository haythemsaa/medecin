/**
 * General helper functions for Seha Digital
 */

import type { APPOINTMENT_STATUSES, PAYMENT_STATUSES, VALIDATION_STATUSES } from './constants'

/**
 * Get status badge color class
 */
export function getStatusColor(
  status: string,
  type: 'appointment' | 'payment' | 'validation' = 'appointment'
): string {
  const colors: Record<string, Record<string, string>> = {
    appointment: {
      pending: 'bg-yellow-100 text-yellow-800',
      confirmed: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800',
      completed: 'bg-blue-100 text-blue-800',
      no_show: 'bg-gray-100 text-gray-800'
    },
    payment: {
      pending: 'bg-yellow-100 text-yellow-800',
      paid: 'bg-green-100 text-green-800',
      refunded: 'bg-blue-100 text-blue-800',
      failed: 'bg-red-100 text-red-800'
    },
    validation: {
      pending: 'bg-yellow-100 text-yellow-800',
      incomplete: 'bg-orange-100 text-orange-800',
      validated: 'bg-green-100 text-green-800',
      rejected: 'bg-red-100 text-red-800'
    }
  }

  return colors[type]?.[status] || 'bg-gray-100 text-gray-800'
}

/**
 * Get status label in French
 */
export function getStatusLabel(
  status: string,
  type: 'appointment' | 'payment' | 'validation' = 'appointment'
): string {
  const labels: Record<string, Record<string, string>> = {
    appointment: {
      pending: 'En attente',
      confirmed: 'Confirmé',
      cancelled: 'Annulé',
      completed: 'Terminé',
      no_show: 'Absent'
    },
    payment: {
      pending: 'En attente',
      paid: 'Payé',
      refunded: 'Remboursé',
      failed: 'Échoué'
    },
    validation: {
      pending: 'En attente',
      incomplete: 'Incomplet',
      validated: 'Validé',
      rejected: 'Rejeté'
    }
  }

  return labels[type]?.[status] || status
}

/**
 * Get user initials from name
 */
export function getInitials(firstName: string, lastName: string): string {
  const first = firstName?.charAt(0)?.toUpperCase() || ''
  const last = lastName?.charAt(0)?.toUpperCase() || ''
  return `${first}${last}`
}

/**
 * Get full name
 */
export function getFullName(firstName: string, lastName: string, title?: string): string {
  const name = `${firstName} ${lastName}`
  return title ? `${title} ${name}` : name
}

/**
 * Truncate text with ellipsis
 */
export function truncate(text: string, maxLength: number): string {
  if (text.length <= maxLength) return text
  return `${text.substring(0, maxLength)}...`
}

/**
 * Generate random color for avatar
 */
export function getAvatarColor(name: string): string {
  const colors = [
    'bg-red-500',
    'bg-orange-500',
    'bg-yellow-500',
    'bg-green-500',
    'bg-teal-500',
    'bg-blue-500',
    'bg-indigo-500',
    'bg-purple-500',
    'bg-pink-500'
  ]

  const index = name.charCodeAt(0) % colors.length
  return colors[index]
}

/**
 * Copy text to clipboard
 */
export async function copyToClipboard(text: string): Promise<boolean> {
  try {
    await navigator.clipboard.writeText(text)
    return true
  } catch (error) {
    console.error('Failed to copy to clipboard:', error)
    return false
  }
}

/**
 * Download file from URL
 */
export function downloadFile(url: string, filename: string): void {
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

/**
 * Generate unique ID
 */
export function generateId(): string {
  return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
}

/**
 * Debounce function
 */
export function debounce<T extends (...args: any[]) => any>(
  func: T,
  wait: number
): (...args: Parameters<T>) => void {
  let timeout: NodeJS.Timeout | null = null

  return function (...args: Parameters<T>) {
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => func(...args), wait)
  }
}

/**
 * Throttle function
 */
export function throttle<T extends (...args: any[]) => any>(
  func: T,
  limit: number
): (...args: Parameters<T>) => void {
  let inThrottle: boolean = false

  return function (...args: Parameters<T>) {
    if (!inThrottle) {
      func(...args)
      inThrottle = true
      setTimeout(() => (inThrottle = false), limit)
    }
  }
}

/**
 * Format phone number for display
 */
export function formatPhoneNumber(phone: string): string {
  const cleaned = phone.replace(/\D/g, '')

  if (cleaned.length === 8) {
    return `${cleaned.slice(0, 2)} ${cleaned.slice(2, 5)} ${cleaned.slice(5)}`
  }

  return phone
}

/**
 * Format CIN for display
 */
export function formatCIN(cin: string): string {
  const cleaned = cin.replace(/\D/g, '')

  if (cleaned.length === 8) {
    return `${cleaned.slice(0, 4)} ${cleaned.slice(4)}`
  }

  return cin
}

/**
 * Get rating stars as emoji
 */
export function getRatingStars(rating: number): string {
  const fullStars = Math.floor(rating)
  const hasHalfStar = rating % 1 >= 0.5
  const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0)

  return '⭐'.repeat(fullStars) +
         (hasHalfStar ? '✨' : '') +
         '☆'.repeat(emptyStars)
}

/**
 * Get rating color class
 */
export function getRatingColor(rating: number): string {
  if (rating >= 4.5) return 'text-green-600'
  if (rating >= 3.5) return 'text-yellow-600'
  if (rating >= 2.5) return 'text-orange-600'
  return 'text-red-600'
}

/**
 * Parse array from textarea (newline separated)
 */
export function parseTextareaArray(text: string): string[] {
  return text
    .split('\n')
    .map(line => line.trim())
    .filter(line => line.length > 0)
}

/**
 * Join array for textarea display
 */
export function joinTextareaArray(array: string[]): string {
  return array.join('\n')
}

/**
 * Sleep/delay function
 */
export function sleep(ms: number): Promise<void> {
  return new Promise(resolve => setTimeout(resolve, ms))
}

/**
 * Check if string is Arabic
 */
export function isArabic(text: string): boolean {
  const arabicRegex = /[\u0600-\u06FF]/
  return arabicRegex.test(text)
}

/**
 * Get text direction based on content
 */
export function getTextDirection(text: string): 'ltr' | 'rtl' {
  return isArabic(text) ? 'rtl' : 'ltr'
}

/**
 * Format large numbers (1000 -> 1K, 1000000 -> 1M)
 */
export function formatNumber(num: number): string {
  if (num >= 1000000) {
    return `${(num / 1000000).toFixed(1)}M`
  }
  if (num >= 1000) {
    return `${(num / 1000).toFixed(1)}K`
  }
  return num.toString()
}

/**
 * Calculate percentage
 */
export function calculatePercentage(value: number, total: number): number {
  if (total === 0) return 0
  return Math.round((value / total) * 100)
}

/**
 * Group array by key
 */
export function groupBy<T>(array: T[], key: keyof T): Record<string, T[]> {
  return array.reduce((result, item) => {
    const groupKey = String(item[key])
    if (!result[groupKey]) {
      result[groupKey] = []
    }
    result[groupKey].push(item)
    return result
  }, {} as Record<string, T[]>)
}

/**
 * Sort array by key
 */
export function sortBy<T>(array: T[], key: keyof T, order: 'asc' | 'desc' = 'asc'): T[] {
  return [...array].sort((a, b) => {
    const aVal = a[key]
    const bVal = b[key]

    if (aVal < bVal) return order === 'asc' ? -1 : 1
    if (aVal > bVal) return order === 'asc' ? 1 : -1
    return 0
  })
}
