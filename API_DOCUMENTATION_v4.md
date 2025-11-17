# Seha Digital API Documentation - v4.0

Documentation complète des nouveaux endpoints de la version 4.0.

## 📋 Table des Matières

1. [Consultation Urgente](#consultation-urgente)
2. [Renouvellement d'Ordonnances](#renouvellement-dordonnances)
3. [Géolocalisation](#géolocalisation)
4. [Questionnaires Pré-Consultation](#questionnaires-pré-consultation)
5. [Rappels de Médicaments](#rappels-de-médicaments)
6. [Intégration Calendrier](#intégration-calendrier)

---

## 🚨 Consultation Urgente

### GET /api/urgent-consultations/available

Récupère la liste des médecins disponibles pour une consultation urgente dans l'heure.

**Auth**: Required (Bearer Token)

**Query Parameters**:
```
specialty     (optional): Filtrer par spécialité
max_price     (optional): Prix maximum acceptable
```

**Response 200**:
```json
{
  "doctors": [
    {
      "id": 1,
      "user": {
        "nom": "Amara",
        "prenom": "Leila",
        "photo": "https://..."
      },
      "specialite": "Médecine générale",
      "tarif": 100.00,
      "urgent_price": 150.00,
      "urgent_response_time": 60,
      "available_in_minutes": 15,
      "rating": 4.8,
      "total_reviews": 42
    }
  ],
  "total": 5
}
```

---

### POST /api/urgent-consultations/request

Demander une consultation urgente.

**Auth**: Required (Patient)

**Request Body**:
```json
{
  "medecin_id": 1,
  "motif": "Forte fièvre et maux de tête",
  "symptoms_description": "Symptômes depuis ce matin, température 39°C"
}
```

**Response 201**:
```json
{
  "message": "Demande de consultation urgente envoyée",
  "appointment": {
    "id": 123,
    "patient_id": 45,
    "medecin_id": 1,
    "date": "2025-11-17",
    "heure": "14:30:00",
    "motif": "Forte fièvre et maux de tête",
    "status": "pending",
    "is_urgent": true,
    "urgent_fee": 150.00,
    "urgent_requested_at": "2025-11-17T14:15:00Z"
  }
}
```

**Errors**:
- 400: Médecin n'accepte pas les consultations urgentes
- 400: Médecin non disponible dans l'heure
- 404: Patient profile not found

---

### POST /api/urgent-consultations/{appointmentId}/accept

Accepter une consultation urgente (Médecin).

**Auth**: Required (Medecin)

**Response 200**:
```json
{
  "message": "Consultation urgente confirmée",
  "appointment": {
    "id": 123,
    "status": "confirmed"
  }
}
```

---

### GET /api/urgent-consultations/stats

Statistiques des consultations urgentes (Médecin).

**Auth**: Required (Medecin)

**Response 200**:
```json
{
  "total_urgent": 45,
  "completed": 38,
  "pending": 5,
  "cancelled": 2,
  "total_revenue": 5700.00,
  "average_response_time": 12
}
```

---

### POST /api/urgent-consultations/toggle-availability

Activer/désactiver les consultations urgentes (Médecin).

**Auth**: Required (Medecin)

**Request Body**:
```json
{
  "accepts_urgent": true,
  "urgent_surcharge_percentage": 50.00,
  "urgent_response_time": 60
}
```

**Response 200**:
```json
{
  "message": "Paramètres de consultation urgente mis à jour",
  "medecin": {
    "id": 1,
    "accepts_urgent": true,
    "urgent_surcharge_percentage": 50.00
  }
}
```

---

## 💊 Renouvellement d'Ordonnances

### GET /api/prescription-renewals/renewable

Liste des ordonnances renouvelables.

**Auth**: Required (Patient)

**Response 200**:
```json
{
  "prescriptions": [
    {
      "id": 56,
      "medicaments": ["Paracétamol 500mg", "Ibuprofène 400mg"],
      "instructions": "2 fois par jour après les repas",
      "renewals_allowed": 3,
      "renewals_used": 1,
      "renewals_remaining": 2,
      "valid_until": "2026-05-17",
      "renewal_conditions": "Pas de nouveaux symptômes",
      "medecin": {
        "id": 1,
        "nom": "Amara",
        "prenom": "Leila",
        "specialite": "Médecine générale"
      },
      "created_at": "2025-11-17T10:00:00Z"
    }
  ]
}
```

---

### POST /api/prescription-renewals/request

Demander un renouvellement d'ordonnance.

**Auth**: Required (Patient)

**Request Body**:
```json
{
  "prescription_id": 56,
  "patient_notes": "Le traitement fonctionne bien, aucun effet secondaire"
}
```

**Response 201**:
```json
{
  "message": "Demande de renouvellement envoyée",
  "renewal": {
    "id": 12,
    "prescription_id": 56,
    "patient_id": 45,
    "medecin_id": 1,
    "status": "pending",
    "patient_notes": "Le traitement fonctionne bien...",
    "requested_at": "2025-11-17T14:30:00Z"
  }
}
```

**Errors**:
- 400: Nombre maximum de renouvellements atteint
- 400: Ordonnance expirée
- 400: Demande déjà en cours

---

### GET /api/prescription-renewals/pending

Liste des demandes de renouvellement en attente (Médecin).

**Auth**: Required (Medecin)

**Response 200**:
```json
{
  "renewals": [
    {
      "id": 12,
      "prescription_id": 56,
      "patient": {
        "id": 45,
        "user": {
          "nom": "Ben Ali",
          "prenom": "Ahmed"
        }
      },
      "prescription": {
        "medicaments": ["Paracétamol 500mg"],
        "renewals_used": 1,
        "renewals_allowed": 3
      },
      "patient_notes": "Traitement efficace",
      "status": "pending",
      "requested_at": "2025-11-17T14:30:00Z"
    }
  ]
}
```

---

### POST /api/prescription-renewals/{renewalId}/approve

Approuver un renouvellement (Médecin).

**Auth**: Required (Medecin)

**Request Body**:
```json
{
  "medecin_notes": "Renouvellement approuvé pour 3 mois supplémentaires"
}
```

**Response 200**:
```json
{
  "message": "Renouvellement approuvé",
  "renewal": {
    "id": 12,
    "status": "approved",
    "processed_at": "2025-11-17T15:00:00Z"
  },
  "new_prescription": {
    "id": 78,
    "medicaments": ["Paracétamol 500mg"],
    "pdf_url": "https://..."
  }
}
```

---

### POST /api/prescription-renewals/{renewalId}/reject

Refuser un renouvellement (Médecin).

**Auth**: Required (Medecin)

**Request Body**:
```json
{
  "medecin_notes": "Nécessite une nouvelle consultation pour réévaluation"
}
```

**Response 200**:
```json
{
  "message": "Renouvellement refusé",
  "renewal": {
    "id": 12,
    "status": "rejected",
    "medecin_notes": "Nécessite une nouvelle consultation...",
    "processed_at": "2025-11-17T15:00:00Z"
  }
}
```

---

### GET /api/prescription-renewals/history

Historique des renouvellements (Patient).

**Auth**: Required (Patient)

**Response 200**:
```json
{
  "renewals": [
    {
      "id": 12,
      "prescription_id": 56,
      "status": "approved",
      "patient_notes": "Traitement efficace",
      "medecin_notes": "Approuvé pour 3 mois",
      "requested_at": "2025-11-17T14:30:00Z",
      "processed_at": "2025-11-17T15:00:00Z"
    }
  ]
}
```

---

## 📍 Géolocalisation

### GET /api/geolocation/nearby

Rechercher des médecins à proximité.

**Auth**: Not Required

**Query Parameters**:
```
latitude      (required): Latitude de la position
longitude     (required): Longitude de la position
radius        (optional): Rayon de recherche en km (default: 10, max: 50)
specialty     (optional): Filtrer par spécialité
limit         (optional): Nombre de résultats (default: 20, max: 100)
```

**Example**:
```
GET /api/geolocation/nearby?latitude=36.8065&longitude=10.1815&radius=10&specialty=Cardiologie
```

**Response 200**:
```json
{
  "doctors": [
    {
      "id": 1,
      "user": {
        "nom": "Amara",
        "prenom": "Leila",
        "photo": "https://...",
        "telephone": "+21612345678"
      },
      "specialite": "Cardiologie",
      "adresse": "15 Avenue Habib Bourguiba",
      "ville": "Tunis",
      "code_postal": "1000",
      "tarif": 120.00,
      "latitude": 36.8065,
      "longitude": 10.1815,
      "distance": 0.5,
      "rating": 4.8,
      "total_reviews": 42,
      "accepts_urgent": true
    }
  ],
  "center": {
    "latitude": 36.8065,
    "longitude": 10.1815
  },
  "radius": 10,
  "total": 15
}
```

---

### GET /api/geolocation/by-city

Rechercher des médecins par ville.

**Auth**: Not Required

**Query Parameters**:
```
ville         (required): Nom de la ville
specialty     (optional): Filtrer par spécialité
```

**Response 200**:
```json
{
  "doctors": [...],
  "ville": "Tunis",
  "total": 45
}
```

---

### GET /api/geolocation/popular-cities

Villes les plus populaires (Top 10).

**Auth**: Not Required

**Response 200**:
```json
{
  "cities": [
    {
      "ville": "Tunis",
      "count": 234
    },
    {
      "ville": "Sfax",
      "count": 123
    }
  ]
}
```

---

### POST /api/geolocation/geocode

Convertir une adresse en coordonnées.

**Auth**: Not Required

**Request Body**:
```json
{
  "address": "Avenue Habib Bourguiba, Tunis"
}
```

**Response 200**:
```json
{
  "address": "Avenue Habib Bourguiba, Tunis",
  "coordinates": {
    "latitude": 36.8065,
    "longitude": 10.1815
  }
}
```

---

### POST /api/geolocation/update

Mettre à jour la localisation (Médecin).

**Auth**: Required (Medecin)

**Request Body**:
```json
{
  "latitude": 36.8065,
  "longitude": 10.1815,
  "ville": "Tunis",
  "code_postal": "1000",
  "show_on_map": true
}
```

**Response 200**:
```json
{
  "message": "Localisation mise à jour",
  "medecin": {
    "id": 1,
    "latitude": 36.8065,
    "longitude": 10.1815,
    "ville": "Tunis",
    "show_on_map": true
  }
}
```

---

## 📋 Questionnaires Pré-Consultation

### GET /api/questionnaires/templates

Liste des templates de questionnaires par spécialité.

**Auth**: Not Required

**Query Parameters**:
```
specialty     (optional): Filtrer par spécialité
```

**Response 200**:
```json
{
  "templates": [
    {
      "id": 1,
      "specialty": "Cardiologie",
      "title": "Questionnaire de cardiologie",
      "description": "Questionnaire pré-consultation pour cardiologie",
      "questions": [
        {
          "id": "chest_pain",
          "question": "Ressentez-vous des douleurs thoraciques ?",
          "type": "radio",
          "options": ["Oui", "Non"]
        }
      ]
    }
  ]
}
```

---

### GET /api/questionnaires/appointments/{appointmentId}

Récupérer le questionnaire pour un rendez-vous.

**Auth**: Required

**Response 200**:
```json
{
  "questionnaire": {
    "id": 45,
    "chief_complaint": "Douleur thoracique",
    "symptom_duration": "2 jours",
    "symptom_intensity": 7,
    "current_medications": ["Aspirine 100mg"],
    "allergies": ["Pénicilline"],
    "submitted_at": "2025-11-17T10:00:00Z"
  },
  "template": {...}
}
```

---

### POST /api/questionnaires/submit

Soumettre un questionnaire.

**Auth**: Required (Patient)

**Request Body**:
```json
{
  "appointment_id": 123,
  "chief_complaint": "Douleur thoracique persistante",
  "symptom_duration": "2 jours",
  "symptom_intensity": 7,
  "current_medications": ["Aspirine 100mg"],
  "allergies": ["Pénicilline"],
  "previous_conditions": ["Hypertension"],
  "family_history": "Père décédé d'infarctus",
  "lifestyle": {
    "smoking": "non",
    "alcohol": "occasionnellement",
    "exercise": "régulière"
  },
  "additional_notes": "Stress professionnel élevé",
  "answers": {
    "chest_pain": "Oui",
    "shortness_breath": "Parfois"
  }
}
```

**Response 201**:
```json
{
  "message": "Questionnaire soumis avec succès",
  "questionnaire": {
    "id": 45,
    "submitted_at": "2025-11-17T14:30:00Z"
  }
}
```

---

### POST /api/questionnaires/{id}/mark-reviewed

Marquer comme revu (Médecin).

**Auth**: Required (Medecin)

**Response 200**:
```json
{
  "message": "Questionnaire marqué comme revu",
  "questionnaire": {
    "reviewed_at": "2025-11-17T15:00:00Z"
  }
}
```

---

### GET /api/questionnaires/medecin/all

Tous les questionnaires du médecin.

**Auth**: Required (Medecin)

**Response 200**:
```json
{
  "questionnaires": [...]
}
```

---

## 💊 Rappels de Médicaments

### GET /api/medication-reminders

Liste des rappels actifs.

**Auth**: Required

**Response 200**:
```json
{
  "reminders": [
    {
      "id": 23,
      "medication_name": "Paracétamol",
      "dosage": "500mg",
      "frequency": "twice_daily",
      "reminder_times": ["08:00", "20:00"],
      "start_date": "2025-11-01",
      "end_date": "2025-11-30",
      "is_active": true,
      "notes": "Prendre avec de l'eau"
    }
  ]
}
```

---

### POST /api/medication-reminders

Créer un rappel.

**Auth**: Required

**Request Body**:
```json
{
  "medication_name": "Paracétamol",
  "dosage": "500mg",
  "frequency": "twice_daily",
  "reminder_times": ["08:00", "20:00"],
  "start_date": "2025-11-17",
  "duration_days": 30,
  "notes": "Prendre avec de l'eau"
}
```

**Response 201**:
```json
{
  "message": "Rappel créé avec succès",
  "reminder": {
    "id": 23,
    "end_date": "2025-12-17"
  }
}
```

---

### POST /api/medication-reminders/intake/record

Enregistrer une prise.

**Auth**: Required

**Request Body**:
```json
{
  "reminder_id": 23,
  "taken_at": "2025-11-17T08:00:00Z",
  "skipped": false,
  "notes": "Pris avec le petit déjeuner"
}
```

**Response 201**:
```json
{
  "message": "Prise enregistrée",
  "intake": {
    "id": 456
  }
}
```

---

### GET /api/medication-reminders/{id}/adherence-stats

Statistiques d'adhérence.

**Auth**: Required

**Response 200**:
```json
{
  "adherence_rate": 85.5,
  "total_expected": 60,
  "total_recorded": 51,
  "last_7_days": 13,
  "last_30_days": 51
}
```

---

## 📅 Intégration Calendrier

### GET /api/calendar/status

Statut de la connexion calendrier.

**Auth**: Required

**Response 200**:
```json
{
  "google_connected": true,
  "outlook_connected": false,
  "auto_sync_enabled": true
}
```

---

### GET /api/calendar/google/auth-url

URL d'autorisation Google.

**Auth**: Required

**Response 200**:
```json
{
  "auth_url": "https://accounts.google.com/o/oauth2/v2/auth?client_id=..."
}
```

---

### POST /api/calendar/google/callback

Callback OAuth Google.

**Auth**: Required

**Request Body**:
```json
{
  "code": "4/0AY0..."
}
```

**Response 200**:
```json
{
  "message": "Google Calendar connecté avec succès",
  "connected": true
}
```

---

### POST /api/calendar/auto-sync

Activer/désactiver la sync auto.

**Auth**: Required

**Request Body**:
```json
{
  "enabled": true
}
```

**Response 200**:
```json
{
  "message": "Synchronisation automatique mise à jour",
  "auto_sync_enabled": true
}
```

---

## 🔒 Authentication

Tous les endpoints protégés nécessitent un Bearer Token:

```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

Pour obtenir un token:
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

---

## 📊 Error Responses

**400 Bad Request**:
```json
{
  "message": "Validation error message",
  "errors": {
    "field_name": ["Error description"]
  }
}
```

**401 Unauthorized**:
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden**:
```json
{
  "message": "This action is unauthorized."
}
```

**404 Not Found**:
```json
{
  "message": "Resource not found."
}
```

**500 Server Error**:
```json
{
  "message": "Server error occurred."
}
```

---

## 📌 Rate Limiting

- **Global**: 60 requests / minute par IP
- **Auth endpoints**: 5 requests / minute
- **Write operations**: 30 requests / minute

Headers de réponse:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1637164800
```

---

## 🌐 Base URLs

**Development**:
```
http://localhost:8000/api
```

**Production**:
```
https://api.sehadigital.tn/api
```

---

## 📝 Changelog v4.0

### Ajouté
- Consultation urgente (5 endpoints)
- Renouvellement d'ordonnances (6 endpoints)
- Géolocalisation (5 endpoints)
- Questionnaires pré-consultation (5 endpoints)
- Rappels de médicaments (7 endpoints)
- Intégration calendrier (8 endpoints)

**Total**: 36 nouveaux endpoints

---

**Documentation complète - Version 4.0**
*Dernière mise à jour: 17 novembre 2025*
