<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Promoter;
use App\Models\BandReviews;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Models\DesignerReviews;
use App\Services\FilterService;
use App\Models\OtherServiceList;
use App\Helpers\SocialLinksHelper;
use App\Models\PhotographyReviews;
use App\Models\VideographyReviews;
use Illuminate\Support\Facades\DB;
use App\Models\OtherServicesReview;
use App\Models\PhotographerReviews;

class OtherServiceController extends Controller
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

        $searchQuery = $request->input('search_query');

        // Retrieve all services with their counts
        $otherServices = OtherService::with('otherServiceList')
            ->select('other_service_id', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->when($searchQuery, function ($query, $searchQuery) {
                return $query->where('postal_town', 'like', "%$searchQuery%");
            })
            ->groupBy('other_service_id')
            ->paginate(10);

        // Create an array to hold counts for each service
        $serviceCounts = [];
        foreach ($otherServices as $service) {
            $serviceCounts[$service->other_service_id] = $service->total;
            $service->platforms = SocialLinksHelper::processSocialLinks($service->contact_link);
        }

        return view('other', [
            'otherServices' => $otherServices,
            'serviceCounts' => $serviceCounts,
        ]);
    }

    public function showGroup(Request $request, $serviceName)
    {
        // Get the service type ID
        $otherServiceIds = OtherServiceList::where('service_name', $serviceName)->pluck('id');

        $bandTypes = [
            'original-bands',
            'cover-bands',
            'tribute-bands',
            'all'
        ];

        // Get genres from JSON file
        $genresPath = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genresPath, true);
        $genres = $data['genres'];
        $genres = $genres['genres'] ?? $genres;

        // Convert to simple array if needed
        if (isset($genres[0]['name'])) {
            $genres = array_column($genres, 'name');
        }

        $photographyEnvironments = config('environment_types');

        $query = OtherService::whereIn('other_service_id', $otherServiceIds);

        // Get the town from the request if it exists
        $town = $request->query('town');
        if ($town) {
            $query->where('postal_town', 'LIKE', "%{$town}%");
        }

        // Get paginated services
        $singleServices = $query->orderBy('name')->paginate(10);

        // Transform the services to include all necessary data
        $transformedServices = $singleServices->through(function ($service) use ($serviceName) {
            // Get the appropriate review model based on service type
            $reviewScore = match ($serviceName) {
                'artist' => BandReviews::calculateOverallScore($service->id),
                'photography' => PhotographyReviews::calculateOverallScore($service->id),
                'videography' => VideographyReviews::calculateOverallScore($service->id),
                'designer' => DesignerReviews::calculateOverallScore($service->id),
                default => 0
            };

            return [
                'id' => $service->id,
                'name' => $service->name,
                'postal_town' => $service->postal_town,
                'contact_number' => $service->contact_number,
                'contact_email' => $service->contact_email,
                'location' => $service->location,
                'services' => $service->services,
                'is_verified' => $service->is_verified,
                'preferred_contact' => $service->preferred_contact,
                'platforms' => SocialLinksHelper::processSocialLinks($service->contact_link),
                'average_rating' => $reviewScore,
                'rating_icons' => $this->renderRatingIcons($reviewScore),
                'service_type' => $serviceName,
                'genres' => json_decode($service->genre ?? '{}', true),
                'environments' => json_decode($service->environment_type ?? '{}', true)
            ];
        });

        // Get unique locations for filtering
        $locations = OtherService::whereIn('other_service_id', $otherServiceIds)
            ->whereNotNull('postal_town')
            ->pluck('postal_town')
            ->unique()
            ->values()
            ->toArray();

        return view('single-service-group', [
            'singleServices' => $transformedServices,
            'genres' => $genres,
            'bandTypes' => $bandTypes,
            'photographyEnvironments' => $photographyEnvironments,
            'locations' => $locations,
            'town' => $town,
            'serviceName' => $serviceName,
            'serviceType' => ucfirst($serviceName)
        ]);
    }

    public function show($serviceName, $name)
    {
        $formattedName = Str::title(str_replace('-', ' ', $name));
        $singleService = OtherService::where('name', $formattedName)->first();

        // Fetch genres for initial page load
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        $serviceData = match ($singleService->services) {
            'Artist' => $this->getArtistData($singleService),
            'Photography' => $this->getPhotographerData($singleService),
            'Videography' => $this->getVideographerData($singleService),
            'Designer' => $this->getDesignerData($singleService),
            default => []
        };

        // Calculate review score based on service type
        $reviewScore = match ($singleService->services) {
            'Artist' => BandReviews::calculateOverallScore($singleService->id),
            'Photography' => PhotographyReviews::calculateOverallScore($singleService->id),
            'Videography' => VideographyReviews::calculateOverallScore($singleService->id),
            'Designer' => DesignerReviews::calculateOverallScore($singleService->id),
            default => 0
        };

        $overallReviews = [];
        $overallReviews[$singleService->id] = $this->renderRatingIcons($reviewScore);

        return view('single-service', [
            'singleService' => $singleService,
            'genres' => $genres,
            'overallReviews' => $overallReviews,
            'reviewCount' => $reviewScore,
            'serviceData' => $serviceData,
            'genreNames' => $serviceData['genreNames'] ?? [],
        ]);
    }

    public function filter(Request $request)
    {
        switch ($request->serviceType) {
            case 'Photographer':
                $serviceType = 'Photography';
                break;
            case 'Videographer':
                $serviceType = 'Videography';
                break;
            case 'Designer':
                $serviceType = 'Designer';
                break;
            case 'Artist':
                $serviceType = 'Artist';
                break;
        }
        $query = OtherService::query()->where('services', $serviceType);

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

        // Apply genre filters
        if ($selectedBandTypes = $request->input('filters.bandTypes')) {
            if (!empty($selectedBandTypes)) {
                $query->where(function ($q) use ($selectedBandTypes) {
                    foreach ($selectedBandTypes as $bandType) {
                        $q->orWhereJsonContains('band_type', $bandType);
                    }
                });
            }
        }

        if ($selectedEnvironments = $request->input('filters.environments')) {
            if (!empty($selectedEnvironments)) {
                $query->where(function ($q) use ($selectedEnvironments) {
                    foreach ($selectedEnvironments as $category => $environments) {
                        foreach ($environments as $environment) {
                            // Check both types and settings arrays in the JSON
                            $q->orWhere(function ($subQ) use ($environment) {
                                $subQ->whereJsonContains('environment_type->types', $environment)
                                    ->orWhereJsonContains('environment_type->settings', $environment);
                            });
                        }
                    }
                });
            }
        }

        // Execute query and log results
        $services = $query->get();

        // Map results
        $services = $services->map(function ($service) {
            $serviceReview = null;
            switch ($service) {
                case 'Artist':
                    $serviceReview = BandReviews::calculateOverallScore($service->id);
                    break;
                case 'Photography':
                    $serviceReview = PhotographyReviews::calculateOverallScore($service->id);
                    break;
                case 'Videography':
                    $serviceReview = VideographyReviews::calculateOverallScore($service->id);
                    break;
                case 'Designer':
                    $serviceReview = DesignerReviews::calculateOverallScore($service->id);
                    break;
            }
            return [
                'id' => $service->id,
                'name' => $service->name,
                'postal_town' => $service->postal_town,
                'contact_number' => $service->contact_number,
                'contact_email' => $service->contact_email,
                'location' => $service->location,
                'is_verified' => $service->is_verified,
                'preferred_contact' => $service->preferred_contact,
                'platforms' => SocialLinksHelper::processSocialLinks($service->contact_link),
                'average_rating' => $serviceReview,
                'rating_icons' => $this->renderRatingIcons($serviceReview)

            ];
        });

        return response()->json([
            'results' => $services
        ]);
    }

    /**
     * Get single service specific data
     */
    private function getArtistData(OtherService $singleService)
    {
        $service = $singleService;
        $serviceId = $service->id;

        $members = $singleService['members'] ? json_decode($singleService['members'], true) : [];
        $streamUrls = $service->stream_urls;
        $platforms = SocialLinksHelper::processSocialLinks($service->contact_link);
        $service->platforms = $platforms;

        $overallScore = OtherServicesReview::calculateOverallScore($serviceId);
        $overallReviews[$serviceId] = $this->renderRatingIcons($overallScore);
        $bandAverageCommunicationRating = BandReviews::calculateAverageScore($serviceId, 'communication_rating');
        $bandAverageMusicRating = BandReviews::calculateAverageScore($serviceId, 'music_rating');
        $bandAveragePromotionRating = BandReviews::calculateAverageScore($serviceId, 'promotion_rating');
        $bandAverageGigQualityRating = BandReviews::calculateAverageScore($serviceId, 'gig_quality_rating');
        $reviewCount = BandReviews::getReviewCount($serviceId);
        $recentReviews = BandReviews::getRecentReviews($serviceId);

        return [
            'members' => $members,
            'overallScore' => $overallScore,
            'overallReviews' => $overallReviews,
            'bandAverageCommunicationRating' => $bandAverageCommunicationRating,
            'bandAverageMusicRating' => $bandAverageMusicRating,
            'bandAveragePromotionRating' => $bandAveragePromotionRating,
            'bandAverageGigQualityRating' => $bandAverageGigQualityRating,
            'renderRatingIcons' => [$this, 'renderRatingIcons'],
            'reviewCount' => $reviewCount,
            'recentReviews' => $recentReviews,
            'streamUrls' => $streamUrls,
        ];
    }

    private function getPhotographerData(OtherService $singleService)
    {
        // Handle portfolio images
        $portfolioImages = [];
        if ($singleService->portfolio_images) {
            if (is_string($singleService->portfolio_images)) {
                try {
                    $portfolioImages = json_decode($singleService->portfolio_images, true) ?: [];
                } catch (\Exception $e) {
                    \Log::error('Failed to decode portfolio images: ' . $e->getMessage());
                }
            } elseif (is_array($singleService->portfolio_images)) {
                $portfolioImages = $singleService->portfolio_images;
            }
        }
        $platforms = SocialLinksHelper::processSocialLinks($singleService->contact_link);
        $singleService->platforms = $platforms;
        $packages = $singleService ? json_decode($singleService->packages) : [];
        $services = collect($packages)->pluck('job_type')->unique()->values()->toArray();
        $genres = $singleService ? json_decode($singleService->genre) : [];
        $genreNames = collect($genres)->keys()->toArray();
        $overallScore = PhotographerReviews::calculateOverallScore($singleService->id);
        $overallReviews[$singleService->id] = $this->renderRatingIcons($overallScore);
        $reviewCount = PhotographerReviews::getReviewCount($singleService->id);
        $recentReviews = PhotographerReviews::getRecentReviews($singleService->id);
        $environmentData = json_decode($singleService->environment_type, true) ?? [];


        return [
            'description' => $singleService->description ?? '',
            'portfolioImages' => $portfolioImages,
            'packages' => $packages,
            'portfolioLink' => $singleService->portfolio_link ?? '',
            'environmentTypes' => $singleService->environment_type
                ? json_decode($singleService->environment_type, true)
                : [],
            'types' => isset($singleService->environment_type)
                ? array_keys(json_decode($singleService->environment_type, true))
                : [],
            'settings' => isset($singleService->environment_type)
                ? array_values(json_decode($singleService->environment_type, true))
                : [],
            'photographerAverageCommunicationRating' => PhotographerReviews::calculateAverageScore($singleService->id, 'communication_rating'),
            'photographerAverageFlexibilityRating' => PhotographerReviews::calculateAverageScore($singleService->id, 'flexibility_rating'),
            'photographerAverageProfessionalismRating' => PhotographerReviews::calculateAverageScore($singleService->id, 'professionalism_rating'),
            'photographerAveragePhotoQualityRating' => PhotographerReviews::calculateAverageScore($singleService->id, 'photo_quality_rating'),
            'photographerAveragePriceRating' => PhotographerReviews::calculateAverageScore($singleService->id, 'price_rating'),
            'overallScore' => $overallScore,
            'overallReviews' => $overallReviews,
            'reviewCount' => $reviewCount,
            'recentReviews' => $recentReviews,
            'renderRatingIcons' => [$this, 'renderRatingIcons'],
            'platforms' => $singleService->platforms,
            'services' => $services,
            'genres' => $genres,
            'genreNames' => $genreNames,
            'types' => $environmentData['types'] ?? [],
            'settings' => $environmentData['settings'] ?? [],
        ];
    }

    private function getVideographerData(OtherService $singleService)
    {
        $service = $singleService;
        $serviceId = $service->id;

        $description = $service ? $service->description : '';
        $packages = $service ? json_decode($service->packages) : [];
        $portfolioImages = $service->portfolio_images;
        $portfolioLink = $service->portfolio_link;
        $platforms = SocialLinksHelper::processSocialLinks($service->contact_link);
        $service->platforms = $platforms;
        $environmentTypes = $service ? json_decode($service->environment_type, true) : [];
        $types = $environmentTypes ? $environmentTypes['types'] : [];
        $styles = $service ? json_decode($service->styles, true) : [];
        $settings = $environmentTypes ? $environmentTypes['settings'] : [];
        $workingTimes = $service ? json_decode($service->working_times, true) : [];

        $overallScore = OtherServicesReview::calculateOverallScore($serviceId);
        $overallReviews[$serviceId] = $this->renderRatingIcons($overallScore);

        $videographyAverageCommunicationRating = VideographyReviews::calculateAverageScore($serviceId, 'communication_rating');
        $videographyAverageFlexibilityRating = VideographyReviews::calculateAverageScore($serviceId, 'flexibility_rating');
        $videographyAverageProfessionalismRating = VideographyReviews::calculateAverageScore($serviceId, 'professionalism_rating');
        $videographyAverageVideoQualityRating = VideographyReviews::calculateAverageScore($serviceId, 'video_quality_rating');
        $videographyAveragePriceRating = VideographyReviews::calculateAverageScore($serviceId, 'price_rating');
        $reviewCount = VideographyReviews::getReviewCount($serviceId);
        $recentReviews = VideographyReviews::getRecentReviews($serviceId);


        return [
            'description' => $description,
            'packages' => $packages,
            'portfolioImages' => $portfolioImages,
            'portfolioLink' => $portfolioLink,
            'environmentTypes' => $environmentTypes,
            'types' => $types,
            'styles' => $styles,
            'workingTimes' => $workingTimes,
            'settings' => $settings,
            'workingTimes' => $workingTimes,
            'overallScore' => $overallScore,
            'overallReviews' => $overallReviews,
            'videographyAverageCommunicationRating' => $videographyAverageCommunicationRating,
            'videographyAverageFlexibilityRating' => $videographyAverageFlexibilityRating,
            'videographyAverageProfessionalismRating' => $videographyAverageProfessionalismRating,
            'videographyAverageVideoQualityRating' => $videographyAverageVideoQualityRating,
            'videographyAveragePriceRating' => $videographyAveragePriceRating,
            'renderRatingIcons' => [$this, 'renderRatingIcons'],
            'reviewCount' => $reviewCount,
            'recentReviews' => $recentReviews,
            'platforms' => $service->platforms,
        ];
    }

    private function getDesignerData(OtherService $singleService)
    {
        $service = $singleService;
        $serviceId = $service->id;

        $description = $service ? $service->description : '';
        $portfolioImages = $service->portfolio_images;
        $portfolioLink = $service->portfolio_link;
        $platforms = SocialLinksHelper::processSocialLinks($service->contact_link);
        $service->platforms = $platforms;
        $packages = $service ? json_decode($service->packages) : [];
        $services = collect($packages)->pluck('job_type')->unique()->values()->toArray();

        $overallScore = DesignerReviews::calculateOverallScore($serviceId);
        $overallReviews[$serviceId] = $this->renderRatingIcons($overallScore);

        $designerAverageCommunicationRating = DesignerReviews::calculateAverageScore($serviceId, 'communication_rating');
        $designerAverageFlexibilityRating = DesignerReviews::calculateAverageScore($serviceId, 'flexibility_rating');
        $designerAverageProfessionalismRating = DesignerReviews::calculateAverageScore($serviceId, 'professionalism_rating');
        $designerAverageDesignQualityRating = DesignerReviews::calculateAverageScore($serviceId, 'design_quality_rating');
        $designerAveragePriceRating = DesignerReviews::calculateAverageScore($serviceId, 'price_rating');
        $reviewCount = DesignerReviews::getReviewCount($serviceId);
        $recentReviews = DesignerReviews::getRecentReviews($serviceId);

        return [
            'serviceType' => $service->services,
            'serviceId' => $serviceId,
            'description' => $description,
            'packages' => $packages,
            'overallScore' => $overallScore,
            'overallReviews' => $overallReviews,
            'designerAverageCommunicationRating' => $designerAverageCommunicationRating,
            'designerAverageFlexibilityRating' => $designerAverageFlexibilityRating,
            'designerAverageProfessionalismRating' => $designerAverageProfessionalismRating,
            'designerAverageDesignQualityRating' => $designerAverageDesignQualityRating,
            'designerAveragePriceRating' => $designerAveragePriceRating,
            'renderRatingIcons' => [$this, 'renderRatingIcons'],
            'recentReviews' => $recentReviews,
            'reviewCount' => $reviewCount,
            'portfolioImages' => $portfolioImages,
            'portfolioLink' => $portfolioLink,
            'platforms' => $service->platforms,
            'services' => $services,
        ];
    }

    public function submitReview(Request $request, $serviceName, $name)
    {
        $formattedName = Str::title(str_replace('-', ' ', $name));
        $singleService = OtherService::where('name', $formattedName)->first();

        $serviceId = $singleService->id;
        $serviceType = $singleService->services;

        $otherServicesListId = $singleService->other_service_id;

        // Process rating arrays to get final values
        $processRating = function ($ratingArray) {
            return count($ratingArray); // Count checked boxes for final rating
        };

        switch ($serviceType) {
            case 'Artist':
                $bandReview = new BandReviews();
                $bandReview->other_services_id = $serviceId;
                $bandReview->other_services_list_id = $otherServicesListId;
                $bandReview->music_rating = $request->music_rating;
                $bandReview->promotion_rating = $request->promotion_rating;
                $bandReview->gig_quality_rating = $request->gig_quality_rating;
                $bandReview->save();
                break;
            case 'Photography':
                $photographyReview = new PhotographyReviews();
                $photographyReview->other_services_id = $serviceId;
                $photographyReview->other_services_list_id = $otherServicesListId;
                $photographyReview->photo_quality_rating = $request->photo_quality_rating;
                $photographyReview->save();
                break;
            case 'Videography':
                $videographyReview = new VideographyReviews();
                $videographyReview->other_services_id = $serviceId;
                $videographyReview->other_services_list_id = $otherServicesListId;
                $videographyReview->video_quality_rating = $request->video_quality_rating;
                $videographyReview->save();
                break;
            case 'Designer':
                $designerReview = new DesignerReviews();
                $designerReview->other_services_id = $serviceId;
                $designerReview->other_services_list_id = $otherServicesListId;
                $designerReview->reviewer_ip = $request->reviewer_ip;
                $designerReview->communication_rating = $processRating($request->input('communication-rating', []));
                $designerReview->flexibility_rating = $processRating($request->input('flexibility-rating', []));
                $designerReview->professionalism_rating = $processRating($request->input('professionalism-rating', []));
                $designerReview->design_quality_rating = $processRating($request->input('designer_quality-rating', []));
                $designerReview->price_rating = $processRating($request->input('price-rating', []));
                $designerReview->author = $request->review_author;
                $designerReview->review = $request->review_message;
                $designerReview->save();
                break;
        }

        return response()->json(['success' => true, 'message' => 'Review submitted successfully']);
    }
}