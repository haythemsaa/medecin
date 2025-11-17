import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/HomePage.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/LoginPage.vue')
  },
  {
    path: '/register/patient',
    name: 'PatientRegister',
    component: () => import('@/views/auth/PatientRegister.vue')
  },
  {
    path: '/register/medecin',
    name: 'MedecinRegister',
    component: () => import('@/views/auth/MedecinRegister.vue')
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/DashboardPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/medecins',
    name: 'MedecinsList',
    component: () => import('@/views/medecins/MedecinsList.vue')
  },
  {
    path: '/medecins/:id',
    name: 'MedecinProfile',
    component: () => import('@/views/medecins/MedecinProfile.vue')
  },
  {
    path: '/appointments',
    name: 'Appointments',
    component: () => import('@/views/appointments/AppointmentsList.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/appointments/:id',
    name: 'AppointmentDetail',
    component: () => import('@/views/appointments/AppointmentDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/consultations/:appointmentId',
    name: 'VideoConsultation',
    component: () => import('@/views/consultations/VideoConsultationPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/consultations/:id/notes',
    name: 'ConsultationNotes',
    component: () => import('@/views/consultations/ConsultationNotesPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/messages',
    name: 'Messages',
    component: () => import('@/views/messages/MessagesPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('@/views/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/medical-record',
    name: 'MedicalRecord',
    component: () => import('@/views/medical-records/MedicalRecordPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/prescriptions',
    name: 'Prescriptions',
    component: () => import('@/views/prescriptions/PrescriptionsPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile/patient',
    name: 'PatientProfile',
    component: () => import('@/views/profile/PatientProfilePage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile/medecin',
    name: 'MedecinProfilePage',
    component: () => import('@/views/profile/MedecinProfilePage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/medecin/availability',
    name: 'AvailabilityManagement',
    component: () => import('@/views/medecins/AvailabilityManagement.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/medecin/analytics',
    name: 'DashboardAnalytics',
    component: () => import('@/views/analytics/DashboardAnalytics.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresAdmin && authStore.user?.role !== 'admin') {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
