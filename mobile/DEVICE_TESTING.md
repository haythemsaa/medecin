# 📱 Device Testing Guide - Seha Digital Mobile

Guide complet pour tester l'application sur des appareils physiques iOS et Android.

## 🎯 Objectif

Tester l'app sur **vrais devices** avant la soumission aux stores pour garantir:
- ✅ Performances réelles
- ✅ Fonctionnement caméra/micro
- ✅ Géolocalisation précise
- ✅ Notifications push
- ✅ UX sur différentes tailles d'écran

---

## 📋 Checklist Pré-Test

### Configuration Backend

```bash
# 1. Backend doit être accessible depuis le réseau
cd ../backend
php artisan serve --host=0.0.0.0 --port=8000

# 2. Trouver votre IP locale
# macOS/Linux:
ifconfig | grep inet

# Windows:
ipconfig

# Exemple: 192.168.1.100
```

### Configuration Mobile

Modifier `mobile/.env`:

```env
# Utiliser l'IP de votre machine
API_URL=http://192.168.1.100:8000/api
```

Rebuild l'app:

```bash
cd mobile

# Android
npm run android

# iOS
npm run ios
```

---

## 🤖 Tests Android

### Appareil physique

#### 1. Activer le Mode Développeur

1. Aller dans **Paramètres > À propos du téléphone**
2. Taper 7 fois sur "Numéro de build"
3. Mode développeur activé!

#### 2. Activer le Débogage USB

1. **Paramètres > Options pour les développeurs**
2. Activer "Débogage USB"
3. Connecter le téléphone via USB

#### 3. Vérifier la connexion

```bash
# Liste les appareils connectés
adb devices

# Output attendu:
# List of devices attached
# ABC123DEF456    device
```

#### 4. Lancer l'app

```bash
npm run android

# Ou spécifier le device
npx react-native run-android --deviceId=ABC123DEF456
```

### Tests à effectuer

#### Fonctionnalités de base
- [ ] Login / Register
- [ ] Navigation bottom tabs
- [ ] Refresh (pull to refresh)
- [ ] Scroll performance
- [ ] Images chargent correctement

#### Recherche & Booking
- [ ] Recherche médecins
- [ ] Filtres par spécialité
- [ ] Affichage carte (maps)
- [ ] Géolocalisation fonctionne
- [ ] Prise de RDV
- [ ] Calendrier responsive
- [ ] Sélection heure

#### Caméra & Micro
- [ ] Permissions caméra/micro demandées
- [ ] Caméra s'ouvre
- [ ] Micro fonctionne
- [ ] Vidéo consultation (WebRTC)
- [ ] Contrôles vidéo (mute, video off)

#### Notifications
- [ ] Permission notifications demandée
- [ ] Notification de test reçue
- [ ] Tap sur notification ouvre l'app
- [ ] Son de notification

#### Performance
- [ ] App démarre en < 3s
- [ ] Pas de freeze/lag
- [ ] Transitions fluides
- [ ] Scrolling smooth (60 FPS)

### Tailles d'écran à tester

| Appareil | Résolution | Taille |
|----------|-----------|---------|
| **Petit** | 720x1280 | 5" |
| **Moyen** | 1080x1920 | 5.5" |
| **Grand** | 1440x2560 | 6.5" |
| **Tablet** | 1200x1920 | 10" |

### Versions Android à tester

- ✅ **Android 7.0** (API 24) - Minimum
- ✅ **Android 10** (API 29) - Populaire
- ✅ **Android 13** (API 33) - Target
- ✅ **Android 14** (API 34) - Latest

---

## 🍎 Tests iOS

### Appareil physique

#### 1. Configurer le compte développeur

1. Ouvrir Xcode
2. Preferences > Accounts
3. Ajouter votre Apple ID
4. Créer un "Personal Team" (gratuit)

#### 2. Configurer le projet

```bash
open ios/SehaDigitalMobile.xcworkspace
```

Dans Xcode:
1. Sélectionner le projet
2. General > Signing & Capabilities
3. Team: Sélectionner votre team
4. Bundle Identifier: `com.sehadigital.mobile`
5. Signing: Automatically manage signing

#### 3. Connecter l'iPhone

1. Connecter via USB
2. "Trust this computer" sur l'iPhone
3. Dans Xcode, sélectionner votre iPhone dans la liste

#### 4. Lancer l'app

```bash
# Via React Native
npm run ios --device="iPhone de [Votre Nom]"

# Ou via Xcode
# Product > Run (Cmd+R)
```

#### 5. Première installation

Sur l'iPhone:
1. **Paramètres > Général > Gestion des appareils**
2. Faire confiance au développeur
3. Relancer l'app

