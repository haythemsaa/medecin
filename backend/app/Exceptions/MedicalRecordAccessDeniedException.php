<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class MedicalRecordAccessDeniedException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        \Log::warning('Medical record access denied', [
            'message' => $this->getMessage(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Accès au dossier médical refusé. Vous n\'avez pas le consentement nécessaire.',
            'error' => 'medical_record_access_denied',
            'code' => 403,
        ], 403);
    }
}
