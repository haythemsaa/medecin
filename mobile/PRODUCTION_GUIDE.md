# Guide de Production - Seha Digital Mobile App

Ce guide vous accompagne pas à pas pour mettre en production l'application mobile Seha Digital sur iOS et Android.

## 📋 Prérequis

### Environnement de développement

- **Node.js** 18+ installé
- **React Native CLI** installé globalement
- **Xcode** 14+ (pour iOS, macOS uniquement)
- **Android Studio** (pour Android)
- **CocoaPods** installé (pour iOS)
- Compte **Apple Developer** (pour iOS)
- Compte **Google Play Console** (pour Android)

### Vérifications initiales

```bash
node --version  # v18.0.0+
npm --version   # 9.0.0+
npx react-native --version  # 0.73.0+
```

## 🔧 Configuration Initiale

### 1. Installation des dépendances

```bash
cd mobile
npm install

# iOS uniquement
cd ios
pod install
cd ..
```

### 2. Configuration de l'environnement

Créer le fichier `.env` à partir de `.env.example`:

```bash
cp .env.example .env
```

Modifier `.env` avec vos valeurs de production:

```env
API_URL=https://api.sehadigital.tn/api
GOOGLE_MAPS_API_KEY=votre_clé_google_maps
WEBRTC_STUN_SERVER=stun:stun.l.google.com:19302
WEBRTC_TURN_SERVER=turn:turn.sehadigital.tn:3478
WEBRTC_TURN_USERNAME=votre_username
WEBRTC_TURN_CREDENTIAL=votre_password
PAYMENT_PUBLIC_KEY=votre_clé_publique_paiement
```

### 3. Configuration React Native Config

Installer `react-native-config`:

```bash
npm install react-native-config
cd ios && pod install && cd ..
```

Modifier `src/services/api.ts` pour utiliser les variables d'environnement:

```typescript
import Config from 'react-native-config';

const API_URL = Config.API_URL || 'http://localhost:8000/api';
```

## 📱 Build Android

### 1. Configuration de l'app

Modifier `android/app/build.gradle`:

```gradle
android {
    defaultConfig {
        applicationId "com.sehadigital.mobile"
        minSdkVersion 24
        targetSdkVersion 33
        versionCode 1
        versionName "4.0.0"
    }

    signingConfigs {
        release {
            storeFile file('sehadigital-release.keystore')
            storePassword System.getenv("KEYSTORE_PASSWORD")
            keyAlias System.getenv("KEY_ALIAS")
            keyPassword System.getenv("KEY_PASSWORD")
        }
    }

    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            proguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'
        }
    }
}
```

### 2. Générer la clé de signature

```bash
cd android/app

keytool -genkeypair -v -storetype PKCS12 \
  -keystore sehadigital-release.keystore \
  -alias sehadigital \
  -keyalg RSA \
  -keysize 2048 \
  -validity 10000
```

**IMPORTANT**: Sauvegarder le fichier `.keystore` et les mots de passe dans un endroit sécurisé!

### 3. Variables d'environnement pour le build

Créer `android/gradle.properties`:

```properties
KEYSTORE_PASSWORD=votre_mot_de_passe_keystore
KEY_ALIAS=sehadigital
KEY_PASSWORD=votre_mot_de_passe_cle
```

**IMPORTANT**: NE PAS committer ce fichier! Ajouter à `.gitignore`.

### 4. Build APK de production

```bash
cd android
./gradlew assembleRelease
```

APK généré: `android/app/build/outputs/apk/release/app-release.apk`

### 5. Build AAB pour Google Play

```bash
cd android
./gradlew bundleRelease
```

AAB généré: `android/app/build/outputs/bundle/release/app-release.aab`

### 6. Publication sur Google Play

1. Créer un compte Google Play Console
2. Créer une nouvelle application
3. Remplir les informations (titre, description, screenshots)
4. Upload l'AAB dans "Production"
5. Configurer le prix et la distribution
6. Soumettre pour révision

