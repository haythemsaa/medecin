# 📸 Store Screenshots Guide - Seha Digital Mobile

Guide pour créer des screenshots professionnels pour Google Play Store et Apple App Store.

## 📋 Exigences

### Google Play Store

**Tailles requises:**
- **Phone:** 1080 x 1920 pixels minimum
- **7-inch Tablet:** 1024 x 1920 pixels
- **10-inch Tablet:** 1920 x 2560 pixels

**Nombre:** 2 minimum, 8 maximum

**Format:** PNG ou JPG (24-bit, pas de transparence)

### Apple App Store

**Tailles requises:**
- **6.7" Display (iPhone 14 Pro Max):** 1290 x 2796 pixels
- **6.5" Display (iPhone 11 Pro Max):** 1242 x 2688 pixels
- **5.5" Display (iPhone 8 Plus):** 1242 x 2208 pixels
- **12.9" iPad Pro:** 2048 x 2732 pixels

**Nombre:** 1-10 screenshots par taille

**Format:** PNG ou JPG (RGB, pas de transparence)

---

## 🎯 Screenshots Recommandés (5 minimum)

### 1. Login/Onboarding
**Message:** "Votre santé, notre priorité"
- Écran de login avec logo
- CTA: "Connexion rapide et sécurisée"

### 2. Recherche Médecins
**Message:** "Trouvez le bon médecin"
- Liste de médecins avec filtres
- Highlight: Distance, spécialité, tarif

### 3. Prise de Rendez-vous
**Message:** "Prenez RDV en quelques taps"
- Calendrier avec créneaux disponibles
- Highlight: Cabinet ou vidéo

### 4. Vidéo Consultation
**Message:** "Consultations vidéo HD"
- Écran de vidéo call
- Highlight: Sécurisé, pratique

### 5. Ordonnances & Rappels
**Message:** "Gérez vos traitements"
- Liste d'ordonnances
- Notifications de rappel

---

## 🛠 Outils Recommandés

### Pour créer les screenshots

1. **Real device screenshots** (Meilleur)
   - Prendre des captures sur vrais devices
   - Qualité maximale
   - Tailles exactes

2. **Simulators/Emulators** (OK)
   - Android Studio Emulator
   - Xcode iOS Simulator
   - Facile mais moins réaliste

3. **Design tools** (Pro)
   - Figma: Mockups professionnels
   - Sketch: Design Mac
   - Adobe XD: Design multi-plateforme

### Pour ajouter du texte/design

1. **Figma** (Gratuit, recommandé)
   - https://figma.com
   - Templates disponibles
   - Collaboration facile

2. **Canva** (Gratuit)
   - https://canva.com
   - Templates App Store
   - Drag & drop simple

3. **PlaceIt** (Payant)
   - https://placeit.net
   - Mockups professionnels
   - Device frames

---

## 📱 Méthode: Real Device Screenshots

### Android

#### Via Android Studio

1. Lancer l'app sur l'emulator:
```bash
npm run android
```

2. Dans Android Studio:
   - Tools > Device Manager
   - Pixel 5 (1080 x 2340)
   - Camera button pour screenshot
   - Sauvegarde dans `~/Desktop/`

#### Via ADB

```bash
# Prendre screenshot
adb exec-out screencap -p > screenshot.png

# Ou script automatique
for i in {1..5}; do
  echo "Screenshot $i - Press Enter when ready"
  read
  adb exec-out screencap -p > "screen_$i.png"
done
```

### iOS

#### Via Xcode Simulator

1. Lancer l'app:
```bash
npm run ios --simulator="iPhone 14 Pro Max"
```

2. Dans Simulator:
   - Cmd + S pour screenshot
   - Sauvegarde sur Desktop
   - Ou: File > Save Screen

#### Via Device

1. Connecter l'iPhone
2. Lancer l'app
3. Prendre screenshot (Power + Volume Up)
4. Airdrop vers Mac

---

## 🎨 Créer des Screenshots Professionnels

### Template Figma (Recommandé)

1. **Créer un nouveau fichier Figma**

2. **Frame pour chaque taille:**
```
iPhone 14 Pro Max: 1290 x 2796
Android Phone: 1080 x 1920
```

3. **Insérer votre screenshot**
   - Drag & drop l'image
   - Fill entire frame

4. **Ajouter des éléments:**

```
┌─────────────────────────┐
│                         │
│   [Your Screenshot]     │
│                         │
│                         │
│   ┌───────────────┐    │
│   │  Texte Titre   │    │
│   │  Sous-titre    │    │
│   └───────────────┘    │
│                         │
└─────────────────────────┘
```

