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
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .appointment-card {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
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
        <h1 style="margin: 0;">🏥 Seha Digital</h1>
        <p style="margin: 10px 0 0 0;">Confirmation de rendez-vous</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $appointment->patient->first_name }},</p>

        <p>Votre rendez-vous a été confirmé avec succès ! 🎉</p>

        <div class="appointment-card">
            <h2 style="margin-top: 0; color: #3b82f6;">Détails de votre consultation</h2>

            <div class="info-row">
                <strong>Médecin :</strong>
                <span>Dr. {{ $appointment->medecin->first_name }} {{ $appointment->medecin->last_name }}</span>
            </div>

            <div class="info-row">
                <strong>Spécialité :</strong>
                <span>{{ $appointment->medecin->speciality }}</span>
            </div>

            <div class="info-row">
                <strong>Date et heure :</strong>
                <span>{{ $appointment->appointment_date->format('d/m/Y à H:i') }}</span>
            </div>

            <div class="info-row">
                <strong>Type :</strong>
                <span>{{ $appointment->type === 'video' ? '📹 Visioconférence' : '📞 Téléphone' }}</span>
            </div>

            <div class="info-row">
                <strong>Durée :</strong>
                <span>{{ $appointment->duration }} minutes</span>
            </div>

            <div class="info-row">
                <strong>Prix :</strong>
                <span>{{ $appointment->price }} TND</span>
            </div>
        </div>

        <p><strong>Motif :</strong> {{ $appointment->reason }}</p>

        <div style="text-align: center;">
            <a href="{{ url('/appointments/' . $appointment->id) }}" class="button">
                Voir mon rendez-vous
            </a>
        </div>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;">
            <strong>💡 Rappel important :</strong>
            <ul style="margin: 10px 0;">
                <li>Vous pourrez rejoindre la consultation 10 minutes avant l'heure prévue</li>
                <li>Préparez vos questions et documents médicaux</li>
                <li>Testez votre connexion internet et votre webcam</li>
            </ul>
        </div>

        <p><strong>Politique d'annulation :</strong></p>
        <ul>
            <li>Plus de 24h avant : Remboursement 100%</li>
            <li>Entre 24h et 2h : Remboursement 50%</li>
            <li>Moins de 2h : Pas de remboursement</li>
        </ul>

        <p>Vous recevrez des rappels par email et SMS 24h et 1h avant votre rendez-vous.</p>

        <p>Si vous avez des questions, notre équipe est disponible pour vous aider.</p>

        <p>À bientôt,<br>
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
