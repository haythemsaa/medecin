<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get user's favorite doctors
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $favorites = Favorite::where('user_id', $user->id)
            ->with('medecin.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'favorites' => $favorites->map(fn($fav) => [
                'id' => $fav->id,
                'medecin' => $fav->medecin,
                'notes' => $fav->notes,
                'created_at' => $fav->created_at,
            ]),
        ]);
    }

    /**
     * Add a doctor to favorites
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        // Check if already in favorites
        $existing = Favorite::where('user_id', $user->id)
            ->where('medecin_id', $request->medecin_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Ce médecin est déjà dans vos favoris',
            ], 422);
        }

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'medecin_id' => $request->medecin_id,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Médecin ajouté aux favoris',
            'favorite' => $favorite->load('medecin.user'),
        ], 201);
    }

    /**
     * Update favorite notes
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $favorite = Favorite::where('user_id', $user->id)->findOrFail($id);

        $favorite->update([
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Notes mises à jour',
            'favorite' => $favorite,
        ]);
    }

    /**
     * Remove from favorites
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $favorite = Favorite::where('user_id', $user->id)->findOrFail($id);

        $favorite->delete();

        return response()->json([
            'message' => 'Médecin retiré des favoris',
        ]);
    }

    /**
     * Check if medecin is in favorites
     */
    public function check(Request $request, int $medecinId): JsonResponse
    {
        $user = $request->user();

        $isFavorite = Favorite::where('user_id', $user->id)
            ->where('medecin_id', $medecinId)
            ->exists();

        return response()->json([
            'is_favorite' => $isFavorite,
        ]);
    }
}