5. **Export:**
   - Select frame
   - Export settings: PNG, 3x
   - Export

### Exemple de design

**Screenshot 1: Login**
```
Top 25%: Screenshot de l'écran login
Bottom 75%: Fond gradient teal
Texte overlaid:
  - Title: "Votre santé"
  - Subtitle: "Connexion sécurisée"
  - Badge: "✓ Données chiffrées"
```

**Screenshot 2: Search**
```
Top 70%: Screenshot recherche médecins
Bottom 30%: Fond blanc
Texte:
  - Title: "Trouvez votre médecin"
  - Bullet points:
    • Par spécialité
    • Par distance
    • Par disponibilité
```

---

## 📐 Sizes Cheat Sheet

### Android (Google Play)

```bash
# Phone (Pixel 5)
1080 x 2340

# Tablet 7"
1024 x 1920

# Tablet 10"
1920 x 2560
```

### iOS (App Store)

```bash
# iPhone 14 Pro Max (6.7")
1290 x 2796

# iPhone 13 Pro (6.1")
1170 x 2532

# iPhone SE (4.7")
750 x 1334

# iPad Pro 12.9"
2048 x 2732
```

---

## 🖼 Screenshots à capturer

### Essentiels (5)

1. **Login/Welcome** (`01_login.png`)
   - Écran de login
   - Logo visible
   - Design propre

2. **Search** (`02_search.png`)
   - Liste de médecins
   - Filtres visibles
   - Distance affichée

3. **Doctor Profile** (`03_doctor.png`)
   - Profil médecin complet
   - Rating visible
   - CTA "Prendre RDV"

4. **Booking** (`04_booking.png`)
   - Calendrier
   - Créneaux horaires
   - Type consultation

5. **Appointments** (`05_appointments.png`)
   - Liste RDV
   - Statuts visibles
   - Actions disponibles

### Bonus (3)

6. **Video Call** (`06_video.png`)
   - Consultation vidéo
   - Contrôles visibles
   - Qualité HD

7. **Prescriptions** (`07_prescriptions.png`)
   - Liste ordonnances
   - Badge renouvelable

8. **Profile** (`08_profile.png`)
   - Menu profil
   - Options visibles

---

## ✨ Tips pour de beaux screenshots

### Content

1. **Utiliser de vraies données réalistes**
   - Noms de médecins réels (avec permission)
   - Photos professionnelles
   - Données cohérentes

2. **Remplir les écrans**
   - Pas d'écrans vides
   - Au moins 3-5 items dans les listes
   - Avatars/images partout

3. **Statuts positifs**
   - RDV confirmés (vert)
   - Ratings élevés (4.5+)
   - Messages de succès

### Design

