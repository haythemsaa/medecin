<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use App\Models\Appointment;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Advanced medecin search with caching
     */
    public function searchMedecins(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string|max:255',
            'specialite' => 'nullable|string',
            'governorate' => 'nullable|string',
            'ville' => 'nullable|string',
            'languages' => 'nullable|array',
            'languages.*' => 'string|in:ar,fr,en',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'available_date' => 'nullable|date',
            'available_time' => 'nullable|string',
            'consultation_type' => 'nullable|in:video,cabinet,both',
            'accepts_new_patients' => 'nullable|boolean',
            'sort_by' => 'nullable|in:rating,price,experience,availability,distance',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Generate cache key from search parameters
        $cacheKey = 'search:medecins:' . md5(json_encode($request->all()));

        // Try cache first
        $cachedResults = $this->cacheService->get($cacheKey);
        if ($cachedResults !== null) {
            return response()->json($cachedResults);
        }

        // Build query
        $query = Medecin::query()
            ->where('validation_status', 'validated')
            ->with(['user', 'availabilities']);

        // Text search (name, bio, speciality)
        if ($request->filled('query')) {
            $searchTerm = $request->query;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%");
                })
                ->orWhere('specialite', 'like', "%{$searchTerm}%")
                ->orWhere('bio', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by speciality
        if ($request->filled('specialite')) {
            $query->where('specialite', $request->specialite);
        }

        // Filter by governorate
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // Filter by ville
        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        // Filter by languages
        if ($request->filled('languages')) {
            foreach ($request->languages as $language) {
                $query->whereJsonContains('languages_spoken', $language);
            }
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('tarif_consultation', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('tarif_consultation', '<=', $request->max_price);
        }

        // Filter by rating
        if ($request->filled('min_rating')) {
            $query->where('rating_average', '>=', $request->min_rating);
        }

        // Filter by availability
        if ($request->filled('available_date') && $request->filled('available_time')) {
            $dayOfWeek = date('N', strtotime($request->available_date));
            $time = $request->available_time;

            $query->whereHas('availabilities', function ($q) use ($dayOfWeek, $time) {
                $q->where('day_of_week', $dayOfWeek)
                  ->where('is_available', true)
                  ->where('start_time', '<=', $time)
                  ->where('end_time', '>=', $time);
            });
        }

        // Filter by accepts new patients
        if ($request->filled('accepts_new_patients')) {
            $query->where('accepts_new_patients', $request->accepts_new_patients);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'rating');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'rating':
                $query->orderBy('rating_average', $sortOrder);
                break;
            case 'price':
                $query->orderBy('tarif_consultation', $sortOrder);
                break;
            case 'experience':
                $query->orderBy('annees_experience', $sortOrder);
                break;
            case 'availability':
                // Order by medecins with more available slots
                $query->withCount('availabilities')
                      ->orderBy('availabilities_count', $sortOrder);
                break;
            default:
                $query->orderBy('rating_average', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $results = $query->paginate($perPage);

        // Transform results
        $transformedResults = [
            'data' => $results->items(),
            'pagination' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ],
            'filters' => [
                'specialites' => $this->getAvailableSpecialites(),
                'governorates' => $this->getAvailableGovernorates(),
                'price_range' => $this->getPriceRange(),
            ],
        ];

        // Cache results for 5 minutes
        $this->cacheService->put($cacheKey, $transformedResults, CacheService::TTL_SHORT);

        return response()->json($transformedResults);
    }

    /**
     * Get autocomplete suggestions
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:medecin,specialite,location',
        ]);

        $query = $request->query;
        $type = $request->get('type', 'all');

        $suggestions = [];

        // Medecin names
        if ($type === 'medecin' || $type === 'all') {
            $medecins = Medecin::whereHas('user', function ($q) use ($query) {
                $q->where('first_name', 'like', "{$query}%")
                  ->orWhere('last_name', 'like', "{$query}%");
            })
            ->where('validation_status', 'validated')
            ->with('user')
            ->limit(5)
            ->get()
            ->map(fn($m) => [
                'type' => 'medecin',
                'value' => $m->user->first_name . ' ' . $m->user->last_name,
                'label' => 'Dr. ' . $m->user->first_name . ' ' . $m->user->last_name,
                'subtitle' => $m->specialite,
                'id' => $m->id,
            ]);

            $suggestions = array_merge($suggestions, $medecins->toArray());
        }

        // Specialities
        if ($type === 'specialite' || $type === 'all') {
            $specialites = Medecin::where('validation_status', 'validated')
                ->where('specialite', 'like', "{$query}%")
                ->distinct()
                ->pluck('specialite')
                ->take(5)
                ->map(fn($s) => [
                    'type' => 'specialite',
                    'value' => $s,
                    'label' => $s,
                ]);

            $suggestions = array_merge($suggestions, $specialites->toArray());
        }

        // Locations
        if ($type === 'location' || $type === 'all') {
            $villes = Medecin::where('validation_status', 'validated')
                ->where(function ($q) use ($query) {
                    $q->where('ville', 'like', "{$query}%")
                      ->orWhere('governorate', 'like', "{$query}%");
                })
                ->select('ville', 'governorate')
                ->distinct()
                ->limit(5)
                ->get()
                ->map(fn($m) => [
                    'type' => 'location',
                    'value' => $m->ville,
                    'label' => $m->ville . ', ' . $m->governorate,
                ]);

            $suggestions = array_merge($suggestions, $villes->toArray());
        }

        return response()->json([
            'suggestions' => array_slice($suggestions, 0, 10),
        ]);
    }

    /**
     * Get search filters metadata
     */
    public function getFilters(): JsonResponse
    {
        return response()->json([
            'specialites' => $this->getAvailableSpecialites(),
            'governorates' => $this->getAvailableGovernorates(),
            'villes' => $this->getAvailableVilles(),
            'languages' => [
                ['value' => 'ar', 'label' => 'Arabe'],
                ['value' => 'fr', 'label' => 'Français'],
                ['value' => 'en', 'label' => 'Anglais'],
            ],
            'price_range' => $this->getPriceRange(),
            'consultation_types' => [
                ['value' => 'video', 'label' => 'Consultation vidéo'],
                ['value' => 'cabinet', 'label' => 'Consultation au cabinet'],
                ['value' => 'both', 'label' => 'Les deux'],
            ],
            'sort_options' => [
                ['value' => 'rating', 'label' => 'Note'],
                ['value' => 'price', 'label' => 'Prix'],
                ['value' => 'experience', 'label' => 'Expérience'],
                ['value' => 'availability', 'label' => 'Disponibilité'],
            ],
        ]);
    }

    /**
     * Get available specialities
     */
    private function getAvailableSpecialites(): array
    {
        return Medecin::where('validation_status', 'validated')
            ->distinct()
            ->pluck('specialite')
            ->map(fn($s) => ['value' => $s, 'label' => $s])
            ->values()
            ->toArray();
    }

    /**
     * Get available governorates
     */
    private function getAvailableGovernorates(): array
    {
        return Medecin::where('validation_status', 'validated')
            ->distinct()
            ->pluck('governorate')
            ->filter()
            ->map(fn($g) => ['value' => $g, 'label' => $g])
            ->values()
            ->toArray();
    }

    /**
     * Get available villes
     */
    private function getAvailableVilles(): array
    {
        return Medecin::where('validation_status', 'validated')
            ->select('ville', 'governorate')
            ->distinct()
            ->get()
            ->map(fn($m) => [
                'value' => $m->ville,
                'label' => $m->ville . ', ' . $m->governorate,
            ])
            ->toArray();
    }

    /**
     * Get price range
     */
    private function getPriceRange(): array
    {
        $stats = Medecin::where('validation_status', 'validated')
            ->selectRaw('MIN(tarif_consultation) as min, MAX(tarif_consultation) as max')
            ->first();

        return [
            'min' => (float) ($stats->min ?? 0),
            'max' => (float) ($stats->max ?? 200),
        ];
    }
}
