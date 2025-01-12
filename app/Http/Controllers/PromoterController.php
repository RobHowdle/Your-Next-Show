<?php

namespace App\Http\Controllers;

use App\Models\Promoter;
use Illuminate\Http\Request;
use App\Models\PromoterReview;
use App\Helpers\SocialLinksHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PromoterController extends Controller
{
    protected $dashboardType;

    public function __construct(Request $request)
    {
        $this->dashboardType = $request->dashboardType;
        view()->share('dashboardType', $this->dashboardType);
    }

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

        $promoters = Promoter::whereNull('deleted_at')
            ->with('venues')
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
                'promoters' => $promoters,
                'view' => view('partials.promoter-list', compact('promoters', 'genres'))->render()
            ]);
        }

        // Process each promoter
        $overallReviews = []; // Array to store overall reviews for each venue
        foreach ($promoters as $promoter) {
            $promoter->platforms = SocialLinksHelper::processSocialLinks($promoter->contact_link);
            $overallScore = PromoterReview::calculateOverallScore($promoter->id);
            $overallReviews[$promoter->id] = $this->renderRatingIcons($overallScore);
        }


        $promoterVenueCount = isset($promoter['venues']) ? count($promoter['venues']) : 0;
        return view('promoters', [
            'promoters' => $promoters,
            'genres' => $genres,
            'overallReviews' => $overallReviews,
            'promoterVenueCount' => $promoterVenueCount
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promoter = Promoter::where('id', $id)->with('venues')->first();
        $promoterId = $promoter->id;
        $existingVenues = $promoter->venues;

        $suggestions = app('suggestions', ['promoter' => $promoter]);

        $platforms = SocialLinksHelper::processSocialLinks($promoter->contact_link);
        $promoter->platforms = $platforms;

        // Add the processed data to the venue
        $recentReviews = PromoterReview::getRecentReviewsForPromoter($id);
        $promoter->recentReviews = $recentReviews->isNotEmpty() ? $recentReviews : null;

        $overallScore = PromoterReview::calculateOverallScore($id);
        $overallReviews[$id] = $this->renderRatingIcons($overallScore);

        // Get Review Scores
        $averageCommunicationRating = PromoterReview::calculateAverageScore($id, 'communication_rating');
        $averageRopRating = PromoterReview::calculateAverageScore($id, 'rop_rating');
        $averagePromotionRating = PromoterReview::calculateAverageScore($id, 'promotion_rating');
        $averageQualityRating = PromoterReview::calculateAverageScore($id, 'quality_rating');
        $reviewCount = PromoterReview::getReviewCount($id);

        $genres = json_decode($promoter->genre);

        return view('promoter', compact(
            'promoter',
            'promoterId',
            'genres',
            'overallScore',
            'overallReviews',
            'averageCommunicationRating',
            'averageRopRating',
            'averagePromotionRating',
            'averageQualityRating',
            'reviewCount'
        ))->with([
            'venueWithHighestRating' => $suggestions['venue'],
            'photographerWithHighestRating' => $suggestions['photographer'],
            'videographerWithHighestRating' => $suggestions['videographer'],
            'bandWithHighestRating' => $suggestions['artist'],
            'designerWithHighestRating' => $suggestions['designer'],
            'existingVenues' => $existingVenues,
            'renderRatingIcons' => [$this, 'renderRatingIcons']
        ]);
    }

    public function submitPromoterReview(Request $request, Promoter $id)
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

            PromoterReview::create([
                'promoter_id' => $id['id'],
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

    public function filterCheckboxesSearch(Request $request)
    {
        $query = Promoter::query();

        // Search Results
        $searchQuery = $request->input('search_query');
        if ($searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                $query->where('postal_town', 'LIKE', "%$searchQuery%")
                    ->orWhere('name', 'LIKE', "%$searchQuery%");
            });
        }

        // Band Type Filter
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

        // Genre Filter
        if ($request->has('genres')) {
            $genres = $request->input('genres');
            if (!empty($genres)) {
                $genres = array_map('trim', $genres);
                $query->where(function ($query) use ($genres) {
                    foreach ($genres as $genre) {
                        $query->orWhereRaw('JSON_CONTAINS(genre, ?)', [json_encode($genre)]);
                    }
                });
            }
        }

        $promoters = $query->with('venues')->paginate(10);

        // Process each venue
        $transformedData = $promoters->getCollection()->map(function ($promoter) {
            // Split the field containing multiple URLs into an array
            $urls = explode(',', $promoter->contact_link);
            $platforms = [];

            foreach ($urls as $url) {
                $matchedPlatform = 'Unknown';
                $platformsToCheck = ['facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

                foreach ($platformsToCheck as $platform) {
                    if (stripos($url, $platform) !== false) {
                        $matchedPlatform = $platform;
                        break;
                    }
                }

                $platforms[] = [
                    'url' => $url,
                    'platform' => $matchedPlatform
                ];
            }

            $overallScore = \App\Models\PromoterReview::calculateOverallScore($promoter->id);

            return [
                'id' => $promoter->id,
                'name' => $promoter->name,
                'postal_town' => $promoter->postal_town,
                'contact_number' => $promoter->contact_number,
                'contact_email' => $promoter->contact_email,
                'platforms' => $platforms,
                'promoters' => $promoter->venues,
                'average_rating' => $overallScore,
            ];
        });


        // Return the transformed data with pagination info
        return response()->json([
            'promoters' => $transformedData,
            'pagination' => [
                'current_page' => $promoters->currentPage(),
                'last_page' => $promoters->lastPage(),
                'total' => $promoters->total(),
                'per_page' => $promoters->perPage(),
            ]
        ]);
    }
}
