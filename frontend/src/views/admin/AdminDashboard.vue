<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Navigation -->
    <nav class="bg-gray-900 text-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <div class="flex items-center space-x-2">
              <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl">S</span>
              </div>
              <span class="text-xl font-bold">Seha Admin</span>
            </div>

            <div class="hidden md:flex space-x-4">
              <button
                @click="currentTab = 'dashboard'"
                :class="['px-3 py-2 rounded-lg text-sm font-medium', currentTab === 'dashboard' ? 'bg-gray-800' : 'hover:bg-gray-800']"
              >
                Dashboard
              </button>
              <button
                @click="currentTab = 'validations'"
                :class="['px-3 py-2 rounded-lg text-sm font-medium', currentTab === 'validations' ? 'bg-gray-800' : 'hover:bg-gray-800']"
              >
                Validations
                <span v-if="stats.medecins?.pending_validation > 0" class="ml-2 px-2 py-0.5 bg-red-500 rounded-full text-xs">
                  {{ stats.medecins.pending_validation }}
                </span>
              </button>
              <button
                @click="currentTab = 'users'"
                :class="['px-3 py-2 rounded-lg text-sm font-medium', currentTab === 'users' ? 'bg-gray-800' : 'hover:bg-gray-800']"
              >
                Utilisateurs
              </button>
              <button
                @click="currentTab = 'statistics'"
                :class="['px-3 py-2 rounded-lg text-sm font-medium', currentTab === 'statistics' ? 'bg-gray-800' : 'hover:bg-gray-800']"
              >
                Statistiques
              </button>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <span class="text-sm">Admin</span>
            <button @click="handleLogout" class="px-4 py-2 bg-gray-800 rounded-lg text-sm hover:bg-gray-700">
              Déconnexion
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Dashboard Tab -->
      <div v-if="currentTab === 'dashboard'" class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de bord administrateur</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <!-- Users Stats -->
          <div class="card">
            <h3 class="text-sm text-gray-600 mb-2">Utilisateurs totaux</h3>
            <p class="text-3xl font-bold text-gray-900">{{ stats.users?.total || 0 }}</p>
            <div class="mt-2 text-sm text-gray-500">
              {{ stats.users?.patients || 0 }} patients • {{ stats.users?.medecins || 0 }} médecins
            </div>
          </div>

          <!-- Appointments Stats -->
          <div class="card">
            <h3 class="text-sm text-gray-600 mb-2">Consultations ce mois</h3>
            <p class="text-3xl font-bold text-primary-600">{{ stats.appointments?.this_month || 0 }}</p>
            <div class="mt-2 text-sm text-green-600">
              {{ stats.appointments?.today || 0 }} aujourd'hui
            </div>
          </div>

          <!-- Pending Validations -->
          <div class="card">
            <h3 class="text-sm text-gray-600 mb-2">En attente de validation</h3>
            <p class="text-3xl font-bold text-orange-600">{{ stats.medecins?.pending_validation || 0 }}</p>
            <div class="mt-2 text-sm text-gray-500">
              Médecins à valider
            </div>
          </div>

          <!-- Revenue Stats -->
          <div class="card">
            <h3 class="text-sm text-gray-600 mb-2">Revenus ce mois</h3>
            <p class="text-3xl font-bold text-green-600">{{ (stats.revenue?.this_month || 0).toFixed(2) }} TND</p>
            <div class="mt-2 text-sm text-gray-500">
              Commission plateforme
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
          <h2 class="text-xl font-semibold mb-4">Activité récente</h2>
          <p class="text-gray-600">Statistiques et métriques en temps réel</p>
        </div>
      </div>

      <!-- Validations Tab -->
      <div v-if="currentTab === 'validations'" class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Validation des médecins</h1>

        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
        </div>

        <div v-else-if="pendingMedecins.length === 0" class="card text-center py-12">
          <span class="text-6xl mb-4 block">✅</span>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune validation en attente</h3>
          <p class="text-gray-600">Tous les dossiers ont été traités</p>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="medecin in pendingMedecins"
            :key="medecin.id"
            class="card hover:shadow-lg transition-shadow"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="text-lg font-semibold">
                  Dr. {{ medecin.first_name }} {{ medecin.last_name }}
                </h3>
                <p class="text-sm text-primary-600">{{ medecin.speciality }}</p>

                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                  <div>
                    <p class="text-gray-500">Numéro Ordre</p>
                    <p class="font-medium">{{ medecin.ordre_number }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Expérience</p>
                    <p class="font-medium">{{ medecin.years_of_experience }} ans</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium text-xs">{{ medecin.user?.email }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Date demande</p>
                    <p class="font-medium text-xs">{{ formatDate(medecin.created_at) }}</p>
                  </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                  <a
                    v-if="medecin.cin_file_recto"
                    :href="`/storage/${medecin.cin_file_recto}`"
                    target="_blank"
                    class="text-xs bg-gray-100 px-3 py-1 rounded hover:bg-gray-200"
                  >
                    📄 CIN Recto
                  </a>
                  <a
                    v-if="medecin.ordre_certificate"
                    :href="`/storage/${medecin.ordre_certificate}`"
                    target="_blank"
                    class="text-xs bg-gray-100 px-3 py-1 rounded hover:bg-gray-200"
                  >
                    📄 Certificat Ordre
                  </a>
                  <a
                    v-if="medecin.diploma_file"
                    :href="`/storage/${medecin.diploma_file}`"
                    target="_blank"
                    class="text-xs bg-gray-100 px-3 py-1 rounded hover:bg-gray-200"
                  >
                    📄 Diplôme
                  </a>
                  <a
                    v-if="medecin.rcp_attestation"
                    :href="`/storage/${medecin.rcp_attestation}`"
                    target="_blank"
                    class="text-xs bg-gray-100 px-3 py-1 rounded hover:bg-gray-200"
                  >
                    📄 RCP
                  </a>
                </div>
              </div>

              <div class="ml-4 flex flex-col space-y-2">
                <button
                  @click="validateMedecin(medecin.id, 'validated')"
                  class="btn btn-success text-sm"
                >
                  ✓ Valider
                </button>
                <button
                  @click="validateMedecin(medecin.id, 'rejected')"
                  class="btn bg-red-600 text-white hover:bg-red-700 text-sm"
                >
                  ✗ Rejeter
                </button>
                <button
                  @click="validateMedecin(medecin.id, 'incomplete')"
                  class="btn btn-secondary text-sm"
                >
                  ⚠ Incomplet
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Tab -->
      <div v-if="currentTab === 'users'" class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Gestion des utilisateurs</h1>

        <!-- Filters -->
        <div class="card">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
              <select v-model="userFilters.role" class="input">
                <option value="">Tous</option>
                <option value="patient">Patients</option>
                <option value="medecin">Médecins</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
              <select v-model="userFilters.status" class="input">
                <option value="">Tous</option>
                <option value="active">Actif</option>
                <option value="inactive">Inactif</option>
                <option value="suspended">Suspendu</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
              <input v-model="userFilters.search" type="text" class="input" placeholder="Email ou téléphone" />
            </div>
          </div>
          <button @click="fetchUsers" class="btn btn-primary mt-4">Appliquer les filtres</button>
        </div>

        <p class="text-sm text-gray-600">Total: {{ users.length }} utilisateurs</p>
      </div>

      <!-- Statistics Tab -->
      <div v-if="currentTab === 'statistics'" class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Statistiques détaillées</h1>
        <p class="text-gray-600">Graphiques et analyses en cours de développement</p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const loading = ref(false)
const currentTab = ref('dashboard')

const stats = ref<any>({})
const pendingMedecins = ref<any[]>([])
const users = ref<any[]>([])

const userFilters = ref({
  role: '',
  status: '',
  search: ''
})

async function fetchStats() {
  try {
    const response = await api.get('/admin/dashboard')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

async function fetchPendingValidations() {
  loading.value = true
  try {
    const response = await api.get('/admin/medecins/pending')
    pendingMedecins.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching validations:', error)
  } finally {
    loading.value = false
  }
}

async function validateMedecin(medecinId: number, status: string) {
  const notes = status !== 'validated' ? prompt('Notes (optionnel):') : ''
  if (status !== 'validated' && notes === null) return

  try {
    await api.post(`/admin/medecins/${medecinId}/validate`, { status, notes })
    alert(`Médecin ${status === 'validated' ? 'validé' : 'traité'} avec succès`)
    await fetchPendingValidations()
    await fetchStats()
  } catch (error: any) {
    alert(error.response?.data?.message || 'Erreur')
  }
}

async function fetchUsers() {
  try {
    const response = await api.get('/admin/users', { params: userFilters.value })
    users.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

function formatDate(dateString: string): string {
  return new Date(dateString).toLocaleDateString('fr-FR')
}

function handleLogout() {
  router.push('/login')
}

onMounted(() => {
  fetchStats()
  fetchPendingValidations()
})
</script>
