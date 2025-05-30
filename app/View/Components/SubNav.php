<?php

namespace App\View\Components;

use Closure;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Finance;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Models\VenueReview;

class SubNav extends Component
{
    // Global
    public $userType;
    public $role;
    public $promoter;
    public $overallScore;
    public $user;

    // Promoter
    public $promoterId;
    public $eventsCountPromoterYtd;
    public $overallRatingPromoter;
    public $totalProfitsPromoterYtd;

    // Bands
    public $bandId;
    public $gigsCountBandYtd;
    public $overallRatingBand;
    public $totalProfitsBandYtd;

    // Designer
    public $designerId;
    public $jobsCountDesignerYTD;
    public $overallRatingDesigner;
    public $totalProfitsDesignerYtd;

    // Venue
    public $venueId;
    public $eventsCountVenueYtd;
    public $overallRatingVenue;
    public $totalProfitsVenueYtd;

    // Photographer
    public $photographerId;
    public $jobsCountPhotographerYtd;
    public $totalProfitsPhotographerYtd;
    public $overallPhotographerRating;

    // Videographer
    public $videographerId;
    public $jobsCountVideographerYtd;
    public $overallVideographerRating;
    public $totalProfitsVideographerYtd;

    // Standard
    public $standardUserId;
    public $eventsCountStandardYtd;

    /**
     * Helper function to render rating icons
     */
    public function renderRatingIcons($rating)
    {
        $fullIcon = asset('storage/images/system/ratings/full.png');
        $emptyIcon = asset('storage/images/system/ratings/empty.png');
        $hotIcon = asset('storage/images/system/ratings/hot.png');

        $rating = floatval($rating);
        $output = '';

        // If rating is 0 or null, show all empty stars
        if ($rating <= 0) {
            for ($i = 0; $i < 5; $i++) {
                $output .= sprintf(
                    '<img src="%s" alt="Empty Rating" class="inline-block h-4 w-4" />',
                    $emptyIcon
                );
            }
            return $output;
        }

        // If rating is 5, show all hot stars
        if ($rating >= 5) {
            for ($i = 0; $i < 5; $i++) {
                $output .= sprintf(
                    '<img src="%s" alt="Hot Rating" class="inline-block h-4 w-4" />',
                    $hotIcon
                );
            }
            return $output;
        }

        $fullStars = floor($rating);
        $partialStar = $rating - $fullStars;
        $emptyStars = 5 - ceil($rating);

        // Add full stars
        for ($i = 0; $i < $fullStars; $i++) {
            $output .= sprintf(
                '<img src="%s" alt="Full Star" class="inline-block h-4 w-4" />',
                $fullIcon
            );
        }

        // Add partial star if needed
        if ($partialStar > 0) {
            $output .= sprintf(
                '<img src="%s" alt="Partial Star" class="inline-block h-4 w-4" style="clip-path: inset(0 %d%% 0 0);" />',
                $fullIcon,
                (1 - $partialStar) * 100
            );
        }

        // Add empty stars
        for ($i = 0; $i < $emptyStars; $i++) {
            $output .= sprintf(
                '<img src="%s" alt="Empty Star" class="inline-block h-4 w-4" />',
                $emptyIcon
            );
        }

        return $output;
    }

    /**
     * Create a new component instance.
     */
    public function __construct(int $userId)
    {
        $this->loadUserData($userId);
    }

    private function loadUserData(int $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $this->role = $user->getRoleNames()->first();
        $this->userType = $this->role ?? 'guest';

        switch ($this->userType) {
            case 'promoter':
                $this->loadPromoterData($user);
                break;

            case 'artist':
                $this->loadBandData($user);
                break;

            case 'venue':
                $this->loadVenueData($user);
                break;

            case 'designer':
                $this->loadDesignerData($user);
                break;

            case 'photographer':
                $this->loadPhotographerData($user);
                break;

            case 'videographer':
                $this->loadVideographerData($user);
                break;

            case 'standard':
                $this->loadStandardUserData($user);
                break;
            default:
                break;
        }
    }

