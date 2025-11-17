<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get user's notification preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
                'appointment_reminders' => true,
                'appointment_confirmations' => true,
                'appointment_cancellations' => true,
                'new_messages' => true,
                'prescription_ready' => true,
                'review_reminders' => true,
                'marketing_emails' => false,
                'reminder_hours_before' => [24, 1],
            ]
        );

        return response()->json([
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'push_enabled' => 'boolean',
            'appointment_reminders' => 'boolean',
            'appointment_confirmations' => 'boolean',
            'appointment_cancellations' => 'boolean',
            'new_messages' => 'boolean',
            'prescription_ready' => 'boolean',
            'review_reminders' => 'boolean',
            'marketing_emails' => 'boolean',
            'reminder_hours_before' => 'array',
            'reminder_hours_before.*' => 'integer|min:0|max:168',
        ]);

        $user = $request->user();

        $preferences = NotificationPreference::updateOrCreate(
            ['user_id' => $user->id],
            $request->all()
        );

        return response()->json([
            'message' => 'Préférences mises à jour avec succès',
            'preferences' => $preferences,
        ]);
    }

    /**
     * Test notification
     */
    public function testNotification(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => 'required|in:email,sms,push',
        ]);

        $user = $request->user();
        $channel = $request->channel;

        // TODO: Implement actual notification sending
        // For now, just return success

        return response()->json([
            'message' => "Notification de test envoyée via {$channel}",
            'channel' => $channel,
            'recipient' => $channel === 'email' ? $user->email : $user->phone,
        ]);
    }
}
