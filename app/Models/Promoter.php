<?php

namespace App\Models;

use App\Models\Venue;
use App\Models\PromoterReview;
use App\Models\Traits\HasVerification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Promoter extends Model
{
    use HasVerification;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'promoters';

    protected $fillable = [
        'name',
        'location',
        'postal_town',
        'longitude',
        'latitude',
        'logo_url',
        'description',
        'my_venues',
        'genre',
        'band_type',
        'contact_name',
        'contact_number',
        'contact_email',
        'contact_link',
        'is_verified',
        'verified_at',
        'preferred_contact',
        'packages',
    ];

    protected $casts = [
        'contact_link' => 'array',
    ];

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'promoter_venue_pivot', 'promoters_id', 'venues_id');
    }

    public function review()
    {
        return $this->hasMany(PromoterReview::class);
    }

    public function linkedUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'serviceable', 'service_user', 'serviceable_id', 'user_id')
            ->withPivot('created_at', 'updated_at', 'role_id')
            ->join('roles', 'service_user.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->whereNull('service_user.deleted_at');
    }

    public function todos()
    {
        return $this->morphMany(Todo::class, 'serviceable');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_promoter');
    }

    public function user()
    {
        return $this->morphToMany(User::class, 'serviceable', 'service_user', 'serviceable_id', 'user_id');
    }

    public function apiKeys()
    {
        return $this->morphMany(ApiKey::class, 'serviceable');
    }

    public function upcomingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_promoter')
            ->where('events.event_date', '>=', now())
            ->orderBy('events.event_date', 'asc');
    }

    public function opportunities()
    {
        return $this->morphMany(Opportunity::class, 'serviceable');
    }
}
