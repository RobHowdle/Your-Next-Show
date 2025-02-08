<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTicketPlatform extends Model
{
    protected $fillable = [
        'event_id',
        'platform_name',
        'platform_event_id',
        'platform_event_url',
        'platform_event_data'
    ];

    protected $casts = [
        'platform_event_data' => 'array'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
