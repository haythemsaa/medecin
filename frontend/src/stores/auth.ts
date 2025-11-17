import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import type { User } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isPatient = computed(() => user.value?.role === 'patient')
  const isMedecin = computed(() => user.value?.role === 'medecin')

  async function login(email: string, password: string) {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/auth/login', { email, password })

      if (response.data.requires_2fa) {
        return { requires_2fa: true, user_id: response.data.user_id }
      }

      token.value = response.data.access_token
      user.value = response.data.user
      localStorage.setItem('token', response.data.access_token)

      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erreur de connexion'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  async function checkAuth() {
    if (!token.value) return

    try {
      const response = await api.get('/auth/me')
      user.value = response.data
    } catch (err) {
      // Token invalid, clear auth
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  async function register(data: any, role: 'patient' | 'medecin') {
    loading.value = true
    error.value = null

    try {
      const endpoint = role === 'patient' ? '/patients/register' : '/medecins/register'
      const response = await api.post(endpoint, data)

      if (role === 'patient' && response.data.access_token) {
        token.value = response.data.access_token
        user.value = response.data.user
        localStorage.setItem('token', response.data.access_token)
      }

      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erreur d\'inscription'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    isPatient,
    isMedecin,
    login,
    logout,
    checkAuth,
    register
  }
})
