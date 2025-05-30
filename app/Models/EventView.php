<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class EventView extends Model
{
    protected $table = 'event_views';

    protected $fillable = [
        'event_id',
        'visitor_id',
        'ip_address',
        'referrer_url',
        'referrer_type',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}