    private function loadPromoterData($user)
    {
        $promoters = $user->promoters()->get();
        if ($promoters->isNotEmpty()) {
            $promoter = $promoters->first();
            $this->promoterId = $promoter->id;
            $this->eventsCountPromoterYtd = $this->calculateEventsCountPromoterYtd($promoter);
            $this->totalProfitsPromoterYtd = $this->calculateTotalProfitsPromoterYtd($promoter);
            $this->overallRatingPromoter = $this->renderRatingIcons($this->promoterId);
        }
    }

    private function loadBandData($user)
    {
        $bands = $user->otherService("Artist")->get();
        if ($bands->isNotEmpty()) {
            $band = $bands->first();
            $this->bandId = $band->id;
            $this->gigsCountBandYtd = $this->calculateGigsCountBandYtd($band->id);

            // Calculate average across multiple rating fields
            $reviews = \App\Models\BandReviews::where('other_services_id', $band->id)
                ->where('other_services_list_id', 4)
                ->get();

            $fields = ['communication_rating', 'music_rating', 'promotion_rating', 'gig_quality_rating'];
            $total = 0;
            $count = 0;

            foreach ($reviews as $review) {
                $sum = 0;
                $fieldCount = 0;
                foreach ($fields as $field) {
                    if (isset($review->$field)) {
                        $sum += $review->$field;
                        $fieldCount++;
                    }
                }
                if ($fieldCount > 0) {
                    $total += $sum / $fieldCount;
                    $count++;
                }
            }

            $averageRating = $count > 0 ? $total / $count : 0;

            $this->overallRatingBand = $this->renderRatingIcons($averageRating);
            $this->totalProfitsBandYtd = $this->calculateTotalProfitsBandYtd($band->id);
        }
    }

    private function loadDesignerData($user)
    {
        $designers = $user->otherService('Designer')->get();
        if ($designers->isNotEmpty()) {
            $designer = $designers->first();
            $this->designerId = $designer->id;
            $this->jobsCountDesignerYTD = $this->calculateJobsDesignerYtd($designer);
            $this->totalProfitsDesignerYtd = $this->calculateTotalProfitsDesignerYtd($designer);
            $this->overallRatingDesigner = $this->renderRatingIcons($this->designerId);
        }

        return null;
    }

    private function loadVideographerData($user)
    {
        $videographers = $user->otherService('Videography')->get();
        if ($videographers->isNotEmpty()) {
            $videographer = $videographers->first();
            $this->videographerId = $videographer->id;
            $this->jobsCountVideographerYtd = $this->calculateJobsVideographerYtd($videographer);
            $this->totalProfitsVideographerYtd = $this->calculateTotalProfitsVideographerYtd($videographer);
            $this->overallVideographerRating = $this->renderRatingIcons($this->videographerId);
        }
    }

    private function loadVenueData($user)
    {
        $venues = $user->venues()->get();
        if ($venues->isNotEmpty()) {
            $venue = $venues->first();
            $this->venueId = $venue->id;
            $this->eventsCountVenueYtd = $this->calculateEventsCountVenueYtd($venue);
            $this->totalProfitsVenueYtd = $this->calculateTotalProfitsVenueYtd($venue);
            $overallScore = VenueReview::calculateOverallScore($venue->id);
            $this->overallRatingVenue = $this->renderRatingIcons($overallScore);
        }
    }

    private function loadPhotographerData($user)
    {
        $photographers = $user->otherService("Photography")->get();
        if ($photographers->isNotEmpty()) {
            $photographer = $photographers->first();
            $this->photographerId = $photographer->id;
            $this->jobsCountPhotographerYtd = $this->calculateJobsCountPhotographerYtd($photographer);
            $this->totalProfitsPhotographerYtd = $this->calculateTotalProfitsPhotographerYtd($photographer);
            $this->overallPhotographerRating = $this->renderRatingIcons($this->photographerId);
        }
    }

    private function loadStandardUserData($user)
    {
        $standardUsers = $user->standardUser()->get();
        if ($standardUsers) {
            $standardUser = $standardUsers->first();
            $this->eventsCountStandardYtd = $this->calculateStandardUserEventsCountYtd($standardUser);
        }
    }

