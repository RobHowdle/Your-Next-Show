<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtherServicesReview extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'other_services_reviews';

    protected $fillable = [
        'other_services_id',
        'other_services_list_id',
        'communication_rating',
        'price_rating',
        'promotion_rating',
        'quality_rating',
        'review',
        'author',
        'reviewer_ip',
        'review_approved',
        'display',
    ];

    public function otherService()
    {
        return $this->belongsTo(OtherService::class, 'other_services_id');
    }

    public function otherServiceList()
    {
        return $this->belongsTo(OtherServiceList::class, 'other_services_list_id');
    }

    public static function getRecentReviewsForOtherService($otherServiceId)
    {
        return self::where('other_services_id', $otherServiceId)
            ->whereNull('deleted_at')
            ->where('display', 1)
            ->where('review_approved', 1)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    public static function getReviewCount($otherServiceId)
    {
        return self::where('other_services_id', $otherServiceId)
            ->whereNull('deleted_at')
            ->where('display', 1)
            ->where('review_approved', 1)
            ->orderBy('created_at', 'desc')
            ->count();
    }

    public static function calculateOverallScore($otherServiceId)
    {
        $reviews = OtherServicesReview::where('other_services_id', $otherServiceId)->where('review_approved', 1)->get();

        // Calculate the total and count of each rating
        $totalCommunication = 0;
        $totalROP = 0;
        $totalPromotion = 0;
        $totalQuality = 0;
        $totalFlexibility = 0;
        $totalProfe
        $totalReviews = $reviews->count();

        foreach ($reviews as $review) {
            $totalCommunication += intval($review->communication_rating);
            $totalROP += intval($review->rop_rating);
            $totalPromotion += intval($review->promotion_rating);
            $totalQuality += intval($review->quality_rating);
        }

        // Calculate the average for each rating
        $averageCommunication = $totalReviews > 0 ? $totalCommunication / $totalReviews : 0;
        $averageROP = $totalReviews > 0 ? $totalROP / $totalReviews : 0;
        $averagePromotion = $totalReviews > 0 ? $totalPromotion / $totalReviews : 0;
        $averageQuality = $totalReviews > 0 ? $totalQuality / $totalReviews : 0;

        // Calculate the overall score
        $overallScore = $totalReviews > 0 ? ($averageCommunication + $averageROP + $averagePromotion + $averageQuality) / 4 : 0;

        // Round it 2dp
        $overallScore = round($overallScore, 2);

        return $overallScore;
    }

    public static function calculateAverageScore($otherServiceId, $field)
    {
        $reviews = OtherServicesReview::where('other_services_id', $otherServiceId)
            ->where('review_approved', 1)
            ->get();

        // Calculate the total rating for the specified field
        $totalRating = 0;
        $totalReviews = $reviews->count();

        foreach ($reviews as $review) {
            $totalRating += intval($review->{$field});
        }

        // Calculate the average rating for the specified field
        $averageRating = $totalReviews > 0 ? $totalRating / $totalReviews : 0;
        // $averageRating = $totalReviews > 0 ? 

        // Round it to 2 decimal places
        $averageRating = round($averageRating, 2);

        return $averageRating;
    }
}
