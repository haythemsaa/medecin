# Seha Digital - Plateforme de Télémédecine Tunisie 🏥

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)
![TypeScript](https://img.shields.io/badge/TypeScript-5.0-blue.svg)
![License](https://img.shields.io/badge/license-Proprietary-yellow.svg)

**Seha Digital** est une plateforme complète et moderne de télémédecine conçue spécifiquement pour le marché tunisien, permettant aux patients de consulter des médecins qualifiés depuis chez eux via vidéo ou en cabinet.

## 🌟 Points Forts

- ✅ **Production-Ready** - Code testé, sécurisé et optimisé
- ✅ **100% Conforme INPDP** - Protection des données personnelles tunisiennes
- ✅ **Architecture Scalable** - Redis caching, job queues, observers
- ✅ **UX Moderne** - Interface intuitive en Vue.js 3 + TypeScript
- ✅ **Tests Complets** - 50+ tests d'intégration
- ✅ **Multi-paiement** - Carte, e-Dinar, Mobile Money, espèces

## 🚀 Fonctionnalités Complètes (v2.0)

### 👨‍⚕️ Pour les Médecins

**Gestion de Cabinet**
- ✅ Profil professionnel complet avec validation Ordre des Médecins
- ✅ Gestion des disponibilités avec détection de chevauchement
- ✅ Tarification personnalisable par type de consultation
- ✅ Dashboard quotidien avec programme du jour
- ✅ Statistiques et analytics avancés (revenus, satisfaction, consultations)

**Consultations**
- ✅ Interface de consultation avec signes vitaux
- ✅ Création d'ordonnances multi-médicaments
- ✅ Accès au dossier médical avec consentement
- ✅ Notes de consultation détaillées
- ✅ Historique patient complet
- ✅ Téléconsultation vidéo HD (WebRTC)

**Analytics & Rapports**
- ✅ 8 types d'analytics (revenus, consultations, patients, satisfaction, tendances, diagnostics)
- ✅ Graphiques interactifs (bar, line, pie, stats)
- ✅ Filtres par période (7j, 30j, 3m, 1an)
- ✅ Export des données (CSV/JSON)
- ✅ Indicateurs de performance en temps réel

### 👨‍💼 Pour les Patients

**Recherche & Booking**
- ✅ Recherche avancée avec 10+ filtres
- ✅ Autocomplétion intelligente
- ✅ Tri par note, prix, expérience, disponibilité
- ✅ Filtres: spécialité, gouvernorat, langues, prix, note, disponibilité
- ✅ Système de favoris pour médecins préférés
- ✅ Cartes médecins avec informations clés

**Dossier Médical Numérique**
- ✅ Allergies et maladies chroniques
- ✅ Traitements en cours et vaccinations
- ✅ Calcul IMC automatique
- ✅ Gestion des consentements d'accès
- ✅ Historique des accès avec IP tracking (INPDP)
- ✅ Conservation 10 ans conforme réglementation

**Ordonnances & Prescriptions**
- ✅ Liste avec filtres actif/expiré
- ✅ Téléchargement PDF avec QR code de vérification
- ✅ Détails complets (médicaments, dosage, durée)
- ✅ Historique complet
- ✅ Recherche et tri

**Rendez-vous & Consultations**
- ✅ Prise de RDV en ligne
- ✅ Annulation avec politique de remboursement
- ✅ Rappels automatiques (24h et 1h avant)
- ✅ Statut en temps réel
- ✅ Historique complet
- ✅ Notes et avis post-consultation

**Dashboard Personnel**
- ✅ Prochain rendez-vous en évidence
- ✅ Statistiques personnelles (consultations, prescriptions, médecins favoris)
- ✅ Actions rapides (trouver médecin, RDV, prescriptions, dossier)
- ✅ RDV à venir avec détails
- ✅ Prescriptions récentes
- ✅ Conseils santé

### 🏦 Système de Paiement

**Multi-méthodes de Paiement**
- ✅ Carte bancaire (Visa/Mastercard)
- ✅ e-Dinar Tunisie
- ✅ Mobile Money (Tunisie Telecom, Orange Money)
- ✅ Espèces (confirmation par médecin)

**Gestion des Transactions**
- ✅ Statuts en temps réel (pending, completed, failed, cancelled)
- ✅ Callbacks pour passerelles de paiement
- ✅ Système de remboursement complet
- ✅ Workflow d'approbation admin
- ✅ Historique et export des paiements

### 📊 Système d'Avis et Notation

**Évaluation Multi-critères**
- ✅ 5 critères de notation: professionnalisme, écoute, explications, ponctualité, efficacité
- ✅ Note globale automatique
- ✅ Commentaires détaillés
- ✅ Fenêtre d'édition de 7 jours
- ✅ Prévention des doublons
- ✅ Signalement des avis inappropriés
- ✅ Mise à jour automatique de la note du médecin

### 🔔 Notifications Intelligentes

**Canaux de Communication**
- ✅ Email avec templates HTML
- ✅ SMS
- ✅ Notifications push

**Types de Notifications**
- ✅ Rappels de rendez-vous (timing personnalisable)
- ✅ Confirmations et annulations
- ✅ Nouveaux messages
- ✅ Prescriptions disponibles
- ✅ Rappels d'avis
- ✅ Emails marketing (opt-in)

**Préférences Utilisateur**
- ✅ Interface de gestion complète
- ✅ Contrôle par canal et par type
- ✅ Test de notifications
- ✅ Choix du timing des rappels (24h, 12h, 6h, 3h, 1h)

### 📤 Export de Données

**Types d'Export**
- ✅ Rendez-vous (avec filtres date/statut)
- ✅ Consultations (historique complet)
- ✅ Paiements (avec calculs de revenus)
- ✅ Avis (analytics de satisfaction)

**Formats**
- ✅ CSV avec en-têtes français
- ✅ JSON structuré
- ✅ Téléchargement direct
- ✅ Noms de fichiers horodatés

### 🛡️ Système de Favoris

- ✅ Marquer des médecins favoris
- ✅ Notes personnelles sur chaque médecin
- ✅ Accès rapide depuis le dashboard
- ✅ Vérification statut favori en temps réel

### 👑 Administration

**Dashboard Administrateur**
- ✅ Stats globales (utilisateurs, médecins, RDV, revenus)
- ✅ Graphiques visuels par statut et rôle
- ✅ Alertes de validation en attente
- ✅ Timeline d'activité récente
- ✅ Boutons d'export intégrés
- ✅ Analytics complètes

**Gestion**
- ✅ Validation des médecins (documents, Ordre)
- ✅ Gestion des utilisateurs
- ✅ Modération des avis
- ✅ Traitement des remboursements
- ✅ Vue d'ensemble complète

### ⚡ Performance & Sécurité

**Caching Redis**
- ✅ CacheService avec méthodes dédiées
- ✅ Invalidation intelligente avec observers
- ✅ Mise en cache des recherches
- ✅ Cache des analytics
- ✅ TTL personnalisables (5min, 30min, 1h, 24h)
- ✅ Statistiques de cache (hit rate)

**Sécurité**
- ✅ Authentification Laravel Sanctum
- ✅ Authorization par rôle (patient, medecin, admin)
- ✅ CSRF protection
- ✅ Rate limiting
- ✅ Audit logging (INPDP)
- ✅ Chiffrement des données sensibles

**Jobs & Queues**
- ✅ Envoi email asynchrone
- ✅ Envoi SMS asynchrone
- ✅ Rappels automatiques
- ✅ Retry mechanism avec backoff exponentiel
- ✅ Commande scheduler: `appointments:send-reminders`

### 🧪 Tests

**Coverage**
- ✅ 50+ tests d'intégration
- ✅ AuthTest (10+ tests)
- ✅ AppointmentTest (12+ tests)
- ✅ MedicalRecordTest (10+ tests)
- ✅ ReviewTest (10+ tests)

**Types de Tests**
- ✅ Tests d'authentification
- ✅ Tests d'autorisation
- ✅ Tests de validation
- ✅ Tests métier
- ✅ Tests de sécurité

## 🏗️ Architecture Technique

### Backend (Laravel 11)

**Controllers (20+)**
- AdminController - Gestion administrative
- AnalyticsController - 8 endpoints analytics
- AppointmentController - Gestion RDV
- AuthController - Authentification & 2FA
- AvailabilityController - Disponibilités médecins
- ConsultationController - Téléconsultations
- ExportController - 4 types d'export
- FavoriteController - Système favoris
- FileController - Upload sécurisé
- MedecinController - Profils médecins
- MedicalRecordController - Dossiers médicaux
- MessageController - Messagerie E2E
- NotificationController - Préférences notifications
- PatientController - Profils patients
- PaymentController - Paiements multi-méthodes
- PrescriptionController - Ordonnances
- ReviewController - Avis 5 critères
- SearchController - Recherche avancée

**Services**
- CacheService - Gestion cache Redis
- NotificationService - Envoi multi-canal
- PdfService - Génération PDF
- WebRTC Service - Vidéo consultation

**Jobs**
- SendEmailNotification
- SendSMSNotification
- SendAppointmentReminder

**Commands**
- SendAppointmentReminders (scheduler)

**Observers**
- MedecinObserver - Invalidation cache
- AppointmentObserver - Invalidation cache
- ReviewObserver - Invalidation cache

### Frontend (Vue.js 3 + TypeScript)

**Views (20+)**
- PatientDashboard - Dashboard patient personnalisé
- MedecinDashboard - Dashboard médecin avec planning
- AdminAnalyticsDashboard - Analytics admin
- AdvancedSearch - Recherche multi-filtres
- AvailabilityManagement - Gestion disponibilités
- DashboardAnalytics - Analytics médecin
- PaymentPage - Interface paiement
- NotificationSettings - Préférences notifications
- MedicalRecordPage - Dossier médical
- PrescriptionsPage - Liste prescriptions
- ConsultationNotesPage - Interface consultation
- ... et 10+ autres pages

**Components Réutilisables (15+)**
- AnalyticsChart - 4 types de graphiques
- FileUpload - Upload drag & drop
- LoadingSpinner - Loading configurable
- Modal - Fenêtre modale
- Card - Carte flexible
- Badge - Badge de statut
- Button - Bouton amélioré
- EmptyState - État vide
- MedecinCard - Carte médecin
- NavigationBar - Navigation responsive
- ToastNotification - Notifications toast

**Stores (Pinia)**
- authStore - Authentification
- appointmentStore - Rendez-vous
- notificationStore - Notifications

**Composables**
- useToast - Notifications toast
- useAuth - Authentification
- useApi - Appels API

## 📡 API Endpoints (60+)

### Authentication
```
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/register/patient
POST   /api/auth/register/medecin
GET    /api/auth/me
POST   /api/auth/forgot-password
POST   /api/auth/reset-password
```

### Recherche
```
GET    /api/search/medecins (filters, sort, pagination)
GET    /api/search/autocomplete
GET    /api/search/filters
```

### Rendez-vous
```
GET    /api/appointments
POST   /api/appointments
GET    /api/appointments/{id}
POST   /api/appointments/{id}/cancel
```

### Disponibilités
```
GET    /api/availabilities
POST   /api/availabilities
PUT    /api/availabilities/{id}
DELETE /api/availabilities/{id}
POST   /api/availabilities/{id}/toggle
GET    /api/availabilities/medecins/{medecinId}/slots
```

### Avis
```
GET    /api/reviews/medecins/{medecinId}
POST   /api/reviews
PUT    /api/reviews/{id}
DELETE /api/reviews/{id}
POST   /api/reviews/{id}/report
```

### Analytics (Médecins)
```
GET    /api/analytics/overview
GET    /api/analytics/revenue?period=30d
GET    /api/analytics/consultations-by-status
GET    /api/analytics/consultations-by-month
GET    /api/analytics/patients-by-age
GET    /api/analytics/satisfaction
GET    /api/analytics/appointment-trends
GET    /api/analytics/top-diagnoses
```

### Paiements
```
GET    /api/payments
POST   /api/payments/initiate
POST   /api/payments/callback
GET    /api/payments/{id}
POST   /api/payments/{id}/confirm-cash
POST   /api/payments/{id}/request-refund
POST   /api/payments/{id}/process-refund
```

### Export
```
GET    /api/export/appointments?format=csv&start_date&end_date&status
GET    /api/export/consultations?format=csv
GET    /api/export/payments?format=csv
GET    /api/export/reviews?format=csv
```

### Notifications
```
GET    /api/notifications/preferences
PUT    /api/notifications/preferences
POST   /api/notifications/test
```

### Favoris
```
GET    /api/favorites
POST   /api/favorites
PUT    /api/favorites/{id}
DELETE /api/favorites/{id}
GET    /api/favorites/check/{medecinId}
```

### Fichiers
```
GET    /api/files
POST   /api/files/upload
POST   /api/files/upload-multiple
GET    /api/files/{id}/download
DELETE /api/files/{id}
```

## 📊 Base de Données

**Tables (20+)**
- users, patients, medecins
- appointments, consultations
- medical_records, medical_consents
- prescriptions, prescription_medications
- payments, favorites
- reviews, availabilities
- files, notification_preferences
- messages, conversations
- audit_logs

## 🚀 Installation & Déploiement

### Prérequis
```bash
- PHP 8.3+
- Composer
- Node.js 18+
- PostgreSQL 16
- Redis 7
- Docker (optionnel)
```

### Installation Locale

**Backend**
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan queue:work
php artisan schedule:work
php artisan serve
```

**Frontend**
```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

### Comptes de Test

**Admin**
- Email: admin@sehadigital.tn
- Password: admin123

**Patient**
- Email: patient1@example.com
- Password: password123

**Médecin**
- Email: medecin1@example.com
- Password: password123

### Scheduler
```bash
# Ajouter au crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## 📝 Conformité & Réglementation

### INPDP (Protection des données)
- ✅ Consentement explicite pour accès dossier médical
- ✅ Audit logging de tous les accès
- ✅ Traçabilité IP et user agent
- ✅ Droit à l'oubli
- ✅ Export de données personnelles
- ✅ Conservation 10 ans

### Ordre des Médecins de Tunisie
- ✅ Validation numéro d'ordre
- ✅ Vérification diplômes
- ✅ Attestation RCP
- ✅ CIN recto/verso

## 🎯 Roadmap Future

- [ ] Application mobile (React Native)
- [ ] Intégration CNAM (remboursements)
- [ ] IA pour triage médical
- [ ] Téléconsultation multi-participant
- [ ] Intégration pharmacies
- [ ] Télé-expertise entre médecins
- [ ] API publique pour partenaires

## 📄 License

Proprietary - Tous droits réservés © 2024 Seha Digital

## 👥 Support

Pour toute question ou support :
- Email: support@sehadigital.tn
- Documentation: docs.sehadigital.tn
- Status: status.sehadigital.tn

---

**Made with ❤️ for Tunisia 🇹🇳**
