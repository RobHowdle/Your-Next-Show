<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'additional_info',
        'type',                 // artist_wanted, venue_wanted, etc.
        'position_type',        // headliner, support, opener, etc
        'status',               // open, closed
        'poster_url',           // URL to the poster if they upload a separate one
        'use_related_poster',   // Whether to use the related event's poster
        'set_length',           // Length of the set
        'genres',               // Main genres and subgenres
        'excluded_entities',    // This will store excluded people already linked
        'serviceable_type',     // Who created the opportunity
        'serviceable_id',       // The ID of the creator (venue, promoter, etc)
        'related_type',         // Usually 'event'
        'related_id'            // The event ID
    ];

    protected $casts = [
        'requirements' => 'array',    // Store times and set length here
        'preferences' => 'array',     // Store genres and subgenres here
        'excluded_entities' => 'array',
        'use_related_poster' => 'boolean',
        'start_time' => 'time',
        'end_time' => 'time',
        'application_deadline' => 'datetime',
    ];

    // Creator relationship (venue, promoter, etc)
    public function serviceable()
    {
        return $this->morphTo();
    }

    // Related content relationship (event, etc)
    public function related()
    {
        return $this->morphTo();
    }

    // Applications relationship
    public function applications()
    {
        return $this->hasMany(OpportunityApplication::class);
    }
}