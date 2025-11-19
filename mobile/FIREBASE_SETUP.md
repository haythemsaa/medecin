# 🔥 Firebase Configuration Guide - Seha Digital Mobile

Guide complet pour configurer Firebase dans l'application mobile Seha Digital.

## 📋 Prérequis

- Compte Google/Firebase
- Accès Firebase Console: https://console.firebase.google.com
- React Native CLI configuré
- Android Studio & Xcode installés

---

## 🚀 Étape 1: Créer un Projet Firebase

1. Aller sur https://console.firebase.google.com
2. Cliquer sur "Ajouter un projet"
3. Nom du projet: `Seha Digital`
4. Activer Google Analytics (recommandé)
5. Créer le projet

---

## 📱 Étape 2: Configurer Android

### 2.1 Ajouter l'app Android

1. Dans Firebase Console, cliquer sur l'icône Android
2. Remplir les informations:
   - **Package name**: `com.sehadigital.mobile`
   - **App nickname**: `Seha Digital Android`
   - **Debug signing certificate SHA-1**: (optionnel pour dev)

### 2.2 Générer SHA-1 (pour dev)

```bash
cd android
./gradlew signingReport

# Output: copier le SHA-1 sous "Variant: debug"
# Exemple: SHA1: A1:B2:C3:D4:...
```

### 2.3 Télécharger google-services.json

1. Télécharger `google-services.json`
2. Placer dans: `mobile/android/app/google-services.json`

```bash
# Vérifier l'emplacement
ls android/app/google-services.json
```

### 2.4 Modifier build.gradle (Project-level)

Fichier: `android/build.gradle`

```gradle
buildscript {
    dependencies {
        // ...
        classpath 'com.google.gms:google-services:4.4.0'  // ✅ Ajouter
    }
}
```

### 2.5 Modifier build.gradle (App-level)

Fichier: `android/app/build.gradle`

En bas du fichier:

```gradle
apply plugin: 'com.google.gms.google-services'  // ✅ Ajouter à la fin
```

---

## 🍎 Étape 3: Configurer iOS

### 3.1 Ajouter l'app iOS

1. Dans Firebase Console, cliquer sur l'icône iOS
2. Remplir les informations:
   - **Bundle ID**: `com.sehadigital.mobile`
   - **App nickname**: `Seha Digital iOS`
   - **App Store ID**: (laisser vide pour dev)

### 3.2 Télécharger GoogleService-Info.plist

1. Télécharger `GoogleService-Info.plist`
2. Ouvrir Xcode: `open ios/SehaDigitalMobile.xcworkspace`
3. Drag & Drop `GoogleService-Info.plist` dans Xcode
4. **Important**: Cocher "Copy items if needed"

### 3.3 Vérifier dans Xcode

1. Dans Xcode, vérifier que le fichier apparaît dans le projet
2. Build Phases > Copy Bundle Resources
3. `GoogleService-Info.plist` doit être listée

---

## 🔥 Étape 4: Installer les Packages Firebase

```bash
cd mobile

# Firebase Core
npm install @react-native-firebase/app

# Analytics
npm install @react-native-firebase/analytics

# Crashlytics (error tracking)
npm install @react-native-firebase/crashlytics

# Cloud Messaging (push notifications)
npm install @react-native-firebase/messaging

# Performance Monitoring
npm install @react-native-firebase/perf

# iOS: Install pods
cd ios && pod install && cd ..
```

---

## 📊 Étape 5: Configurer Analytics

### 5.1 Initialiser dans l'app

Fichier: `App.tsx`

```typescript
import analytics from '@react-native-firebase/analytics';
import { useEffect } from 'react';

export default function App() {
  useEffect(() => {
    // Log app open
    analytics().logAppOpen();
  }, []);

  return (
    // ... votre app
  );
}
```

### 5.2 Tracker des événements

Créer: `mobile/src/utils/analytics.ts`

```typescript
import analytics from '@react-native-firebase/analytics';

export const trackEvent = async (eventName: string, params?: object) => {
  try {
    await analytics().logEvent(eventName, params);
  } catch (error) {
    console.error('Analytics error:', error);
  }
};

// Événements prédéfinis
export const analyticsEvents = {
  // Auth
  login: (method: string) =>
    trackEvent('login', { method }),

  signup: (role: string) =>
    trackEvent('sign_up', { role }),

  // Appointments
  bookAppointment: (doctorId: number, type: string) =>
    trackEvent('book_appointment', { doctor_id: doctorId, type }),

  // Video
  startVideoCall: (appointmentId: number) =>
    trackEvent('video_call_start', { appointment_id: appointmentId }),

  // Prescriptions
  viewPrescription: (prescriptionId: number) =>
    trackEvent('view_prescription', { prescription_id: prescriptionId }),
};
```

