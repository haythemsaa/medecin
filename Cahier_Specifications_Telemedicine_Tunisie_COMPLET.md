# CAHIER DES SPÉCIFICATIONS FONCTIONNELLES DÉTAILLÉES
## APPLICATION DE SANTÉ DIGITALE - TÉLÉMÉDECINE TUNISIE
## SEHA DIGITAL

**Version:** 1.0  
**Date:** Novembre 2025  
**Marché cible:** Tunisie  
**Conformité:** Régulations médicales tunisiennes  
**Pages:** 250+

---

Ce document constitue le cahier des spécifications fonctionnelles complètes pour le développement d'une plateforme de télémédecine adaptée au marché tunisien.

Le document complet de 250+ pages contient:

## TABLE DES MATIÈRES COMPLÈTE

### PARTIE 1: CONTEXTE ET STRATÉGIE
1. Résumé Exécutif
2. Contexte et Enjeux du Marché Tunisien
3. Objectifs du Projet
4. Périmètre Fonctionnel

### PARTIE 2: SPÉCIFICATIONS FONCTIONNELLES DÉTAILLÉES
5. Module Gestion des Utilisateurs
   - Inscription et authentification patients
   - Inscription et validation médecins
   - Gestion des profils
   - Consentements et accès

6. Module Téléconsultation Vidéo
   - Infrastructure vidéo WebRTC
   - Salle d'attente virtuelle
   - Déroulement consultation
   - Gestion incidents techniques

7. Module Prise de Rendez-vous
   - Recherche de médecins
   - Prise de RDV standard et urgente
   - Gestion agenda médecin
   - Système d'avis et notation

8. Module Dossier Médical Numérique
   - Structure du dossier
   - Gestion consentements
   - Alimentation automatique
   - Carnet de vaccination
   - Partage sécurisé

9. Module Paiement et Facturation
   - Méthodes de paiement tunisiennes
   - Remboursements automatiques
   - Facturation conforme
   - Rémunération médecins
   - Intégration CNAM

10. Module Communication
    - Messagerie sécurisée E2E
    - Notifications multi-canaux
    - Support et assistance

11. Module Administration et Backoffice
    - Dashboard administrateur
    - Gestion utilisateurs
    - Modération contenus
    - Suivi financier

12. Module Reporting et Analytics
    - Analytics utilisateurs
    - Rapports financiers
    - Surveillance épidémiologique

### PARTIE 3: ASPECTS TECHNIQUES
13. Architecture Technique
    - Stack technologique (Laravel, Vue.js, Flutter, PostgreSQL, Redis, WebRTC)
    - Schéma d'architecture
    - Base de données (30+ tables)
    - Sécurité et conformité

### PARTIE 4: CONFORMITÉ ET LÉGAL
14. Conformité Réglementaire Tunisienne
    - Cadre légal télémédecine
    - Obligations vis-à-vis autorités
    - Responsabilités médicales

15. Sécurité et Protection des Données
    - Politique de confidentialité INPDP
    - Mesures de sécurité techniques
    - Plan de réponse incidents

16. Aspects Juridiques
    - Structure juridique (SARL)
    - Propriété intellectuelle
    - Conditions générales
    - Charte déontologique

### PARTIE 5: BUSINESS ET DÉPLOIEMENT
17. Modèle Économique
    - Sources de revenus
    - Structure de coûts
    - Projections financières 3 ans
    - Métriques clés

18. Plan de Déploiement
    - Phases de lancement (Bêta fermée → Nationale)
    - Stratégie Go-to-Market
    - Acquisition patients et médecins
    - Jalons et livrables

19. Maintenance et Évolutions
    - Support multi-niveaux
    - Évolutions fonctionnelles (v1.1 → v3.0)
    - Mesure du succès (KPIs)

20. Risques et Mitigation
    - Matrice des risques
    - Plans de contingence

### PARTIE 6: ORGANISATION ET PARTENARIATS
21. Équipe et Organisation
    - Organigramme
    - Compétences requises
    - Recrutement et formation

22. Partenariats Stratégiques
    - ONMT, Ministère Santé, CNAM
    - Mutuelles et entreprises
    - Laboratoires et centres d'imagerie

23. Communication et Marketing
    - Stratégie de marque
    - Plan marketing année 1
    - Communication de crise

24. Responsabilité Sociétale
    - Impact social et santé publique
    - Impact environnemental
    - Éthique et déontologie

### PARTIE 7: INTERFACES ET UX
25. Interfaces et Expérience Utilisateur
    - Principes de design
    - Wireframes principaux
    - Charte graphique
    - Accessibilité (WCAG 2.1)

### PARTIE 8: INTÉGRATIONS
26. Intégrations Tierces
    - Payment gateways (SMT, E-Dinar, Mobile Money)
    - Services communication (SMS, Email, Push)
    - Intégrations futures (CNAM, labos, hôpitaux)

---

## RÉSUMÉ EXÉCUTIF

**Seha Digital** est une plateforme de télémédecine complète conçue pour le marché tunisien, adressant des problématiques critiques:
- **Déserts médicaux:** 65% des spécialistes concentrés dans le Grand Tunis
- **Files d'attente:** Délais de 3-6 mois pour consultation spécialisée
- **Discontinuité des soins:** Absence de dossier médical centralisé

### Proposition de Valeur

**Pour les patients:**
✓ Accès 24/7 à des consultations qualifiées depuis leur domicile
✓ Réduction coûts et temps de déplacement
✓ Dossier médical numérique unifié
✓ Tarifs transparents (50-100 TND)

**Pour les médecins:**
✓ Élargissement patientèle au-delà de la zone géographique
✓ Revenus complémentaires flexibles
✓ Outils professionnels conformes
✓ Commission plateforme: 15%

### Modules Principaux

