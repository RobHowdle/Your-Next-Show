<?php

namespace App\Http\Controllers;

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
    public function index(Request $request)
    {
        $searchQuery = $request->input('search_query');

        $venues = Venue::whereNull('deleted_at')
            ->with('extraInfo', 'promoters')
            ->when($searchQuery, function ($query, $searchQuery) {
                return $query->where('postal_town', 'like', "%$searchQuery%");
            })
            ->paginate(10);

        // Fetch genres for initial page load
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        if ($request->ajax()) {
            return response()->json([
                'venues' => $venues,
                'view' => view('partials.venue-list', compact('venues', 'genres'))->render()
            ]);
        }

        $overallReviews = []; // Array to store overall reviews for each venue
        // Process each venue
        foreach ($venues as $venue) {
            $venue->platforms = SocialLinksHelper::processSocialLinks($venue->contact_link);
            $overallScore = VenueReview::calculateOverallScore($venue->id);
            $overallReviews[$venue->id] = $this->renderRatingIcons($overallScore);
        }


        $venuePromoterCount = isset($venue['promoters']) ? count($venue['promoters']) : 0;
        return view('venues', [
            'venues' => $venues,
            'genres' => $genres,
            'overallReviews' => $overallReviews,
            'venuePromoterCount' => $venuePromoterCount
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $venue = Venue::where('id', '=', $id)->with('extraInfo')->first();
        $venueId = $venue->id;
        $existingPromoters = $venue->promoters;

        $suggestions = app('suggestions', ['venue' => $venue]);

        $platforms = SocialLinksHelper::processSocialLinks($venue->contact_link);
        $venue->platforms = $platforms;

        $recentReviews = VenueReview::getRecentReviewsForVenue($id);
        $venue->recentReviews = $recentReviews->isNotEmpty() ? $recentReviews : null;

        $overallScore = VenueReview::calculateOverallScore($id);
        $overallReviews[$id] = $this->renderRatingIcons($overallScore);

        // Get Review Scores
        $averageCommunicationRating = VenueReview::calculateAverageScore($id, 'communication_rating');
        $averageRopRating = VenueReview::calculateAverageScore($id, 'rop_rating');
        $averagePromotionRating = VenueReview::calculateAverageScore($id, 'promotion_rating');
        $averageQualityRating = VenueReview::calculateAverageScore($id, 'quality_rating');
        $reviewCount = VenueReview::getReviewCount($id);

        $genres = json_decode($venue->genre);

        return view('venue', compact(
            'venue',
            'venueId',
            'genres',
            'overallScore',
            'overallReviews',
            'averageCommunicationRating',
            'averageRopRating',
            'averagePromotionRating',
            'averageQualityRating',
            'reviewCount'
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

        // Filter venues by latitude and longitude
        $venuesByCoordinatesQuery = Venue::where('latitude', $latitude)
            ->where('longitude', $longitude);

        // Initialize an empty query for venues by address
        $venuesByAddressQuery = Venue::query();

        // Check if the search query contains a comma (indicating both town and specific address)
        if (strpos($searchQuery, ',') !== false) {
            // If the search query contains a comma, split it into town and address
            list($town, $address) = explode(',', $searchQuery);

            // Perform search for venues matching the town or the address
            $venuesByAddressQuery->where(function ($query) use ($town, $address) {
                $query->where('postal_town', 'LIKE', "%$address%")
                    ->orWhere('postal_town', 'LIKE', "%$town%");
            });
        } else {
            // If the search query does not contain a comma, search for venues matching the town only
            $venuesByAddressQuery->where('postal_town', 'LIKE', "%$searchQuery%");
        }

        // Get the paginated results
        $venuesByCoordinates = $venuesByCoordinatesQuery->paginate(10, ['*'], 'coordinates_page');
        $venuesByAddress = $venuesByAddressQuery->paginate(10, ['*'], 'address_page');

        // Merge the paginated results, ensure to avoid duplicates
        $mergedVenues = $venuesByCoordinates->merge($venuesByAddress)->unique('id');

        // Paginate the merged results manually if needed (assuming 10 per page)
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageResults = $mergedVenues->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedResults = new LengthAwarePaginator($currentPageResults, $mergedVenues->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        // Process contact links for each venue to identify platforms
        foreach ($paginatedResults as $venue) {
            if ($venue->contact_link) {
                $urls = explode(',', $venue->contact_link);
                $platforms = [];

                // Check each URL against the platforms
                foreach ($urls as $url) {
                    // Initialize the platform as unknown
                    $matchedPlatform = 'Unknown';

                    // Check if the URL contains platform names
                    $platformsToCheck = ['facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];
                    foreach ($platformsToCheck as $platform) {
                        if (stripos($url, $platform) !== false) {
                            $matchedPlatform = $platform;
                            break;
                        }
                    }

                    // Store the platform information for each URL
                    $platforms[] = [
                        'url' => $url,
                        'platform' => $matchedPlatform
                    ];
                }

                // Add the processed data to the venue
                $venue->platforms = $platforms;
            }
        }

        // Fetch genres for initial page load
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        $overallReviews = [];

        foreach ($paginatedResults as $venue) {
            $overallScore = VenueReview::calculateOverallScore($venue->id);
            $overallReviews[$venue->id] = $this->renderRatingIcons($overallScore);
        }

        $venuePromoterCount = isset($venue['promoters']) ? count($venue['promoters']) : 0;

        return view('venues', [
            'venues' => $paginatedResults,
            'genres' => $genres,
            'searchQuery' => $searchQuery,
            'overallReviews' => $overallReviews,
            'venuePromoterCount' => $venuePromoterCount,
        ]);
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
}