### 5.3 Utiliser dans les screens

```typescript
import { analyticsEvents } from '@utils/analytics';

// Dans un screen
const handleLogin = async () => {
  await login(email, password);
  await analyticsEvents.login('email');
};
```

---

## 🔥 Étape 6: Configurer Crashlytics

### 6.1 Activer dans Firebase Console

1. Aller dans Build > Crashlytics
2. Activer Crashlytics
3. Suivre les instructions

### 6.2 Initialiser dans l'app

Fichier: `App.tsx`

```typescript
import crashlytics from '@react-native-firebase/crashlytics';

export default function App() {
  useEffect(() => {
    // Enable crash reporting
    crashlytics().setCrashlyticsCollectionEnabled(true);
  }, []);

  return (
    // ... app
  );
}
```

### 6.3 Logger les erreurs

Créer: `mobile/src/utils/errorTracking.ts`

```typescript
import crashlytics from '@react-native-firebase/crashlytics';

export const logError = (error: Error, context?: string) => {
  if (__DEV__) {
    console.error(context || 'Error:', error);
  }

  crashlytics().recordError(error);

  if (context) {
    crashlytics().log(`Context: ${context}`);
  }
};

export const setUser = (userId: string) => {
  crashlytics().setUserId(userId);
};

export const logNonFatal = (message: string) => {
  crashlytics().log(message);
};
```

### 6.4 Utiliser

```typescript
import { logError, setUser } from '@utils/errorTracking';

// Dans authStore après login
setUser(user.id.toString());

// Dans un catch
try {
  await api.something();
} catch (error) {
  logError(error, 'Failed to load data');
}
```

---

## 🔔 Étape 7: Configurer Push Notifications (FCM)

### 7.1 Android: Rien à faire!

Firebase Cloud Messaging fonctionne automatiquement sur Android.

### 7.2 iOS: Configurer APNs

1. Aller sur Apple Developer
2. Certificates, Identifiers & Profiles
3. Keys > Create a new key
4. Cocher "Apple Push Notifications service (APNs)"
5. Télécharger le fichier `.p8`
6. Dans Firebase Console:
   - Project Settings > Cloud Messaging
   - iOS app configuration
   - Upload APNs Authentication Key
   - Key ID, Team ID, Fichier .p8

### 7.3 Permissions iOS

Fichier: `ios/SehaDigitalMobile/Info.plist`

Déjà configuré dans AndroidManifest.xml et Info.plist.

### 7.4 Gérer les notifications

Créer: `mobile/src/utils/pushNotifications.ts`

```typescript
import messaging from '@react-native-firebase/messaging';
import { Platform } from 'react-native';

export const requestPermission = async () => {
  const authStatus = await messaging().requestPermission();
  const enabled =
    authStatus === messaging.AuthorizationStatus.AUTHORIZED ||
    authStatus === messaging.AuthorizationStatus.PROVISIONAL;

  if (enabled) {
    console.log('Authorization status:', authStatus);
  }

  return enabled;
};

export const getToken = async () => {
  const token = await messaging().getToken();
  console.log('FCM Token:', token);
  return token;
};

export const onNotification = (callback: (message: any) => void) => {
  // Foreground messages
  const unsubscribe = messaging().onMessage(async (remoteMessage) => {
    callback(remoteMessage);
  });

  return unsubscribe;
};

export const onNotificationOpened = (callback: (message: any) => void) => {
  messaging().onNotificationOpenedApp((remoteMessage) => {
    callback(remoteMessage);
  });

  // Check if app was opened from a notification (app was closed)
  messaging()
    .getInitialNotification()
    .then((remoteMessage) => {
      if (remoteMessage) {
        callback(remoteMessage);
      }
    });
};
```

### 7.5 Utiliser dans App.tsx

```typescript
import { useEffect } from 'react';
import { requestPermission, getToken, onNotification } from '@utils/pushNotifications';

export default function App() {
  useEffect(() => {
    setupPushNotifications();
  }, []);

  const setupPushNotifications = async () => {
    const hasPermission = await requestPermission();

    if (hasPermission) {
      const token = await getToken();
      // Envoyer le token au backend
      await api.updateFCMToken(token);
    }

    // Listen to notifications
    const unsubscribe = onNotification((message) => {
      console.log('Notification received:', message);
      // Afficher notification avec @notifee/react-native
    });

    return unsubscribe;
  };

  return (
    // ... app
  );
}
```

