<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPlatform extends Model
{
    use SoftDeletes;

    protected $table = 'event_ticket_platforms';

    protected $fillable = [
        'event_id',
        'platform_name',
        'platform_event_id',
        'platform_event_url',
        'platform_event_data',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
