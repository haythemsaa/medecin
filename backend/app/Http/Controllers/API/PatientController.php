<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * Register a new patient
     */
    public function register(Request $request)
    {
        $request->validate([
            // User information
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|unique:users,phone',
            'preferred_language' => 'nullable|in:fr,ar,en',

            // Patient information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'birth_date' => 'required|date|before:today',
            'cin' => 'required|string|size:8|unique:patients,cin',
            'gender' => 'required|in:male,female,other',
            'governorate' => 'nullable|string',
            'delegation' => 'nullable|string',
            'address' => 'nullable|string',
            'health_coverage' => 'nullable|in:cnam,mutuelle,none',
            'cnam_number' => 'nullable|string',
            'mutuelle_name' => 'nullable|string',

            // Medical information
            'allergies' => 'nullable|array',
            'chronic_diseases' => 'nullable|array',
            'current_treatments' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'patient',
                'preferred_language' => $request->preferred_language ?? 'fr',
                'status' => 'active',
            ]);

            // Create patient profile
            $patient = Patient::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'first_name_ar' => $request->first_name_ar,
                'last_name_ar' => $request->last_name_ar,
                'birth_date' => $request->birth_date,
                'cin' => $request->cin,
                'gender' => $request->gender,
                'governorate' => $request->governorate,
                'delegation' => $request->delegation,
                'address' => $request->address,
                'health_coverage' => $request->health_coverage ?? 'none',
                'cnam_number' => $request->cnam_number,
                'mutuelle_name' => $request->mutuelle_name,
            ]);

            // Create medical record
            MedicalRecord::create([
                'patient_id' => $patient->id,
                'allergies' => $request->allergies,
                'chronic_diseases' => $request->chronic_diseases,
                'current_treatments' => $request->current_treatments,
            ]);

            DB::commit();

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Inscription réussie. Bienvenue sur Seha Digital!',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('patient'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get patient profile
     */
    public function show(Request $request)
    {
        $patient = $request->user()->patient;
        $patient->load('medicalRecord');

        return response()->json($patient);
    }

    /**
     * Update patient profile
     */
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|unique:users,phone,' . $request->user()->id,
            'governorate' => 'nullable|string',
            'delegation' => 'nullable|string',
            'address' => 'nullable|string',
            'health_coverage' => 'nullable|in:cnam,mutuelle,none',
            'cnam_number' => 'nullable|string',
            'mutuelle_name' => 'nullable|string',
        ]);

        $patient = $request->user()->patient;
        $patient->update($request->only([
            'first_name',
            'last_name',
            'governorate',
            'delegation',
            'address',
            'health_coverage',
            'cnam_number',
            'mutuelle_name',
        ]));

        if ($request->has('phone')) {
            $request->user()->update(['phone' => $request->phone]);
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'patient' => $patient,
        ]);
    }
}
