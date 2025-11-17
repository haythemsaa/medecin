<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MedecinController extends Controller
{
    /**
     * Register a new medecin
     */
    public function register(Request $request)
    {
        $request->validate([
            // User information
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|unique:users,phone',
            'preferred_language' => 'nullable|in:fr,ar,en',

            // Medecin information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cin' => 'required|string|size:8|unique:medecins,cin',
            'ordre_number' => 'required|string|unique:medecins,ordre_number',
            'speciality' => 'required|string',
            'sub_specialities' => 'nullable|array',
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'nullable|string|max:1000',
            'consultation_languages' => 'nullable|array',
            'consultation_price' => 'required|numeric|min:0',
            'urgent_consultation_price' => 'nullable|numeric|min:0',

            // Documents
            'cin_file_recto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'cin_file_verso' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ordre_certificate' => 'nullable|file|mimes:pdf|max:5120',
            'diploma_file' => 'nullable|file|mimes:pdf|max:5120',
            'rcp_attestation' => 'nullable|file|mimes:pdf|max:5120',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'medecin',
                'preferred_language' => $request->preferred_language ?? 'fr',
                'status' => 'inactive', // Inactive until validated
            ]);

            // Handle file uploads (simplified - in production use proper storage)
            $fileFields = [
                'cin_file_recto',
                'cin_file_verso',
                'ordre_certificate',
                'diploma_file',
                'rcp_attestation',
                'photo',
            ];

            $uploadedFiles = [];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('medecins/documents', 'public');
                    $uploadedFiles[$field] = $path;
                }
            }

            // Create medecin profile
            $medecin = Medecin::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'cin' => $request->cin,
                'ordre_number' => $request->ordre_number,
                'speciality' => $request->speciality,
                'sub_specialities' => $request->sub_specialities,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'consultation_languages' => $request->consultation_languages ?? ['fr'],
                'consultation_price' => $request->consultation_price,
                'urgent_consultation_price' => $request->urgent_consultation_price ?? ($request->consultation_price * 1.3),
                'validation_status' => 'pending',
                ...$uploadedFiles,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Inscription soumise avec succès. Votre dossier sera examiné sous 72 heures.',
                'user' => $user->load('medecin'),
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
     * Get medecin profile
     */
    public function show(Request $request, $id = null)
    {
        if ($id) {
            $medecin = Medecin::with('user')->findOrFail($id);
        } else {
            $medecin = $request->user()->medecin;
        }

        $medecin->load(['reviews' => function ($query) {
            $query->where('status', 'approved')->latest()->limit(10);
        }]);

        return response()->json($medecin);
    }

    /**
     * Search medecins
     */
    public function search(Request $request)
    {
        $query = Medecin::query()
            ->where('validation_status', 'validated')
            ->with('user');

        // Filter by speciality
        if ($request->has('speciality')) {
            $query->where('speciality', $request->speciality);
        }

        // Filter by governorate (would need to add governorate field to medecins table)
        if ($request->has('governorate')) {
            // $query->where('governorate', $request->governorate);
        }

        // Filter by language
        if ($request->has('language')) {
            $query->whereJsonContains('consultation_languages', $request->language);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('consultation_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('consultation_price', '<=', $request->max_price);
        }

        // Filter by rating
        if ($request->has('min_rating')) {
            $query->where('rating_average', '>=', $request->min_rating);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'rating_average');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $medecins = $query->paginate(20);

        return response()->json($medecins);
    }

    /**
     * Update medecin profile
     */
    public function update(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'consultation_languages' => 'nullable|array',
            'consultation_price' => 'sometimes|numeric|min:0',
            'urgent_consultation_price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $medecin = $request->user()->medecin;

        $data = $request->only([
            'bio',
            'consultation_languages',
            'consultation_price',
            'urgent_consultation_price',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('medecins/photos', 'public');
        }

        $medecin->update($data);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'medecin' => $medecin,
        ]);
    }
}
