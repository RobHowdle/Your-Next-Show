<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\VenueReview;
use Illuminate\Http\Request;
use App\Services\FilterService;
use App\Helpers\SocialLinksHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;


class VenueController extends Controller
{
    /**
     * Helper function to render rating icons
     */
    public function renderRatingIcons($overallScore)
    {
        $output = '';
        $totalIcons = 5;
        $fullIcons = floor($overallScore);
        $fraction = $overallScore - $fullIcons;
        $emptyIcon = asset('storage/images/system/ratings/empty.png');
        $fullIcon = asset('storage/images/system/ratings/full.png');
        $hotIcon = asset('storage/images/system/ratings/hot.png');

        if ($overallScore == $totalIcons) {
            // Display 5 hot icons when the score is 5/5
            $output = str_repeat('<img src="' . $hotIcon . '" alt="Hot Icon" />', $totalIcons);
        } else {
            // Add full icons
            for ($i = 0; $i < $fullIcons; $i++) {
                $output .= '<img src="' . $fullIcon . '" alt="Full Icon" />';
            }

            // Handle the fractional icon using clip-path
            if ($fraction > 0) {
                $output .= '<img src="' . $fullIcon . '" alt="Partial Full Icon" style="clip-path: inset(0 ' . ((1 - $fraction) * 100) . '% 0 0);" />';
            }

            // Add empty icons to fill the rest
            $iconsDisplayed = $fullIcons + ($fraction > 0 ? 1 : 0);
            $remainingIcons = $totalIcons - $iconsDisplayed;

            for ($i = 0; $i < $remainingIcons; $i++) {
                $output .= '<img src="' . $emptyIcon . '" alt="Empty Icon" />';
            }
        }

        return $output;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bandTypes = [
            'original-bands',
            'cover-bands',
            'tribute-bands',
            'all'
        ];

        $genresPath = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genresPath, true);
        $genres = $data['genres'];

        $genres = $genres['genres'] ?? $genres;

        // Convert to simple array if needed
        if (isset($genres[0]['name'])) {
            $genres = array_column($genres, 'name');
        }

