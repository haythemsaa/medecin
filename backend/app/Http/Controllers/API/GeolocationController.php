<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeolocationController extends Controller
{
    /**
     * Search doctors near a location
     */
    public function searchNearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50', // km
            'specialty' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // Default 10km
        $limit = $request->limit ?? 20;

        // Haversine formula to calculate distance
        $medecins = Medecin::select([
                'medecins.*',
                DB::raw("
                    (
                        6371 * acos(
                            cos(radians($latitude))
                            * cos(radians(latitude))
                            * cos(radians(longitude) - radians($longitude))
                            + sin(radians($latitude))
                            * sin(radians(latitude))
                        )
                    ) AS distance
                ")
            ])
            ->where('status', 'validated')
            ->where('show_on_map', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius);

        // Filter by specialty if provided
        if ($request->specialty) {
            $medecins->where('specialite', $request->specialty);
        }

        $medecins = $medecins
            ->with(['user', 'reviews'])
            ->orderBy('distance')
            ->limit($limit)
            ->get();

        return response()->json([
            'doctors' => $medecins->map(function ($medecin) {
                return [
                    'id' => $medecin->id,
                    'user' => [
                        'nom' => $medecin->user->nom,
                        'prenom' => $medecin->user->prenom,
                        'photo' => $medecin->user->photo,
                        'telephone' => $medecin->user->telephone,
                    ],
                    'specialite' => $medecin->specialite,
                    'adresse' => $medecin->adresse,
                    'ville' => $medecin->ville,
                    'code_postal' => $medecin->code_postal,
                    'tarif' => $medecin->tarif,
                    'latitude' => $medecin->latitude,
                    'longitude' => $medecin->longitude,
                    'distance' => round($medecin->distance, 2), // km
                    'rating' => $medecin->reviews->avg('note') ?? 0,
                    'total_reviews' => $medecin->reviews->count(),
                    'accepts_urgent' => $medecin->accepts_urgent,
                ];
            }),
            'center' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
            'radius' => $radius,
            'total' => $medecins->count(),
        ]);
    }

    /**
     * Get doctors by city
     */
    public function searchByCity(Request $request): JsonResponse
    {
        $request->validate([
            'ville' => 'required|string',
            'specialty' => 'nullable|string',
        ]);

        $query = Medecin::where('status', 'validated')
            ->where('show_on_map', true)
            ->where('ville', 'like', '%' . $request->ville . '%');

        if ($request->specialty) {
            $query->where('specialite', $request->specialty);
        }

        $medecins = $query
            ->with(['user', 'reviews'])
            ->get();

        return response()->json([
            'doctors' => $medecins->map(function ($medecin) {
                return [
                    'id' => $medecin->id,
                    'user' => [
                        'nom' => $medecin->user->nom,
                        'prenom' => $medecin->user->prenom,
                        'photo' => $medecin->user->photo,
                        'telephone' => $medecin->user->telephone,
                    ],
                    'specialite' => $medecin->specialite,
                    'adresse' => $medecin->adresse,
                    'ville' => $medecin->ville,
                    'code_postal' => $medecin->code_postal,
                    'tarif' => $medecin->tarif,
                    'latitude' => $medecin->latitude,
                    'longitude' => $medecin->longitude,
                    'rating' => $medecin->reviews->avg('note') ?? 0,
                    'total_reviews' => $medecin->reviews->count(),
                ];
            }),
            'ville' => $request->ville,
            'total' => $medecins->count(),
        ]);
    }

    /**
     * Update doctor geolocation
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'ville' => 'required|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'show_on_map' => 'nullable|boolean',
        ]);

        $medecin = $request->user()->medecin;
        if (!$medecin) {
            return response()->json(['message' => 'Medecin profile not found'], 404);
        }

        $medecin->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'ville' => $request->ville,
            'code_postal' => $request->code_postal,
            'show_on_map' => $request->show_on_map ?? $medecin->show_on_map,
        ]);

        return response()->json([
            'message' => 'Localisation mise à jour',
            'medecin' => $medecin,
        ]);
    }

    /**
     * Get popular cities
     */
    public function getPopularCities(): JsonResponse
    {
        $cities = Medecin::where('status', 'validated')
            ->whereNotNull('ville')
            ->select('ville', DB::raw('COUNT(*) as count'))
            ->groupBy('ville')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'cities' => $cities,
        ]);
    }

    /**
     * Geocode address (using a geocoding service)
     */
    public function geocodeAddress(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        // This is a placeholder - you would integrate with a real geocoding service
        // like Google Maps Geocoding API, OpenStreetMap Nominatim, etc.

        // For Tunisia, you could use:
        // - Google Maps Geocoding API
        // - OpenStreetMap Nominatim
        // - MapBox Geocoding API

        // Example implementation (placeholder):
        $coordinates = $this->mockGeocode($request->address);

        return response()->json([
            'address' => $request->address,
            'coordinates' => $coordinates,
        ]);
    }

    /**
     * Mock geocoding function (replace with real implementation)
     */
    private function mockGeocode(string $address): array
    {
        // Major Tunisian cities coordinates
        $tunisianCities = [
            'tunis' => ['latitude' => 36.8065, 'longitude' => 10.1815],
            'sfax' => ['latitude' => 34.7406, 'longitude' => 10.7603],
            'sousse' => ['latitude' => 35.8256, 'longitude' => 10.6369],
            'kairouan' => ['latitude' => 35.6781, 'longitude' => 10.0963],
            'bizerte' => ['latitude' => 37.2744, 'longitude' => 9.8739],
            'gabes' => ['latitude' => 33.8815, 'longitude' => 10.0982],
            'ariana' => ['latitude' => 36.8625, 'longitude' => 10.1956],
            'monastir' => ['latitude' => 35.7776, 'longitude' => 10.8264],
            'nabeul' => ['latitude' => 36.4561, 'longitude' => 10.7367],
            'ben arous' => ['latitude' => 36.7543, 'longitude' => 10.2176],
        ];

        $addressLower = strtolower($address);

        foreach ($tunisianCities as $city => $coords) {
            if (stripos($addressLower, $city) !== false) {
                return $coords;
            }
        }

        // Default to Tunis center
        return ['latitude' => 36.8065, 'longitude' => 10.1815];
    }
}
