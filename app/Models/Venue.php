<?php

namespace App\Models;

use App\Models\Promoter;
use App\Models\VenueReview;
use App\Models\VenueExtraInfo;
use App\Models\Traits\HasVerification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Venue extends Model
{
    use HasVerification;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'venues';

    protected $fillable = [
        'name',
        'location',
        'postal_town',
        'longitude',
        'latitude',
        'w3w',
        'capacity',
        'in_house_gear',
        'deposit_required',
        'deposit_amount',
        'band_type',
        'genre',
        'contact_name',
        'contact_number',
        'contact_email',
        'contact_link',
        'description',
        'additional_info',
        'logo_url',
        'is_verified',
        'verified_at',
        'preferred_contact',
    ];

    protected $casts = [
        'contact_link' => 'array',
    ];

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'serviceable', 'service_user', 'serviceable_id', 'user_id');
    }

    public function extraInfo()
    {
        return $this->hasOne(VenueExtraInfo::class, 'venues_id');
    }

    public function promoters()
    {
        return $this->belongsToMany(Promoter::class, 'promoter_venue_pivot', 'promoters_id', 'venues_id');
    }

    public function review()
    {
        return $this->hasMany(VenueReview::class);
    }

    public function todos()
    {
        return $this->morphMany(Todo::class, 'serviceable');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_venue');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkedUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'serviceable', 'service_user', 'serviceable_id', 'user_id')
            ->withPivot('created_at', 'updated_at', 'role_id')
            ->join('roles', 'service_user.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->whereNull('service_user.deleted_at');
    }

    public function apiKeys()
    {
        return $this->morphMany(ApiKey::class, 'serviceable');
    }

    public function performingBands()
    {
        return $this->belongsToMany(OtherService::class, 'event_venue')
            ->join('events', 'event_venue.event_id', '=', 'events.id')
            ->join('event_band', 'events.id', '=', 'event_band.event_id')
            ->where('other_services.other_service_id', 4);
    }

    public function bands()
    {
        return $this->belongsToMany(OtherService::class, 'event_band')
            ->where('other_services.other_service_id', 4);
    }

    public function upcomingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_venue')
            ->where('events.event_date', '>=', now())
            ->orderBy('events.event_date', 'asc');
    }
}