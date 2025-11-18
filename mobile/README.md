# Seha Digital Mobile App (React Native)

Application mobile React Native pour Seha Digital - Plateforme de télémédecine et gestion de cabinets médicaux.

## 📱 Fonctionnalités

### Pour les Patients
- 🔐 Inscription et connexion
- 🔍 Recherche de médecins (par spécialité, ville, nom)
- 📍 Géolocalisation des médecins à proximité
- 📅 Prise de rendez-vous (cabinet ou vidéo)
- ⚡ Consultations urgentes
- 💊 Gestion des ordonnances
- 🔔 Rappels de médicaments
- ⭐ Médecins favoris
- 💳 Paiement en ligne

### Pour les Médecins
- 📊 Dashboard avec statistiques
- 📅 Gestion du planning
- 👥 Gestion des patients
- 📝 Création d'ordonnances
- 🎥 Vidéo consultations
- 💰 Suivi des revenus

## 🛠 Technologies

- **React Native** 0.73.0
- **TypeScript**
- **React Navigation** v6 (Stack, Bottom Tabs)
- **Zustand** (state management)
- **Axios** (API client)
- **AsyncStorage** (persistence)
- **react-native-maps** (cartes)
- **react-native-webrtc** (vidéo)
- **@notifee/react-native** (notifications)
- **react-native-vector-icons** (icônes Ionicons)

## 📂 Structure du Projet

```
mobile/
├── src/
│   ├── navigation/          # Navigateurs
│   │   ├── AuthNavigator.tsx
│   │   ├── PatientNavigator.tsx
│   │   ├── DoctorNavigator.tsx
│   │   └── RootNavigator.tsx
│   ├── screens/             # Écrans
│   │   ├── auth/
│   │   │   ├── LoginScreen.tsx
│   │   │   └── RegisterScreen.tsx
│   │   ├── patient/
│   │   │   ├── HomeScreen.tsx
│   │   │   ├── SearchDoctorsScreen.tsx
│   │   │   ├── AppointmentsScreen.tsx
│   │   │   └── ProfileScreen.tsx
│   │   └── doctor/
│   │       └── DoctorDashboardScreen.tsx
│   ├── services/            # API & Services
│   │   └── api.ts
│   ├── store/               # State Management
│   │   └── authStore.ts
│   └── types/               # TypeScript types
├── App.tsx                  # Point d'entrée
├── package.json
└── tsconfig.json
```

## 🚀 Installation

### Prérequis

- Node.js 18+
- React Native CLI
- Android Studio (pour Android)
- Xcode (pour iOS, macOS uniquement)
- CocoaPods (pour iOS)

### 1. Installer les dépendances

```bash
cd mobile
npm install
```

### 2. iOS: Installer les pods

```bash
cd ios
pod install
cd ..
```

### 3. Configuration de l'API

Modifier l'URL de l'API dans `src/services/api.ts`:

```typescript
const API_URL = 'http://YOUR_API_URL/api';
```

**Note**:
- Pour Android Emulator: `http://10.0.2.2:8000/api`
- Pour iOS Simulator: `http://localhost:8000/api`
- Pour appareil physique: IP de votre machine (ex: `http://192.168.1.100:8000/api`)

## 🏃 Lancement

### Android

```bash
npm run android
# ou
npx react-native run-android
```

### iOS

```bash
npm run ios
# ou
npx react-native run-ios
```

### Metro Bundler

```bash
npm start
# ou
npx react-native start
```

## 📝 Configuration Backend

Assurez-vous que le backend Laravel est démarré:

```bash
cd ../backend
php artisan serve
```

Pour permettre les connexions depuis des appareils physiques:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 🔧 Développement

### Hot Reload

Le Hot Reload est activé par défaut. Secouez l'appareil (ou Cmd+D sur iOS / Cmd+M sur Android) pour accéder au menu développeur.

### Debug

1. Ouvrir le menu développeur
2. Sélectionner "Debug"
3. Ouvrir Chrome DevTools: `chrome://inspect`

### TypeScript

Vérifier les types:

```bash
npx tsc --noEmit
```

### Linter

```bash
npm run lint
```

## 📱 Tests

### Tests unitaires

```bash
npm test
```

### Tests E2E (Detox)

```bash
npm run e2e:ios
npm run e2e:android
```

## 🏗 Build Production

### Android APK

```bash
cd android
./gradlew assembleRelease
```

APK généré dans: `android/app/build/outputs/apk/release/app-release.apk`

### Android AAB (Google Play)

```bash
cd android
./gradlew bundleRelease
```

AAB généré dans: `android/app/build/outputs/bundle/release/app-release.aab`

### iOS

```bash
npx react-native run-ios --configuration Release
```

Puis ouvrir Xcode pour archiver et distribuer.

## 🔐 Configuration OAuth

Les OAuth Google Calendar et Microsoft Outlook fonctionnent également sur mobile. Configurez les redirect URIs dans les consoles:

- Google: `sehadigital://oauth/google/callback`
- Microsoft: `sehadigital://oauth/microsoft/callback`

## 📦 Dépendances Principales

```json
{
  "react": "18.2.0",
  "react-native": "0.73.0",
  "@react-navigation/native": "^6.1.9",
  "@react-navigation/stack": "^6.3.20",
  "@react-navigation/bottom-tabs": "^6.5.11",
  "zustand": "^4.4.7",
  "axios": "^1.6.2",
  "react-native-maps": "^1.10.0",
  "react-native-webrtc": "^118.0.0"
}
```

## 🐛 Dépannage

### Metro Bundler ne démarre pas

```bash
npx react-native start --reset-cache
```

### Erreurs de build Android

```bash
cd android
./gradlew clean
cd ..
npm run android
```

### Erreurs de build iOS

```bash
cd ios
pod deintegrate
pod install
cd ..
npm run ios
```

### Problèmes de connexion API

1. Vérifier que le backend Laravel est démarré
2. Vérifier l'URL de l'API dans `api.ts`
3. Désactiver temporairement le pare-feu
4. Sur Android: vérifier les permissions réseau dans `AndroidManifest.xml`

## 📚 Documentation

- [React Native Docs](https://reactnative.dev/docs/getting-started)
- [React Navigation](https://reactnavigation.org/docs/getting-started)
- [Zustand](https://docs.pmnd.rs/zustand/getting-started/introduction)
- [API Backend Documentation](../API_DOCUMENTATION_v4.md)

## 🤝 Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet fait partie de Seha Digital v4.0

## 📞 Support

- Email: support@sehadigital.tn
- Documentation: https://docs.sehadigital.tn

---

**Version:** 4.0.0
**Dernière mise à jour:** Novembre 2025
