export interface User {
  id: number
  email: string
  phone: string
  role: 'patient' | 'medecin' | 'admin'
  status: 'active' | 'inactive' | 'suspended'
  preferred_language: string
  patient?: Patient
  medecin?: Medecin
}

export interface Patient {
  id: number
  user_id: number
  first_name: string
  last_name: string
  first_name_ar?: string
  last_name_ar?: string
  birth_date: string
  cin: string
  gender: 'male' | 'female' | 'other'
  governorate?: string
  delegation?: string
  address?: string
  health_coverage: 'cnam' | 'mutuelle' | 'none'
  cnam_number?: string
  mutuelle_name?: string
  blood_type?: string
}

export interface Medecin {
  id: number
  user_id: number
  first_name: string
  last_name: string
  cin: string
  ordre_number: string
  speciality: string
  sub_specialities?: string[]
  years_of_experience: number
  bio?: string
  photo?: string
  consultation_languages: string[]
  consultation_price: number
  urgent_consultation_price: number
  validation_status: 'pending' | 'validated' | 'rejected' | 'incomplete'
  rating_average: number
  rating_count: number
  consultation_count: number
}

export interface Appointment {
  id: number
  patient_id: number
  medecin_id: number
  appointment_date: string
  duration: number
  type: 'video' | 'phone'
  status: 'pending' | 'confirmed' | 'completed' | 'cancelled' | 'no_show'
  reason: string
  symptoms?: string[]
  price: number
  is_urgent: boolean
  patient?: Patient
  medecin?: Medecin
  payment?: Payment
}

export interface Payment {
  id: number
  transaction_id: string
  payment_method: 'card' | 'e_dinar' | 'mobile_money' | 'postal_mandate'
  amount: number
  medecin_amount: number
  platform_commission: number
  vat_amount: number
  status: 'pending' | 'completed' | 'failed' | 'refunded' | 'cancelled'
}