1. **Couleurs vives**
   - Notre teal (#14B8A6) doit être visible
   - Contrastes clairs
   - Pas trop de gris

2. **Texte lisible**
   - Font size minimum 12px
   - Contraste 4.5:1 minimum
   - Pas de texte trop petit

3. **Actions claires**
   - Boutons visibles
   - CTAs évidents
   - Navigation intuitive

### Technique

1. **Qualité maximale**
   - PNG 24-bit
   - Pas de compression excessive
   - Couleurs RGB (pas CMYK)

2. **Safe zones**
   - Pas de contenu important dans les 5% du bord
   - Centrer les éléments importants

3. **Cohérence**
   - Même style sur tous les screenshots
   - Même données dummy
   - Même heure/date

---

## 📝 Checklist Screenshots

### Avant de capturer

- [ ] App en mode Release (pas Debug)
- [ ] Données réalistes chargées
- [ ] Pas d'erreurs/warnings visibles
- [ ] Battery/signal bonne
- [ ] Heure cohérente (ex: 10:00)

### Pendant la capture

- [ ] Écran propre (pas de popups)
- [ ] Navigation claire
- [ ] Actions évidentes
- [ ] Texte lisible
- [ ] Images chargées

### Après capture

- [ ] Vérifier la taille (pixels exacts)
- [ ] Vérifier le format (PNG/JPG)
- [ ] Vérifier la qualité
- [ ] Pas de bords noirs
- [ ] Couleurs correctes

### Design (si édition)

- [ ] Texte ajouté lisible
- [ ] Pas de fautes d'orthographe
- [ ] Brand colors utilisées
- [ ] Call-to-action clair
- [ ] Compatible Safe zone

### Upload

- [ ] Nommage cohérent (01_login.png, etc.)
- [ ] Ordre logique
- [ ] 5-8 screenshots total
- [ ] Toutes les tailles requises

---

## 🚀 Script automatique

### Pour Android

```bash
#!/bin/bash
# screenshot_android.sh

echo "📸 Android Screenshots Generator"
echo "================================"

# Screens à capturer
screens=(
  "Login"
  "Search"
  "Doctor Profile"
  "Booking"
  "Appointments"
)

for i in "${!screens[@]}"; do
  number=$((i + 1))
  screen="${screens[$i]}"

  echo ""
  echo "Screenshot $number: $screen"
  echo "Navigate to the screen and press Enter..."
  read

  filename="android_$(printf %02d $number)_${screen// /_}.png"
  adb exec-out screencap -p > "$filename"

  echo "✅ Saved: $filename"
done

echo ""
echo "🎉 Done! Check your screenshots."
```

Usage:
```bash
chmod +x screenshot_android.sh
./screenshot_android.sh
```

### Pour iOS

```bash
#!/bin/bash
# screenshot_ios.sh

echo "📸 iOS Screenshots Generator"
echo "============================"
echo ""
echo "Open Simulator and launch the app."
echo "This script will help you organize screenshots."
echo ""

screens=(
  "Login"
  "Search"
  "Doctor_Profile"
  "Booking"
  "Appointments"
)

for i in "${!screens[@]}"; do
  number=$((i + 1))
  screen="${screens[$i]}"

  echo ""
  echo "Screenshot $number: $screen"
  echo "1. Navigate to the screen"
  echo "2. Press Cmd+S in Simulator"
  echo "3. Press Enter here when done..."
  read
done

echo ""
echo "🎉 Done! Find screenshots on your Desktop."
echo "Rename them to: ios_01_Login.png, etc."
```

---

## 🎯 Screenshots par Store

### Google Play (Priorité)

**Phone:**
1. `google_phone_01_login.png` - 1080 x 1920
2. `google_phone_02_search.png` - 1080 x 1920
3. `google_phone_03_doctor.png` - 1080 x 1920
4. `google_phone_04_booking.png` - 1080 x 1920
5. `google_phone_05_appointments.png` - 1080 x 1920

**Tablet 10":**
1. `google_tablet_01_search.png` - 1920 x 2560
(Minimum 1, recommandé 3)

### App Store (Priorité)

**6.7" Display (iPhone 14 Pro Max):**
1. `ios_67_01_login.png` - 1290 x 2796
2. `ios_67_02_search.png` - 1290 x 2796
3. `ios_67_03_doctor.png` - 1290 x 2796
4. `ios_67_04_booking.png` - 1290 x 2796
5. `ios_67_05_appointments.png` - 1290 x 2796

**6.5" Display (iPhone 11 Pro Max):**
1. `ios_65_01_login.png` - 1242 x 2688
(Minimum 1, recommandé 5)

**12.9" iPad Pro:**
1. `ios_ipad_01_search.png` - 2048 x 2732
(Optionnel)

---

## 🎨 Resources

### Dummy Data

**Médecins de test:**
- Dr. Ahmed Ben Salem - Cardiologue - 4.8★ - 150 TND
- Dr. Fatma Mansour - Dermatologue - 4.9★ - 120 TND
- Dr. Mohamed Trabelsi - Pédiatre - 4.7★ - 100 TND

**Images:**
- https://unsplash.com (photos libres)
- https://ui-avatars.com (avatars générés)

### Templates

- Figma: https://figma.com/templates/app-store-screenshots
- Canva: https://canva.com/templates/app-screenshots

### Guides officiels

- Google Play: https://support.google.com/googleplay/android-developer/answer/9866151
- App Store: https://developer.apple.com/app-store/product-page/

---

## ✅ Checklist finale

- [ ] 5+ screenshots capturés
- [ ] Toutes les tailles requises
- [ ] Qualité vérifiée (full resolution)
- [ ] Données réalistes
- [ ] Pas d'informations sensibles
- [ ] Texte ajouté (optionnel)
- [ ] Nommage cohérent
- [ ] Prêt pour upload

---

## 🎉 Résultat

Vous aurez:
- 5-8 screenshots professionnels
- Toutes les tailles pour Google Play
- Toutes les tailles pour App Store
- Prêt pour soumission stores!

**Temps estimé:** 2-3 heures

---

**Dernière mise à jour:** Novembre 2025
**Version:** 4.0.0
