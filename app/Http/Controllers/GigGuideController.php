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
            $distance = $request->get('distance', 100);
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $events = Event::with(['venues', 'bands'])
                ->whereBetween('event_date', [$startOfWeek, $endOfWeek])
                ->get();

            $client = new \GuzzleHttp\Client();
            $origin = "{$latitude},{$longitude}";
            $filteredEvents = collect();

            foreach ($events->chunk(25) as $eventChunk) {
                // Filter out events without venues
                $validEvents = $eventChunk->filter(function ($event) {
                    return $event->venues && $event->venues->first();
                });

                if ($validEvents->isEmpty()) {
                    continue;
                }

                $destinations = $validEvents->map(function ($event) {
                    $venue = $event->venues->first();
                    return "{$venue->latitude},{$venue->longitude}";
                })->join('|');

                $apiKey = env('GOOGLE_MAPS_API_KEY');
                $response = $client->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'query' => [
                        'origins' => $origin,
                        'destinations' => $destinations,
                        'key' => $apiKey,
                        'units' => 'imperial'
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if (!isset($data['rows']) || empty($data['rows']) || !isset($data['rows'][0]['elements'])) {
                    continue;
                }

                foreach ($data['rows'][0]['elements'] as $index => $element) {
                    if ($element['status'] === 'OK' && isset($element['distance']['value'])) {
                        $event = $validEvents->values()->get($index);
                        if (!$event || !$event->venues->first()) continue;

                        $distanceInMiles = $element['distance']['value'] * 0.000621371;
                        if ($distanceInMiles <= $distance) {
                            $event->distance = round($distanceInMiles, 2);
                            $filteredEvents->push($event);
                        }
                    }
                }
            }

            $formattedEvents = $filteredEvents->sortBy('distance')->values()->map(function ($event) {
                $venue = $event->venues->first();
                if (!$venue) return null;

                return [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'date' => $event->event_date,
                    'start_time' => $event->event_start_time,
                    'venue_name' => $venue->name,
                    'venue_town' => $venue->postal_town,
                    'distance' => $event->distance,
                    'headliner' => optional($event->bands)->name
                ];
            })->filter();

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
