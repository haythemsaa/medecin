# Seha Digital - Plateforme de Télémédecine Tunisie

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)
![License](https://img.shields.io/badge/license-Proprietary-yellow.svg)

**Seha Digital** est une plateforme complète de télémédecine conçue spécifiquement pour le marché tunisien, permettant aux patients de consulter des médecins qualifiés depuis chez eux via vidéo ou téléphone.

## 🏥 Description du Projet

La plateforme répond aux enjeux critiques du système de santé tunisien :
- **Déserts médicaux** : 65% des spécialistes concentrés dans le Grand Tunis
- **Files d'attente** : Délais de 3-6 mois pour une consultation spécialisée
- **Discontinuité des soins** : Absence de dossier médical centralisé

### Proposition de Valeur

**Pour les patients :**
- ✅ Accès 24/7 à des consultations qualifiées depuis leur domicile
- ✅ Réduction des coûts et du temps de déplacement
- ✅ Dossier médical numérique unifié et sécurisé
- ✅ Tarifs transparents (50-100 TND)

**Pour les médecins :**
- ✅ Élargissement de la patientèle au-delà de la zone géographique
- ✅ Revenus complémentaires flexibles
- ✅ Outils professionnels conformes à la réglementation
- ✅ Commission plateforme : 15%

## 🏗️ Architecture

### Stack Technologique

**Backend:**
- Laravel 11 (PHP 8.3)
- PostgreSQL 16
- Redis 7
- Laravel Sanctum (Authentication)
- Laravel Horizon (Queue Management)

**Frontend:**
- Vue.js 3 + TypeScript
- Tailwind CSS + HeadlessUI
- Pinia (State Management)
- Vite (Build Tool)

**Infrastructure:**
- Docker + Docker Compose
- Nginx
- WebRTC (Téléconsultation vidéo)

### Modules Principaux

1. **Gestion des Utilisateurs** - Inscription, authentification 2FA, profils patients/médecins
2. **Téléconsultation** - Vidéo HD (WebRTC), salle d'attente virtuelle, outils de consultation
3. **Rendez-vous** - Recherche de médecins, prise de RDV, gestion d'agenda
4. **Dossier Médical** - Stockage sécurisé, consentements, historique complet
5. **Paiement** - Multi-méthodes (carte, e-dinar, mobile), remboursements automatiques
6. **Communication** - Messagerie E2E chiffrée, notifications intelligentes
7. **Administration** - Backoffice complet, modération, analytics
8. **Reporting** - KPIs temps réel, surveillance épidémiologique

## ✨ Nouvelles Fonctionnalités (v1.2)

### Frontend Complété

**Authentification & Accueil**
- ✅ **Page d'accueil** - Landing page complète avec présentation des fonctionnalités
- ✅ **Pages d'authentification** - Login, inscription patient, inscription médecin avec upload de documents
- ✅ **Navigation globale** - Barre de navigation responsive avec menu utilisateur et notifications
- ✅ **Dashboards dynamiques** - Tableau de bord patient et médecin avec statistiques en temps réel

**Fonctionnalités Patients**
- ✅ **Recherche de médecins** - Filtres avancés (spécialité, prix, note, localisation)
- ✅ **Profil médecin** - Affichage complet avec avis, biographie, réservation
- ✅ **Gestion rendez-vous** - Liste, détails, annulation avec politique de remboursement
- ✅ **Dossier médical** - Gestion complète avec allergies, maladies chroniques, traitements
- ✅ **Contrôle d'accès** - Système de consentements et historique des accès
- ✅ **Ordonnances** - Visualisation, filtres, téléchargement PDF avec QR code
- ✅ **Profil patient** - Édition complète des informations personnelles et santé
- ✅ **Système d'avis** - Évaluation détaillée sur 5 critères

**Fonctionnalités Médecins**
- ✅ **Profil professionnel** - Gestion complète des informations médicales et cabinet
- ✅ **Notes de consultation** - Interface complète avec signes vitaux et diagnostic
- ✅ **Création d'ordonnances** - Multi-médicaments avec génération PDF automatique
- ✅ **Historique médical** - Accès au dossier patient avec consentement
- ✅ **Statistiques** - Vue d'ensemble des consultations et revenus

**Fonctionnalités Admin**
- ✅ **Dashboard admin** - Validation des médecins, gestion des utilisateurs
- ✅ **Statistiques globales** - Analytics et KPIs en temps réel
- ✅ **Messagerie sécurisée** - Chat chiffré E2E entre patients et médecins
- ✅ **Notifications** - Système de toast pour les alertes

**Composants Réutilisables**
- ✅ **LoadingSpinner** - Indicateur de chargement configurable
- ✅ **Modal** - Fenêtre modale responsive avec slots
- ✅ **Card** - Composant carte flexible pour l'affichage
- ✅ **Badge** - Badges de statut avec variantes de couleurs
- ✅ **Button** - Bouton avec états loading et variantes
- ✅ **EmptyState** - État vide personnalisable
- ✅ **ToastNotification** - Notifications toast avec animations

### Backend Enrichi
- ✅ **Services PDF** - Génération d'ordonnances et factures avec QR codes
- ✅ **Notifications SMS/Email** - Confirmations, rappels, annulations
- ✅ **WebRTC Service** - Infrastructure vidéo pour téléconsultations
- ✅ **API Consultations** - Gestion complète des consultations vidéo avec notes
- ✅ **API Dossier Médical** - Accès sécurisé avec système de consentements
- ✅ **API Prescriptions** - Création, visualisation et téléchargement PDF
- ✅ **API Admin** - Dashboard, validation médecins, statistiques
- ✅ **API Messages** - Messagerie chiffrée E2E
- ✅ **PrescriptionController** - Gestion complète des ordonnances avec vérification QR

## 📦 Installation

### Prérequis

- Docker Desktop 20.10+
- Docker Compose 2.0+
- Git

### Installation avec Docker (Recommandé)

1. **Cloner le repository**
```bash
git clone https://github.com/haythemsaa/medecin.git
cd medecin
```

2. **Créer le fichier .env pour le backend**
```bash
cp backend/.env.example backend/.env
```

3. **Lancer les containers Docker**
```bash
docker-compose up -d
```

4. **Installer les dépendances backend**
```bash
docker-compose exec backend composer install
docker-compose exec backend php artisan key:generate
```

5. **Exécuter les migrations**
```bash
docker-compose exec backend php artisan migrate
```

6. **Installer les dépendances frontend**
```bash
docker-compose exec frontend npm install
```

7. **Accéder à l'application**
- Frontend : http://localhost:3000
- Backend API : http://localhost:8000/api
- Health Check : http://localhost:8000/api/health

### Installation Manuelle (Sans Docker)

#### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

#### Frontend

```bash
cd frontend
npm install
npm run dev
```

## 🗄️ Base de Données

### Tables Principales

- **users** - Comptes utilisateurs (patients, médecins, admins)
- **patients** - Profils patients avec informations médicales
- **medecins** - Profils médecins avec validations
- **medical_records** - Dossiers médicaux patients
- **availabilities** - Disponibilités des médecins
- **appointments** - Rendez-vous de consultation
- **consultations** - Détails des consultations effectuées
- **prescriptions** - Ordonnances numériques avec QR code
- **payments** - Paiements et remboursements
- **reviews** - Avis et évaluations des consultations
- **conversations** / **messages** - Messagerie sécurisée
- **medical_consents** - Consentements d'accès au dossier médical
- **audit_logs** - Traçabilité exhaustive des accès

### Schéma Complet

Voir le document de spécifications : `Cahier_Specifications_Telemedicine_Tunisie_COMPLET.md`

## 🔐 Sécurité et Conformité

### Sécurité

- **Authentification** : JWT tokens avec refresh token rotation, 2FA optionnel
- **Chiffrement** : HTTPS (TLS 1.3), AES-256 pour les données au repos
- **Données médicales** : Chiffrement additionnel, traçabilité exhaustive
- **RBAC** : Contrôle d'accès basé sur les rôles

### Conformité Réglementaire Tunisienne

✅ **INPDP** (Instance Nationale de Protection des Données Personnelles)
- Hébergement des données 100% en Tunisie
- Consentements documentés
- Droits utilisateurs (accès, rectification, suppression, portabilité)
- Conservation des données : 10 ans minimum

✅ **Ordre National des Médecins de Tunisie (ONMT)**
- Validation des qualifications médicales
- Vérification annuelle des documents (Ordre, RCP)
- Respect du code de déontologie

✅ **Ministère de la Santé**
- Conformité avec les régulations sanitaires
- Protocoles de télémédecine stricts

## 📱 API Documentation

### Endpoints Principaux

#### Authentification
```
POST   /api/auth/login              - Connexion
POST   /api/auth/logout             - Déconnexion
POST   /api/auth/verify-2fa         - Vérification 2FA
GET    /api/auth/me                 - Utilisateur connecté
POST   /api/auth/forgot-password    - Mot de passe oublié
POST   /api/auth/reset-password     - Réinitialiser mot de passe
```

#### Patients
```
POST   /api/patients/register       - Inscription patient
GET    /api/patients/profile        - Profil patient
PUT    /api/patients/profile        - Mise à jour profil
```

#### Médecins
```
POST   /api/medecins/register       - Inscription médecin
GET    /api/medecins/search         - Recherche de médecins
GET    /api/medecins/{id}           - Profil médecin
GET    /api/medecins/profile        - Profil médecin connecté
PUT    /api/medecins/profile        - Mise à jour profil
```

#### Rendez-vous
```
GET    /api/appointments            - Liste des rendez-vous
POST   /api/appointments            - Créer un rendez-vous
GET    /api/appointments/{id}       - Détails rendez-vous
POST   /api/appointments/{id}/cancel - Annuler rendez-vous
```

#### Consultations (Nouveau)
```
GET    /api/consultations/{id}                    - Détails consultation
PUT    /api/consultations/{id}/notes              - Mettre à jour notes (médecin)
POST   /api/consultations/{id}/prescriptions      - Créer ordonnance
POST   /api/consultations/{id}/report-issue       - Signaler problème technique
POST   /api/consultations/appointments/{id}/room  - Obtenir config WebRTC
POST   /api/consultations/appointments/{id}/end   - Terminer consultation
```

#### Dossier Médical
```
GET    /api/medical-records/my-record             - Mon dossier médical (patient)
PUT    /api/medical-records/my-record             - Mettre à jour dossier
GET    /api/medical-records/consents              - Mes consentements
POST   /api/medical-records/consents              - Créer consentement
DELETE /api/medical-records/consents/{id}         - Révoquer consentement
GET    /api/medical-records/access-history        - Historique d'accès
GET    /api/medical-records/patients/{id}         - Accéder au dossier (médecin)
```

#### Ordonnances (Nouveau)
```
GET    /api/prescriptions/my-prescriptions        - Mes ordonnances (patient)
GET    /api/prescriptions/medecin-prescriptions   - Mes ordonnances (médecin)
GET    /api/prescriptions/{id}                    - Détails d'une ordonnance
GET    /api/prescriptions/{id}/download           - Télécharger PDF
POST   /api/prescriptions/verify                  - Vérifier ordonnance par QR
```

#### Administration (Nouveau)
```
GET    /api/admin/dashboard                       - Dashboard admin
GET    /api/admin/medecins/pending                - Médecins en attente
GET    /api/admin/medecins/{id}                   - Détails médecin
POST   /api/admin/medecins/{id}/validate          - Valider/rejeter médecin
GET    /api/admin/users                           - Liste utilisateurs
PUT    /api/admin/users/{id}/status               - Modifier statut utilisateur
GET    /api/admin/statistics                      - Statistiques globales
```

#### Messages (Nouveau)
```
GET    /api/messages/conversations                - Mes conversations
POST   /api/messages/conversations                - Créer conversation
GET    /api/messages/conversations/{id}/messages  - Messages d'une conversation
POST   /api/messages/conversations/{id}/messages  - Envoyer message
GET    /api/messages/unread-count                 - Nombre de messages non lus
```

#### Health Check
```
GET    /api/health                  - État de l'API
```

### Exemples de Requêtes

#### Connexion Patient
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "patient@example.com",
    "password": "password123"
  }'
