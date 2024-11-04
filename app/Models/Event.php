<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'event_date',
        'event_start_time',
        'event_end_time',
        'event_description',
        'facebook_event_url',
        'poster_url',
        'band_ids',
        'ticket_url',
        'on_the_door_ticket_price',
        'attendance',
        'ticket_sales',
        'ratings',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function services()
    {
        return $this->belongsToMany(OtherService::class, 'event_band', 'event_id', 'band_id');
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'event_venue');
    }

    public function promoters()
    {
        return $this->belongsToMany(Promoter::class);
    }

    public function bands()
    {
        return $this->services()
            ->where('other_service_id', 4)
            ->where('services', 'Band');
    }

    public function getBandsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id');
    }

    public function eventPromoters()
    {
        return $this->hasMany(EventPromoter::class);
    }

    public function eventVenues()
    {
        return $this->hasMany(EventVenue::class);
    }

    public function eventBands()
    {
        return $this->hasMany(EventBand::class);
    }
}