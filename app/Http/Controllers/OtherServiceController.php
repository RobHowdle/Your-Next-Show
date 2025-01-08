<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Promoter;
use App\Models\BandReviews;
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
        $otherServiceIds = OtherServiceList::where('service_name', $serviceName)->pluck('id');
        $searchQuery = $request->input('search_query');

        $singleServices = OtherService::with('otherServiceList')
            ->whereIn('other_service_id', $otherServiceIds)
            ->when($searchQuery, function ($query, $searchQuery) {
                return $query->whereHas('otherServiceList', function ($query) use ($searchQuery) {
                    $query->where('postal_town', 'like', "%$searchQuery%");
                });
            })
            ->paginate(10);

        // Fetch genres for initial page load
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        if ($request->ajax()) {
            return response()->json([
                'singleServices' => $singleServices,
                'view' => view('partials.otherServices-list', compact('singleServices', 'genres'))->render()
            ]);
        }


        $overallReviews = [];
        // Process contact links using SocialLinksHelper
        foreach ($singleServices as $singleOtherService) {
            $singleOtherService->platforms = SocialLinksHelper::processSocialLinks($singleOtherService->contact_link);
            $overallScore = OtherServicesReview::calculateOverallScore($singleOtherService->id);
            $overallReviews[$singleOtherService->id] = $this->renderRatingIcons($overallScore);
        }

        $firstService = $singleServices->first();
        $serviceName = $firstService->services;

        return view('single-service-group', [
            'singleServices' => $singleServices,
            'genres' => $genres,
            'overallReviews' => $overallReviews,
            'serviceName' => $serviceName,
        ]);
    }

    public function show(Request $request, $serviceName, $serviceId)
    {
        $singleService = OtherService::where('id', $serviceId)->first();

        $singleArtistData = [];
        $singlePhotographerData = [];
        $singleVideographerData = [];
        $singleDesignerData = [];

        // Fetch genres for initial page load
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];

        switch ($serviceName) {
            case 'Artist':
                $singleArtistData = $this->getArtistData($singleService);
                break;
            case 'Photography':
                $singlePhotographerData = $this->getPhotographerData($singleService);
                break;
            case 'Videographer':
                $singleVideographerData = $this->getVideographerData($singleService);
                break;
            case 'Designer':
                $singleDesignerData = $this->getDesignerData($singleService);
                break;
        }

        $overallReviews = [];
        $overallScore = OtherServicesReview::calculateOverallScore($singleService->id);
        $overallReviews[$singleService->id] = $this->renderRatingIcons($overallScore);

        return view('single-service', [
            'singleService' => $singleService,
            'genres' => $genres,
            'overallReviews' => $overallReviews,
            'singleArtistData' => $singleArtistData,
            'singlePhotographerData' => $singlePhotographerData,
            'singleVideographerData' => $singleVideographerData,
            'singleDesignerData' => $singleDesignerData,
        ]);
    }

    public function filterCheckboxesSearch(Request $request, $serviceType)
    {
        $serviceTypeId = OtherServiceList::where('service_name', $serviceType)->first()->id;

        $filters = [
            'service_type' => $serviceTypeId, // The column to filter by service type
            'search_fields' => ['postal_town', 'name'], // Fields to search
            'transform' => function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'postal_town' => $item->postal_town,
                    'contact_number' => $item->contact_number,
                    'contact_email' => $item->contact_email,
                    'platforms' => explode(',', $item->contact_link),
                    'average_rating' => \App\Models\OtherServicesReview::calculateOverallScore($item->id),
                    'service_type' => $item->services,
                ];
            },
        ];

        $model = '';
      
        switch ($serviceType) {
            case 'Artist':
                $model = OtherService::class;
                break;
            case 'Photography':
                $model = OtherService::class;
                break;

            case 'Videography':
                $model = OtherService::class;
                break;
            case 'Designer':
                $model = OtherService::class;
                break;
            case 'Venue':
                $model = Venue::class;
                break;
            case 'Promoter':
                $model = Promoter::class;
                break;
        }

        $data = FilterService::filterEntities($request, $model, $filters);

        return response()->json($data);
    }

    /**
     * Get single service specific data
     */
    private function getArtistData(OtherService $singleService)
    {
        $service = $singleService;
        $serviceId = $service->id;

        $members = $service->linkedUsers()->get() ?? [];
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
            'streamUrls' => $streamUrls,
        ];
    }

    private function getPhotographerData(OtherService $singleService)
    {
        $service = $singleService;
        $serviceId = $service->id;

        $description = $service ? $service->description : '';
        $packages = $service ? json_decode($service->packages) : [];
        $portfolioImages = $service ? $service->portfolio_images : [];
        $portfolioLink = $service ? $service->portfolio_link : '';
        $platforms = SocialLinksHelper::processSocialLinks($service->contact_link);
        $service->platforms = $platforms;
        $environmentTypes = $service ? json_decode($service->environment_type, true) : [];
        $types = $environmentTypes ? $environmentTypes['types'] : [];
        $settings = $environmentTypes ? $environmentTypes['settings'] : [];
        $workingTimes = $service ? json_decode($service->working_times, true) : [];

        $overallScore = OtherServicesReview::calculateOverallScore($serviceId);
        $overallReviews[$serviceId] = $this->renderRatingIcons($overallScore);

        $photographerAverageCommunicationRating = PhotographyReviews::calculateAverageScore($serviceId, 'communication_rating');
        $photographerAverageFlexibilityRating = PhotographyReviews::calculateAverageScore($serviceId, 'flexibility_rating');
        $photographerAverageProfessionalismRating = PhotographyReviews::calculateAverageScore($serviceId, 'professionalism_rating');
        $photographerAveragePhotoQualityRating = PhotographyReviews::calculateAverageScore($serviceId, 'photo_quality_rating');
        $photographerAveragePriceRating = PhotographyReviews::calculateAverageScore($serviceId, 'price_rating');
        $reviewCount = PhotographyReviews::getReviewCount($serviceId);

        return [
            'description' => $description,
            'packages' => $packages,
            'portfolioImages' => $portfolioImages,
            'portfolioLink' => $portfolioLink,
            'environmentTypes' => $environmentTypes,
            'types' => $types,
            'settings' => $settings,
            'workingTimes' => $workingTimes,
            'overallScore' => $overallScore,
            'overallReviews' => $overallReviews,
            'photographerAverageCommunicationRating' => $photographerAverageCommunicationRating,
            'photographerAverageFlexibilityRating' => $photographerAverageFlexibilityRating,
            'photographerAverageProfessionalismRating' => $photographerAverageProfessionalismRating,
            'photographerAveragePhotoQualityRating' => $photographerAveragePhotoQualityRating,
            'photographerAveragePriceRating' => $photographerAveragePriceRating,
            'renderRatingIcons' => [$this, 'renderRatingIcons'],
            'reviewCount' => $reviewCount,
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

        return [
            'description' => $description,
            'packages' => $packages,
            'portfolioImages' => $portfolioImages,
            'portfolioLink' => $portfolioLink,
            'environmentTypes' => $environmentTypes,
            'types' => $types,
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
        ];
    }

    private function getDesignerData(OtherService $singleService)
    {
        $service = $singleService;
        $serviceId = $service->id;

        $description = $service ? $service->description : '';
        $packages = json_decode($service->packages);
        $portfolioImages = $service->portfolio_images;
        $portfolioLink = $service->portfolio_link;
        $platforms = SocialLinksHelper::processSocialLinks($service->contact_link);
        $service->platforms = $platforms;

        $overallScore = OtherServicesReview::calculateOverallScore($serviceId);
        $overallReviews[$serviceId] = $this->renderRatingIcons($overallScore);

        $designerAverageCommunicationRating = DesignerReviews::calculateAverageScore($serviceId, 'communication_rating');
        $designerAverageFlexibilityRating = DesignerReviews::calculateAverageScore($serviceId, 'flexibility_rating');
        $designerAverageProfessionalismRating = DesignerReviews::calculateAverageScore($serviceId, 'professionalism_rating');
        $designerAverageDesignQualityRating = DesignerReviews::calculateAverageScore($serviceId, 'design_quality_rating');
        $designerAveragePriceRating = DesignerReviews::calculateAverageScore($serviceId, 'price_rating');
        $reviewCount = DesignerReviews::getReviewCount($serviceId);

        return [
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
            'reviewCount' => $reviewCount,
            'portfolioImages' => $portfolioImages,
            'portfolioLink' => $portfolioLink,
        ];
    }
}