```

#### Recherche de Médecins
```bash
curl -X GET "http://localhost:8000/api/medecins/search?speciality=Cardiologie&min_rating=4" \
  -H "Accept: application/json"
```

## 🧪 Tests

### Backend
```bash
docker-compose exec backend php artisan test
```

### Frontend
```bash
docker-compose exec frontend npm run test
```

## 📊 Développement

### Structure du Projet

```
medecin/
├── backend/                    # Application Laravel
│   ├── app/
│   │   ├── Http/Controllers/  # Contrôleurs API
│   │   ├── Models/            # Modèles Eloquent
│   │   └── Services/          # Logique métier
│   ├── database/
│   │   ├── migrations/        # Migrations de base de données
│   │   ├── seeders/           # Seeders
│   │   └── factories/         # Factories
│   ├── routes/
│   │   └── api.php            # Routes API
│   └── composer.json
│
├── frontend/                   # Application Vue.js
│   ├── src/
│   │   ├── components/        # Composants réutilisables
│   │   │   ├── Badge.vue
│   │   │   ├── Button.vue
│   │   │   ├── Card.vue
│   │   │   ├── EmptyState.vue
│   │   │   ├── LoadingSpinner.vue
│   │   │   ├── Modal.vue
│   │   │   ├── NavigationBar.vue
│   │   │   ├── ToastNotification.vue
│   │   │   └── VideoConsultation.vue
│   │   ├── views/             # Pages
│   │   │   ├── auth/          # Pages d'authentification
│   │   │   ├── admin/         # Administration
│   │   │   ├── appointments/  # Rendez-vous
│   │   │   ├── consultations/ # Consultations
│   │   │   ├── medecins/      # Médecins
│   │   │   ├── medical-records/ # Dossiers médicaux
│   │   │   ├── messages/      # Messagerie
│   │   │   ├── prescriptions/ # Ordonnances
│   │   │   ├── profile/       # Profils utilisateurs
│   │   │   ├── DashboardPage.vue
│   │   │   └── HomePage.vue
│   │   ├── composables/       # Composables Vue
│   │   ├── stores/            # Stores Pinia
│   │   ├── services/          # Services API
│   │   ├── router/            # Configuration routeur
│   │   ├── utils/             # Utilitaires
│   │   └── types/             # Types TypeScript
│   └── package.json
│
├── docker/                     # Configuration Docker
│   ├── Dockerfile.backend
│   ├── Dockerfile.frontend
│   ├── nginx/
│   └── php/
│
├── docker-compose.yml
└── README.md
```

### Commandes Utiles

#### Backend
```bash
# Générer une migration
docker-compose exec backend php artisan make:migration create_table_name

