<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class MedecinNotValidatedException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Votre compte médecin n\'a pas encore été validé par l\'administration. Veuillez patienter pendant la vérification de vos documents.',
            'error' => 'medecin_not_validated',
            'code' => 403,
        ], 403);
    }
}
