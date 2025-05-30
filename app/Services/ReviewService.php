<?php

namespace App\Services;

class ReviewService
{
    private $reviewTypes = [
        'promoter' => [
            'model' => \App\Models\PromoterReview::class,
            'relation' => 'promoter',
            'ratings' => ['communication_rating', 'rop_rating', 'promotion_rating', 'quality_rating']
        ],
        'venue' => [
            'model' => \App\Models\VenueReview::class,
            'relation' => 'venue',
            'ratings' => ['communication_rating', 'rop_rating', 'promotion_rating', 'quality_rating']
        ],
        'artist' => [
            'model' => \App\Models\BandReviews::class,
            'relation' => 'otherService',
            'service_type' => 'Artist',
            'service_id' => 4,
            'ratings' => ['communication_rating', 'music_rating', 'promotion_rating', 'gig_quality_rating']
        ],
        'designer' => [
            'model' => \App\Models\DesignerReviews::class,
            'relation' => 'otherService',
            'service_type' => 'Designer',
            'ratings' => ['communication_rating', 'flexibility_rating', 'professionalism_rating', 'design_quality_rating']
        ],
        'photographer' => [
            'model' => \App\Models\PhotographerReviews::class,
            'relation' => 'otherService',
            'service_type' => 'Photography',
            'service_id' => 1,
            'ratings' => ['communication_rating', 'flexibility_rating', 'professionalism_rating', 'photo_quality_rating']
        ],
        'videographer' => [
            'model' => \App\Models\VideographyReviews::class,
            'relation' => 'otherService',
            'service_type' => 'Videography',
            'service_id' => 2,
            'ratings' => ['communication_rating', 'flexibility_rating', 'professionalism_rating', 'video_quality_rating']
        ]
    ];

    public function getReviewQuery($dashboardType, $user)
    {
        $config = $this->reviewTypes[strtolower($dashboardType)] ?? null;
        if (!$config) {
            throw new \Exception('Invalid dashboard type: ' . $dashboardType);
        }

        $modelClass = $config['model'];

        try {
            // Build the base query
            $query = $modelClass::query();

            if (isset($config['service_type'])) {
                // Handle other services (artist, designer, photographer, videographer)
                $otherServices = $user->otherService()
                    ->where('services', $config['service_type'])
                    ->get();

                if ($otherServices->isEmpty()) {
                    return $this->emptyQuery($modelClass);
                }

                $query->whereIn('other_services_id', $otherServices->pluck('id'));

                if (isset($config['service_id'])) {
                    $query->where('other_services_list_id', $config['service_id']);
                }
            } else {
                // Handle direct relations (promoter, venue)
                $relationName = $config['relation'] . 's';
                $relatedModels = $user->$relationName;

                if ($relatedModels->isEmpty()) {
                    return $this->emptyQuery($modelClass);
                }

                $query->whereIn("{$dashboardType}_id", $relatedModels->pluck('id'));
            }

            // Add eager loading
            $query->with($config['relation']);

            return $query;
        } catch (\Exception $e) {
            \Log::error('Error in getReviewQuery:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'dashboard_type' => $dashboardType,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Returns an empty query builder instance
     */
    private function emptyQuery($modelClass)
    {
        return $modelClass::whereRaw('1 = 0');
    }

    public function getRatingFields($dashboardType)
    {
        return $this->reviewTypes[strtolower($dashboardType)]['ratings'] ?? [];
    }
}
