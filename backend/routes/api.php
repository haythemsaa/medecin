<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ConsultationController;
use App\Http\Controllers\API\MedecinController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\PrescriptionController;
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

// Consultation routes (protected)
Route::middleware('auth:sanctum')->prefix('consultations')->group(function () {
    Route::get('/{id}', [ConsultationController::class, 'show']);
    Route::put('/{id}/notes', [ConsultationController::class, 'updateNotes']);
    Route::post('/{id}/prescriptions', [ConsultationController::class, 'createPrescription']);
    Route::post('/{id}/report-issue', [ConsultationController::class, 'reportIssue']);
    Route::post('/appointments/{id}/room', [ConsultationController::class, 'getRoomConfig']);
    Route::post('/appointments/{id}/end', [ConsultationController::class, 'endConsultation']);
});

// Medical Record routes (protected)
Route::middleware('auth:sanctum')->prefix('medical-records')->group(function () {
    // Patient routes
    Route::get('/my-record', [MedicalRecordController::class, 'show']);
    Route::put('/my-record', [MedicalRecordController::class, 'update']);
    Route::get('/consents', [MedicalRecordController::class, 'getConsents']);
    Route::post('/consents', [MedicalRecordController::class, 'createConsent']);
    Route::delete('/consents/{id}', [MedicalRecordController::class, 'revokeConsent']);
    Route::get('/access-history', [MedicalRecordController::class, 'getAccessHistory']);

    // Medecin routes
    Route::get('/patients/{patientId}', [MedicalRecordController::class, 'access']);
});

// Prescription routes (protected)
Route::middleware('auth:sanctum')->prefix('prescriptions')->group(function () {
    Route::get('/my-prescriptions', [PrescriptionController::class, 'myPrescriptions']);
    Route::get('/medecin-prescriptions', [PrescriptionController::class, 'medecinPrescriptions']);
    Route::get('/{id}', [PrescriptionController::class, 'show']);
    Route::get('/{id}/download', [PrescriptionController::class, 'download']);
    Route::post('/verify', [PrescriptionController::class, 'verify']);
});

// Admin routes (protected)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/medecins/pending', [AdminController::class, 'getPendingValidations']);
    Route::get('/medecins/{id}', [AdminController::class, 'getMedecinForValidation']);
    Route::post('/medecins/{id}/validate', [AdminController::class, 'validateMedecin']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
    Route::get('/statistics', [AdminController::class, 'getStatistics']);
});

// Messages routes (protected)
Route::middleware('auth:sanctum')->prefix('messages')->group(function () {
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/{id}/messages', [MessageController::class, 'getMessages']);
    Route::post('/conversations/{id}/messages', [MessageController::class, 'sendMessage']);
    Route::get('/unread-count', [MessageController::class, 'getUnreadCount']);
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
