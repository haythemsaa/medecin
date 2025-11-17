<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #22c55e 0%, #3b82f6 100%);
            color: white;
            padding: 40px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .success-badge {
            background: #dcfce7;
            color: #166534;
            padding: 15px 25px;
            border-radius: 50px;
            display: inline-block;
            font-weight: bold;
            margin: 20px 0;
        }
        .step-box {
            background: #f9fafb;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #22c55e;
        }
        .button {
            display: inline-block;
            background: #22c55e;
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 36px;">✅ Félicitations !</h1>
        <p style="margin: 15px 0 0 0; font-size: 18px;">Votre compte médecin est validé</p>
    </div>

    <div class="content">
        <p>Bonjour Dr. {{ $user->medecin->last_name }},</p>

        <div style="text-align: center;">
            <div class="success-badge">
                🎉 COMPTE VALIDÉ 🎉
            </div>
        </div>

        <p>Excellente nouvelle ! Votre dossier a été vérifié et validé par notre équipe. Vous faites maintenant partie du réseau Seha Digital et pouvez commencer à recevoir des patients.</p>

        <h2 style="color: #22c55e; margin-top: 30px;">🚀 Prochaines étapes</h2>

        <div class="step-box">
            <strong>1️⃣ Configurez votre agenda</strong>
            <p style="margin: 5px 0 0 0;">Définissez vos disponibilités pour que les patients puissent réserver leurs consultations</p>
        </div>

        <div class="step-box">
            <strong>2️⃣ Complétez votre profil</strong>
            <p style="margin: 5px 0 0 0;">Ajoutez une biographie professionnelle et une photo pour rassurer vos futurs patients</p>
        </div>

        <div class="step-box">
            <strong>3️⃣ Testez la plateforme</strong>
            <p style="margin: 5px 0 0 0;">Familiarisez-vous avec les outils de consultation vidéo et les fonctionnalités</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/dashboard') }}" class="button">
                Accéder à mon tableau de bord
            </a>
        </div>

        <h3 style="color: #22c55e;">💰 Rémunération</h3>
        <p>Rappel de notre modèle de rémunération :</p>
        <ul>
            <li><strong>Commission plateforme : 15%</strong></li>
            <li>Vous définissez vos tarifs librement</li>
            <li>Paiements bi-mensuels (5 et 20 du mois)</li>
            <li>Suivi en temps réel de vos revenus</li>
        </ul>

        <h3 style="color: #22c55e;">🛠️ Vos outils professionnels</h3>
        <ul>
            <li><strong>Visioconférence HD</strong> - Qualité optimale pour vos consultations</li>
            <li><strong>Dossier médical sécurisé</strong> - Accès au dossier patient avec consentement</li>
            <li><strong>Ordonnances numériques</strong> - Génération automatique avec QR code</li>
            <li><strong>Certificats médicaux</strong> - Création simplifiée et sécurisée</li>
            <li><strong>Messagerie E2E</strong> - Communication chiffrée avec vos patients</li>
            <li><strong>Statistiques</strong> - Suivi de votre activité et revenus</li>
        </ul>

        <div style="background: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #1e40af;">📚 Formation et support</h4>
            <p>Nous mettons à votre disposition :</p>
            <ul style="margin: 10px 0;">
                <li>Guide du médecin Seha Digital</li>
                <li>Vidéos tutorielles</li>
                <li>Support technique prioritaire</li>
                <li>Webinaires de formation mensuels</li>
            </ul>
            <a href="{{ url('/medecin/guide') }}" style="color: #2563eb;">Accéder aux ressources →</a>
        </div>

        <h3 style="color: #22c55e;">⚖️ Conformité et déontologie</h3>
        <p>Rappels importants :</p>
        <ul>
            <li>Respect du code de déontologie médicale</li>
            <li>Secret médical absolu (chiffrement E2E)</li>
            <li>Documents à jour (Ordre, RCP) - vérification annuelle</li>
            <li>Limites de la télémédecine (pas d'urgences vitales)</li>
            <li>Traçabilité de tous les actes médicaux</li>
        </ul>

        <p>Besoin d'aide ou de conseils ?</p>
        <ul>
            <li>📧 Email médecins : medecins@sehadigital.tn</li>
            <li>📞 Support technique : 80 XXX XXX</li>
            <li>💬 Chat prioritaire dans votre tableau de bord</li>
        </ul>

        <p style="margin-top: 30px;">Bienvenue dans l'équipe Seha Digital ! Ensemble, révolutionnons l'accès aux soins en Tunisie. 🇹🇳</p>

        <p>Cordialement,<br>
        <strong>L'équipe Seha Digital</strong></p>
    </div>

    <div class="footer">
        <p>© 2025 Seha Digital - Télémédecine Tunisie</p>
        <p>
            <a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">www.sehadigital.tn</a> |
            <a href="mailto:medecins@sehadigital.tn" style="color: #3b82f6; text-decoration: none;">medecins@sehadigital.tn</a>
        </p>
        <p style="font-size: 12px; color: #9ca3af;">
            Plateforme conforme INPDP - Données hébergées en Tunisie
        </p>
    </div>
</body>
</html>
