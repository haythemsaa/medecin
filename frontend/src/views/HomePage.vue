<template>
  <div class="min-h-screen bg-gradient-to-b from-teal-50 to-white">
    <!-- Hero Section -->
    <section class="py-20 px-4">
      <div class="max-w-7xl mx-auto text-center">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
          Seha Digital
        </h1>
        <p class="text-xl md:text-2xl text-gray-600 mb-8">
          La plateforme de télémédecine en Tunisie
        </p>
        <p class="text-lg text-gray-600 mb-12 max-w-3xl mx-auto">
          Consultez des médecins qualifiés depuis chez vous. Accédez à vos ordonnances,
          gérez votre dossier médical et bénéficiez d'un suivi personnalisé.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            v-if="!authStore.isAuthenticated"
            to="/register/patient"
            class="px-8 py-4 bg-teal-600 text-white text-lg font-semibold rounded-lg hover:bg-teal-700 transition-colors shadow-lg"
          >
            S'inscrire en tant que Patient
          </router-link>
          <router-link
            v-if="!authStore.isAuthenticated"
            to="/register/medecin"
            class="px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg"
          >
            Rejoindre en tant que Médecin
          </router-link>
          <router-link
            v-if="authStore.isAuthenticated"
            to="/dashboard"
            class="px-8 py-4 bg-teal-600 text-white text-lg font-semibold rounded-lg hover:bg-teal-700 transition-colors shadow-lg"
          >
            Accéder au Tableau de Bord
          </router-link>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
          <div class="p-6">
            <div class="text-4xl font-bold text-teal-600 mb-2">500+</div>
            <div class="text-gray-600">Médecins Qualifiés</div>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold text-teal-600 mb-2">10,000+</div>
            <div class="text-gray-600">Consultations Réalisées</div>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold text-teal-600 mb-2">24/7</div>
            <div class="text-gray-600">Disponibilité</div>
          </div>
          <div class="p-6">
            <div class="text-4xl font-bold text-teal-600 mb-2">4.8/5</div>
            <div class="text-gray-600">Satisfaction Patient</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-4">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-bold text-center text-gray-900 mb-16">
          Pourquoi Choisir Seha Digital ?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="text-5xl mb-4">📹</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Consultations Vidéo</h3>
            <p class="text-gray-600">
              Consultez des médecins spécialistes en visioconférence sécurisée.
              Qualité HD et connexion fiable.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="text-5xl mb-4">📋</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Ordonnances Numériques</h3>
            <p class="text-gray-600">
              Recevez vos ordonnances en format numérique avec code QR de vérification.
              Accès permanent à votre historique.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="text-5xl mb-4">🏥</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Dossier Médical Centralisé</h3>
            <p class="text-gray-600">
              Tous vos antécédents médicaux en un seul endroit.
              Partagez-les avec vos médecins en toute sécurité.
            </p>
          </div>

          <!-- Feature 4 -->
          <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="text-5xl mb-4">💳</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Paiement Sécurisé</h3>
            <p class="text-gray-600">
              Payez en ligne en toute sécurité. Compatible avec CNAM et mutuelles privées.
            </p>
          </div>

          <!-- Feature 5 -->
          <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="text-5xl mb-4">🔒</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Données Sécurisées</h3>
            <p class="text-gray-600">
              Conforme INPDP. Chiffrement de bout en bout.
              Vous contrôlez l'accès à vos données.
            </p>
          </div>

          <!-- Feature 6 -->
          <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="text-5xl mb-4">📱</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">Accessible Partout</h3>
            <p class="text-gray-600">
              Accessible depuis ordinateur, tablette ou smartphone.
              Interface responsive et intuitive.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 px-4 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-bold text-center text-gray-900 mb-16">
          Comment Ça Marche ?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-teal-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              1
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Inscrivez-vous</h3>
            <p class="text-gray-600">
              Créez votre compte gratuitement en quelques minutes
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 bg-teal-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              2
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Choisissez un Médecin</h3>
            <p class="text-gray-600">
              Parcourez les profils et choisissez votre spécialiste
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 bg-teal-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              3
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Réservez un Créneau</h3>
            <p class="text-gray-600">
              Sélectionnez un horaire qui vous convient
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 bg-teal-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              4
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Consultez</h3>
            <p class="text-gray-600">
              Recevez votre consultation et ordonnance en ligne
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Specialties Section -->
    <section class="py-20 px-4">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-bold text-center text-gray-900 mb-16">
          Nos Spécialités Médicales
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <div v-for="specialty in popularSpecialties" :key="specialty" class="bg-white p-4 rounded-lg shadow text-center hover:shadow-lg transition-shadow cursor-pointer">
            <div class="text-3xl mb-2">👨‍⚕️</div>
            <div class="text-sm font-medium text-gray-900">{{ specialty }}</div>
          </div>
        </div>

        <div class="text-center mt-8">
          <router-link
            to="/medecins"
            class="text-teal-600 hover:text-teal-700 font-semibold"
          >
            Voir tous les médecins →
          </router-link>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 bg-teal-600 text-white">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold mb-6">
          Prêt à Prendre Soin de Votre Santé ?
        </h2>
        <p class="text-xl mb-8">
          Rejoignez des milliers de Tunisiens qui font confiance à Seha Digital
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            v-if="!authStore.isAuthenticated"
            to="/register/patient"
            class="px-8 py-4 bg-white text-teal-600 text-lg font-semibold rounded-lg hover:bg-gray-100 transition-colors"
          >
            Commencer Maintenant
          </router-link>
          <router-link
            to="/medecins"
            class="px-8 py-4 bg-teal-700 text-white text-lg font-semibold rounded-lg hover:bg-teal-800 transition-colors"
          >
            Consulter les Médecins
          </router-link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 px-4">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div>
            <h3 class="text-xl font-bold text-white mb-4">Seha Digital</h3>
            <p class="text-sm">
              La première plateforme de télémédecine en Tunisie,
              conforme aux normes INPDP et validée par l'Ordre des Médecins.
            </p>
          </div>

          <div>
            <h4 class="font-semibold text-white mb-4">Patients</h4>
            <ul class="space-y-2 text-sm">
              <li><router-link to="/register/patient" class="hover:text-white">S'inscrire</router-link></li>
              <li><router-link to="/medecins" class="hover:text-white">Trouver un médecin</router-link></li>
              <li><router-link to="/login" class="hover:text-white">Se connecter</router-link></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold text-white mb-4">Médecins</h4>
            <ul class="space-y-2 text-sm">
              <li><router-link to="/register/medecin" class="hover:text-white">Rejoindre la plateforme</router-link></li>
              <li><router-link to="/login" class="hover:text-white">Espace médecin</router-link></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold text-white mb-4">Contact</h4>
            <ul class="space-y-2 text-sm">
              <li>📧 contact@sehadigital.tn</li>
              <li>📞 80 XXX XXX</li>
              <li>🏢 Tunis, Tunisie</li>
            </ul>
          </div>
        </div>

        <div class="border-t border-gray-800 pt-8 text-center text-sm">
          <p>&copy; {{ currentYear }} Seha Digital. Tous droits réservés.</p>
          <p class="mt-2 text-xs text-gray-500">
            Conforme INPDP | Validé par l'Ordre des Médecins de Tunisie | Conservation des données : 10 ans
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const currentYear = computed(() => new Date().getFullYear())

const popularSpecialties = [
  'Médecine générale',
  'Pédiatrie',
  'Gynécologie',
  'Cardiologie',
  'Dermatologie',
  'Psychiatrie',
  'ORL',
  'Ophtalmologie',
  'Pneumologie',
  'Rhumatologie'
]
</script>