        // Get paginated venues with 10 per page
        $venues = Venue::orderBy('name')
            ->paginate(10)
            ->through(function ($venue) {
                $overallScore = VenueReview::calculateOverallScore($venue->id);
                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'postal_town' => $venue->postal_town,
                    'contact_number' => $venue->contact_number,
                    'contact_email' => $venue->contact_email,
                    'location' => $venue->location,
                    'capacity' => $venue->capacity,
                    'is_verified' => $venue->is_verified,
                    'preferred_contact' => $venue->preferred_contact,
                    'platforms' => SocialLinksHelper::processSocialLinks($venue->contact_link),
                    'average_rating' => $overallScore,
                    'rating_icons' => $this->renderRatingIcons($overallScore)
                ];
            });

        return view('venues', compact('venues', 'genres', 'bandTypes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $venue = Venue::with(['extraInfo', 'upcomingEvents' => function ($query) {
            $query->where('event_date', '>=', now())
                ->orderBy('event_date', 'asc');
        }])->first()->where('name', 'LIKE', str_replace('-', ' ', $slug))
            ->orWhere('name', 'LIKE', ucwords(str_replace('-', ' ', $slug)))
            ->firstOrFail();
        $venueId = $venue->id;
        $existingPromoters = $venue->promoters;

        $suggestions = app('suggestions', ['venue' => $venue]);

        $platforms = SocialLinksHelper::processSocialLinks($venue->contact_link);
        $venue->platforms = $platforms;

        $overallScore = VenueReview::calculateOverallScore($venueId);
        $overallReviews[$venueId] = $this->renderRatingIcons($overallScore);

        // Get Review Scores
        $averageCommunicationRating = VenueReview::calculateAverageScore($venueId, 'communication_rating');
        $averageRopRating = VenueReview::calculateAverageScore($venueId, 'rop_rating');
        $averagePromotionRating = VenueReview::calculateAverageScore($venueId, 'promotion_rating');
        $averageQualityRating = VenueReview::calculateAverageScore($venueId, 'quality_rating');
        $reviewCount = VenueReview::getReviewCount($venueId);
        $recentReviews = VenueReview::getRecentReviews($venueId);
        $venue->recentReviews = $recentReviews->isNotEmpty() ? $recentReviews : null;

        $genres = json_decode($venue->genre);
        $genreNames = collect($genres)->keys()->toArray();

        $filteredGenres = collect($genres)->filter(function ($genreData, $genreName) {
            return $genreData->all === true || (isset($genreData->subgenres) && count($genreData->subgenres) > 0);
        })->toArray();

        // Prepare a structured array for the view
        $processedGenres = [];
        foreach ($filteredGenres as $genreName => $genreData) {
            $processedGenres[$genreName] = [
                'all' => $genreData->all,
                'subgenres' => $genreData->subgenres ?? []
            ];
        }

        // Fetch upcoming events through the pivot table
        $upcomingEvents = Event::whereHas('venues', function ($query) use ($venueId) {
            $query->where('venue_id', $venueId);
        })
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addMonth())
            ->orderBy('event_date', 'asc')
            ->get();

        $venue->upcomingEvents = $upcomingEvents;

        return view('venue', compact(
            'venue',
            'venueId',
            'genreNames',
            'processedGenres',
            'overallScore',
            'overallReviews',
            'averageCommunicationRating',
            'averageRopRating',
            'averagePromotionRating',
            'averageQualityRating',
            'reviewCount',
            'recentReviews',
            'platforms',
            'upcomingEvents',
        ))
            ->with([
                'promoterWithHighestRating' => $suggestions['promoter'],
                'photographerWithHighestRating' => $suggestions['photographer'],
                'videographerWithHighestRating' => $suggestions['videographer'],
                'bandWithHighestRating' => $suggestions['artist'],
                'designerWithHighestRating' => $suggestions['designer'],
                'existingPromoters' => $existingPromoters,
                'renderRatingIcons' => [$this, 'renderRatingIcons']
            ]);
    }

    public function filter(Request $request)
    {
        $query = Venue::query()->with(['review']);

        $filters = $request->input('filters', []);

        if (!empty($filters['bandTypes'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['bandTypes'] as $type) {
                    $q->orWhereJsonContains('band_type', $type);
                }
            });
        }

        if (!empty($filters['genres'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['genres'] as $genre) {
                    $q->orWhere('genre', 'LIKE', '%"' . $genre . '"%');
                }
            });
        }

        // Apply search filter
        if ($search = $request->input('filters.search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('postal_town', 'like', "%{$search}%");
            });
        }

        // Apply capacity filters
        if ($minCapacity = $request->input('filters.minCapacity')) {
            $query->where('capacity', '>=', $minCapacity);
        }

        if ($maxCapacity = $request->input('filters.maxCapacity')) {
            $query->where('capacity', '<=', $maxCapacity);
        }

        // Apply sorting
        if ($sort = $request->input('sort')) {
            $field = $sort['field'] ?? 'name';
            $direction = $sort['direction'] ?? 'asc';

            switch ($field) {
                case 'rating':
                    $query->orderBy('average_rating', $direction);
                    break;
                case 'capacity':
                    $query->orderBy('capacity', $direction);
                    break;
                case 'location':
                    $query->orderBy('postal_town', $direction);
                    break;
                default:
                    $query->orderBy('name', $direction);
            }
        }

        $venues = $query->paginate(10);

        // Transform the venues to include rating icons
        $transformedVenues = $venues->through(function ($venue) {
            $overallScore = VenueReview::calculateOverallScore($venue->id);
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'postal_town' => $venue->postal_town,
                'contact_number' => $venue->contact_number,
                'contact_email' => $venue->contact_email,
                'location' => $venue->location,
                'capacity' => $venue->capacity,
                'is_verified' => $venue->is_verified,
                'preferred_contact' => $venue->preferred_contact,
                'platforms' => SocialLinksHelper::processSocialLinks($venue->contact_link),
                'average_rating' => $overallScore,
                'rating_icons' => $this->renderRatingIcons($overallScore)
            ];
        });

        return response()->json([
            'results' => $transformedVenues->items(),
            'pagination' => view('components.pagination', ['paginator' => $venues])->render()
        ]);
    }

    public function locations()
    {
        $locations = Venue::whereNull('deleted_at')
            ->select('postal_town', DB::raw('COUNT(*) as count'))
            ->groupBy('postal_town')
            ->get();

        return view('locations', compact('locations'));
    }

    public function filterByCoordinates(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $searchQuery = $request->input('search_query');
        $town = null; // Initialize $town variable

        $bandTypes = [
            'original-bands',
            'cover-bands',
            'tribute-bands',
            'all'
        ];

        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        $query = Venue::query();

        // If we have a search query with comma (town, address format)
        if (strpos($searchQuery, ',') !== false) {
            list($town, $address) = explode(',', $searchQuery);
            $query->where(function ($q) use ($town, $latitude, $longitude) {
                $q->where('postal_town', 'LIKE', "%$town%");
            });
        } else {
            // Search by town name only
            $town = $searchQuery; // Set $town to the search query
            $query->where('postal_town', 'LIKE', "%$searchQuery%");
        }

        $venues = $query->paginate(10)->appends([
            'search_query' => $searchQuery,
            'latitude' => $latitude,
            'longitude' => $longitude,
            '_token' => $request->input('_token')
        ]);

        $overallReviews = [];

        // Process social links
        foreach ($venues as $venue) {
            $venue->platforms = SocialLinksHelper::processSocialLinks($venue->contact_link);
            $overallScore = VenueReview::calculateOverallScore($venue->id);
            $overallReviews[$venue->id] = $this->renderRatingIcons($overallScore);
        }

        $venuePromoterCount = isset($venue['promoters']) ? count($venue['promoters']) : 0;

        return view('venues', compact('venues', 'genres', 'bandTypes', 'town', 'venuePromoterCount', 'overallReviews'));
    }

    public function filterCheckboxesSearch(Request $request)
    {
        $filters = [
            'search_fields' => ['postal_town', 'name'], // Fields to search
            'transform' => function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'postal_town' => $item->postal_town,
                    'contact_number' => $item->contact_number,
                    'contact_email' => $item->contact_email,
                    'platforms' => explode(',', $item->contact_link),
                    'average_rating' => \App\Models\VenueReview::calculateOverallScore($item->id),
                ];
            },
        ];

        $data = FilterService::filterEntities($request, Venue::class, $filters);

        return response()->json($data);
    }

    public function submitVenueReview(Request $request, Venue $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'communication-rating' => 'required',
                'rop-rating' => 'required',
                'promotion-rating' => 'required',
                'quality-rating' => 'required',
                'review_author' => 'required',
                'review_message' => 'required',
                'reviewer_ip' => 'required'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            VenueReview::create([
                'venue_id' => $id['id'],
                'communication_rating' => $request->input('communication-rating'),
                'rop_rating' => $request->input('rop-rating'),
                'promotion_rating' => $request->input('promotion-rating'),
                'quality_rating' => $request->input('quality-rating'),
                'author' => $request->input('review_author'),
                'review' => $request->input('review_message'),
                'reviewer_ip' => $request->input('reviewer_ip'),
            ]);

            return back()->with('success', 'Review submitted successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error submitting review: ' . $e->getMessage());

            // Optionally, you can return an error response or redirect to an error page
            return back()->with('error', 'An error occurred while submitting the review. Please try again later.')->withInput();
        }
    }

    public function suggestPromoters(Request $request)
    {
        $venueId = $request->input('venue_id');
        $venue = Venue::findOrFail($venueId);

        $location = $venue->postal_town;
        $promotersByLocation = Promoter::where('location', $location)->get();

        dd($promotersByLocation);

        return view('components.promoter-suggestions', [
            'venueId' => $venueId,
            'promotersByLocation' => $promotersByLocation
        ]);
    }

    // Admin Functions
    public function edit(Venue $venue)
    {
        return view('admin.venues.edit', compact('venue'));
    }
}
