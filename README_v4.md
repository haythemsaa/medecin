# 🏥 Seha Digital - Plateforme de Télémédecine Tunisienne

**Version 4.0** - Plateforme complète de télémédecine avec parité concurrentielle

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)](https://vuejs.org)
[![Tests](https://img.shields.io/badge/tests-80%2B-success.svg)]()

---

## 🎯 Vue d'Ensemble

Seha Digital est une plateforme de télémédecine moderne conçue spécifiquement pour le marché tunisien, offrant une parité fonctionnelle complète avec les leaders internationaux (Doctolib, Practo, Vezeeta) tout en étant adaptée aux besoins locaux.

### ✨ Caractéristiques Principales

- 🚨 **Consultation Urgente** - Consultez un médecin en moins d'1 heure
- 💊 **Ordonnances Renouvelables** - Renouvellement sans consultation
- 📍 **Géolocalisation** - Trouvez des médecins près de vous
- 📋 **Questionnaires Pré-Consultation** - Gain de temps pour tous
- 💰 **Paiement Multi-Méthodes** - e-Dinar, Mobile Money, Carte, Cash
- 📅 **Intégration Calendrier** - Google Calendar & Outlook
- 💊 **Rappels Médicaments** - Amélioration de l'adhérence
- 🎥 **Vidéo Consultation** - WebRTC sécurisé
- 📱 **Application Mobile-Ready** - PWA compatible

---

## 📊 Statistiques Techniques

### Backend
- **Framework**: Laravel 11 (PHP 8.3)
- **Base de données**: PostgreSQL 16
- **Cache**: Redis 7
- **Files d'attente**: Redis Queue
- **Tests**: 80+ tests automatisés
- **API**: 100+ endpoints RESTful

### Frontend
- **Framework**: Vue.js 3 + TypeScript
- **Build**: Vite
- **State Management**: Pinia
- **UI**: Tailwind CSS
- **Maps**: Leaflet + OpenStreetMap

### Lignes de Code
- **Backend**: ~15,000 lignes
- **Frontend**: ~8,000 lignes
- **Tests**: ~3,000 lignes
- **Documentation**: ~5,000 lignes
- **Total**: ~31,000 lignes

---

## 🚀 Installation Rapide

### Prérequis

```bash
- PHP >= 8.3
- PostgreSQL >= 16
- Redis >= 7
- Node.js >= 18
- Composer
- npm/yarn
```

### 1. Cloner le Projet

```bash
git clone https://github.com/haythemsaa/medecin.git sehadigital
cd sehadigital
```

### 2. Backend Setup

```bash
cd backend

# Installer les dépendances
composer install

# Configurer .env
cp .env.example .env
nano .env  # Configurer DB, Redis, etc.

# Générer la clé
php artisan key:generate

# Migrations & seeders
php artisan migrate --seed

# Lancer le serveur
php artisan serve
```

### 3. Frontend Setup

```bash
cd frontend

# Installer les dépendances
npm install

# Lancer le dev server
npm run dev
```

---

## 📚 Documentation Complète

### Guides Principaux

1. **[API Documentation](API_DOCUMENTATION_v4.md)** - 100+ endpoints
2. **[OAuth Setup Guide](OAUTH_SETUP.md)** - Google & Outlook
3. **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Production deployment
4. **[Features v4.0](FEATURES_v4.md)** - Complete feature list
5. **[Competitive Analysis](COMPETITIVE_ANALYSIS.md)** - Market analysis

---

## 🧪 Tests

### Lancer les Tests

```bash
cd backend
php artisan test --coverage
```

**80+ tests automatisés** couvrant toutes les fonctionnalités critiques.

---

## 🔐 Sécurité & Conformité

- ✅ **INPDP Compliant** (Protection des données Tunisie)
- ✅ Encryption des données sensibles
- ✅ Consentement explicite
- ✅ Audit logs
- ✅ HTTPS obligatoire

---

## 📞 Support

- **Email**: support@sehadigital.tn
- **Documentation**: Voir fichiers MD dans le repo
- **Issues**: GitHub Issues

---

**Fait avec ❤️ pour la e-santé en Tunisie**

*Version 4.0 - Novembre 2025*
