/**
 * Currency formatting utilities for Tunisian Dinar (TND)
 */

import { PLATFORM_COMMISSION, VAT_RATE } from './constants'

/**
 * Format amount to Tunisian Dinar
 * @param amount - Amount in TND
 * @param showSymbol - Whether to show the currency symbol
 */
export function formatCurrency(amount: number, showSymbol: boolean = true): string {
  if (isNaN(amount)) return '0'

  const formatted = new Intl.NumberFormat('fr-TN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)

  return showSymbol ? `${formatted} TND` : formatted
}

/**
 * Calculate platform commission
 * @param amount - Base amount
 */
export function calculateCommission(amount: number): number {
  return amount * PLATFORM_COMMISSION
}

/**
 * Calculate doctor's net amount after commission
 * @param amount - Gross amount
 */
export function calculateDoctorNet(amount: number): number {
  return amount * (1 - PLATFORM_COMMISSION)
}

/**
 * Calculate VAT amount
 * @param amount - Base amount
 */
export function calculateVAT(amount: number): number {
  return amount * VAT_RATE
}

/**
 * Calculate total with VAT
 * @param amount - Base amount
 */
export function calculateWithVAT(amount: number): number {
  return amount * (1 + VAT_RATE)
}

/**
 * Calculate refund amount based on cancellation policy
 * @param amount - Original payment amount
 * @param hoursBeforeAppointment - Hours before appointment
 */
export function calculateRefund(amount: number, hoursBeforeAppointment: number): number {
  if (hoursBeforeAppointment > 24) {
    return amount // 100% refund
  } else if (hoursBeforeAppointment >= 2) {
    return amount * 0.5 // 50% refund
  } else {
    return 0 // No refund
  }
}

/**
 * Get refund percentage based on cancellation timing
 * @param hoursBeforeAppointment - Hours before appointment
 */
export function getRefundPercentage(hoursBeforeAppointment: number): number {
  if (hoursBeforeAppointment > 24) {
    return 100
  } else if (hoursBeforeAppointment >= 2) {
    return 50
  } else {
    return 0
  }
}

/**
 * Format price breakdown for invoice
 */
export interface PriceBreakdown {
  baseAmount: number
  commission: number
  doctorNet: number
  vat: number
  total: number
}

export function getPriceBreakdown(baseAmount: number): PriceBreakdown {
  const commission = calculateCommission(baseAmount)
  const doctorNet = calculateDoctorNet(baseAmount)
  const vat = calculateVAT(baseAmount)
  const total = calculateWithVAT(baseAmount)

  return {
    baseAmount,
    commission,
    doctorNet,
    vat,
    total
  }
}

/**
 * Format price breakdown as string
 */
export function formatPriceBreakdown(baseAmount: number): string {
  const breakdown = getPriceBreakdown(baseAmount)

  return `
Prix HT: ${formatCurrency(breakdown.baseAmount)}
Commission (15%): -${formatCurrency(breakdown.commission)}
Net médecin: ${formatCurrency(breakdown.doctorNet)}
TVA (19%): ${formatCurrency(breakdown.vat)}
Total TTC: ${formatCurrency(breakdown.total)}
  `.trim()
}
