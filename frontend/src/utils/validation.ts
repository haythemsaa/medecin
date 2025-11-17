/**
 * Validation utilities for forms and file uploads
 */

import { MAX_FILE_SIZE, ALLOWED_FILE_TYPES } from './constants'

/**
 * Validate email format
 */
export function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Validate Tunisian phone number (8 digits)
 */
export function isValidPhone(phone: string): boolean {
  const phoneRegex = /^[2-9]\d{7}$/
  return phoneRegex.test(phone.replace(/\s/g, ''))
}

/**
 * Validate Tunisian CIN (8 digits)
 */
export function isValidCIN(cin: string): boolean {
  const cinRegex = /^\d{8}$/
  return cinRegex.test(cin)
}

/**
 * Validate password strength
 * At least 8 characters, 1 uppercase, 1 lowercase, 1 number
 */
export function isValidPassword(password: string): boolean {
  if (password.length < 8) return false

  const hasUpperCase = /[A-Z]/.test(password)
  const hasLowerCase = /[a-z]/.test(password)
  const hasNumber = /\d/.test(password)

  return hasUpperCase && hasLowerCase && hasNumber
}

/**
 * Get password strength level
 */
export function getPasswordStrength(password: string): {
  level: 'weak' | 'medium' | 'strong'
  percentage: number
} {
  let strength = 0

  if (password.length >= 8) strength++
  if (password.length >= 12) strength++
  if (/[a-z]/.test(password)) strength++
  if (/[A-Z]/.test(password)) strength++
  if (/\d/.test(password)) strength++
  if (/[^a-zA-Z\d]/.test(password)) strength++

  if (strength <= 2) {
    return { level: 'weak', percentage: 33 }
  } else if (strength <= 4) {
    return { level: 'medium', percentage: 66 }
  } else {
    return { level: 'strong', percentage: 100 }
  }
}

/**
 * Validate file size
 */
export function isValidFileSize(file: File, maxSize: number = MAX_FILE_SIZE): boolean {
  return file.size <= maxSize
}

/**
 * Validate file type
 */
export function isValidFileType(file: File, allowedTypes: string[] = ALLOWED_FILE_TYPES): boolean {
  return allowedTypes.includes(file.type)
}

/**
 * Validate file for upload (size and type)
 */
export function validateFile(file: File): { valid: boolean; error?: string } {
  if (!isValidFileType(file)) {
    return {
      valid: false,
      error: 'Type de fichier non autorisé. Formats acceptés: JPG, PNG, PDF'
    }
  }

  if (!isValidFileSize(file)) {
    return {
      valid: false,
      error: `Fichier trop volumineux. Taille maximale: ${formatFileSize(MAX_FILE_SIZE)}`
    }
  }

  return { valid: true }
}

/**
 * Format file size to human readable
 */
export function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 B'

  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`
}

/**
 * Validate Ordre des Médecins number format
 */
export function isValidOrdreNumber(numero: string): boolean {
  // Format: XXXX/YYYY where X is number and Y is year
  const ordreRegex = /^\d{4}\/\d{4}$/
  return ordreRegex.test(numero)
}

/**
 * Validate CNAM number (social security)
 */
export function isValidCNAMNumber(numero: string): boolean {
  // CNAM numbers are typically 13 digits
  const cnamRegex = /^\d{13}$/
  return cnamRegex.test(numero.replace(/\s/g, ''))
}

/**
 * Validate price range
 */
export function isValidPrice(price: number, min: number = 30, max: number = 500): boolean {
  return price >= min && price <= max
}

/**
 * Sanitize input text (remove dangerous characters)
 */
export function sanitizeText(text: string): string {
  return text
    .replace(/[<>]/g, '') // Remove < and >
    .trim()
}

/**
 * Validate date is in future
 */
export function isFutureDate(date: string | Date): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  return dateObj.getTime() > Date.now()
}

/**
 * Validate date is within range
 */
export function isDateInRange(
  date: string | Date,
  minDate?: string | Date,
  maxDate?: string | Date
): boolean {
  const dateObj = typeof date === 'string' ? new Date(date) : date

  if (minDate) {
    const minDateObj = typeof minDate === 'string' ? new Date(minDate) : minDate
    if (dateObj < minDateObj) return false
  }

  if (maxDate) {
    const maxDateObj = typeof maxDate === 'string' ? new Date(maxDate) : maxDate
    if (dateObj > maxDateObj) return false
  }

  return true
}

/**
 * Validate age (must be 18+)
 */
export function isValidAge(dateOfBirth: string | Date, minAge: number = 18): boolean {
  const birthDate = typeof dateOfBirth === 'string' ? new Date(dateOfBirth) : dateOfBirth
  const today = new Date()

  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()

  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }

  return age >= minAge
}

/**
 * Get validation errors for a form
 */
export interface FormErrors {
  [key: string]: string
}

export function validateForm(data: Record<string, any>, rules: Record<string, Function[]>): FormErrors {
  const errors: FormErrors = {}

  for (const field in rules) {
    const value = data[field]
    const validators = rules[field]

    for (const validator of validators) {
      const result = validator(value)
      if (result !== true) {
        errors[field] = result
        break
      }
    }
  }

  return errors
}
