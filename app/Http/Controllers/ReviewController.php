<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function getReviews($dashboardType, $filter = 'all')
    {
        return view('admin.dashboards.show-reviews', [
            'userId' => Auth::id(),
            'dashboardType' => $dashboardType,
            'modules' => collect(session('modules', [])),
            'filter' => $filter,
        ]);
    }

    public function fetchReviews($dashboardType, $filter = 'all')
    {
        try {
            $user = Auth::user()->load(['promoters', 'venues', 'otherService']);

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $query = $this->reviewService->getReviewQuery($dashboardType, $user);

            if (!$query) {
                return response()->json(['error' => 'Invalid dashboard type'], 400);
            }

            // Apply filters and select fields
            $query->when($filter === 'pending', fn($q) => $q->where('review_approved', 0))
                ->select([
                    'id',
                    'author',
                    'review',
                    'review_approved',
                    'display',
                    'created_at'
                ]);

            // Add rating fields to select
            $ratingFields = $this->reviewService->getRatingFields($dashboardType);
            foreach ($ratingFields as $rating) {
                $query->addSelect($rating);
            }

            $reviews = $query->orderBy('created_at', 'DESC')->paginate(6);

            return response()->json([
                'reviews' => $reviews->items(),
                'hasMorePages' => $reviews->hasMorePages(),
                'totalItems' => $reviews->total(),
                'currentPage' => $reviews->currentPage(),
                'itemsPerPage' => 6
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching reviews:', [
                'error' => $e->getMessage(),
                'dashboardType' => $dashboardType,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch reviews',
                'message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function approveReview($dashboardType, $reviewId)
    {
        return $this->updateReviewStatus($dashboardType, $reviewId, 'review_approved', true);
    }

    public function unapproveReview($dashboardType, $reviewId)
    {
        return $this->updateReviewStatus($dashboardType, $reviewId, 'review_approved', false);
    }

    public function displayReview($dashboardType, $reviewId)
    {
        return $this->updateReviewStatus($dashboardType, $reviewId, 'display');
    }

    public function deleteReview($dashboardType, $reviewId)
    {
        try {
            $user = Auth::user()->load(['promoters', 'venues', 'otherService']);
            $query = $this->reviewService->getReviewQuery($dashboardType, $user);
            $review = $query->findOrFail($reviewId);

            // Soft delete the review
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error("Error deleting review:", [
                'error' => $e->getMessage(),
                'dashboardType' => $dashboardType
            ]);
            return response()->json(['error' => "Failed to delete review"], 500);
        }
    }

    protected function updateReviewStatus($dashboardType, $reviewId, $field, $value = null)
    {
        try {
            $user = Auth::user()->load(['promoters', 'venues', 'otherService']);
            $query = $this->reviewService->getReviewQuery($dashboardType, $user);
            $review = $query->findOrFail($reviewId);

            // If value is provided, use it; otherwise toggle the current value
            $review->$field = $value ?? !$review->$field;

            // If approving, also set display to true; if unapproving, set display to false
            if ($field === 'review_approved') {
                $review->display = (bool)$review->$field;
            }

            $review->save();

            $status = $review->$field ? 'approved' : 'unapproved';

            return response()->json([
                'success' => true,
                'message' => "Review {$status} successfully",
                'review' => [
                    'id' => $review->id,
                    'review_approved' => $review->review_approved,
                    'display' => $review->display
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("Error updating review {$field}:", [
                'error' => $e->getMessage(),
                'dashboardType' => $dashboardType
            ]);
            return response()->json(['error' => "Failed to update review status"], 500);
        }
    }
}