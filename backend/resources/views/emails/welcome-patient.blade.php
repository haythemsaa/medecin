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
            background: linear-gradient(135deg, #3b82f6 0%, #22c55e 100%);
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
        .feature-box {
            background: #f9fafb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
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
        <h1 style="margin: 0; font-size: 36px;">🏥 Bienvenue sur Seha Digital !</h1>
        <p style="margin: 15px 0 0 0; font-size: 18px;">La santé accessible à tous, partout en Tunisie</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $user->patient->first_name }} 👋</p>

        <p>Bienvenue dans la famille Seha Digital ! Nous sommes ravis de vous accompagner dans votre parcours de santé.</p>

        <p><strong>Votre compte a été créé avec succès.</strong> Vous pouvez maintenant profiter de tous nos services de télémédecine.</p>

        <h2 style="color: #3b82f6; margin-top: 30px;">✨ Ce que vous pouvez faire dès maintenant :</h2>

        <div class="feature-box">
            <strong>🔍 Trouver un médecin</strong>
            <p style="margin: 5px 0 0 0;">Recherchez parmi nos {{ $stats['medecins_count'] ?? '300+' }} médecins qualifiés dans toutes les spécialités</p>
        </div>

        <div class="feature-box">
            <strong>📅 Réserver une consultation</strong>
            <p style="margin: 5px 0 0 0;">Choisissez votre créneau et consultez par vidéo ou téléphone en toute sécurité</p>
        </div>

        <div class="feature-box">
            <strong>📋 Gérer votre dossier médical</strong>
            <p style="margin: 5px 0 0 0;">Centralisez vos informations médicales, ordonnances et résultats d'examens</p>
        </div>

        <div class="feature-box">
            <strong>💬 Messagerie sécurisée</strong>
            <p style="margin: 5px 0 0 0;">Communiquez avec vos médecins après consultation en toute confidentialité</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/medecins') }}" class="button">
                Trouver un médecin maintenant
            </a>
        </div>

        <h3 style="color: #3b82f6;">🎯 Pourquoi choisir Seha Digital ?</h3>
        <ul>
            <li><strong>Rapide</strong> - Consultation en moins de 24h</li>
            <li><strong>Pratique</strong> - Depuis chez vous, sans déplacement</li>
            <li><strong>Sécurisé</strong> - Chiffrement de bout en bout, conformité INPDP</li>
            <li><strong>Accessible</strong> - Tarifs transparents de 50 à 100 TND</li>
            <li><strong>100% Tunisien</strong> - Données hébergées en Tunisie</li>
        </ul>

        <div style="background: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #1e40af;">📚 Besoin d'aide ?</h4>
            <p>Notre guide de démarrage rapide vous explique tout :</p>
            <ul style="margin: 10px 0;">
                <li>Comment rechercher un médecin</li>
                <li>Comment réserver une consultation</li>
                <li>Comment préparer votre premier rendez-vous</li>
                <li>Comment utiliser la visioconférence</li>
            </ul>
            <a href="{{ url('/guide') }}" style="color: #2563eb;">Voir le guide complet →</a>
        </div>

        <p>Si vous avez la moindre question, notre équipe support est là pour vous aider :</p>
        <ul>
            <li>📧 Email : support@sehadigital.tn</li>
            <li>📞 Téléphone : 80 XXX XXX (Lun-Sam 8h-18h)</li>
            <li>💬 Chat en direct sur le site</li>
        </ul>

        <p style="margin-top: 30px;">Nous vous souhaitons une excellente expérience sur Seha Digital !</p>

        <p>Cordialement,<br>
        <strong>L'équipe Seha Digital</strong></p>
    </div>

    <div class="footer">
        <p>© 2025 Seha Digital - Télémédecine Tunisie</p>
        <p>
            <a href="{{ url('/') }}" style="color: #3b82f6; text-decoration: none;">www.sehadigital.tn</a> |
            <a href="mailto:contact@sehadigital.tn" style="color: #3b82f6; text-decoration: none;">contact@sehadigital.tn</a>
        </p>
        <p style="font-size: 12px; color: #9ca3af;">
            Plateforme conforme INPDP - Données hébergées en Tunisie
        </p>
    </div>
</body>
</html>
