<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\BandReviews;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Models\DesignerReviews;
use App\Models\OtherServiceList;
use App\Helpers\SocialLinksHelper;
use App\Models\PhotographyReviews;
use App\Models\VideographyReviews;
use Illuminate\Support\Facades\DB;
use App\Models\OtherServicesReview;
use App\Models\PhotographerReviews;

class OtherServiceController extends Controller
{
    private function extractGenres($genresJson)
    {
        if (empty($genresJson)) {
            return [];
        }

        $genres = json_decode($genresJson, true);
        if (!$genres) {
            return [];
        }

        $selectedGenres = [];
        foreach ($genres as $genre => $data) {
            if ($data['all'] || !empty($data['subgenres'])) {
                $selectedGenres[] = $genre;
            }
        }

        return array_unique($selectedGenres);
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
                'genres' => $this->extractGenres($service->genre),
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
        $singleServiceId = $singleService->id;

        // Fetch genres for initial page load
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        $serviceData = $this->getServiceSpecificData($singleService);


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

        // Fetch upcoming events for this artist (band)
        // The events table has a JSON column 'band_ids' with band_id and role
        $upcomingEvents = \App\Models\Event::whereJsonContains('band_ids', [['band_id' => (string)$singleServiceId]])
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addMonth())
            ->orderBy('event_date', 'asc')
            ->with('venues') // eager load venues relationship
            ->get();

        // Optionally, you can map the role for this artist in each event
        foreach ($upcomingEvents as $event) {
            $bandIds = json_decode($event->band_ids, true);
            $event->artist_role = null;
            if (is_array($bandIds)) {
                foreach ($bandIds as $band) {
                    if ((int)($band['band_id'] ?? 0) === (int)$singleServiceId) {
                        // Use the 'role' field directly (e.g., 'Headliner', 'Main Support', 'Artist', 'Opener')
                        $event->artist_role = $band['role'] ?? null;
                        break;
                    }
                }
            }
        }

        $singleService->upcomingEvents = $upcomingEvents;

