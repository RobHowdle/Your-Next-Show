<?php

namespace App\Http\Controllers;

use App\Models\Promoter;
use App\Models\BandReviews;
use App\Models\VenueReview;
use Illuminate\Http\Request;
use App\Models\PromoterReview;
use App\Models\DesignerReviews;
use App\Models\OtherServicesReview;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function getReviews($dashboardType, $filter = 'all')
    {
        $modules = collect(session('modules', []));

        switch ($filter) {
            case 'pending':
                $filter = 'pending';
                break;
            case 'all':
                $filter = 'all';
                break;
        }

        return view('admin.dashboards.show-reviews', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'filter' => $filter,
        ]);
    }

    public function fetchReviews($dashboardType, $filter = 'all')
    {
        $user = Auth::user()->load(['promoters', 'venues', 'otherService']);
        $reviews = collect();

        try {
            switch (strtolower($dashboardType)) {
                case 'promoter':
                    $query = PromoterReview::where('promoter_id', $user->promoters->pluck('id'));
                    break;

                case 'artist':
                    $query = OtherServicesReview::where('other_services_id', $user->otherService("Artist")->pluck('other_services.id'))
                        ->where('other_services_list_id', 4);
                    break;

                case 'designer':
                    $serviceIds = $user->otherService("Designer")
                        ->pluck('other_services.id')
                        ->unique()
                        ->values()
                        ->toArray();

                    $query = DesignerReviews::whereIn('other_services_id', $serviceIds);
                    break;

                case 'photographer':
                    $query = OtherServicesReview::where('other_services_id', $user->otherService("Photography")->pluck('other_services.id'))
                        ->where('other_services_list_id', 1);
                    break;

                case 'videographer':
                    $query = OtherServicesReview::where('other_services_id', $user->otherService("Videography")->pluck('other_services.id'))
                        ->where('other_services_list_id', 2);
                    break;

                case 'venue':
                    $query = VenueReview::where('venue_id', $user->venues->pluck('id'));
                    break;

                default:
                    return response()->json(['error' => 'Invalid service type'], 400);
            }

            if ($filter === 'pending') {
                $query->where('review_approved', 0);
            }

            $reviews = $query->orderBy('created_at', 'DESC')->get();

            return response()->json(['reviews' => $reviews]);
        } catch (\Exception $e) {
            \Log::error('Error fetching reviews:', [
                'error' => $e->getMessage(),
                'service_type' => $dashboardType
            ]);
            return response()->json(['error' => 'Failed to fetch reviews'], 500);
        }
    }

    public function approveReview($dashboardType, $reviewId)
    {
        try {
            switch ($dashboardType) {
                case 'promoter':
                    $review = PromoterReview::findOrFail($reviewId);
                    break;
                case 'artist':
                    $review = BandReviews::findOrFail($reviewId);
                    break;
                case 'designer':
                    $review = DesignerReviews::findOrFail($reviewId);
                    break;
                case 'photographer':
                    $review = OtherServicesReview::findOrFail($reviewId);
                    break;
                case 'videographer':
                    $review = OtherServicesReview::findOrFail($reviewId);
                    break;
                case 'venue':
                    $review = VenueReview::findOrFail($reviewId);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid service type'
                    ]);
            }

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
            }

            $review->review_approved = !$review->review_approved;
            $review->save();

            return response()->json([
                'success' => true,
                'message' => $review->review_approved ? 'Review approved successfully' : 'Review unapproved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving review:', [
                'error' => $e->getMessage(),
                'service_type' => $dashboardType
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to approve review'
            ], 500);
        }
    }

    public function displayReview($dashboardType, $reviewId)
    {
        try {
            switch ($dashboardType) {
                case 'promoter':
                    $review = PromoterReview::findOrFail($reviewId);
                    break;
                case 'artist':
                    $review = BandReviews::findOrFail($reviewId);
                    break;
                case 'designer':
                    $review = DesignerReviews::findOrFail($reviewId);
                    break;
                case 'photographer':
                    $review = OtherServicesReview::findOrFail($reviewId);
                    break;
                case 'videographer':
                    $review = OtherServicesReview::findOrFail($reviewId);
                    break;
                case 'venue':
                    $review = VenueReview::findOrFail($reviewId);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid service type'
                    ]);
            }

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
            }

            $review->display = !$review->display;
            $review->save();

            return response()->json([
                'success' => true,
                'message' => $review->display ? 'Review displayed successfully' : 'Review hidden successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error displaying review:', [
                'error' => $e->getMessage(),
                'service_type' => $dashboardType
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to display review'
            ], 500);
        }
    }

    public function hideReview($dashboardType, $reviewId)
    {
        try {
            switch ($dashboardType) {
                case 'promoter':
                    $review = PromoterReview::findOrFail($reviewId);
                    break;
                case 'artist':
                    $review = BandReviews::findOrFail($reviewId);
                    break;
                case 'designer':
                    $review = DesignerReviews::findOrFail($reviewId);
                    break;
                case 'photographer':
                    $review = OtherServicesReview::findOrFail($reviewId);
                    break;
                case 'videographer':
                    $review = OtherServicesReview::findOrFail($reviewId);
                    break;
                case 'venue':
                    $review = VenueReview::findOrFail($reviewId);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid service type'
                    ]);
            }

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ]);
            }

            $review->display = !$review->display;
            $review->save();

            return response()->json([
                'success' => true,
                'message' => $review->display ? 'Review hidden successfully' : 'Review hidden successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error hiding review:', [
                'error' => $e->getMessage(),
                'service_type' => $dashboardType
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to hide review'
            ], 500);
        }
    }

    public function deleteReview($dashboardType, $reviewId)
    {
        $review = collect();

        if ($dashboardType === 'promoter') {
            $review = PromoterReview::findOrFail($reviewId);

            if ($review) {
                $review->delete();

                return response()->json(['success' => true, 'message' => 'Review deleted successfully.']);
            }
        } elseif ($dashboardType === 'artist') {
            $review = OtherServicesReview::findOrFail($reviewId);

            if ($review) {
                $review->delete();

                return response()->json(['success' => true, 'message' => 'Review deleted successfully.']);
            }
        } elseif ($dashboardType === 'designer') {
            $review = OtherServicesReview::findOrFail($reviewId);

            if ($review) {
                $review->delete();

                return response()->json(['success' => true, 'message' => 'Review deleted successfully.']);
            }
        }
        return response()->json(['success' => false, 'message' => 'Review not found']);
    }
}