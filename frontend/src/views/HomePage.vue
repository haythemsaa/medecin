<template>
  <div class="min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">S</span>
            </div>
            <span class="text-2xl font-bold text-primary-600">Seha Digital</span>
          </div>
          <div class="flex items-center space-x-4">
            <router-link to="/medecins" class="text-gray-600 hover:text-primary-600">
              Trouver un médecin
            </router-link>
            <router-link to="/login" class="text-gray-600 hover:text-primary-600">
              Connexion
            </router-link>
            <router-link to="/register/patient" class="btn btn-primary">
              S'inscrire
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-success-50 py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-5xl font-bold text-gray-900 mb-6">
            La santé accessible à tous,<br/>
            partout en Tunisie
          </h1>
          <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Consultez un médecin qualifié depuis chez vous en moins de 24h.
            Simple, rapide et sécurisé.
          </p>
          <div class="flex justify-center space-x-4">
            <router-link to="/register/patient" class="btn btn-primary text-lg px-8 py-3">
              Réserver une consultation
            </router-link>
            <router-link to="/register/medecin" class="btn btn-secondary text-lg px-8 py-3">
              Vous êtes médecin ?
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">Comment ça marche ?</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="card text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">🔍</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">1. Trouvez un médecin</h3>
            <p class="text-gray-600">
              Recherchez par spécialité, localisation et disponibilité
            </p>
          </div>
          <div class="card text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">📅</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">2. Réservez un créneau</h3>
            <p class="text-gray-600">
              Choisissez la date et l'heure qui vous conviennent
            </p>
          </div>
          <div class="card text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">💚</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">3. Consultez en vidéo</h3>
            <p class="text-gray-600">
              Recevez votre ordonnance digitale instantanément
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <h4 class="font-bold text-lg mb-4">Seha Digital</h4>
            <p class="text-gray-400">
              La télémédecine accessible à tous en Tunisie
            </p>
          </div>
          <div>
            <h4 class="font-bold mb-4">Patients</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#">Trouver un médecin</a></li>
              <li><a href="#">Comment ça marche</a></li>
              <li><a href="#">Tarifs</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-bold mb-4">Médecins</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#">S'inscrire</a></li>
              <li><a href="#">Avantages</a></li>
              <li><a href="#">Tarification</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-bold mb-4">Support</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#">FAQ</a></li>
              <li><a href="#">Contact</a></li>
              <li><a href="#">Mentions légales</a></li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2025 Seha Digital. Tous droits réservés. Conforme INPDP.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
// Home page component
</script>
