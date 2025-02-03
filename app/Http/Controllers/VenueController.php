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

        $overallReviews = [];
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
    public function show(string $slug)
    {
        $venue = Venue::where('name', '=', $slug)->with('extraInfo')->first();
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

        return view('venue', compact(
            'venue',
            'venueId',
            'genreNames',
            'overallScore',
            'overallReviews',
            'averageCommunicationRating',
            'averageRopRating',
            'averagePromotionRating',
            'averageQualityRating',
            'reviewCount',
            'platforms',
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

        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        $query = Venue::query();

        // If we have a search query with comma (town, address format)
        if (strpos($searchQuery, ',') !== false) {
            list($town, $address) = explode(',', $searchQuery);
            $query->where(function ($q) use ($town, $latitude, $longitude) {
                $q->where('postal_town', 'LIKE', "%$town%");
                // ->where('latitude', $latitude)
                // ->where('longitude', $longitude);
            });
        } else {
            // Search by town name only
            $query->where('postal_town', 'LIKE', "%$searchQuery%");
            // ->where('latitude', $latitude)
            // ->where('longitude', $longitude);
        }

        // Get paginated results
        $venues = $query->paginate(10);

        $overallReviews = [];

        // Process social links
        foreach ($venues as $venue) {
            $venue->platforms = SocialLinksHelper::processSocialLinks($venue->contact_link);
            $overallScore = VenueReview::calculateOverallScore($venue->id);
            $overallReviews[$venue->id] = $this->renderRatingIcons($overallScore);
        }

        $venuePromoterCount = isset($venue['promoters']) ? count($venue['promoters']) : 0;

        return view('venues', compact('venues', 'genres', 'venuePromoterCount', 'overallReviews'));
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