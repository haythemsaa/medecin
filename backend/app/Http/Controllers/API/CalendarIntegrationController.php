<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CalendarSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarIntegrationController extends Controller
{
    public function __construct(
        private CalendarSyncService $calendarService
    ) {}

    /**
     * Get calendar integration status
     */
    public function getStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'google_connected' => !empty($user->google_calendar_token),
            'outlook_connected' => !empty($user->outlook_calendar_token),
            'auto_sync_enabled' => $user->calendar_auto_sync ?? false,
        ]);
    }

    /**
     * Get Google Calendar authorization URL
     */
    public function getGoogleAuthUrl(): JsonResponse
    {
        $url = $this->calendarService->getGoogleAuthUrl();

        return response()->json([
            'auth_url' => $url,
        ]);
    }

    /**
     * Get Outlook Calendar authorization URL
     */
    public function getOutlookAuthUrl(): JsonResponse
    {
        $url = $this->calendarService->getOutlookAuthUrl();

        return response()->json([
            'auth_url' => $url,
        ]);
    }

    /**
     * Handle Google Calendar OAuth callback
     */
    public function handleGoogleCallback(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $tokenData = $this->calendarService->exchangeGoogleCode($request->code);

        if (!$tokenData) {
            return response()->json([
                'message' => 'Échec de l\'authentification Google',
            ], 400);
        }

        $user->update([
            'google_calendar_token' => $tokenData['access_token'],
            'google_calendar_refresh_token' => $tokenData['refresh_token'] ?? null,
        ]);

        return response()->json([
            'message' => 'Google Calendar connecté avec succès',
            'connected' => true,
        ]);
    }

    /**
     * Handle Outlook Calendar OAuth callback
     */
    public function handleOutlookCallback(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $tokenData = $this->calendarService->exchangeOutlookCode($request->code);

        if (!$tokenData) {
            return response()->json([
                'message' => 'Échec de l\'authentification Outlook',
            ], 400);
        }

        $user->update([
            'outlook_calendar_token' => $tokenData['access_token'],
            'outlook_calendar_refresh_token' => $tokenData['refresh_token'] ?? null,
        ]);

        return response()->json([
            'message' => 'Outlook Calendar connecté avec succès',
            'connected' => true,
        ]);
    }

    /**
     * Disconnect Google Calendar
     */
    public function disconnectGoogle(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'google_calendar_token' => null,
            'google_calendar_refresh_token' => null,
        ]);

        return response()->json([
            'message' => 'Google Calendar déconnecté',
        ]);
    }

    /**
     * Disconnect Outlook Calendar
     */
    public function disconnectOutlook(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'outlook_calendar_token' => null,
            'outlook_calendar_refresh_token' => null,
        ]);

        return response()->json([
            'message' => 'Outlook Calendar déconnecté',
        ]);
    }

    /**
     * Toggle auto-sync
     */
    public function toggleAutoSync(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->update([
            'calendar_auto_sync' => $request->enabled,
        ]);

        return response()->json([
            'message' => 'Synchronisation automatique mise à jour',
            'auto_sync_enabled' => $request->enabled,
        ]);
    }
}
