<?php

namespace App\Models;

use App\Models\Traits\HasVerification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class OtherService extends Model
{
    use HasVerification;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'other_services';

    protected $fillable = [
        'name',
        'logo_url',
        'location',
        'postal_town',
        'longitude',
        'latitude',
        'other_service_id',
        'description',
        'packages',
        'environment_type',
        'working_times',
        'members',
        'stream_urls',
        'band_type',
        'genre',
        'styles',
        'print',
        'default_lead_time_value',
        'default_lead_time_unit',
        'contact_name',
        'contact_number',
        'contact_email',
        'contact_link',
        'portfolio_link',
        'portfolio_images',
        'services',
        'is_verified',
        'verified_at',
        'preferred_contact',
    ];

    protected $casts = [
        'packages' => 'array',
        'environment_type' => 'array',
        'working_times' => 'array',
        'members' => 'array',
        'contact_links' => 'array',
        'portfolio_images' => 'array',
    ];

    /**
     * Polymorphic relation to the users.
     */
    public function users()
    {
        return $this->morphToMany(User::class, 'serviceable', 'service_user', 'serviceable_id', 'user_id');
    }

    /**
     * Retrieve all photographers (other services with `other_service_id` as 1).
     */
    public static function photographers()
    {
        return self::where('other_service_id', 1);
    }

    /**
     * Retrieve all videogrphers (other services with `other_service_id` as 2).
     */
    public static function videographers()
    {
        return self::where('other_service_id', 2);
    }

    /**
     * Retrieve all designers (other services with `other_service_id` as 3).
     */
    public static function designers()
    {
        return self::where('other_service_id', 3);
    }


    /**
     * Retrieve all bands (other services with `other_service_id` as 4).
     */
    public static function bands()
    {
        return self::where('other_service_id', 4);
    }
    /**
     * Belongs to OtherServiceList relation.
     */
    public function otherServiceList()
    {
        return $this->belongsTo(OtherServiceList::class, 'other_service_id');
    }

    /**
     * MorphMany relation to Todo items.
     */
    public function todos()
    {
        return $this->morphMany(Todo::class, 'serviceable');
    }

    /**
     * BelongsToMany relation to Events for band events.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_band', 'band_id', 'event_id');
    }

    public function performedAtVenues()
    {
        return $this->belongsToMany(Venue::class,  'event_bands')
            ->join('events', 'event_bands.event_id', '=', 'events.id')
            ->join('event_venue', 'events.id', '=', 'event_venue.event_id')
            ->where('other_services.other_service_id', 4);
    }


    /**
     * Get the highest rated service of a specific type in a location.
     */
    public static function getHighestRatedService($serviceType, $location)
    {
        return self::with('otherServiceList') // Eager load related services
            ->whereHas('otherServiceList', function ($query) use ($serviceType) {
                $query->where('service_name', $serviceType);
            })
            ->where('postal_town', $location)
            ->get()
            ->map(function ($service) {
                $service->overall_score = OtherServicesReview::calculateOverallScore($service->id);
                return $service;
            })
            ->sortByDesc('overall_score')
            ->first();
    }

    /**
     * Retrieve all bands associated with this service.
     */
    public function getAllBands()
    {
        return self::bands()->get(); // Get all band services
    }

    public function getAllPhotographers()
    {
        return self::photographers()->get(); // Get all photographer services
    }

    public function linkedUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'serviceable', 'service_user', 'serviceable_id', 'user_id')
            ->withPivot('created_at', 'updated_at', 'role')
            ->whereNull('service_user.deleted_at');
    }

    public function jobs()
    {
        return $this->morphToMany(Job::class, 'serviceable', 'job_service', 'serviceable_id', 'job_id')
            ->wherePivot('serviceable_type', '=', Job::class);
    }

    public function apiKeys()
    {
        return $this->morphMany(ApiKey::class, 'serviceable');
    }

    public function reviews($dashboardType)
    {
        switch ($dashboardType) {
            case 'artist':
                return $this->hasMany(BandReviews::class, 'other_services_id');
            case 'designer':
                return $this->hasMany(DesignerReviews::class, 'other_services_id');
            case 'photographer':
                return $this->hasMany(PhotographerReviews::class, 'other_services_id');
            case 'videographer':
                return $this->hasMany(VideographyReviews::class, 'other_services_id');
            default:
                return null;
        }
    }

    public function opportunities()
    {
        return $this->morphMany(Opportunity::class, 'serviceable');
    }
}