## 🍎 Build iOS

### 1. Configuration Xcode

1. Ouvrir `ios/SehaDigitalMobile.xcworkspace` dans Xcode
2. Sélectionner le projet dans le navigateur
3. Dans "General":
   - Bundle Identifier: `com.sehadigital.mobile`
   - Version: `4.0.0`
   - Build: `1`
   - Deployment Target: `13.0`

### 2. Configuration des permissions

Modifier `ios/SehaDigitalMobile/Info.plist`:

```xml
<key>NSCameraUsageDescription</key>
<string>Seha Digital a besoin d'accéder à votre caméra pour les consultations vidéo</string>
<key>NSMicrophoneUsageDescription</key>
<string>Seha Digital a besoin d'accéder à votre microphone pour les consultations vidéo</string>
<key>NSLocationWhenInUseUsageDescription</key>
<string>Seha Digital a besoin d'accéder à votre position pour trouver des médecins à proximité</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>Seha Digital a besoin d'accéder à vos photos pour télécharger des documents médicaux</string>
```

### 3. Configuration du certificat

1. Aller sur [Apple Developer](https://developer.apple.com)
2. Créer un App ID: `com.sehadigital.mobile`
3. Créer un Provisioning Profile (Distribution)
4. Télécharger et installer le certificat
5. Dans Xcode, aller dans "Signing & Capabilities"
6. Sélectionner votre équipe
7. Vérifier que le provisioning profile est correct

### 4. Build pour production

```bash
# Build depuis le terminal
npx react-native run-ios --configuration Release --device

# OU dans Xcode:
# 1. Product > Scheme > Edit Scheme
# 2. Run > Build Configuration > Release
# 3. Product > Archive
```

### 5. Upload vers App Store Connect

1. Après Archive, cliquer sur "Distribute App"
2. Choisir "App Store Connect"
3. Suivre l'assistant
4. Attendre le traitement (~15 minutes)

### 6. Soumission sur App Store

1. Aller sur [App Store Connect](https://appstoreconnect.apple.com)
2. Créer une nouvelle app
3. Remplir les métadonnées:
   - Nom: Seha Digital
   - Catégorie: Médecine
   - Screenshots (iPhone 6.7" et iPad Pro 12.9")
   - Description, keywords
4. Sélectionner le build uploadé
5. Soumettre pour révision

## 🔐 Configuration des services tiers

### Google Maps API

1. Aller sur [Google Cloud Console](https://console.cloud.google.com)
2. Créer un projet
3. Activer "Maps SDK for Android" et "Maps SDK for iOS"
4. Créer des clés API (une pour Android, une pour iOS)
5. Ajouter les restrictions:
   - Android: Ajouter le SHA-1 de votre keystore
   - iOS: Ajouter le Bundle ID

### WebRTC TURN Server

Pour la production, vous avez besoin d'un serveur TURN:

```bash
# Installation de coturn sur Ubuntu
sudo apt-get install coturn

# Configuration /etc/turnserver.conf
listening-port=3478
fingerprint
lt-cred-mech
user=username:password
realm=sehadigital.tn
```

### Notifications Push

#### Firebase Cloud Messaging (Android + iOS)

1. Créer un projet Firebase
2. Ajouter les apps Android et iOS
3. Télécharger `google-services.json` (Android)
4. Télécharger `GoogleService-Info.plist` (iOS)
5. Installer `@react-native-firebase/messaging`

```bash
npm install @react-native-firebase/app @react-native-firebase/messaging
cd ios && pod install && cd ..
```

## 📊 Analytics et Monitoring

### Configuration Firebase Analytics

```bash
npm install @react-native-firebase/analytics
```

### Configuration Sentry (Error Tracking)

```bash
npm install @sentry/react-native
npx @sentry/wizard -i reactNative -p ios android
```

Modifier `index.js`:

```javascript
import * as Sentry from '@sentry/react-native';

Sentry.init({
  dsn: 'votre_dsn_sentry',
  environment: 'production',
});
```

## 🧪 Tests avant production

### Tests unitaires

```bash
npm test
```

### Tests E2E avec Detox

```bash
# iOS
detox build --configuration ios.sim.release
detox test --configuration ios.sim.release

# Android
detox build --configuration android.emu.release
detox test --configuration android.emu.release
```

### Checklist de pré-lancement

- [ ] Tests unitaires passent (100% coverage critical paths)
- [ ] Tests E2E passent
- [ ] App testée sur vrais devices (iOS et Android)
- [ ] Performance validée (< 3s cold start)
- [ ] Crash rate < 0.1%
- [ ] API endpoints en production fonctionnels
- [ ] Certificats SSL valides
- [ ] Push notifications testées
- [ ] Deep links configurés
- [ ] Analytics trackent correctement
- [ ] Paiements testés (sandbox puis prod)
- [ ] Vidéo consultation testée
- [ ] Screenshots et descriptions prêts
- [ ] Privacy policy et Terms of Service publiés

## 🚀 Déploiement continu (CI/CD)

### GitHub Actions (exemple)

Créer `.github/workflows/build.yml`:

```yaml
name: Build and Deploy

on:
  push:
    branches: [ main ]

jobs:
  build-android:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      - name: Install dependencies
        run: |
          cd mobile
          npm install
      - name: Build Android
        run: |
          cd mobile/android
          ./gradlew bundleRelease
        env:
          KEYSTORE_PASSWORD: ${{ secrets.KEYSTORE_PASSWORD }}
          KEY_ALIAS: ${{ secrets.KEY_ALIAS }}
          KEY_PASSWORD: ${{ secrets.KEY_PASSWORD }}
      - name: Upload to Play Store
        uses: r0adkll/upload-google-play@v1
        with:
          serviceAccountJsonPlainText: ${{ secrets.SERVICE_ACCOUNT_JSON }}
          packageName: com.sehadigital.mobile
          releaseFiles: mobile/android/app/build/outputs/bundle/release/app-release.aab
          track: production
```

## 📈 Monitoring post-lancement

### Métriques à surveiller

- **Crash rate**: < 0.1%
- **ANR rate**: < 0.1%
- **Cold start time**: < 3s
- **API response time**: < 500ms
- **Video call quality**: > 90% success rate
- **User retention**: Day 1, Day 7, Day 30
- **Rating**: > 4.5 stars

### Outils recommandés

- **Firebase Crashlytics**: Crash reporting
- **Firebase Performance**: Performance monitoring
- **Google Analytics**: User behavior
- **Sentry**: Error tracking
- **Mixpanel**: Advanced analytics

## 🔄 Mises à jour OTA (Over-The-Air)

### CodePush par Microsoft

```bash
npm install -g appcenter-cli
appcenter login

# iOS
appcenter codepush release-react -a sehadigital/SehaDigital-iOS

# Android
appcenter codepush release-react -a sehadigital/SehaDigital-Android
```

Permet de pousser des mises à jour JavaScript sans passer par l'App Store/Play Store.

## 📞 Support

Pour toute question sur le déploiement:

- Email: dev@sehadigital.tn
- Documentation: https://docs.sehadigital.tn
- Slack: #mobile-deployment

## ✅ Checklist finale

### Android
- [ ] AAB généré et signé
- [ ] Testé sur 5+ devices physiques
- [ ] Screenshots 1080x1920 (5 minimum)
- [ ] Privacy Policy URL configurée
- [ ] Content rating rempli
- [ ] Prix et pays de distribution configurés
- [ ] Soumis pour révision

### iOS
- [ ] Archive créée et uploadée
- [ ] Testé sur 5+ devices physiques
- [ ] Screenshots iPhone et iPad
- [ ] App Store description en français et anglais
- [ ] Privacy policy et support URL
- [ ] Export Compliance configuré
- [ ] Soumis pour révision

---

**Dernière mise à jour**: Novembre 2025
**Version**: 4.0.0