    // Promoter Calculations
    public function calculateTotalProfitsPromoterYtd($promoter)
    {
        if ($promoter) {
            $promoterCompany = $promoter->first();

            if ($promoterCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                // Query the finances table for the current year's profits
                $totalProfitsYTD = Finance::where('serviceable_id', $promoterCompany->id)
                    ->where('serviceable_type', 'App\Models\Promoter')
                    ->whereBetween('date_to', [$startOfYear, $endOfYear])
                    ->sum('total_profit');

                return $totalProfitsYTD;
            }
        }

        return 0;
    }

    public function calculateEventsCountPromoterYtd($promoter)
    {
        if ($promoter) {
            $promoterCompany = $promoter->first();

            if ($promoterCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                $eventsCountYTD = DB::table('event_promoter')
                    ->join('events', 'event_promoter.event_id', '=', 'events.id')
                    ->where('promoter_id', $promoterCompany->id)
                    ->whereBetween('events.event_date', [$startOfYear, $endOfYear])
                    ->count();

                return $eventsCountYTD;
            }
        }

        return 0;
    }

    // Photograher Calculations
    public function calculateJobsCountPhotographerYtd($photographer)
    {
        if ($photographer) {
            $photographerCompany = $photographer->first();

            if ($photographerCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                $jobsCountYTD = DB::table('job_service')
                    ->join('module_jobs', 'job_service.job_id', '=', 'module_jobs.id')
                    ->where('serviceable_id', $photographerCompany->id)
                    ->where('serviceable_type', 'App\Models\OtherService')
                    ->whereBetween('module_jobs.job_start_date', [$startOfYear, $endOfYear])
                    ->count();

                return $jobsCountYTD;
            }
        }

        return 0;
    }

    public function calculateTotalProfitsPhotographerYtd($photographer)
    {
        if ($photographer) {
            $photographerCompany = $photographer->first();

            if ($photographerCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                // Query the finances table for the current year's profits
                $totalProfitsYTD = Finance::where('serviceable_id', $photographerCompany->id)
                    ->where('serviceable_type', 'App\Models\OtherService')
                    ->whereBetween('date_to', [$startOfYear, $endOfYear])
                    ->sum('total_profit');

                return $totalProfitsYTD;
            }
        }

        // Return 0 if no promoter company or no profits found
        return 0;
    }

    // Band Calculations
    public function calculateGigsCountBandYtd($band)
    {
        if ($band) {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $gigsCountBandYtd = DB::table('event_band')
                ->join('events', 'event_band.event_id', '=', 'events.id')
                ->where('event_band.band_id', $band)
                ->whereBetween('events.event_date', [$startOfYear, $endOfYear])
                ->count();

            return $gigsCountBandYtd;
        }

        return 0;
    }

    public function calculateTotalProfitsBandYtd($band)
    {
        if ($band) {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $totalProfitsYTD = Finance::where('serviceable_id', $band)
                ->where('serviceable_type', 'App\Models\OtherService')
                ->whereBetween('date_to', [$startOfYear, $endOfYear])
                ->sum('total_profit');

            return $totalProfitsYTD;
        }

        return 0;
    }

    // Venue Calculations
    public function calculateTotalProfitsVenueYtd($venue)
    {
        if ($venue) {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $totalProfitsYTD = Finance::where('serviceable_id', $venue->id)
                ->where('serviceable_type', 'App\Models\Venue')
                ->whereBetween('date_to', [$startOfYear, $endOfYear])
                ->sum('total_profit');

            return $totalProfitsYTD;
        }

        return 0;
    }

    public function calculateEventsCountVenueYtd($venue)
    {
        if ($venue) {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $eventsCountYTD = DB::table('event_venue')
                ->join('events', 'event_venue.event_id', '=', 'events.id')
                ->where('venue_id', $venue->id)
                ->whereBetween('events.event_date', [$startOfYear, $endOfYear])
                ->count();

            return $eventsCountYTD;
        }

        return 0;
    }