### Tests à effectuer

#### Fonctionnalités de base
- [ ] Login / Register
- [ ] Navigation bottom tabs
- [ ] Refresh (pull to refresh)
- [ ] Scroll performance
- [ ] Images chargent correctement
- [ ] Gestures (swipe, pinch)

#### Recherche & Booking
- [ ] Recherche médecins
- [ ] Filtres par spécialité
- [ ] Affichage carte (maps)
- [ ] Géolocalisation fonctionne
- [ ] Prise de RDV
- [ ] Calendrier responsive

#### Caméra & Micro
- [ ] Permissions demandées (caméra, micro)
- [ ] Caméra s'ouvre
- [ ] Micro fonctionne
- [ ] Vidéo consultation (WebRTC)
- [ ] Rotation device (portrait/landscape)

#### Notifications
- [ ] Permission notifications demandée
- [ ] Notification de test reçue
- [ ] Tap sur notification ouvre l'app
- [ ] Badge icon mis à jour
- [ ] Notification dans Lock Screen

#### iOS Specific
- [ ] Safe Area gérée correctement
- [ ] Notch (iPhone X+) géré
- [ ] Dynamic Island (iPhone 14 Pro+)
- [ ] Dark mode (si implémenté)
- [ ] Haptic feedback

### Tailles d'écran à tester

| Appareil | Résolution | Taille |
|----------|-----------|---------|
| **iPhone SE** | 750x1334 | 4.7" |
| **iPhone 13** | 1170x2532 | 6.1" |
| **iPhone 14 Plus** | 1284x2778 | 6.7" |
| **iPhone 14 Pro Max** | 1290x2796 | 6.7" |
| **iPad** | 1620x2160 | 10.2" |

### Versions iOS à tester

- ✅ **iOS 13.0** - Minimum
- ✅ **iOS 15.0** - Populaire
- ✅ **iOS 17.0** - Target
- ✅ **iOS 17.2** - Latest

---

## 🎯 Scénarios de Test Complets

### Scénario 1: Patient Book Appointment

**Étapes:**
1. Ouvrir l'app
2. Login avec compte test
3. Tap "Rechercher" dans bottom tab
4. Filtrer par "Cardiologue"
5. Tap sur un médecin
6. Voir profil complet
7. Tap "Prendre rendez-vous"
8. Sélectionner "Cabinet"
9. Choisir date (demain)
10. Choisir heure (10:00)
11. Entrer motif
12. Confirmer

**Résultat attendu:** RDV créé, confirmation affichée

**Durée:** ~2 minutes

### Scénario 2: Video Consultation

**Étapes:**
1. Créer un RDV vidéo
2. Confirmer le RDV (côté médecin ou API)
3. Dans "Rendez-vous", tap sur RDV vidéo
4. Tap "Rejoindre"
5. Autoriser caméra/micro
6. Attendre connexion
7. Tester mute
8. Tester video off
9. Terminer l'appel

**Résultat attendu:** Vidéo fonctionne, contrôles réactifs

**Durée:** ~3 minutes

### Scénario 3: Medication Reminder

**Étapes:**
1. Aller dans Profile
2. Tap "Rappels médicaments"
3. Tap "Ajouter"
4. Entrer nom médicament
5. Choisir horaire (dans 2 min pour test)
6. Sauvegarder
7. Attendre notification

**Résultat attendu:** Notification reçue à l'heure exacte

**Durée:** ~5 minutes

### Scénario 4: Urgent Consultation

**Étapes:**
1. Tap "Consultation urgente"
2. Voir médecins disponibles
3. Entrer motif urgent
4. Sélectionner médecin
5. Voir tarif +50%
6. Confirmer demande

**Résultat attendu:** Demande envoyée

**Durée:** ~1 minute

---

## 🔍 Tests Réseau

### WiFi

- [ ] Connexion WiFi normale
- [ ] WiFi lent (simuler avec Network Link Conditioner)
- [ ] Déconnexion WiFi (erreur gérée?)
- [ ] Reconnexion WiFi (retry auto?)

### 4G/5G

- [ ] Connexion 4G
- [ ] Connexion 5G
- [ ] Passage WiFi → 4G
- [ ] Passage 4G → WiFi

### Offline

- [ ] Mode avion activé
- [ ] Erreurs affichées correctement
- [ ] Retry button fonctionne
- [ ] Reconnexion auto

---

## 🔋 Tests Batterie & Performance

### Battery Usage

```bash
# Android
adb shell dumpsys batterystats --reset
# Utiliser l'app 30 min
adb shell dumpsys batterystats

# iOS
Xcode > Instruments > Energy Log
```