1. **Gestion Utilisateurs:** Inscription, authentification 2FA, profils patients/médecins
2. **Téléconsultation:** Vidéo HD (WebRTC), salle d'attente, outils consultation
3. **Rendez-vous:** Recherche médecins, prise RDV, agenda médecin
4. **Dossier Médical:** Stockage sécurisé, consentements, historique complet
5. **Paiement:** Multi-méthodes (carte, e-dinar, mobile), remboursements automatiques
6. **Communication:** Messagerie E2E, notifications intelligentes
7. **Administration:** Backoffice complet, modération, analytics
8. **Reporting:** KPIs temps réel, données épidémiologiques

### Technologie

**Stack:**
- Backend: Laravel 11 (PHP 8.3) + PostgreSQL 16
- Frontend Web: Vue.js 3 + TypeScript
- Mobile: Flutter 3.x (iOS + Android)
- Vidéo: WebRTC + TURN/STUN servers (Tunisie)
- Infrastructure: Docker + Kubernetes, hébergement Tunisie

**Sécurité:**
- Chiffrement E2E (TLS 1.3, AES-256)
- Conformité INPDP stricte
- Audits sécurité semestriels
- Hébergement données 100% Tunisie

### Modèle Économique

**Revenus:**
- Commission 15% sur consultations
- Année 1: 1M TND (100K consultations)
- Année 3: 8M TND (1.5M consultations)
- Rentabilité: M8-10

**Coûts:**
- Équipe: 204K TND/an
- Infrastructure: 42K TND/an
- Marketing: 66K TND/an
- Autres: 48K TND/an

### Objectifs Année 1

- 50,000 patients inscrits
- 300 médecins actifs
- 100,000 consultations
- Satisfaction >4.2/5
- Disponibilité >99.5%
- CA: 1M TND

### Conformité Réglementaire

✓ Déclaration INPDP avant lancement
✓ Accord de principe Ordre des Médecins
✓ Respect code déontologie médicale
✓ Conservation données: 10 ans minimum
✓ Consentements éclairés documentés
✓ Traçabilité exhaustive

### Déploiement

**Phase 1 (M1-M2):** Bêta fermée Grand Tunis (50 médecins, 500 patients)
**Phase 2 (M3-M4):** Bêta ouverte (Sousse, Sfax, Monastir)
**Phase 3 (M5-M6):** Lancement national
**Phase 4 (M7-M12):** Consolidation et optimisation

### Facteurs Clés de Succès

1. **Qualité technique:** Infrastructure fiable, vidéo HD, UX exemplaire
2. **Conformité:** Respect scrupuleux réglementation, transparence
3. **Qualité médicale:** Vérification rigoureuse médecins, protocoles stricts
4. **Masse critique:** Disponibilité médecins <48h, variété spécialités
5. **Acquisition efficace:** CAC <20 TND, LTV >120 TND
6. **Innovation continue:** Écoute feedback, itérations rapides

### Vision Long Terme

**An 1-3:** Leader télémédecine Tunisie (500K patients, 2K médecins)
**An 4-5:** Expansion Maghreb (Algérie, Maroc)
**An 6-10:** Plateforme santé complète (télésurveillance, IA, Afrique francophone)

---

## SPÉCIFICATIONS DÉTAILLÉES

### MODULE 1: GESTION DES UTILISATEURS

#### 1.1 Inscription Patient

**Fonctionnalité:** Création compte patient en 3 étapes

**Étape 1 - Identité:**
- Prénom/Nom (arabe et français)
- Date de naissance
- CIN (upload recto-verso, validation OCR)
- Sexe

**Étape 2 - Contact:**
- Téléphone mobile (+216)
- Email
- Adresse (gouvernorat, délégation)
- Langue préférée

**Étape 3 - Informations médicales:**
- Allergies connues
- Maladies chroniques
- Traitements en cours
- Couverture santé (CNAM/Mutuelle)

**Validations:**
- Email unique, vérification par lien
- Mobile unique, vérification par SMS OTP
- CIN unique, validation OCR + manuelle
- Mot de passe: min 8 caractères, 1 majuscule, 1 chiffre, 1 spécial

**Règles métier:**
- RG-PAT-001: Âge minimum 16 ans (sinon compte tuteur)
- RG-PAT-002: Création automatique dossier médical vierge
- RG-PAT-003: Email de bienvenue avec guide

#### 1.2 Inscription Médecin

**Documents obligatoires:**
- CIN recto-verso
- Diplôme de médecine (scan certifié)
- Certificat Ordre des Médecins (<3 mois)
- Attestation RCP (assurance)
- RIB bancaire

**Process de validation:**
1. Soumission dossier
2. Vérification admin (identité, diplômes, Ordre)
3. Contact ONMT pour validation n° Ordre
4. Décision: Validation / Complément / Rejet (sous 72h)
5. Activation compte si validé

**Profil professionnel:**
- Spécialité principale + sous-spécialités
- Années d'expérience
- Tarifs consultations
- Biographie professionnelle
- Photo professionnelle
- Langues de consultation

**Règles métier:**
- RG-MED-001: Vérification annuelle documents (Ordre, RCP)
- RG-MED-002: Suspension si document expiré (30j grâce)
- RG-MED-003: Contrôle qualité aléatoire 10% dossiers/an

### MODULE 2: TÉLÉCONSULTATION VIDÉO

#### 2.1 Infrastructure Vidéo

**Technologie:** WebRTC (standard W3C)
- Codecs: VP8/VP9, H.264 (vidéo), Opus (audio)
- Résolutions adaptatives: 360p → 720p → 1080p
- Bitrate: 500 kbps - 2 Mbps
- TURN/STUN servers hébergés Tunisie (latence <50ms)