    // Standard User Calculations
    public function calculateStandardUserEventsCountYtd($standardUser)
    {
        if ($standardUser) {
            $standard = $standardUser->first();

            if ($standard) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                // Query the finances table for the current year's profits
                $eventsCountYTD = DB::table('event_promoter')
                    ->join('events', 'event_promoter.event_id', '=', 'events.id')
                    ->where('promoter_id', $standard->id)
                    ->whereBetween('events.event_date', [$startOfYear, $endOfYear])
                    ->count();

                return $eventsCountYTD;
            }
        }

        return 0;
    }

    // Designer Calculations
    public function calculateTotalProfitsDesignerYtd($designer)
    {
        if ($designer) {
            $designerCompany = $designer->first();

            if ($designerCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                // Query the finances table for the current year's profits
                $totalProfitsYTD = Finance::where('serviceable_id', $designerCompany->id)
                    ->where('serviceable_type', 'App\Models\OtherService')
                    ->whereBetween('date_to', [$startOfYear, $endOfYear])
                    ->sum('total_profit');

                return $totalProfitsYTD;
            }
        }

        return 0;
    }

    public function calculateJobsDesignerYtd($designer)
    {
        if ($designer) {
            $designerCompany = $designer->first();

            if ($designerCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                $jobsCountDesignerYTD = DB::table('job_service')
                    ->join('module_jobs', 'job_service.job_id', '=', 'module_jobs.id')
                    ->where('serviceable_id', $designerCompany->id)
                    ->where('serviceable_type', 'App\Models\OtherService')
                    ->whereBetween('module_jobs.job_start_date', [$startOfYear, $endOfYear])
                    ->count();

                return $jobsCountDesignerYTD;
            }
        }

        return 0;
    }

    // Videographer Calculations
    public function calculateTotalProfitsVideographerYtd($videographer)
    {
        if ($videographer) {
            $videographerCompany = $videographer->first();

            if ($videographerCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                // Query the finances table for the current year's profits
                $totalProfitsYTD = Finance::where('serviceable_id', $videographerCompany->id)
                    ->where('serviceable_type', 'App\Models\OtherService')
                    ->whereBetween('date_to', [$startOfYear, $endOfYear])
                    ->sum('total_profit');

                return $totalProfitsYTD;
            }
        }

        return 0;
    }

    public function calculateJobsVideographerYtd($videographer)
    {
        if ($videographer) {
            $videographerCompany = $videographer->first();

            if ($videographerCompany) {
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear = Carbon::now()->endOfYear();

                $jobsCountDesignerYTD = DB::table('job_service')
                    ->join('module_jobs', 'job_service.job_id', '=', 'module_jobs.id')
                    ->where('serviceable_id', $videographerCompany->id)
                    ->where('serviceable_type', 'App\Models\OtherService')
                    ->whereBetween('module_jobs.job_start_date', [$startOfYear, $endOfYear])
                    ->count();

                return $jobsCountDesignerYTD;
            }
        }

        return 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sub-nav', [
            'userType' => $this->userType,
            'overallRatingPromoter' => $this->overallRatingPromoter,
            'overallRatingBand' => $this->overallRatingBand,
            'gigsCountBandYtd' => $this->gigsCountBandYtd,
            'totalProfitsBandYtd' => $this->totalProfitsBandYtd,
            'eventsCountPromoterYtd' => $this->userType === 'promoter' ? $this->eventsCountPromoterYtd : null,
            'totalProfitsPromoterYtd' => $this->totalProfitsPromoterYtd,
            'totalProfitsVenueYtd' => $this->totalProfitsVenueYtd,
            'eventsCountVenueYtd' => $this->eventsCountVenueYtd,
            'overallRatingVenue' => $this->overallRatingVenue,
            'jobsCountPhotographerYtd' => $this->userType === 'photographer' ? $this->jobsCountPhotographerYtd : null,
            'totalProfitsPhotographerYtd' => $this->totalProfitsPhotographerYtd,
            'overallPhotographerRating' => $this->overallPhotographerRating,
            'eventsCountStandardYtd' => $this->eventsCountStandardYtd,
            'totalProfitsDesignerYtd' => $this->totalProfitsDesignerYtd,
            'jobsCountDesignerYTD' => $this->jobsCountDesignerYTD,
        ]);
    }
}
