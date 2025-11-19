// User types
export interface User {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone?: string;
  date_naissance?: string;
  sexe?: 'homme' | 'femme';
  adresse?: string;
  ville?: string;
  photo_profil?: string;
  role: 'patient' | 'medecin' | 'admin';
  created_at: string;
  updated_at: string;
}

// Doctor types
export interface Medecin {
  id: number;
  user_id: number;
  user: User;
  specialite: string;
  numero_ordre: string;
  tarif: number;
  experience_annees?: number;
  formation?: string;
  langues_parlees?: string[];
  adresse: string;
  ville: string;
  latitude?: number;
  longitude?: number;
  distance?: number;
  show_on_map: boolean;
  status: 'en_attente' | 'validated' | 'rejected';
  rating?: number;
  total_reviews?: number;
  created_at: string;
  updated_at: string;
}

// Patient types
export interface Patient {
  id: number;
  user_id: number;
  user: User;
  numero_securite_sociale?: string;
  groupe_sanguin?: string;
  allergies?: string[];
  antecedents_medicaux?: string[];
  created_at: string;
  updated_at: string;
}

// Appointment types
export interface Appointment {
  id: number;
  patient_id: number;
  patient?: Patient;
  medecin_id: number;
  medecin?: Medecin;
  date_heure: string;
  motif: string;
  type_consultation: 'cabinet' | 'video';
  statut: 'en_attente' | 'confirme' | 'termine' | 'annule';
  notes_medecin?: string;
  ordonnance_id?: number;
  montant?: number;
  paiement_statut?: 'en_attente' | 'paye' | 'rembourse';
  created_at: string;
  updated_at: string;
}

// Prescription types
export interface Prescription {
  id: number;
  medecin_id: number;
  medecin?: Medecin;
  patient_id: number;
  patient?: Patient;
  appointment_id?: number;
  date_prescription: string;
  medicaments: Medicament[];
  instructions?: string;
  duree_traitement?: string;
  renewable: boolean;
  times_renewed?: number;
  max_renewals?: number;
  created_at: string;
  updated_at: string;
}

export interface Medicament {
  nom: string;
  dosage: string;
  frequence: string;
  duree: string;
  instructions?: string;
}

// Prescription Renewal types
export interface PrescriptionRenewal {
  id: number;
  prescription_id: number;
  prescription?: Prescription;
  patient_id: number;
  patient?: Patient;
  medecin_id: number;
  medecin?: Medecin;
  status: 'pending' | 'approved' | 'rejected';
  patient_notes?: string;
  medecin_notes?: string;
  new_prescription_id?: number;
  created_at: string;
  updated_at: string;
}

// Medication Reminder types
export interface MedicationReminder {
  id: number;
  patient_id: number;
  patient?: Patient;
  prescription_id?: number;
  prescription?: Prescription;
  medication_name: string;
  dosage: string;
  frequency: string;
  times_per_day: number;
  reminder_times: string[];
  start_date: string;
  end_date?: string;
  active: boolean;
  notes?: string;
  created_at: string;
  updated_at: string;
}

// Urgent Consultation types
export interface UrgentConsultation {
  id: number;
  patient_id: number;
  patient?: Patient;
  medecin_id: number;
  medecin?: Medecin;
  appointment_id?: number;
  appointment?: Appointment;
  motif: string;
  status: 'pending' | 'accepted' | 'rejected' | 'completed';
  urgent_fee: number;
  created_at: string;
  updated_at: string;
}

// Review types
export interface Review {
  id: number;
  patient_id: number;
  patient?: Patient;
  medecin_id: number;
  medecin?: Medecin;
  appointment_id: number;
  rating: number;
  comment?: string;
  created_at: string;
  updated_at: string;
}

// Questionnaire types
export interface Questionnaire {
  id: number;
  medecin_id: number;
  medecin?: Medecin;
  title: string;
  description?: string;
  questions: Question[];
  active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Question {
  id: string;
  question: string;
  type: 'text' | 'number' | 'boolean' | 'choice' | 'multiple_choice';
  options?: string[];
  required: boolean;
}

export interface QuestionnaireResponse {
  id: number;
  questionnaire_id: number;
  questionnaire?: Questionnaire;
  patient_id: number;
  patient?: Patient;
  appointment_id?: number;
  responses: Record<string, any>;
  created_at: string;
  updated_at: string;
}

// Favorite types
export interface Favorite {
  id: number;
  patient_id: number;
  medecin_id: number;
  medecin?: Medecin;
  created_at: string;
}

// Notification types
export interface Notification {
  id: number;
  user_id: number;
  type: string;
  title: string;
  message: string;
  data?: Record<string, any>;
  read: boolean;
  created_at: string;
  updated_at: string;
}

// Availability types
export interface Availability {
  id: number;
  medecin_id: number;
  day_of_week: number;
  start_time: string;
  end_time: string;
  is_available: boolean;
}

// API Response types
export interface ApiResponse<T> {
  data: T;
  message?: string;
  success?: boolean;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

// Navigation types
export type RootStackParamList = {
  Auth: undefined;
  App: undefined;
};

export type AuthStackParamList = {
  Login: undefined;
  Register: undefined;
  ForgotPassword: undefined;
};

export type PatientTabParamList = {
  Home: undefined;
  Search: undefined;
  Appointments: undefined;
  Profile: undefined;
};

export type PatientStackParamList = {
  DoctorDetails: { id: number };
  BookAppointment: { doctorId: number };
  AppointmentDetails: { id: number };
  VideoConsultation: { appointmentId: number };
  Prescriptions: undefined;
  PrescriptionDetails: { id: number };
  PrescriptionRenewal: { prescriptionId: number };
  MedicationReminders: undefined;
  AddMedicationReminder: { prescriptionId?: number };
  EditMedicationReminder: { id: number };
  UrgentConsultation: undefined;
  Questionnaire: { questionnaireId: number; appointmentId?: number };
  Favorites: undefined;
  EditProfile: undefined;
  PaymentMethods: undefined;
  NotificationSettings: undefined;
  Privacy: undefined;
  Support: undefined;
};

export type DoctorTabParamList = {
  DoctorDashboard: undefined;
  DoctorAppointments: undefined;
  DoctorPatients: undefined;
  DoctorProfile: undefined;
};

export type DoctorStackParamList = {
  DoctorAppointmentDetails: { id: number };
  DoctorPatientDetails: { id: number };
  CreatePrescription: { appointmentId: number };
  DoctorSchedule: undefined;
  DoctorQuestionnaires: undefined;
  CreateQuestionnaire: undefined;
  EditQuestionnaire: { id: number };
  DoctorStats: undefined;
  DoctorVideoConsultation: { appointmentId: number };
};
