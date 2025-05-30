<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use App\Models\EventTicketPlatform;
use App\Services\EventbriteService;

class EventTicketPlatformController extends Controller
{
    public function searchEventbriteEvents(Request $request)
    {
        $apiKey = ApiKey::where('serviceable_id', auth()->user()->id)
            ->where('name', 'eventbrite')
            ->where('is_active', true)
            ->firstOrFail();

        $service = new EventbriteService($apiKey);
        $events = $service->searchEvents($request->query('search', ''));

        return response()->json($events);
    }

    public function link(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'platform_name' => 'required|string',
            'platform_event_id' => 'required|string',
            'platform_event_url' => 'nullable|url',
            'platform_event_data' => 'nullable|array'
        ]);

        $ticketPlatform = EventTicketPlatform::create($validated);

        return response()->json([
            'message' => 'Event linked successfully',
            'data' => $ticketPlatform
        ]);
    }
}