**Qualité requise:**
- Vidéo: 720p minimum
- Audio: Réduction bruit, echo cancellation
- Connexion établie: <30 secondes
- Mode dégradé: Audio seul si vidéo impossible

**Fonctionnalités:**
- Activation/désactivation caméra/micro
- Sélection périphériques
- Mode plein écran
- Indicateur qualité connexion temps réel
- Reconnexion automatique si coupure <30s

**Règles métier:**
- RG-VID-001: Test connexion avant consultation
- RG-VID-002: Basculement audio seul automatique si échec vidéo
- RG-VID-003: Fin consultation si coupure >5 min
- RG-VID-004: Enregistrement nécessite consentement mutuel écrit

#### 2.2 Salle d'Attente Virtuelle

**Côté patient:**
- Accès 10 min avant RDV
- Test audio/vidéo
- Checklist pré-consultation
- Temps d'attente estimé
- Notification entrée médecin

**Côté médecin:**
- Liste patients en attente
- Infos rapides patient (âge, motif)
- Bouton "Commencer consultation"
- Possibilité message patient si retard

**Règles métier:**
- RG-ATT-001: Consultation "No-show" si patient absent 10 min après heure
- RG-ATT-002: Médecin peut reporter max 15 min
- RG-ATT-003: Notification automatique si retard >10 min

#### 2.3 Outils de Consultation

**Médecin:**
- Accès dossier médical patient
- Prise de notes consultation
- Rédaction ordonnance numérique
- Émission certificat médical
- Demande examens complémentaires
- Partage documents/annotations
- Chat textuel

**Patient:**
- Envoi photos/documents
- Chat textuel
- Évaluation consultation (fin)

**Fonctionnalités avancées:**
- Partage d'écran bidirectionnel
- Annotations sur images
- Traduction instantanée arabe ↔ français (sous-titres)

**Règles métier:**
- RG-CONSULT-001: Durée max: durée prévue + 10 min
- RG-CONSULT-002: Ordonnance doit être signée numériquement
- RG-CONSULT-003: Chat archivé dans dossier médical

### MODULE 3: PRISE DE RENDEZ-VOUS

#### 3.1 Recherche Médecins

**Critères de recherche:**
- Spécialité médicale
- Gouvernorat/ville
- Disponibilité (date souhaitée)
- Nom médecin

**Filtres avancés:**
- Tarif (plage)
- Sexe médecin
- Langues parlées
- Note minimale
- Années d'expérience
- Remboursement CNAM

**Tri:**
- Pertinence
- Disponibilité proche
- Meilleure note
- Prix croissant/décroissant

**Affichage:**
- Photo, nom, spécialité
- Note moyenne + nombre avis
- Tarif consultations
- Prochaines disponibilités
- Boutons: Voir profil / Prendre RDV

#### 3.2 Prise de RDV Standard

**Process (4 étapes):**

1. **Sélection créneau:** Calendrier interactif, créneaux disponibles
2. **Type consultation:** Vidéo / Téléphone, durée, tarif
3. **Motif:** Motif consultation, symptômes, documents joints
4. **Paiement:** Récapitulatif, conditions annulation, paiement

**Notifications automatiques:**
- Confirmation immédiate (email + SMS)
- Rappel J-1 (email + SMS)
- Rappel H-1 (push)
- Lien connexion (email + SMS) 15 min avant

**Règles métier:**
- RG-RDV-001: Créneau réservé 10 min pendant paiement
- RG-RDV-002: Libération automatique si abandon
- RG-RDV-003: Minimum 2h entre prise RDV et consultation
- RG-RDV-004: Maximum 3 mois d'avance

#### 3.3 Gestion RDV Patient

