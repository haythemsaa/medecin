/**
 * Application constants for Seha Digital
 * Tunisian telemedicine platform
 */

export const GOVERNORATES = [
  'Tunis',
  'Ariana',
  'Ben Arous',
  'Manouba',
  'Nabeul',
  'Zaghouan',
  'Bizerte',
  'Béja',
  'Jendouba',
  'Kef',
  'Siliana',
  'Kairouan',
  'Kasserine',
  'Sidi Bouzid',
  'Sousse',
  'Monastir',
  'Mahdia',
  'Sfax',
  'Gabès',
  'Médenine',
  'Tataouine',
  'Gafsa',
  'Tozeur',
  'Kébili'
] as const

export const SPECIALTIES = [
  'Médecine générale',
  'Pédiatrie',
  'Gynécologie-Obstétrique',
  'Cardiologie',
  'Dermatologie',
  'Pneumologie',
  'Gastro-entérologie',
  'Neurologie',
  'Psychiatrie',
  'Ophtalmologie',
  'ORL',
  'Rhumatologie',
  'Endocrinologie',
  'Néphrologie',
  'Urologie',
  'Chirurgie générale',
  'Orthopédie',
  'Radiologie',
  'Anesthésie-Réanimation',
  'Médecine interne',
  'Allergologie',
  'Hématologie',
  'Oncologie',
  'Nutrition',
  'Médecine du sport'
] as const

export const LANGUAGES = [
  { code: 'ar', label: 'العربية', flag: '🇹🇳' },
  { code: 'fr', label: 'Français', flag: '🇫🇷' },
  { code: 'en', label: 'English', flag: '🇬🇧' }
] as const

export const APPOINTMENT_TYPES = [
  { value: 'video', label: 'Visioconférence', icon: '📹' },
  { value: 'phone', label: 'Téléphone', icon: '📞' }
] as const

export const APPOINTMENT_STATUSES = [
  { value: 'pending', label: 'En attente', color: 'yellow' },
  { value: 'confirmed', label: 'Confirmé', color: 'green' },
  { value: 'cancelled', label: 'Annulé', color: 'red' },
  { value: 'completed', label: 'Terminé', color: 'blue' },
  { value: 'no_show', label: 'Absent', color: 'gray' }
] as const

export const PAYMENT_STATUSES = [
  { value: 'pending', label: 'En attente', color: 'yellow' },
  { value: 'paid', label: 'Payé', color: 'green' },
  { value: 'refunded', label: 'Remboursé', color: 'blue' },
  { value: 'failed', label: 'Échoué', color: 'red' }
] as const

export const VALIDATION_STATUSES = [
  { value: 'pending', label: 'En attente', color: 'yellow', icon: '⏳' },
  { value: 'incomplete', label: 'Incomplet', color: 'orange', icon: '📋' },
  { value: 'validated', label: 'Validé', color: 'green', icon: '✅' },
  { value: 'rejected', label: 'Rejeté', color: 'red', icon: '❌' }
] as const

export const HEALTH_COVERAGE_TYPES = [
  { value: 'cnam', label: 'CNAM', icon: '🏥' },
  { value: 'mutuelle', label: 'Mutuelle privée', icon: '🏢' },
  { value: 'none', label: 'Aucune', icon: '💳' }
] as const

export const DOCUMENT_TYPES = [
  { value: 'cin', label: 'CIN', required: true },
  { value: 'diploma', label: 'Diplôme médical', required: true },
  { value: 'ordre_certificate', label: 'Certificat Ordre des Médecins', required: true },
  { value: 'rcp_insurance', label: 'Assurance RCP', required: true },
  { value: 'cv', label: 'CV', required: false },
  { value: 'specialization', label: 'Certificat de spécialisation', required: false }
] as const

export const PRICE_RANGES = [
  { min: 0, max: 50, label: 'Moins de 50 TND' },
  { min: 50, max: 75, label: '50-75 TND' },
  { min: 75, max: 100, label: '75-100 TND' },
  { min: 100, max: 150, label: '100-150 TND' },
  { min: 150, max: 9999, label: 'Plus de 150 TND' }
] as const

export const RATING_CRITERIA = [
  { key: 'professionalism', label: 'Professionnalisme' },
  { key: 'listening', label: 'Écoute' },
  { key: 'explanation', label: 'Clarté des explications' },
  { key: 'punctuality', label: 'Ponctualité' },
  { key: 'effectiveness', label: 'Efficacité' }
] as const

export const REFUND_POLICY = {
  MORE_THAN_24H: { percentage: 100, label: 'Remboursement intégral' },
  BETWEEN_24H_2H: { percentage: 50, label: 'Remboursement 50%' },
  LESS_THAN_2H: { percentage: 0, label: 'Pas de remboursement' }
} as const

export const PLATFORM_COMMISSION = 0.15 // 15%
export const VAT_RATE = 0.19 // 19% TVA Tunisia

export const CONSULTATION_DURATIONS = [
  { value: 15, label: '15 minutes' },
  { value: 30, label: '30 minutes' },
  { value: 45, label: '45 minutes' },
  { value: 60, label: '1 heure' }
] as const

export const URGENCY_MULTIPLIERS = {
  normal: 1.0,
  urgent: 1.5,
  emergency: 2.0
} as const

export const MAX_FILE_SIZE = 5 * 1024 * 1024 // 5MB
export const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf']

export const CONTACT_INFO = {
  email: 'contact@sehadigital.tn',
  supportEmail: 'support@sehadigital.tn',
  medecinEmail: 'medecins@sehadigital.tn',
  phone: '80 XXX XXX',
  emergencyPhone: '190',
  website: 'www.sehadigital.tn'
} as const
