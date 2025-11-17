<?php

use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MedecinController;
use App\Http\Controllers\API\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/verify-2fa', [AuthController::class, 'verify2FA']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

// Patient routes
Route::prefix('patients')->group(function () {
    Route::post('/register', [PatientController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [PatientController::class, 'show']);
        Route::put('/profile', [PatientController::class, 'update']);
    });
});

// Medecin routes
Route::prefix('medecins')->group(function () {
    Route::post('/register', [MedecinController::class, 'register']);
    Route::get('/search', [MedecinController::class, 'search']);
    Route::get('/{id}', [MedecinController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [MedecinController::class, 'show']);
        Route::put('/profile', [MedecinController::class, 'update']);
    });
});

// Appointment routes (protected)
Route::middleware('auth:sanctum')->prefix('appointments')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::post('/', [AppointmentController::class, 'store']);
    Route::get('/{id}', [AppointmentController::class, 'show']);
    Route::post('/{id}/cancel', [AppointmentController::class, 'cancel']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Seha Digital API',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});