---

## 🎯 Étape 8: Configurer Performance Monitoring

### 8.1 Activer dans Firebase

Déjà activé automatiquement avec @react-native-firebase/perf.

### 8.2 Mesurer les performances

```typescript
import perf from '@react-native-firebase/perf';

// Mesurer une opération
const trace = await perf().startTrace('load_doctors');
await api.searchDoctors();
await trace.stop();

// Mesurer une requête HTTP
const httpMetric = perf().newHttpMetric('https://api.sehadigital.tn/doctors', 'GET');
await httpMetric.start();
// ... faire la requête
await httpMetric.stop();
```

---

## ✅ Étape 9: Vérifier l'installation

### 9.1 Build Android

```bash
npm run android
```

Vérifier dans les logs:
```
✅ Firebase initialized
✅ Analytics ready
✅ Crashlytics ready
```

### 9.2 Build iOS

```bash
npm run ios
```

Vérifier dans Xcode Console:
```
✅ Firebase configured
✅ Analytics initialized
```

### 9.3 Tester Crashlytics

```typescript
import crashlytics from '@react-native-firebase/crashlytics';

// Forcer un crash (dev only!)
crashlytics().crash();
```

Attendre 2-3 minutes, puis vérifier Firebase Console > Crashlytics.

### 9.4 Tester Analytics

```typescript
import analytics from '@react-native-firebase/analytics';

analytics().logEvent('test_event', {
  param1: 'value1',
  param2: 'value2',
});
```

Attendre 24h, puis vérifier Firebase Console > Analytics.

---

## 🔧 Configuration Avancée

### Remote Config (optionnel)

```bash
npm install @react-native-firebase/remote-config
```

Utiliser pour:
- Feature flags
- A/B testing
- Configuration dynamique

### Dynamic Links (optionnel)

```bash
npm install @react-native-firebase/dynamic-links
```

Utiliser pour:
- Deep linking
- Partage de contenu
- Invitations

---

## 📊 Événements Recommandés

### Authentification
- `login` - Connexion
- `sign_up` - Inscription
- `logout` - Déconnexion

### Navigation
- `screen_view` - Vue d'écran
- `search` - Recherche médecin
- `select_content` - Sélection médecin

### Engagement
- `book_appointment` - Réservation RDV
- `start_video_call` - Début vidéo
- `view_prescription` - Vue ordonnance
- `set_medication_reminder` - Rappel médicament

### Conversion
- `complete_appointment` - Consultation terminée
- `prescription_created` - Ordonnance créée
- `payment_success` - Paiement réussi

---

## 🐛 Dépannage

### Erreur: google-services.json not found

```bash
# Vérifier l'emplacement
ls android/app/google-services.json

# Si absent, retélécharger depuis Firebase Console
```

### Erreur: GoogleService-Info.plist not found

```bash
# Ouvrir Xcode
open ios/SehaDigitalMobile.xcworkspace

# Vérifier que le fichier est dans le projet
# Sinon, drag & drop à nouveau
```

### Notifications ne fonctionnent pas (iOS)

1. Vérifier APNs key dans Firebase
2. Vérifier capacités dans Xcode:
   - Push Notifications
   - Background Modes > Remote notifications

### Analytics ne s'affiche pas

- **Attendre 24h** pour voir les données
- Vérifier que l'app est en mode Release (pas Debug)

---

## 📝 Checklist Finale

- [ ] Projet Firebase créé
- [ ] `google-services.json` ajouté (Android)
- [ ] `GoogleService-Info.plist` ajouté (iOS)
- [ ] Packages installés
- [ ] Analytics configuré
- [ ] Crashlytics configuré
- [ ] Push notifications configurées
- [ ] APNs key uploadé (iOS)
- [ ] Tests effectués
- [ ] Événements trackés
- [ ] Build production testé

---

## 🎉 Résultat

Vous avez maintenant:

✅ Firebase complètement configuré
✅ Analytics pour suivre l'usage
✅ Crashlytics pour les erreurs
✅ Push notifications (FCM)
✅ Performance monitoring
✅ Production ready!

**Temps total: 30-45 minutes**

---

**Dernière mise à jour:** Novembre 2025
**Version app:** 4.0.0
