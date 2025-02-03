<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Helpers\SocialLinksHelper;

class FilterService
{
    public static function filterEntities(Request $request, $model, $filters)
    {
        try {
            $query = $model::query();

            // Dynamic filter: serviceType
            if (isset($filters['service_type'])) {
                $query->where('other_service_id', $filters['service_type']);
            }

            // Search Results
            $searchQuery = $request->input('search_query');
            if ($searchQuery) {
                $query->where(function ($query) use ($searchQuery, $filters) {
                    foreach ($filters['search_fields'] ?? [] as $field) {
                        $query->orWhere($field, 'LIKE', "%$searchQuery%");
                    }
                });
            }

            // Dynamic filter: Band Type
            if ($request->has('band_type')) {
                $bandType = $request->input('band_type');
                if (!empty($bandType)) {
                    $bandType = array_map('trim', $bandType);
                    $query->where(function ($query) use ($bandType) {
                        foreach ($bandType as $type) {
                            $query->orWhereRaw('JSON_CONTAINS(band_type, ?)', [json_encode($type)]);
                        }
                    });
                }
            }

            // Dynamic filter: Genre
            if ($request->has('genres') || $request->has('subgenres')) {
                $query->where(function ($query) use ($request) {
                    // Handle main genres
                    if ($request->has('genres')) {
                        $genres = array_map('trim', $request->input('genres'));
                        if (!empty($genres)) {
                            foreach ($genres as $genre) {
                                $query->orWhereRaw('JSON_EXTRACT(genre, ?) IS NOT NULL', ['$."' . $genre . '"']);
                            }
                        }
                    }

                    // Handle subgenres
                    if ($request->has('subgenres')) {
                        $subgenres = array_map(function ($subgenre) {
                            return strtolower(str_replace(' ', '_', trim($subgenre)));
                        }, $request->input('subgenres'));

                        if (!empty($subgenres)) {
                            $query->where(function ($query) use ($subgenres) {
                                foreach ($subgenres as $subgenre) {
                                    $query->orWhere(function ($q) use ($subgenre) {
                                        $q->whereRaw('JSON_SEARCH(LOWER(genre), "one", ?)', [$subgenre]);
                                    });
                                }
                            });
                        }
                    }
                });
            }

            // Apply pagination
            $results = $query->paginate(10);

            // Transform data with social links processing
            $transformedData = $results->getCollection()->map(function ($item) use ($filters) {
                $transformed = $filters['transform']($item);

                // Process social links if contact_link exists
                if (isset($transformed['platforms'])) {
                    $transformed['platforms'] = SocialLinksHelper::processSocialLinks($item->contact_link);
                }

                return $transformed;
            });

            return [
                'results' => $transformedData,
                'pagination' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'total' => $results->total(),
                    'per_page' => $results->perPage(),
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('FilterService Error: ' . $e->getMessage());
            throw new \Exception('Error loading relationships: ' . $e->getMessage());
        }
    }
}