# Créer un modèle
docker-compose exec backend php artisan make:model ModelName

# Créer un contrôleur
docker-compose exec backend php artisan make:controller ControllerName

# Lancer les queues
docker-compose exec backend php artisan queue:work

# Nettoyer le cache
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan config:clear
```

#### Frontend
```bash
# Build de production
docker-compose exec frontend npm run build

# Lint
docker-compose exec frontend npm run lint
```

## 🚀 Déploiement

### Production

Pour le déploiement en production, référez-vous au document de spécifications complet qui détaille :
- Les phases de déploiement (Bêta fermée → Nationale)
- L'infrastructure hébergée en Tunisie
- La configuration Kubernetes
- Les procédures de backup
- Le monitoring et les alertes

## 📈 Objectifs Année 1

- 50,000 patients inscrits
- 300 médecins actifs
- 100,000 consultations effectuées
- Satisfaction >4.2/5
- Disponibilité >99.5%
- CA : 1M TND

## 🤝 Contribution

Ce projet est propriétaire. Pour toute question ou suggestion, veuillez contacter l'équipe de développement.

## 📄 License

Copyright © 2025 Seha Digital. Tous droits réservés.

## 📞 Support

- **Email** : contact@sehadigital.tn
- **Documentation** : Voir `Cahier_Specifications_Telemedicine_Tunisie_COMPLET.md`
- **Issues** : Utiliser le système de tickets interne

## 🙏 Remerciements

Développé conformément aux spécifications détaillées du cahier des charges de 250+ pages pour le marché tunisien de la télémédecine.

---

**Seha Digital** - *La santé accessible à tous, partout en Tunisie* 🏥💙