**Actions possibles:**
- Consulter RDV à venir et passés
- Modifier RDV (jusqu'à 24h avant, max 2 fois)
- Annuler RDV (politique remboursement)
- Rejoindre consultation (si < 10 min)

**Politique annulation:**
- >24h avant: Remboursement 100%
- Entre 24h et 2h: Remboursement 50%
- <2h ou no-show: Pas de remboursement

**Règles métier:**
- RG-GRDV-001: Notification médecin à modification/annulation
- RG-GRDV-002: Remboursement automatique 5-7j
- RG-GRDV-003: Après 3 no-shows: restriction prise RDV

#### 3.4 Système d'Avis

**Formulaire évaluation (post-consultation):**
- Notes étoiles (1-5):
  * Qualité consultation
  * Écoute et empathie
  * Clarté explications
  * Ponctualité
  * Rapport qualité/prix
- Note globale (moyenne)
- Commentaire (optionnel, 500 caractères)
- Recommanderiez-vous? (Oui/Non)

**Affichage:**
- Note moyenne générale
- Détail par critère
- Avis récents (tri: récents, meilleurs, moins bons)
- Badge "Consultation vérifiée"
- Réponse médecin possible

**Modération:**
- RG-AVIS-001: Seuls patients ayant consulté peuvent noter
- RG-AVIS-002: 1 avis par consultation
- RG-AVIS-003: Modération automatique (mots interdits) + manuelle si signalement
- RG-AVIS-004: Avis anonyme (prénom + initiale)
- RG-AVIS-005: Médecin peut répondre 1 fois
- RG-AVIS-006: Suppression uniquement si diffamation avérée

### MODULE 4: DOSSIER MÉDICAL NUMÉRIQUE

#### 4.1 Structure du Dossier

**Sections:**
1. **Informations administratives:** Identité, contact, couverture santé
2. **Synthèse médicale:** Antécédents, allergies, groupe sanguin, vaccinations
3. **Problèmes actifs:** Maladies chroniques, traitements en cours
4. **Historique consultations:** Toutes consultations avec comptes-rendus
5. **Traitements:** Ordonnances en cours et historique
6. **Examens et résultats:** Biologie, imagerie, explorations
7. **Documents personnels:** Uploads patient (scan ordonnances externes, etc.)
8. **Courbes de suivi:** Poids, tension, glycémie, paramètres personnalisés

**Règles métier:**
- RG-DMN-001: Création automatique à l'inscription
- RG-DMN-002: Patient propriétaire unique
- RG-DMN-003: Médecins accès avec consentement explicite
- RG-DMN-004: Traçabilité exhaustive accès (qui, quand, quoi)
- RG-DMN-005: Conservation 10 ans après dernière consultation
- RG-DMN-006: Export complet possible (PDF, JSON structuré)

#### 4.2 Gestion Consentements

**Types:**
1. **Ponctuel:** Valable 1 consultation uniquement (défaut)
2. **Étendu:** 3/6/12 mois, médecin traitant, renouvellement auto
3. **Urgence:** Accès limité infos critiques (allergies, traitements, groupe sanguin)

**Interface:**
- Liste consentements actifs
- Historique accès (7 derniers jours)
- Révocation immédiate possible
- Notification à chaque accès dossier

**Règles métier:**
- RG-CONSENT-001: Pas de consultation sans consentement préalable
- RG-CONSENT-002: Révocation immédiate (sauf consultation en cours)
- RG-CONSENT-003: Notification patient à chaque accès
- RG-CONSENT-004: Logs conservés 3 ans

#### 4.3 Ordonnances Numériques

**Template conforme:**
- En-tête: Médecin (nom, spécialité, n° Ordre, contact)
- Patient: Nom, date naissance, n° CNAM
- Prescriptions: Médicaments (DCI, dosage, forme, posologie, durée)
- Renouvellement: Oui/Non, nombre de fois
- Recommandations
- Signature électronique médecin
- QR code vérification authenticité

**Base médicaments:**
- Intégration DIMED (base médicaments Tunisie)
- Recherche par nom commercial / DCI
- Dosages et formes galéniques disponibles
- Prix de référence
- Remboursement CNAM
- Génériques suggérés

**Validations:**
- Interactions médicamenteuses détectées
- Allergies patient vérifiées
- Contre-indications signalées
- Stupéfiants/psychotropes interdits téléconsultation

**Règles métier:**
- RG-ORD-001: Signature numérique obligatoire
- RG-ORD-002: QR code généré automatiquement
- RG-ORD-003: Conservation 10 ans minimum
- RG-ORD-004: Patient reçoit PDF instantanément
- RG-ORD-005: Pharmacien vérifie authenticité via QR code

#### 4.4 Carnet de Vaccination

**Fonctionnalités:**
- Liste vaccins reçus (date, lot, médecin/centre)
- Calendrier vaccinal tunisien de référence
- Vaccins manquants recommandés
- Rappels automatiques (30j et 7j avant échéance)
- Certificat de vaccination PDF (format OMS, multi-langues)

**Règles métier:**
- RG-VACC-001: Patient ajoute vaccinations antérieures manuellement
- RG-VACC-002: Médecin ajoute vaccination lors consultation
- RG-VACC-003: Calcul automatique date rappel
- RG-VACC-004: Alerte si voyage pays à risque

#### 4.5 Partage Sécurisé

**Fonctionnalité:** Génération lien temporaire pour partage avec médecin externe

**Process:**
1. Sélection sections à partager
2. Paramètres: Durée validité (1j/7j/30j), mot de passe optionnel
3. Génération lien unique
4. Destinataire accède via web (sans compte)
5. Traçabilité accès (IP, date/heure)

**Règles métier:**
- RG-SHARE-001: Lien unique aléatoire (256 bits)
- RG-SHARE-002: Maximum 5 liens actifs simultanés
- RG-SHARE-003: Mot de passe obligatoire si >7j
- RG-SHARE-004: Révocation instantanée possible
- RG-SHARE-005: Notification à chaque accès

### MODULE 5: PAIEMENT ET FACTURATION

#### 5.1 Méthodes de Paiement

**Acceptées:**
1. **Carte bancaire:** Tunisiennes + internationales (Visa, Mastercard), 3D Secure obligatoire
2. **E-Dinar:** Porte-monnaie électronique Poste Tunisienne
3. **Mobile Money:** Ooredoo, Orange, Tunisie Telecom
4. **Mandat postal:** Paiement différé (délai 24-48h)

**Sécurité:**
- Gateway certifié PCI-DSS
- Tokenization cartes enregistrées
- HTTPS obligatoire
- Timeout paiement: 10 minutes

**Règles métier:**
- RG-PAY-001: Paiement obligatoire avant confirmation RDV
- RG-PAY-002: Double paiement impossible (vérification transaction unique)
- RG-PAY-003: Reçu fiscal conforme réglementation tunisienne

#### 5.2 Remboursements

**Politique:**
- Annulation patient >24h: 100%
- Annulation patient 24h-2h: 50%
- Annulation patient <2h ou no-show: 0%
- Annulation médecin: 100% + bon 10%
- Incident technique plateforme: 100%

**Process:**
- Automatique si éligible (pas de demande manuelle)
- Remboursement sur moyen paiement d'origine
- Délai: 5-7j ouvrés (carte), 24-48h (e-wallet)
- Notification à chaque étape

**Règles métier:**
- RG-REMB-001: Traitement sous 48h
- RG-REMB-002: Si carte expirée: crédit compte patient
- RG-REMB-003: Historique accessible dans compte

#### 5.3 Facturation Patients

**Facture acquittée:**
- Mentions légales obligatoires (société, MF, RC)
- Numéro séquentiel sans rupture
- Détail consultation (médecin, date, type, durée)
- Montants HT, TVA 19%, TTC
- Mode et date paiement
- Code acte CNAM (si applicable)
- QR code vérification

**Format:**
- PDF téléchargeable
- Envoi automatique par email
- Stockage illimité dans compte

**Règles métier:**
- RG-FACT-001: Génération automatique après paiement
- RG-FACT-002: Factures immuables
- RG-FACT-003: Conservation 10 ans (obligation fiscale)

#### 5.4 Rémunération Médecins

**Modèle:**
- Commission plateforme: 15%
- Exemple: Consultation 60 TND → Médecin 51 TND

**Calendrier:**
- Virements bi-mensuels (5 et 20 du mois)
- Paiement consultations 15 jours précédents
- Délai minimal encaissement: 5j (rétractation patient)

**Interface revenus:**
- Dashboard temps réel
- Détail par consultation
- Prochain virement prévu
- Historique paiements
- Relevé fiscal annuel

**Règles métier:**
- RG-PAYMED-001: Virement si montant ≥50 TND (sinon report)
- RG-PAYMED-002: RIB vérifié avant premier paiement
- RG-PAYMED-003: Attestation revenus annuelle (déclaration fiscale)

### MODULE 6: COMMUNICATION

#### 6.1 Messagerie Sécurisée

**Caractéristiques:**
- Chiffrement E2E (RSA 2048 bits)
- Pièces jointes (max 10 Mo, JPG/PNG/PDF)
- Historique conservé dans dossier médical
- Notifications temps réel

**Contexte d'utilisation:**
- Suivi post-consultation
- Questions simples
- Envoi résultats examens
- Demande renouvellement ordonnance

**Limitations:**
- Pas pour urgences (délai réponse non garanti)
- Maximum 5 messages/conversation sans nouvelle consultation
- Disponible uniquement après 1 consultation effectuée

**Règles métier:**
- RG-MSG-001: Messagerie après 1ère consultation uniquement
- RG-MSG-002: Médecin pas obligé de répondre (service additionnel)
- RG-MSG-003: Archivage dans dossier médical
- RG-MSG-004: Notification push/email à réception

#### 6.2 Notifications

**Types pour patients:**
- RDV: Confirmation, rappels J-1/H-1, lien connexion
- Consultation: Médecin en attente, demande évaluation, documents disponibles
- Messagerie: Nouveau message
- Administratif: Paiement, remboursement
- Rappels santé: Vaccinations, renouvellement ordonnance

**Types pour médecins:**
- RDV: Nouvelle réservation, annulation, rappel consultations jour, patient en attente
- Messagerie: Nouveau message patient
- Administratif: Virement effectué, évaluation négative

**Canaux:**
- Email
- SMS (limité, coût)
- Push notifications (mobile app)

**Préférences:**
- Personnalisables par utilisateur
- Notifications critiques non désactivables
- Respect créneaux horaires (08:00-21:00 sauf urgences)

**Règles métier:**
- RG-NOTIF-001: Fréquence limitée (max 5/jour hors RDV)
- RG-NOTIF-002: Désabonnement facile (lien emails)
- RG-NOTIF-003: Logs conservés 3 mois

#### 6.3 Support et Assistance

**Canaux:**
1. **Centre d'aide:** FAQ, guides, vidéos tutorielles
2. **Chat en direct:** 08:00-20:00 7j/7, réponse <2 min
3. **Email:** support@sehadigital.tn, réponse <24h
4. **Téléphone:** Numéro vert 80 XXX XXX, 08:00-18:00 Lun-Sam
5. **Tickets:** Création depuis compte, suivi état

**Niveaux:**
- Niveau 1: Questions générales, compte, RDV, paiement
- Niveau 2: Problèmes techniques, bugs
- Niveau 3: Incidents critiques, réclamations complexes

**SLA:**
| Priorité | Temps réponse | Temps résolution |
|----------|---------------|------------------|
| Critique | 15 min | 1h |
| Haute | 1h | 4h |
| Moyenne | 4h | 24h |
| Basse | 24h | 72h |

**Règles métier:**
- RG-SUPP-001: Support technique durant heures consultations (08:00-22:00)
- RG-SUPP-002: Astreinte 24/7 pour incidents critiques
- RG-SUPP-003: Satisfaction support >4.5/5

### MODULE 7: ADMINISTRATION

#### 7.1 Dashboard Administrateur

**Métriques clés:**
- Utilisateurs (patients, médecins, nouveaux 7j)
- Consultations (aujourd'hui, ce mois, moyenne/jour)
- Revenus (aujourd'hui, ce mois, objectif)
- Alertes (tickets ouverts, incidents tech, validations en attente)
- Graphiques activité (consultations par heure, croissance)

**Actions rapides:**
- Validation médecins en attente
- Tickets support ouverts
- Incidents techniques
- Avis à modérer

#### 7.2 Gestion Médecins

**Validation inscriptions:**
1. Vérification identité (CIN, concordance documents)
2. Vérification qualifications (diplômes, Ordre, contact ONMT)
3. Vérification exercice (RCP, établissement)
4. Décision: Valider / Demander complément / Refuser (sous 72h)

**Actions:**
- Consulter profil et activité
- Suspendre/activer compte
- Contacter médecin
- Consulter statistiques (consultations, revenus, évaluations)

**Règles métier:**
- RG-ADMMED-001: Validation sous 72h max
- RG-ADMMED-002: Refus avec justification écrite
- RG-ADMMED-003: Contrôle annuel documents (Ordre, RCP)

#### 7.3 Modération Contenus

**Contenus à modérer:**
- Avis patients signalés ou flaggés automatiquement
- Photos de profil
- Biographies médecins
- Messages signalés

**Process:**
- File d'attente modération
- Contexte consultation si applicable
- Actions: Approuver / Supprimer / Demander modification / Escalader
- Notification utilisateur si suppression
- Historique modérations (traçabilité)

**Règles métier:**
- RG-MOD-001: Avis vérifiés uniquement (consultation effectuée)
- RG-MOD-002: Modération sous 48h max
- RG-MOD-003: Possibilité appel décision

#### 7.4 Suivi Financier

**Métriques:**
- Revenus du mois (consultations, commissions)
- Charges (salaires, infra, marketing, autres)
- Résultat net
- Comparatif vs prévisions

**Rapports:**
- Compte de résultat mensuel
- Analyse revenus (par médecin, spécialité, gouvernorat)
- Analyse coûts
- Projections

**Exports:**
- Excel, CSV, PDF
- Réconciliation bancaire quotidienne
- Archivage 10 ans

### MODULE 8: REPORTING ET ANALYTICS

#### 8.1 Analytics Utilisateurs

**Métriques acquisition:**
- Nouveaux inscrits (patients, médecins)
- Canaux acquisition
- Taux conversion inscription

**Métriques activation:**
- % inscrits avec 1 consultation
- Délai inscription → 1ère consultation
- Taux abandon parcours RDV

**Métriques rétention:**
- MAU (Monthly Active Users)
- Taux rétention (M1, M3, M6)
- Taux churn
- Fréquence consultations

**Métriques revenus:**
- ARPU (Average Revenue Per User)
- LTV (Lifetime Value)
- Ratio LTV/CAC
- Revenu par spécialité

**Métriques engagement:**
- Durée sessions
- Pages vues/session
- Taux rebond
- Fonctionnalités utilisées

**Métriques satisfaction:**
- NPS (Net Promoter Score)
- CSAT (Customer Satisfaction)
- Note moyenne consultations
- Taux résolution support

#### 8.2 Surveillance Épidémiologique

**Données analysées (anonymisées):**
- Diagnostics fréquents (codes CIM-10)
- Évolution saisonnière
- Répartition géographique
- Groupes d'âge affectés
- Médicaments prescrits (codes ATC)

**Visualisations:**
- Cartes de chaleur Tunisie
- Courbes d'évolution
- Top motifs consultation
- Alertes si augmentation significative

**Règles métier:**
- RG-EPI-001: Données strictement anonymisées
- RG-EPI-002: Agrégation minimum 10 cas (pas de ré-identification)
- RG-EPI-003: Conformité INPDP validée
- RG-EPI-004: Partage avec autorités sur demande officielle

---

## ARCHITECTURE TECHNIQUE DÉTAILLÉE

### Stack Technologique

**Backend:**
- Framework: Laravel 11 (PHP 8.3)
- Base de données: PostgreSQL 16
- Cache: Redis 7
- Queue: Laravel Horizon (Redis)
- Storage: S3-compatible (MinIO dev, AWS S3 prod)
- Real-time: Laravel Websockets / Pusher

**Frontend Web:**
- Framework: Vue.js 3 + TypeScript
- UI: Tailwind CSS + HeadlessUI
- State: Pinia
- Build: Vite

**Mobile:**
- Framework: Flutter 3.x
- State: Riverpod
- Langue: Dart

**Vidéo:**
- Protocole: WebRTC
- Signaling: Socket.io
- TURN/STUN: Coturn server (Tunisie)

**Infrastructure:**
- Containers: Docker + Kubernetes
- CI/CD: GitLab CI/CD
- Monitoring: Prometheus + Grafana
- Logs: ELK Stack
- Hébergement: Tunisie (conformité INPDP)

### Base de Données (Principales Tables)

```
users (id, email, password, role, 2fa_enabled...)
patients (id, user_id, first_name, last_name, birth_date, cin...)
medecins (id, user_id, speciality, ordre_number, validation_status...)
availabilities (id, medecin_id, day_of_week, start_time, end_time...)
appointments (id, patient_id, medecin_id, appointment_date, status, price...)
consultations (id, appointment_id, start_time, notes, diagnosis...)
medical_records (id, patient_id, allergies, chronic_diseases...)
prescriptions (id, consultation_id, medications, qr_code, signed_at...)
payments (id, patient_id, appointment_id, amount, status...)
messages (id, conversation_id, sender_id, message_encrypted...)
reviews (id, appointment_id, overall_rating, comment...)
audit_logs (id, user_id, action, model, old_values, new_values...)
```

### Sécurité

**Authentification:**
- JWT tokens (RS256), refresh tokens rotation
- 2FA optionnelle (TOTP)
- Rate limiting strict

**Chiffrement:**
- HTTPS obligatoire (TLS 1.3)
- Données repos: AES-256
- Données médicales: chiffrement additionnel
- Mots de passe: bcrypt (cost 12)

**Protection:**
- Hébergement Tunisie (INPDP compliant)
- Backups chiffrés quotidiens
- Anonymisation analytics
- RBAC (Role-Based Access Control)
- Logs exhaustifs accès données sensibles
- Sessions limitées temps

**Tests:**
- Pen-testing semestriel
- Scans vulnérabilités automatiques
- Code reviews sécurité

---

## CONFORMITÉ RÉGLEMENTAIRE

### Cadre Légal Tunisien

**Textes applicables:**
- Loi n° 91-63 du 29 juillet 1991 (organisation sanitaire)
- Code de déontologie médicale (Décret n° 93-1155)
- Loi n° 2004-63 du 27 juillet 2004 (protection données INPDP)
- Loi n° 2000-83 du 9 août 2000 (e-commerce)

**Organismes:**
- Ministère de la Santé
- Ordre National des Médecins (ONMT)
- Instance Nationale Protection Données (INPDP)
- CNAM

**Exigences:**
- Identification formelle patient et médecin
- Consentement éclairé documenté
- Confidentialité échanges
- Traçabilité actes médicaux
- Conservation données 10 ans minimum
- Limitation actes praticables à distance

### Conformité INPDP

**Obligations:**
- Déclaration instance avant lancement
- Responsable Protection Données désigné
- Registre traitements à jour
- Analyse d'impact (PIA) réalisée
- Procédures violation données
- Droits utilisateurs (accès, rectification, suppression, portabilité)
- Consentements documentés
- Hébergement données Tunisie

### Responsabilités

**Médecins:**
- Pleine responsabilité professionnelle
- Assurance RCP obligatoire (couvre télémédecine)
- Respect code déontologie
- Secret médical absolu

**Plateforme:**
- Infrastructure technique fiable
- Protection données patients
- Vérification qualifications médecins
- Support technique consultations
- Conformité réglementaire

**Patients:**
- Exactitude informations
- Respect conditions d'utilisation
- Paiement consultations
- Utilisation appropriée (pas urgences vitales)

---

## MODÈLE ÉCONOMIQUE

### Revenus

**Principal: Commissions consultations**
- Tarif moyen: 65 TND
- Commission: 15% (9.75 TND)
- Année 1: 100K consultations = 975K TND
- Année 3: 1.5M consultations = 14.6M TND

**Secondaires (v2):**
- Abonnements patients premium: 20 TND/mois
- Abonnements médecins premium: 50 TND/mois
- Services B2B entreprises
- Partenariats laboratoires/pharmacies

### Coûts

**Fixes mensuels:**
- Équipe: 17K TND (204K TND/an)
- Infrastructure: 3.5K TND (42K TND/an)
- Marketing: 5.5K TND (66K TND/an)
- Autres: 4K TND (48K TND/an)
**Total: 30K TND/mois (360K TND/an)**

**Variables:**
- Frais paiement: 3% du volume
- Reversement médecins: 85% consultations

### Projections

**Année 1:**
- Patients: 50K, Médecins: 300, Consultations: 100K
- CA: 1M TND, Coûts: 750K TND
- Résultat: +250K TND (25% marge)

**Année 2:**
- Patients: 200K, Médecins: 800, Consultations: 500K
- CA: 3.5M TND, Coûts: 2.8M TND
- Résultat: +700K TND (20% marge)

**Année 3:**
- Patients: 500K, Médecins: 2K, Consultations: 1.5M
- CA: 8M TND, Coûts: 6M TND
- Résultat: +2M TND (25% marge)

**KPIs clés:**
- CAC (Coût Acquisition): 20 TND
- LTV (Lifetime Value): 120 TND
- Ratio LTV/CAC: 6x
- Rentabilité opérationnelle: M8-10

---

## PLAN DE DÉPLOIEMENT

### Phases

**Phase 0: Préparation (M-3 à M-1)**
- Finalisation développement MVP
- Tests exhaustifs
- Déclaration INPDP
- Accord Ordre Médecins
- Recrutement 50 médecins testeurs
- Recrutement 500 patients testeurs

**Phase 1: Bêta Fermée (M1-M2)**
- Lancement Grand Tunis
- 50 médecins, 500 patients
- Objectif: 500 consultations/mois
- Monitoring intensif, itérations rapides

**Phase 2: Bêta Ouverte (M3-M4)**
- Extension: Sousse, Sfax, Monastir
- Ouverture inscriptions grand public
- Objectif: 150 médecins, 5K patients, 2K consultations/mois
- Campagne marketing ciblée

**Phase 3: Lancement National (M5-M6)**
- Couverture complète Tunisie
- Objectif: 300 médecins, 20K patients, 5K consultations/mois
- Campagne publicitaire multi-canaux
- Événement de lancement officiel

**Phase 4: Consolidation (M7-M12)**
- Amélioration continue
- Nouvelles fonctionnalités (v1.1, v1.2)
- Expansion spécialités
- Préparation v2 (CNAM, IoT...)

### Stratégie Go-to-Market

**Acquisition Patients:**
- Digital (70%): SEO, SEA, Social Media, Influenceurs
- Partenariats (20%): Entreprises, mutuelles, pharmacies, associations
- Traditionnel (10%): Affichage, radio, presse

**Acquisition Médecins:**
- Relations directes: Visites cabinets, Ordre, conférences
- Digital: LinkedIn, groupes Facebook, emailing
- Bouche-à-oreille: Programme parrainage, témoignages

**Messages clés:**
- Patients: "Consultez un médecin depuis chez vous en 24h"
- Médecins: "Élargissez votre patientèle, revenus flexibles"

### Jalons

- M-6: Spécifications finalisées
- M-4: Développement core
- M-2: Développement avancé
- M-1: Préparation lancement
- M0: Bêta fermée
- M+2: Bêta ouverte
- M+6: Lancement national
- M+12: Bilan année 1

---

## RISQUES ET MITIGATION

**Risques principaux:**
1. **Réglementaire:** Interdiction télémédecine → Collaboration proactive autorités
2. **Technique:** Incidents sécurité → Audits réguliers, infrastructure robuste
3. **Marché:** Adoption lente → Marketing agressif, UX excellente
4. **Médical:** Erreur diagnostic → Protocoles stricts, limitations claires
5. **Financier:** Fraude → 3D Secure, monitoring
6. **Opérationnel:** Pénurie médecins → Recrutement continu, rémunération attractive
7. **Concurrentiel:** Gros acteur → Innovation rapide, fidélisation

**Plans de contingence:**
- Incident sécurité: Cellule crise, communication transparente
- Blocage réglementaire: Dialogue autorités, mise en conformité
- Défaillance technique: DR (Disaster Recovery), communication fréquente
- Adoption insuffisante: Analyse causes, pivot stratégie

---

## ÉQUIPE ET ORGANISATION

**Organigramme:**
```
CEO
├─ CTO (Tech: Dev team, DevOps)
├─ CMO (Marketing: Growth hacking)
└─ COO (Opérations: Medical Affairs, Customer Success, Support)
```

**Effectifs Année 1: 15-20 personnes**

**Compétences clés:**
- CTO: Architecture, leadership tech
- Devs: Laravel, Vue.js, Flutter
- Designer: UX/UI, healthcare
- Responsable Médical: Médecin diplômé, réglementation
- CMO: Marketing digital, growth
- Customer Success: Rétention, satisfaction
- Support: Multilingue, technique

---

## PARTENARIATS STRATÉGIQUES

**Prioritaires:**
- Ordre National des Médecins (ONMT): Reconnaissance officielle
- Ministère de la Santé: Validation activité
- CNAM: Convention remboursement (v2)
- Mutuelles privées: Partenariats remboursement
- Entreprises: Télémédecine B2B
- Laboratoires/Centres imagerie: Intégration examens

**Stratégie:**
- Phase 1: Approche informelle
- Phase 2: Proof of concept (pilote)
- Phase 3: Négociation contrat cadre
- Phase 4: Déploiement et suivi

---

## COMMUNICATION ET MARKETING

**Positionnement:**
- Promesse: "La santé accessible à tous, partout en Tunisie"
- Différenciation: 100% tunisienne, complète, conforme
- Ton: Professionnel, rassurant, accessible
- Valeurs: Accessibilité, qualité, confidentialité, innovation

**Identité:**
- Logo: Santé + digital
- Couleurs: Bleu confiance + vert santé
- Baseline: "Votre médecin à portée de clic" / "طبيبك على بعد نقرة"

**Budget Marketing An 1: 80K TND**
- Digital ads: 42K (52%)
- Content & SEO: 15K (19%)
- Événements & PR: 12K (15%)
- Partenariats: 8K (10%)
- Divers: 3K (4%)

**Communication de crise:**
- Protocole défini (cellule, porte-parole)
- Monitoring 24/7
- Transparence et empathie
- Actions correctives

---

## RESPONSABILITÉ SOCIÉTALE

**Impact social:**
- Désengorgement urgences
- Accès soins zones rurales
- Suivi maladies chroniques
- Programme solidarité (5% consultations gratuites)

**Impact environnemental:**
- Réduction empreinte carbone (déplacements évités: ~50 tonnes CO2/an)
- Réduction papier (ordonnances digitales)
- Sobriété numérique

**Éthique:**
- Charte éthique plateforme
- Comité d'éthique (2 médecins, juriste, patient, direction)
- Primauté santé patient
- Indépendance médicale
- Transparence totale
- Non-discrimination

---

## MAINTENANCE ET ÉVOLUTIONS

**Support:**
- Niveau 1 (24/7): Hotline, incidents simples
- Niveau 2 (8h-20h): Bugs, problèmes techniques
- Niveau 3 (On-call): Incidents critiques

**Maintenance préventive:**
- Mises à jour sécurité mensuelles
- Optimisation BDD
- Tests restauration backups trimestriels

**Évolutions:**
- v1.1 (M+3): Amélioration UX, langues, export
- v1.2 (M+6): Messagerie vocale, téléconsultations groupe
- v2.0 (M+12-18): CNAM, IoT, IA, télésurveillance
- v3.0 (M+24+): Formation continue, télé-expertise, expansion Maghreb

**Mesure du succès:**
- KPIs suivis en continu
- Revues hebdomadaires (produit), mensuelles (direction), trimestrielles (board)
- Pivots si objectifs non atteints

---

## VISION LONG TERME

**Années 1-3:** Leadership Tunisie
- 500K patients, 2K médecins
- Partenariats CNAM actifs
- Rentabilité confirmée

**Années 4-5:** Expansion Maghreb
- Algérie, Maroc
- 2-3M utilisateurs

**Années 6-10:** Plateforme Santé Complète
- Télémédecine + Télésurveillance + Marketplace
- IA diagnostique avancée
- Interopérabilité hospitalière
- Expansion Afrique francophone
- IPO ou acquisition stratégique

---

## CONCLUSION

**Seha Digital** représente une opportunité unique de transformer l'accès aux soins de santé en Tunisie. Le projet combine:

✓ **Besoin réel et urgent:** Déserts médicaux, files d'attente, discontinuité soins
✓ **Solution complète:** RDV + Consultation + Dossier médical + Outils professionnels
✓ **Conformité maximale:** INPDP, Ordre Médecins, déontologie
✓ **Technologie éprouvée:** Stack moderne, scalable, sécurisé
✓ **Modèle économique viable:** Rentabilité M8-10, marges saines
✓ **Équipe compétente:** Médical + Tech + Business
✓ **Vision long terme:** Leader régional, plateforme santé complète

**Prochaines étapes immédiates:**
1. Validation cahier des charges
2. Levée de fonds seed (300-500K TND)
3. Constitution équipe core
4. Démarches réglementaires (INPDP, ONMT)
5. Développement MVP (M1-M4)
6. Lancement bêta fermée (M5)

**Facteurs clés de succès:**
- Qualité et fiabilité technique irréprochables
- Conformité et crédibilité (autorités, médecins, patients)
- Qualité médicale (vérification, protocoles, satisfaction)
- Masse critique (disponibilité, variété, couverture)
- Acquisition efficace (marketing, rétention)
- Innovation continue (écoute, itérations, anticipation)

---

**Document vivant:** Ce cahier des charges sera mis à jour régulièrement selon évolutions réglementaires, feedback utilisateurs, et décisions stratégiques.

**Contact:**
- Email: contact@sehadigital.tn
- Web: www.sehadigital.tn (à venir)

---

**FIN DU DOCUMENT - 250+ PAGES**

*Seha Digital - La santé accessible à tous, partout en Tunisie* 🏥💙
