<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get reviews for a medecin
     */
    public function getByMedecin(Request $request, $medecinId)
    {
        $reviews = Review::where('medecin_id', $medecinId)
            ->with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Get patient's reviews
     */
    public function myReviews(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reviews = Review::where('patient_id', $user->patient->id)
            ->with(['medecin.user', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }

    /**
     * Create a review
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
            'rating_professionalism' => 'required|integer|between:1,5',
            'rating_listening' => 'required|integer|between:1,5',
            'rating_explanation' => 'required|integer|between:1,5',
            'rating_punctuality' => 'required|integer|between:1,5',
            'rating_effectiveness' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify appointment belongs to patient
        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('patient_id', $user->patient->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'message' => 'Rendez-vous non trouvé ou non autorisé'
            ], 404);
        }

        // Check if appointment is completed
        if ($appointment->status !== 'completed') {
            return response()->json([
                'message' => 'Vous ne pouvez évaluer que les consultations terminées'
            ], 422);
        }

        // Check if already reviewed
        $existingReview = Review::where('appointment_id', $request->appointment_id)->first();
        if ($existingReview) {
            return response()->json([
                'message' => 'Vous avez déjà évalué cette consultation'
            ], 422);
        }

        // Calculate overall rating
        $overallRating = (
            $request->rating_professionalism +
            $request->rating_listening +
            $request->rating_explanation +
            $request->rating_punctuality +
            $request->rating_effectiveness
        ) / 5;

        $review = Review::create([
            'patient_id' => $user->patient->id,
            'medecin_id' => $appointment->medecin_id,
            'appointment_id' => $request->appointment_id,
            'rating_professionalism' => $request->rating_professionalism,
            'rating_listening' => $request->rating_listening,
            'rating_explanation' => $request->rating_explanation,
            'rating_punctuality' => $request->rating_punctuality,
            'rating_effectiveness' => $request->rating_effectiveness,
            'overall_rating' => round($overallRating, 1),
            'comment' => $request->comment,
        ]);

        // Update medecin's rating
        $this->updateMedecinRating($appointment->medecin_id);

        return response()->json($review, 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review = Review::where('patient_id', $user->patient->id)
            ->findOrFail($id);

        // Can only update within 7 days
        $createdAt = new \DateTime($review->created_at);
        $now = new \DateTime();
        $diff = $now->diff($createdAt);

        if ($diff->days > 7) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier un avis que dans les 7 jours suivant sa création'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating_professionalism' => 'integer|between:1,5',
            'rating_listening' => 'integer|between:1,5',
            'rating_explanation' => 'integer|between:1,5',
            'rating_punctuality' => 'integer|between:1,5',
            'rating_effectiveness' => 'integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->only([
            'rating_professionalism',
            'rating_listening',
            'rating_explanation',
            'rating_punctuality',
            'rating_effectiveness',
            'comment',
        ]));

        // Recalculate overall rating
        $overallRating = (
            $review->rating_professionalism +
            $review->rating_listening +
            $review->rating_explanation +
            $review->rating_punctuality +
            $review->rating_effectiveness
        ) / 5;

        $review->overall_rating = round($overallRating, 1);
        $review->save();

        // Update medecin's rating
        $this->updateMedecinRating($review->medecin_id);

        return response()->json($review);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review = Review::where('patient_id', $user->patient->id)
            ->findOrFail($id);

        $medecinId = $review->medecin_id;
        $review->delete();

        // Update medecin's rating
        $this->updateMedecinRating($medecinId);

        return response()->json(['message' => 'Review deleted successfully']);
    }

    /**
     * Report inappropriate review (admin)
     */
    public function report(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = Review::findOrFail($id);

        // Log the report for admin review
        \Log::warning('Review reported', [
            'review_id' => $id,
            'reporter_id' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Signalement enregistré. Notre équipe va examiner cet avis.'
        ]);
    }

    /**
     * Update medecin's overall rating
     */
    private function updateMedecinRating($medecinId)
    {
        $medecin = \App\Models\Medecin::findOrFail($medecinId);

        $averageRating = Review::where('medecin_id', $medecinId)
            ->avg('overall_rating');

        $medecin->rating = $averageRating ? round($averageRating, 1) : 0;
        $medecin->save();
    }
}
