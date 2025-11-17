/**
 * Date and time formatting utilities
 * Tunisian locale (fr-TN)
 */

/**
 * Format date to French locale
 * @param date - Date string or Date object
 * @param format - Format type: 'short', 'medium', 'long'
 */
export function formatDate(date: string | Date, format: 'short' | 'medium' | 'long' = 'medium'): string {
  if (!date) return ''

  const dateObj = typeof date === 'string' ? new Date(date) : date

  if (isNaN(dateObj.getTime())) return ''

  const options: Intl.DateTimeFormatOptions = {
    short: { day: '2-digit', month: '2-digit', year: 'numeric' },
    medium: { day: 'numeric', month: 'long', year: 'numeric' },
    long: { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }
  }[format]

  return dateObj.toLocaleDateString('fr-FR', options)
}

/**
 * Format time to French locale
 * @param date - Date string or Date object
 */
export function formatTime(date: string | Date): string {
  if (!date) return ''

  const dateObj = typeof date === 'string' ? new Date(date) : date

  if (isNaN(dateObj.getTime())) return ''

  return dateObj.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

/**
 * Format date and time together
 * @param date - Date string or Date object
 */
export function formatDateTime(date: string | Date): string {
  if (!date) return ''

  const dateObj = typeof date === 'string' ? new Date(date) : date

  if (isNaN(dateObj.getTime())) return ''

  return dateObj.toLocaleString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

/**
 * Get relative time (e.g., "il y a 2 heures", "dans 3 jours")
 * @param date - Date string or Date object
 */
export function getRelativeTime(date: string | Date): string {
  if (!date) return ''

  const dateObj = typeof date === 'string' ? new Date(date) : date
  const now = new Date()
  const diffMs = dateObj.getTime() - now.getTime()
  const diffSec = Math.abs(Math.floor(diffMs / 1000))
  const diffMin = Math.floor(diffSec / 60)
  const diffHour = Math.floor(diffMin / 60)
  const diffDay = Math.floor(diffHour / 24)
  const isPast = diffMs < 0

  if (diffSec < 60) {
    return 'À l\'instant'
  } else if (diffMin < 60) {
    return isPast ? `Il y a ${diffMin}min` : `Dans ${diffMin}min`
  } else if (diffHour < 24) {
    return isPast ? `Il y a ${diffHour}h` : `Dans ${diffHour}h`
  } else if (diffDay < 7) {
    return isPast ? `Il y a ${diffDay}j` : `Dans ${diffDay}j`
  } else {
    return formatDate(dateObj, 'short')
  }
}

/**
 * Check if date is today
 */
export function isToday(date: string | Date): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  const today = new Date()

  return dateObj.toDateString() === today.toDateString()
}

/**
 * Check if date is tomorrow
 */
export function isTomorrow(date: string | Date): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)

  return dateObj.toDateString() === tomorrow.toDateString()
}

/**
 * Get time until appointment in minutes
 */
export function getTimeUntilAppointment(appointmentDate: string | Date): number {
  const dateObj = typeof appointmentDate === 'string' ? new Date(appointmentDate) : appointmentDate
  const now = new Date()

  return Math.floor((dateObj.getTime() - now.getTime()) / 1000 / 60)
}

/**
 * Check if can join consultation (10 minutes before to end time)
 */
export function canJoinConsultation(appointmentDate: string | Date, duration: number): boolean {
  const minutesUntil = getTimeUntilAppointment(appointmentDate)
  return minutesUntil <= 10 && minutesUntil >= -duration
}

/**
 * Format duration in minutes to human readable
 */
export function formatDuration(minutes: number): string {
  if (minutes < 60) {
    return `${minutes} min`
  }

  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60

  if (mins === 0) {
    return `${hours}h`
  }

  return `${hours}h${mins.toString().padStart(2, '0')}`
}

/**
 * Get age from date of birth
 */
export function getAge(dateOfBirth: string | Date): number {
  const birthDate = typeof dateOfBirth === 'string' ? new Date(dateOfBirth) : dateOfBirth
  const today = new Date()

  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()

  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }

  return age
}

/**
 * Get day of week in French
 */
export function getDayOfWeek(date: string | Date): string {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  return dateObj.toLocaleDateString('fr-FR', { weekday: 'long' })
}

/**
 * Add days to date
 */
export function addDays(date: Date, days: number): Date {
  const result = new Date(date)
  result.setDate(result.getDate() + days)
  return result
}

/**
 * Add hours to date
 */
export function addHours(date: Date, hours: number): Date {
  const result = new Date(date)
  result.setHours(result.getHours() + hours)
  return result
}
