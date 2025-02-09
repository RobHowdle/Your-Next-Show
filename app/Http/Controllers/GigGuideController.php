<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GigGuideController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userLocation = null;

        if ($user && $user->latitude && $user->longitude) {
            $userLocation = [
                'latitude' => $user->latitude,
                'longitude' => $user->longitude
            ];
        }
        return view('gig-guide', compact('userLocation'));
    }

    public function filterGigs(Request $request)
    {
        try {
            $validated = $request->validate([
                'distance' => 'required|numeric|min:1|max:500',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            // Get all events with their venues that have coordinates
            $events = Event::with(['venues', 'bands'])
                ->whereHas('venues', function ($query) {
                    $query->whereNotNull('latitude')
                        ->whereNotNull('longitude');
                })
                ->where('event_date', '>=', now())
                ->get();

            // Calculate distance for each event and filter
            $filteredEvents = $events->map(function ($event) use ($validated) {
                $venue = $event->venues->first();
                // if (!$venue) {
                //     \Log::info('Event has no venue', ['event_id' => $event->id]);
                //     return null;
                // }

                $distance = $this->calculateDistance(
                    $validated['latitude'],
                    $validated['longitude'],
                    $venue->latitude,
                    $venue->longitude
                );

                if ($distance <= $validated['distance']) {
                    $event->distance = round($distance, 1);
                    return $event;
                }
                return null;
            })->filter(function ($event) {
                return !is_null($event);
            });

            $formattedEvents = $filteredEvents->sortBy('distance')
                ->values()
                ->map(function ($event) {
                    $venue = $event->venues->first();

                    return [
                        'id' => $event->id,
                        'name' => $event->event_name,
                        'date' => $event->event_date,
                        'start_time' => $event->event_start_time,
                        'venue_name' => $venue ? $venue->name : null,
                        'venue_town' => $venue ? $venue->postal_town : null,
                        'distance' => $event->distance,
                        'headliner' => optional($event->bands)->name
                    ];
                })->filter(function ($event) {
                    return !is_null($event['venue_name']);
                })->values();

            return response()->json([
                'success' => true,
                'events' => $formattedEvents
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in filterGigs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch events'
            ], 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => "{$lat1},{$lon1}",
                'destinations' => "{$lat2},{$lon2}",
                'mode' => 'driving',
                'units' => 'imperial', // Use miles
                'key' => config('services.google.maps_api_key')
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                    // Convert meters to miles
                    return $data['rows'][0]['elements'][0]['distance']['value'] * 0.000621371;
                }
            }

            return $this->haversineDistance($lat1, $lon1, $lat2, $lon2);
        } catch (\Exception $e) {
            \Log::error('Error calculating distance', [
                'error' => $e->getMessage()
            ]);

            return $this->haversineDistance($lat1, $lon1, $lat2, $lon2);
        }
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $r = 3959; // Earth's radius in miles

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $r * $c;
    }
}