**Acceptable:**
- < 5% batterie/heure en usage normal
- < 1% batterie/heure en background

### Memory Usage

```bash
# Android
adb shell dumpsys meminfo com.sehadigital.mobile

# iOS
Xcode > Product > Profile > Allocations
```

**Acceptable:**
- < 200 MB en usage normal
- Pas de memory leaks

### CPU Usage

**Acceptable:**
- < 20% CPU en idle
- < 60% CPU en vidéo consultation
- Retour à idle après action

---

## 📊 Outils de Debugging

### Android

```bash
# Logs en temps réel
adb logcat | grep ReactNative

# Captures d'écran
adb exec-out screencap -p > screen.png

# Vidéo de l'écran
adb shell screenrecord /sdcard/demo.mp4

# Inspector React Native
Shake device > Toggle Inspector
```

### iOS

```bash
# Logs Xcode
Cmd+0 > Show Reports Console

# Captures d'écran
Cmd+S dans Simulator

# Inspector
Cmd+D > Toggle Inspector
```

### React Native Debugger

```bash
# Standalone
npm install -g react-native-debugger

# Lancer
react-native-debugger

# Dans l'app
Shake > Debug
```

---

## 🐛 Problèmes Courants

### App ne s'installe pas (Android)

```bash
# Clear cache
cd android && ./gradlew clean
npm start --reset-cache

# Désinstaller l'ancienne version
adb uninstall com.sehadigital.mobile

# Reinstaller
npm run android
```

### App crash au démarrage (iOS)

```bash
# Clean build
cd ios
rm -rf build
pod deintegrate && pod install
cd ..
npm run ios
```

### WebRTC ne fonctionne pas

- Vérifier permissions caméra/micro
- Utiliser HTTPS pour le backend (ou exception)
- Vérifier firewall

### Maps ne s'affichent pas

- Vérifier Google Maps API Key
- Activer Maps SDK dans Google Cloud
- Vérifier restrictions API Key

---

## ✅ Checklist Finale

### Android
- [ ] Testé sur 3+ devices physiques
- [ ] Toutes fonctionnalités validées
- [ ] Permissions testées
- [ ] Performance acceptable
- [ ] Pas de crash
- [ ] Notifications fonctionnent
- [ ] Vidéo fonctionne
- [ ] Maps s'affichent

### iOS
- [ ] Testé sur 3+ devices physiques
- [ ] Toutes fonctionnalités validées
- [ ] Permissions testées
- [ ] Performance acceptable
- [ ] Pas de crash
- [ ] Notifications fonctionnent
- [ ] Vidéo fonctionne
- [ ] Maps s'affichent
- [ ] Safe Area OK
- [ ] Gestures OK

### Cross-Platform
- [ ] Design cohérent iOS/Android
- [ ] Même fonctionnalités
- [ ] Même navigation
- [ ] Même performance

---

## 📝 Rapport de Test

Utiliser ce template:

```markdown
# Test Report - Seha Digital Mobile

**Date:** YYYY-MM-DD
**Tester:** [Nom]
**Version:** 4.0.0
**Build:** [Number]

## Devices Tested

### Android
- Device 1: [Modèle, Android version]
- Device 2: [Modèle, Android version]
- Device 3: [Modèle, Android version]

### iOS
- Device 1: [Modèle, iOS version]
- Device 2: [Modèle, iOS version]
- Device 3: [Modèle, iOS version]

## Test Results

| Feature | Android | iOS | Notes |
|---------|---------|-----|-------|
| Login/Register | ✅ | ✅ | - |
| Search Doctors | ✅ | ✅ | - |
| Book Appointment | ✅ | ⚠️ | iOS: Slow calendar |
| Video Consultation | ✅ | ✅ | - |
| Notifications | ✅ | ❌ | iOS: Not received |
| Maps | ✅ | ✅ | - |

## Issues Found

1. **iOS Notifications not working**
   - Priority: High
   - Steps to reproduce: ...
   - Expected: ...
   - Actual: ...

## Performance

- Cold start: 2.1s (Android), 1.8s (iOS)
- Memory usage: 180MB (Android), 150MB (iOS)
- Battery drain: 4%/hour (both)

## Recommendation

- [ ] Ready for production
- [ ] Needs fixes before release
```

---

## 🎉 Résultat

Après ces tests, vous aurez:

✅ Confiance dans l'app
✅ Liste des bugs à corriger
✅ Métriques de performance
✅ Validation sur vrais devices
✅ Prêt pour la soumission stores!

**Durée totale tests:** 4-6 heures

---

**Dernière mise à jour:** Novembre 2025
**Version:** 4.0.0
