<?php

namespace App\Models;

use App\Models\OtherService;
use App\Models\OtherServiceList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BandReviews extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'band_reviews';

    protected $fillable = [
        'other_services_id',
        'other_services_list_id',
        'communication_rating',
        'music_rating',
        'promotion_rating',
        'gig_quality_rating',
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

    public static function getRecentReviews($otherServiceId)
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
        $reviews = self::where('other_services_id', $otherServiceId)
            ->where('review_approved', 1)
            ->get();

        $totalReviews = $reviews->count();

        if ($totalReviews === 0) {
            return 0;
        }

        $totals = $reviews->reduce(function ($carry, $review) {
            return [
                'communication' => $carry['communication'] + (float)$review->communication_rating,
                'music' => $carry['music'] + (float)$review->music_rating,
                'promotion' => $carry['promotion'] + (float)$review->promotion_rating,
                'quality' => $carry['quality'] + (float)$review->gig_quality_rating
            ];
        }, [
            'communication' => 0,
            'music' => 0,
            'promotion' => 0,
            'quality' => 0
        ]);

        $averages = array_map(function ($total) use ($totalReviews) {
            return $total / $totalReviews;
        }, $totals);

        $overallScore = array_sum($averages) / count($averages);

        return round($overallScore, 2);
    }

    public static function calculateAverageScore($otherServiceId, $field)
    {
        $reviews = self::where('other_services_id', $otherServiceId)
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