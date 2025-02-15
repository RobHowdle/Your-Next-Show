<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Http\Request;
use App\Models\PromoterReview;
use App\Services\FilterService;
use App\Helpers\SocialLinksHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PromoterController extends Controller
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

        $locations = Promoter::distinct()
            ->whereNotNull('postal_town')
            ->pluck('postal_town')
            ->sort()
            ->values()
            ->toArray();

        // Get paginated promoters with 10 per page
        $promoters = Promoter::orderBy('name')
            ->paginate(10)
            ->through(function ($promoter) {
                $genresArray = [];
                try {
                    $genresArray = json_decode($promoter->genre, true) ?? [];
                } catch (\Exception $e) {
                    Log::error("Error decoding genres for promoter {$promoter->id}: " . $e->getMessage());
                }

                return [
                    'id' => $promoter->id,
                    'name' => $promoter->name,
                    'postal_town' => $promoter->postal_town,
                    'contact_number' => $promoter->contact_number,
                    'contact_email' => $promoter->contact_email,
                    'location' => $promoter->location,
                    'genres' => $genresArray,
                    'is_verified' => $promoter->is_verified,
                    'preferred_contact' => $promoter->preferred_contact,
                    'platforms' => SocialLinksHelper::processSocialLinks($promoter->contact_link),
                    'average_rating' => PromoterReview::calculateOverallScore($promoter->id)
                ];
            });

        return view('promoters', compact('promoters', 'genres', 'bandTypes', 'locations'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $promoter = Promoter::with(['upcomingEvents' => function ($query) {
            $query->where('event_date', '>=', now())
                ->orderBy('event_date', 'asc');
        }])->first()->where('name', 'LIKE', str_replace('-', ' ', $slug))
            ->orWhere('name', 'LIKE', ucwords(str_replace('-', ' ', $slug)))
            ->firstOrFail();
        $promoterId = $promoter->id;
        $existingVenues = $promoter->venues;

        $suggestions = app('suggestions', ['promoter' => $promoter]);

        $platforms = SocialLinksHelper::processSocialLinks($promoter->contact_link);
        $promoter->platforms = $platforms;

        // Add the processed data to the venue
        $recentReviews = PromoterReview::getRecentReviews($promoterId);

        $overallScore = PromoterReview::calculateOverallScore($promoterId);
        $overallReviews[$promoterId] = $this->renderRatingIcons($overallScore);

        // Get Review Scores
        $averageCommunicationRating = PromoterReview::calculateAverageScore($promoterId, 'communication_rating');
        $averageRopRating = PromoterReview::calculateAverageScore($promoterId, 'rop_rating');
        $averagePromotionRating = PromoterReview::calculateAverageScore($promoterId, 'promotion_rating');
        $averageQualityRating = PromoterReview::calculateAverageScore($promoterId, 'quality_rating');
        $reviewCount = PromoterReview::getReviewCount($promoterId);
        $recentReviews = PromoterReview::getRecentReviews($promoterId);
        $promoter->recentReviews = $recentReviews->isNotEmpty() ? $recentReviews : null;


        $genres = json_decode($promoter->genre ?? '{}');
        $genreNames = collect($genres)->keys()->toArray();

        // Fetch upcoming events through the pivot table
        $upcomingEvents = Event::whereHas('promoters', function ($query) use ($promoterId) {
            $query->where('promoter_id', $promoterId);
        })
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addMonth())
            ->orderBy('event_date', 'asc')
            ->get();

        $promoter->upcomingEvents = $upcomingEvents;

        return view('promoter', compact(
            'promoter',
            'promoterId',
            'genreNames',
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

    public function filter(Request $request)
    {
        $query = Promoter::query();

        // Apply search filter with detailed logging
        if ($search = $request->input('filters.search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('postal_town', 'like', "%{$search}%");
            });
        }

        // Apply capacity filters
        if ($locations = $request->input('filters.locations')) {
            $query->where('postal_town', '>=', $locations);
        }

        // Apply band type filters
        if ($bandTypes = $request->input('filters.bandTypes')) {
            if (!empty($bandTypes)) {
                $query->where(function ($q) use ($bandTypes) {
                    foreach ($bandTypes as $type) {
                        if ($type !== 'all') {
                            $q->orWhereJsonContains('band_type', $type);
                        }
                    }
                });
            }
        }

        // Apply genre filters
        if ($selectedGenres = $request->input('filters.genres')) {
            if (!empty($selectedGenres)) {
                $query->where(function ($q) use ($selectedGenres) {
                    foreach ($selectedGenres as $genre) {
                        $q->orWhereJsonContains('genre', $genre);
                    }
                });
            }
        }

        // Execute query and log results
        $promoters = $query->get();

        // Map results
        $promoters = $promoters->map(function ($promoter) {
            return [
                'id' => $promoter->id,
                'name' => $promoter->name,
                'postal_town' => $promoter->postal_town,
                'contact_number' => $promoter->contact_number,
                'contact_email' => $promoter->contact_email,
                'location' => $promoter->location,
                'is_verified' => $promoter->is_verified,
                'preferred_contact' => $promoter->preferred_contact,
                'platforms' => SocialLinksHelper::processSocialLinks($promoter->contact_link),
                'average_rating' => PromoterReview::calculateOverallScore($promoter->id),
            ];
        });

        return response()->json([
            'results' => $promoters
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
                    'average_rating' => \App\Models\PromoterReview::calculateOverallScore($item->id),
                ];
            },
        ];

        $data = FilterService::filterEntities($request, Promoter::class, $filters);

        return response()->json($data);
    }
}