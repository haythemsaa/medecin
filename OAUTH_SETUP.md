# Configuration OAuth - Guide Complet

Ce guide explique comment configurer l'intégration OAuth pour Google Calendar et Microsoft Outlook dans Seha Digital.

## 📋 Table des Matières

1. [Google Calendar OAuth](#google-calendar-oauth)
2. [Microsoft Outlook OAuth](#microsoft-outlook-oauth)
3. [Configuration Backend](#configuration-backend)
4. [Tests OAuth](#tests-oauth)
5. [Dépannage](#dépannage)

---

## 🔵 Google Calendar OAuth

### Étape 1: Créer un Projet Google Cloud

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez-en un existant
3. Nom suggéré: **"Seha Digital Calendar Integration"**

### Étape 2: Activer l'API Google Calendar

1. Dans le menu navigation → **APIs & Services** → **Library**
2. Recherchez **"Google Calendar API"**
3. Cliquez sur **Enable**

### Étape 3: Créer les Credentials OAuth 2.0

1. **APIs & Services** → **Credentials**
2. Cliquez sur **+ CREATE CREDENTIALS** → **OAuth client ID**
3. Si demandé, configurez l'écran de consentement OAuth:
   - Type: **External**
   - Nom de l'application: **Seha Digital**
   - Email support: votre_email@domain.com
   - Logo: (optionnel)
   - Scopes: `https://www.googleapis.com/auth/calendar`
   - Domaines autorisés: `sehadigital.tn` (production)

4. Créer OAuth Client ID:
   - Type: **Web application**
   - Nom: **Seha Digital Web Client**
   - Authorized JavaScript origins:
     ```
     http://localhost:8000 (dev)
     https://sehadigital.tn (prod)
     ```
   - Authorized redirect URIs:
     ```
     http://localhost:8000/api/calendar/google/callback (dev)
     https://sehadigital.tn/api/calendar/google/callback (prod)
     ```

5. Téléchargez le JSON avec `client_id` et `client_secret`

### Étape 4: Configuration .env

Ajoutez dans `backend/.env`:

```env
GOOGLE_CLIENT_ID=votre_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/calendar/google/callback
```

---

## 🔷 Microsoft Outlook OAuth

### Étape 1: Créer une Application Azure AD

1. Allez sur [Azure Portal](https://portal.azure.com/)
2. **Azure Active Directory** → **App registrations** → **New registration**
3. Configuration:
   - Name: **Seha Digital Calendar**
   - Supported account types: **Accounts in any organizational directory and personal Microsoft accounts**
   - Redirect URI:
     - Type: **Web**
     - URL: `http://localhost:8000/api/calendar/outlook/callback` (dev)

### Étape 2: Configurer les Permissions API

1. Dans votre application → **API permissions**
2. Cliquez sur **+ Add a permission**
3. Sélectionnez **Microsoft Graph**
4. Choisissez **Delegated permissions**
5. Ajoutez les permissions:
   - `Calendars.ReadWrite`
   - `offline_access`
6. Cliquez sur **Grant admin consent** (si admin)

### Étape 3: Créer un Client Secret

1. **Certificates & secrets** → **New client secret**
2. Description: **Seha Digital Production**
3. Expiration: **24 months** (recommandé)
4. Copiez la **Value** (secret) immédiatement (ne sera plus visible)

### Étape 4: Configuration .env

Ajoutez dans `backend/.env`:

```env
MICROSOFT_CLIENT_ID=votre_application_client_id
MICROSOFT_CLIENT_SECRET=votre_client_secret_value
MICROSOFT_REDIRECT_URI=http://localhost:8000/api/calendar/outlook/callback
```

---

## ⚙️ Configuration Backend

### 1. Vérifier config/services.php

Le fichier devrait contenir:

```php
<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect_uri' => env('MICROSOFT_REDIRECT_URI'),
    ],
];
```

### 2. Migrations Base de Données

Les champs OAuth sont déjà dans la migration:

```php
// backend/database/migrations/*_add_calendar_sync_fields_to_users_table.php
$table->text('google_calendar_token')->nullable();
$table->text('google_calendar_refresh_token')->nullable();
$table->text('outlook_calendar_token')->nullable();
$table->text('outlook_calendar_refresh_token')->nullable();
$table->boolean('calendar_auto_sync')->default(false);
```

Exécutez la migration:
```bash
php artisan migrate
```

### 3. Routes API

Les routes OAuth sont déjà configurées dans `routes/api.php`:

```php
Route::middleware('auth:sanctum')->prefix('calendar')->group(function () {
    Route::get('/status', [CalendarIntegrationController::class, 'getStatus']);
    Route::get('/google/auth-url', [CalendarIntegrationController::class, 'getGoogleAuthUrl']);
    Route::get('/outlook/auth-url', [CalendarIntegrationController::class, 'getOutlookAuthUrl']);
    Route::post('/google/callback', [CalendarIntegrationController::class, 'handleGoogleCallback']);
    Route::post('/outlook/callback', [CalendarIntegrationController::class, 'handleOutlookCallback']);
    Route::post('/google/disconnect', [CalendarIntegrationController::class, 'disconnectGoogle']);
    Route::post('/outlook/disconnect', [CalendarIntegrationController::class, 'disconnectOutlook']);
    Route::post('/auto-sync', [CalendarIntegrationController::class, 'toggleAutoSync']);
});
```

---

## 🧪 Tests OAuth

### Test Google Calendar

1. **Obtenir l'URL d'autorisation**:
```bash
curl -X GET http://localhost:8000/api/calendar/google/auth-url \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Response:
```json
{
  "auth_url": "https://accounts.google.com/o/oauth2/v2/auth?client_id=..."
}
```

2. **Ouvrir l'URL** dans le navigateur
3. **Autoriser** l'accès au calendrier
4. **Copier le code** de l'URL de callback
5. **Échanger le code** contre un token:
```bash
curl -X POST http://localhost:8000/api/calendar/google/callback \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"code": "YOUR_AUTH_CODE"}'
```

### Test Microsoft Outlook

Même processus que Google:

1. GET `/api/calendar/outlook/auth-url`
2. Autoriser dans le navigateur
3. POST `/api/calendar/outlook/callback` avec le code

### Vérifier le Statut

```bash
curl -X GET http://localhost:8000/api/calendar/status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Response:
```json
{
  "google_connected": true,
  "outlook_connected": true,
  "auto_sync_enabled": false
}
```

---

## 🔧 Dépannage

### Erreur: "redirect_uri_mismatch"

**Cause**: L'URI de redirection ne correspond pas

**Solution**:
1. Vérifiez que l'URL dans Google Cloud Console / Azure AD correspond EXACTEMENT
2. Attention aux trailing slashes (`/callback` vs `/callback/`)
3. HTTP vs HTTPS doit correspondre

### Erreur: "invalid_client"

**Cause**: Client ID ou Secret incorrect

**Solution**:
1. Vérifiez le `.env`
2. Regénérez le secret si nécessaire
3. Clear config cache: `php artisan config:clear`

### Erreur: "insufficient_scope"

**Cause**: Permissions manquantes

**Solution**:
1. Ajoutez le scope `calendar` dans Google
2. Ajoutez `Calendars.ReadWrite` dans Microsoft
3. Grant admin consent dans Azure AD

### Token Expiry

**Refresh Tokens**:
- Google: `offline_access` requis
- Microsoft: `offline_access` scope requis

Le refresh se fait automatiquement via `CalendarSyncService`.

### Test Connexion

```bash
# Test Google API
curl -H "Authorization: Bearer TOKEN" \
  https://www.googleapis.com/calendar/v3/calendars/primary/events

# Test Microsoft Graph
curl -H "Authorization: Bearer TOKEN" \
  https://graph.microsoft.com/v1.0/me/events
```

---

## 📚 Ressources

### Google Calendar API
- [Documentation officielle](https://developers.google.com/calendar/api/v3/reference)
- [OAuth 2.0](https://developers.google.com/identity/protocols/oauth2)
- [Scopes](https://developers.google.com/calendar/api/auth)

### Microsoft Graph API
- [Documentation officielle](https://learn.microsoft.com/en-us/graph/api/resources/calendar)
- [OAuth 2.0](https://learn.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow)
- [Permissions](https://learn.microsoft.com/en-us/graph/permissions-reference)

### Tutoriels
- [Google Calendar OAuth Flow](https://developers.google.com/calendar/api/quickstart/php)
- [Microsoft Graph Authentication](https://learn.microsoft.com/en-us/graph/auth-v2-user)

---

## ✅ Checklist de Production

Avant le déploiement en production:

- [ ] Client IDs configurés pour production domain
- [ ] Redirect URIs mis à jour avec HTTPS
- [ ] Secrets stockés de manière sécurisée (pas dans Git)
- [ ] Écran de consentement OAuth validé et public
- [ ] Logs d'erreurs OAuth configurés
- [ ] Rate limiting configuré
- [ ] Token refresh automatique testé
- [ ] Gestion des erreurs de synchronisation
- [ ] Documentation utilisateur créée
- [ ] Support pour token révocation
- [ ] Monitoring des API quotas

---

## 🔐 Sécurité

### Meilleures Pratiques

1. **Tokens**:
   - Stockez les tokens cryptés dans la base de données
   - Utilisez HTTPS en production
   - Refresh tokens avant expiration

2. **Scopes**:
   - Demandez uniquement les permissions nécessaires
   - Expliquez pourquoi chaque permission est requise

3. **State Parameter**:
   - Utilisez un state parameter pour prévenir CSRF
   - Vérifiez-le dans le callback

4. **Révocation**:
   - Permettez aux utilisateurs de révoquer l'accès
   - Supprimez les tokens de la DB lors de la révocation

### Exemple avec State (amélioré)

```php
// Generate state
$state = bin2hex(random_bytes(16));
session(['oauth_state' => $state]);

// In auth URL
$url .= '&state=' . $state;

// In callback
if ($request->state !== session('oauth_state')) {
    throw new Exception('Invalid state parameter');
}
```

---

**Prêt à synchroniser les calendriers ! 📅**
