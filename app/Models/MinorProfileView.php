<?php

namespace App\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MinorProfileView extends Model
{
    protected $table = 'minor_profile_views';

    protected $fillable = [
        'serviceable_id',
        'serviceable_type',
        'profile_type',
        'ip_address',
        'user_id',
        'user_agent',
        'referrer_url',
        'geo_location'
    ];

    protected $casts = [
        'geo_location' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the owning serviceable model (Venue, Promoter, or Service)
     */
    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who viewed the profile (if authenticated)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to get views within last X days
     */
    public function scopeRecentViews($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}