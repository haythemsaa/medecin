<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogMedicalRecordAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log access to medical records for INPDP compliance
        if ($request->user() && $response->isSuccessful()) {
            // Extract patient ID from route or request
            $patientId = $request->route('patientId') ?? $request->route('id');

            if ($patientId) {
                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'access_medical_record',
                    'model_type' => 'MedicalRecord',
                    'model_id' => $patientId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'metadata' => [
                        'route' => $request->route()->getName(),
                        'method' => $request->method(),
                        'timestamp' => now()->toIso8601String(),
                    ],
                ]);
            }
        }

        return $response;
    }
}
