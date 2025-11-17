<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AppointmentNotCancellableException extends Exception
{
    protected int $hoursBeforeAppointment;

    public function __construct(int $hoursBeforeAppointment, string $message = "")
    {
        $this->hoursBeforeAppointment = $hoursBeforeAppointment;
        parent::__construct($message ?: "Le rendez-vous ne peut pas être annulé à moins de {$hoursBeforeAppointment}h.");
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'appointment_not_cancellable',
            'hours_before' => $this->hoursBeforeAppointment,
            'code' => 422,
        ], 422);
    }
}