        return view('single-service', [
            'singleService' => $singleService,
            'genres' => $genres,
            'overallReviews' => $overallReviews,
            'reviewCount' => $reviewScore,
            'serviceData' => $serviceData,
            'genreNames' => $serviceData['genreNames'] ?? [],
            'hasMinors' => $serviceData['hasMinors'] ?? false,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    public function filter(Request $request)
    {
        // Log incoming request
        \Log::info('Filter request received:', [
            'service_type' => $request->serviceType,
            'filters' => $request->input('filters')
        ]);

        // Normalize service type
        $serviceType = match ($request->serviceType) {
            'Photographer' => 'Photography',
            'Videographer' => 'Videography',
            'Designer' => 'Designer',
            'Artist' => 'Artist',
            default => $request->serviceType
        };

        \Log::info('Normalized service type:', ['type' => $serviceType]);

        $query = OtherService::query()->where('services', $serviceType);
        $filters = $request->input('filters', []);

        // Apply search filter
        if (!empty($filters['search'])) {
            \Log::info('Applying search filter:', ['search' => $filters['search']]);
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('postal_town', 'like', "%{$filters['search']}%");
            });
        }

        // Apply location filter
        if (!empty($filters['locations'])) {
            \Log::info('Applying location filter:', ['locations' => $filters['locations']]);
            $query->whereIn('postal_town', $filters['locations']);
        }

        // Service-specific filters
        switch ($serviceType) {
            case 'Photography':
                if (!empty($filters['environments'])) {
                    \Log::info('Applying photography environment filters:', [
                        'environments' => $filters['environments']
                    ]);

                    $query->where(function ($q) use ($filters) {
                        foreach ($filters['environments'] as $category => $environments) {
                            if (!empty($environments)) {
                                foreach ($environments as $environment) {
                                    // Modified JSON query to handle array structure
                                    $q->orWhere(function ($subQ) use ($environment) {
                                        $subQ->whereRaw('JSON_CONTAINS(JSON_EXTRACT(environment_type, "$.types"), ?)', ['"' . $environment . '"'])
                                            ->orWhereRaw('JSON_CONTAINS(JSON_EXTRACT(environment_type, "$.settings"), ?)', ['"' . $environment . '"']);
                                    });
                                }
                            }
                        }
                    });
                }
                break;

            case 'Artist':
                if (!empty($filters['genres'])) {
                    \Log::info('Applying artist genre filters:', ['genres' => $filters['genres']]);
                    $query->where(function ($q) use ($filters) {
                        foreach ($filters['genres'] as $genre) {
                            $q->orWhereRaw("JSON_EXTRACT(genre, '$." . $genre . ".all') = true")
                                ->orWhereRaw("JSON_EXTRACT(genre, '$." . $genre . ".subgenres') != '[]'");
                        }
                    });
                }

                if (!empty($filters['bandTypes'])) {
                    \Log::info('Applying band type filters:', ['bandTypes' => $filters['bandTypes']]);
                    $query->where(function ($q) use ($filters) {
                        foreach ($filters['bandTypes'] as $type) {
                            $q->orWhereJsonContains('band_type', $type);
                        }
                    });
                }
                break;
        }

        // Log the final SQL query
        \Log::info('Generated SQL query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Get results and transform
        $services = $query->paginate(10);

        \Log::info('Query results:', [
            'total_results' => $services->total(),
            'current_page' => $services->currentPage(),
            'per_page' => $services->perPage()
        ]);

        // Get results and transform
        $services = $query->paginate(10);
        $transformedServices = $services->through(function ($service) use ($serviceType) {
            $reviewScore = match ($serviceType) {
                'Artist' => BandReviews::calculateOverallScore($service->id),
                'Photography' => PhotographyReviews::calculateOverallScore($service->id),
                'Videography' => VideographyReviews::calculateOverallScore($service->id),
                'Designer' => DesignerReviews::calculateOverallScore($service->id),
                default => 0
            };

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
                'average_rating' => $reviewScore,
                'rating_icons' => $this->renderRatingIcons($reviewScore),
                'service_type' => strtolower($serviceType),
                'genres' => $this->extractGenres($service->genre),
                'environments' => json_decode($service->environment_type ?? '{}', true),
                'band_types' => json_decode($service->band_type ?? '{}', true),
                'styles' => json_decode($service->styles ?? '{}', true),
                'design_types' => json_decode($service->design_type ?? '{}', true)
            ];
        });

        return response()->json([
            'results' => $transformedServices->items(),
            'pagination' => $services->links()->render()
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

    public function submitReview(Request $request, $service, $name)
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

        try {
            switch ($serviceType) {
                case 'Artist':
                    $bandReview = new BandReviews();
                    $bandReview->other_services_id = $serviceId;
                    $bandReview->other_services_list_id = $otherServicesListId;
                    $bandReview->communication_rating = $request->communication_rating;
                    $bandReview->music_rating = $request->music_rating;
                    $bandReview->promotion_rating = $request->promotion_rating;
                    $bandReview->gig_quality_rating = $request->gig_quality_rating;
                    $bandReview->review_approved = 0;
                    $bandReview->review = $request->review_message;
                    $bandReview->author = $request->review_author;
                    $bandReview->display = 0;
                    $bandReview->reviewer_ip = $request->reviewer_ip;
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
                default:
                    return response()->json(['success' => false, 'message' => 'Unknown service type.'], 400);
            }
        } catch (Exception $e) {
            \Log::error('Review submission failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to submit review. Please try again.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Review submitted for review']);
    }

    /**
     * Get service-specific data based on the service type
     */
    private function getServiceSpecificData(OtherService $singleService)
    {
        return match ($singleService->services) {
            'Artist' => array_merge($this->getArtistData($singleService), ['hasMinors' => $this->checkForMinors($singleService)]),
            'Photography' => $this->getPhotographerData($singleService),
            'Videography' => $this->getVideographerData($singleService),
            'Designer' => $this->getDesignerData($singleService),
            default => [],
        };
    }

    // Check if the service has minors
    private function checkForMinors(OtherService $service): bool
    {
        return DB::table('service_user')
            ->join('users', 'users.id', '=', 'service_user.user_id')
            ->where('service_user.serviceable_id', $service->id)
            ->where('service_user.serviceable_type', 'App\Models\OtherService')
            ->where(function ($query) {
                $eighteenYearsAgo = now()->subYears(18);
                $query->whereNotNull('users.date_of_birth')
                    ->where('users.date_of_birth', '>', $eighteenYearsAgo);
            })
            ->exists();
    }
}
