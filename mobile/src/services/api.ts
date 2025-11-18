import axios, { AxiosInstance, AxiosError } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL = __DEV__
  ? 'http://10.0.2.2:8000/api' // Android emulator
  : 'https://api.sehadigital.tn/api'; // Production

class ApiService {
  private api: AxiosInstance;

  constructor() {
    this.api = axios.create({
      baseURL: API_URL,
      timeout: 30000,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    // Request interceptor
    this.api.interceptors.request.use(
      async (config) => {
        const token = await AsyncStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor
    this.api.interceptors.response.use(
      (response) => response,
      async (error: AxiosError) => {
        if (error.response?.status === 401) {
          // Token expired, logout
          await AsyncStorage.removeItem('auth_token');
          await AsyncStorage.removeItem('user');
          // Navigate to login (handled by auth store)
        }
        return Promise.reject(error);
      }
    );
  }

  // Auth
  async login(email: string, password: string) {
    const response = await this.api.post('/auth/login', { email, password });
    return response.data;
  }

  async register(data: any) {
    const response = await this.api.post('/patients/register', data);
    return response.data;
  }

  async logout() {
    const response = await this.api.post('/auth/logout');
    return response.data;
  }

  async getMe() {
    const response = await this.api.get('/auth/me');
    return response.data;
  }

  // Doctors
  async searchDoctors(params: any) {
    const response = await this.api.get('/search/medecins', { params });
    return response.data;
  }

  async getDoctorDetails(id: number) {
    const response = await this.api.get(`/medecins/${id}`);
    return response.data;
  }

  async getDoctorReviews(medecinId: number) {
    const response = await this.api.get(`/reviews/medecins/${medecinId}`);
    return response.data;
  }

  // Geolocation
  async searchNearbyDoctors(latitude: number, longitude: number, radius: number, specialty?: string) {
    const response = await this.api.get('/geolocation/nearby', {
      params: { latitude, longitude, radius, specialty },
    });
    return response.data;
  }

  async searchDoctorsByCity(ville: string, specialty?: string) {
    const response = await this.api.get('/geolocation/by-city', {
      params: { ville, specialty },
    });
    return response.data;
  }

  // Appointments
  async getAppointments() {
    const response = await this.api.get('/appointments');
    return response.data;
  }

  async createAppointment(data: any) {
    const response = await this.api.post('/appointments', data);
    return response.data;
  }

  async cancelAppointment(id: number) {
    const response = await this.api.post(`/appointments/${id}/cancel`);
    return response.data;
  }

  async getAppointmentDetails(id: number) {
    const response = await this.api.get(`/appointments/${id}`);
    return response.data;
  }

  // Urgent Consultations
  async getAvailableUrgentDoctors(specialty?: string, maxPrice?: number) {
    const response = await this.api.get('/urgent-consultations/available', {
      params: { specialty, max_price: maxPrice },
    });
    return response.data;
  }

  async requestUrgentConsultation(medecinId: number, motif: string, symptomsDescription?: string) {
    const response = await this.api.post('/urgent-consultations/request', {
      medecin_id: medecinId,
      motif,
      symptoms_description: symptomsDescription,
    });
    return response.data;
  }

  // Prescriptions
  async getMyPrescriptions() {
    const response = await this.api.get('/prescriptions/my-prescriptions');
    return response.data;
  }

  async getPrescriptionDetails(id: number) {
    const response = await this.api.get(`/prescriptions/${id}`);
    return response.data;
  }

  async downloadPrescription(id: number) {
    const response = await this.api.get(`/prescriptions/${id}/download`, {
      responseType: 'blob',
    });
    return response.data;
  }

  // Prescription Renewals
  async getRenewablePrescriptions() {
    const response = await this.api.get('/prescription-renewals/renewable');
    return response.data;
  }

  async requestPrescriptionRenewal(prescriptionId: number, patientNotes?: string) {
    const response = await this.api.post('/prescription-renewals/request', {
      prescription_id: prescriptionId,
      patient_notes: patientNotes,
    });
    return response.data;
  }

  async getRenewalHistory() {
    const response = await this.api.get('/prescription-renewals/history');
    return response.data;
  }

  // Medication Reminders
  async getMedicationReminders() {
    const response = await this.api.get('/medication-reminders');
    return response.data;
  }

  async createMedicationReminder(data: any) {
    const response = await this.api.post('/medication-reminders', data);
    return response.data;
  }

  async recordMedicationIntake(reminderId: number, takenAt: string, skipped: boolean) {
    const response = await this.api.post('/medication-reminders/intake/record', {
      reminder_id: reminderId,
      taken_at: takenAt,
      skipped,
    });
    return response.data;
  }

  async getAdherenceStats(reminderId: number) {
    const response = await this.api.get(`/medication-reminders/${reminderId}/adherence-stats`);
    return response.data;
  }

  // Questionnaires
  async getQuestionnaireTemplates(specialty?: string) {
    const response = await this.api.get('/questionnaires/templates', {
      params: { specialty },
    });
    return response.data;
  }

  async getQuestionnaireForAppointment(appointmentId: number) {
    const response = await this.api.get(`/questionnaires/appointments/${appointmentId}`);
    return response.data;
  }

  async submitQuestionnaire(data: any) {
    const response = await this.api.post('/questionnaires/submit', data);
    return response.data;
  }

  // Consultations
  async getConsultationDetails(id: number) {
    const response = await this.api.get(`/consultations/${id}`);
    return response.data;
  }

  async getRoomConfig(appointmentId: number) {
    const response = await this.api.post(`/consultations/appointments/${appointmentId}/room`);
    return response.data;
  }

  async endConsultation(appointmentId: number) {
    const response = await this.api.post(`/consultations/appointments/${appointmentId}/end`);
    return response.data;
  }

  // Reviews
  async createReview(data: any) {
    const response = await this.api.post('/reviews', data);
    return response.data;
  }

  // Favorites
  async getFavorites() {
    const response = await this.api.get('/favorites');
    return response.data;
  }

  async addFavorite(medecinId: number) {
    const response = await this.api.post('/favorites', { medecin_id: medecinId });
    return response.data;
  }

  async removeFavorite(id: number) {
    const response = await this.api.delete(`/favorites/${id}`);
    return response.data;
  }

  // Profile
  async getProfile() {
    const response = await this.api.get('/patients/profile');
    return response.data;
  }

  async updateProfile(data: any) {
    const response = await this.api.put('/patients/profile', data);
    return response.data;
  }

  // Medical Records
  async getMyMedicalRecord() {
    const response = await this.api.get('/medical-records/my-record');
    return response.data;
  }

  async updateMedicalRecord(data: any) {
    const response = await this.api.put('/medical-records/my-record', data);
    return response.data;
  }

  // Payments
  async getPayments() {
    const response = await this.api.get('/payments');
    return response.data;
  }

  async initiatePayment(data: any) {
    const response = await this.api.post('/payments/initiate', data);
    return response.data;
  }

  // Notifications
  async getNotificationPreferences() {
    const response = await this.api.get('/notifications/preferences');
    return response.data;
  }

  async updateNotificationPreferences(data: any) {
    const response = await this.api.put('/notifications/preferences', data);
    return response.data;
  }
}

export default new ApiService();
