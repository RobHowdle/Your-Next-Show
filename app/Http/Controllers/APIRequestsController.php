<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;
use Illuminate\Http\Request;

class APIRequestsController extends Controller
{
    /**
     * Events - Searching for bands
     */
    public function searchBands(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json(['error' => 'Query is required'], 400);
        }

        // Fetch bands with the specified other_service_id
        $bands = OtherService::where('other_service_id', 4)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->get(['id', 'name']);

        // Prepare response
        if ($bands->isEmpty()) {
            $bands = [
                'bands' => [],
                'createNewBandOption' => [
                    'name' => $query,
                    'message' => "No results found. Click to create a new band: $query",
                ],
            ];
        } else {
            $bands = [
                'bands' => $bands,
                'createNewBandOption' => null,
            ];
        }

        // Return the response as JSON
        return response()->json($bands);
    }

    /**
     * Events - Create a new band from search input
     */
    public function createBand(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $query = $request->input('name');
        $cleanedQuery = cleanQuery($query);

        $newBand = OtherService::create([
            'other_service_id' => 4,
            'name' => $cleanedQuery,
            'location' => 'Unknown',
            'postal_town' => 'Unknown',
            'longitude' => 0,
            'latitude' => 0,
            'description' => 'Nothing here yet!',
            'contact_name' => 'Unknown',
            'contact_number' => '00000000000',
            'contact_email' => 'blank@yournextshow.co.uk',
            'contact_link' => json_encode(['website' => 'https://yournextshow.co.uk']),
            'services' => 'Artist',
        ]);

        return response()->json([
            'success' => true,
            'band' => $newBand,
            'message' => 'Artist not found, created new artist'
        ]);
    }

    /**
     * Events - Searching for promoters
     */
    public function searchPromoters(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json(['error' => 'Query is required'], 400);
        }

        // Fetch bands with the specified other_service_id
        $promoters = Promoter::where('name', 'LIKE', '%' . $query . '%')
            ->get(['id', 'name']);

        // Prepare response
        if ($promoters->isEmpty()) {
            $promoters = [
                'promoters' => [],
                'createNewBandOption' => [
                    'name' => $query,
                    'message' => "No results found. Click to create a new promoter: $query",
                ],
            ];
        } else {
            $promoters = [
                'promoters' => $promoters,
                'createNewPromoterOption' => null,
            ];
        }

        // Return the response as JSON
        return response()->json($promoters);
    }

    /**
     * Events - Searching for promoters
     */
    public function createPromoter(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string',
            ]);

            $query = $request->input('name');
            $cleanedQuery = cleanQuery($query);

            $newPromoter = Promoter::create([
                'name' => $cleanedQuery,
                'location' => 'Unknown',
                'postal_town' => 'Unknown',
                'longitude' => 0,
                'latitude' => 0,
                'description' => 'Nothing here yet!',
                'contact_name' => 'Unknown',
                'contact_number' => '00000000000',
                'contact_email' => 'blank@yournextshow.co.uk',
                'contact_link' => json_encode(['website' => 'https://yournextshow.co.uk']),
            ]);

            return response()->json([
                'success' => true,
                'promoter' => [
                    'id' => $newPromoter->id,
                    'name' => $newPromoter->name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create promoter'
            ], 500);
        }
    }

    /**
     * Events - Searching for venues
     */
    public function searchVenues(Request $request)
    {
        try {
            $query = $request->get('q');

            if (strlen($query) < 3) {
                return response()->json(['venues' => []], 200);
            }

            $venues = Venue::where('name', 'like', '%' . $query . '%')
                ->orderBy('name')
                ->get();

            return response()->json(['venues' => $venues]);
        } catch (\Exception $e) {
            \Log::error('Venue search failed: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Events - Searching for venue
     */
    public function createVenue(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string',
            ]);

            $query = $request->input('name');
            $cleanedQuery = cleanQuery($query);

            $newVenue = Venue::create([
                'name' => $cleanedQuery,
                'location' => 'Unknown',
                'postal_town' => 'Unknown',
                'longitude' => 0,
                'latitude' => 0,
                'description' => 'Nothing here yet!',
                'in_house_gear' => 'Nothing here yet!',
                'capacity' => 0,
                'contact_name' => 'Unknown',
                'contact_number' => '00000000000',
                'contact_email' => 'blank@yournextshow.co.uk',
                'contact_link' => json_encode(['website' => 'https://yournextshow.co.uk']),
            ]);

            return response()->json([
                'success' => true,
                'venue' => [
                    'id' => $newVenue->id,
                    'name' => $newVenue->name
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Venue creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create venue'
            ], 500);
        }
    }

    /**
     * Get Users Calendar Events
     */
    public function getUserCalendarEvents($dashboardType, Request $request, $userId)
    {
        // Fetch the current user along with relationships
        $currentUser = User::with(['promoters', 'venues', 'otherService'])->find($userId);

        $service = '';
        switch ($dashboardType) {
            case 'promoter':
                $service = $currentUser->promoters()->first();
                break;
            case 'venue':
                $service = $currentUser->venues()->first();
                break;
            case 'artist':
                $service = $currentUser->otherService('Artist')->first();
                break;
            case 'designer':
                $service = $currentUser->otherService('Designer')->first();
                break;
            case 'photographer':
                $service = $currentUser->otherService('Photographer')->first();
                break;
            case 'videographer':
                $service = $currentUser->otherService('Videographer')->first();
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid Dashboard Type'], 400);
        }

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service Not Found'], 404);
        }

        if ($request->query('view') === 'calendar') {
            $start = $request->query('start');
            $end = $request->query('end');

            // Fetch events based on the service type
            $events = Event::with(['promoters', 'services', 'venues'])
                ->where(function ($query) use ($dashboardType, $service) {
                    switch ($dashboardType) {
                        case 'promoter':
                            $query->whereHas('promoters', function ($subQuery) use ($service) {
                                $subQuery->where('promoter_id', $service->id);
                            });
                            break;
                        case 'venue':
                            $query->whereHas('venues', function ($subQuery) use ($service) {
                                $subQuery->where('venue_id', $service->id);
                            });
                            break;
                        case 'artist':
                            $query->whereHas('bands', function ($subQuery) use ($service) {
                                $subQuery->where('band_id', $service->id);
                            });
                            break;
                        default:
                            break;
                    }
                })
                ->whereBetween('event_date', [$start, $end])
                ->get();

            // Format events for the calendar view
            $formattedEvents = $events->map(function ($event) {
                $eventDate = Carbon::parse($event->event_date)->format('Y-m-d');

                return [
                    'title' => $event->event_name,
                    'start' => $eventDate . 'T' . $event->event_start_time,
                    'end' => $eventDate . 'T' . $event->event_end_time,
                    'description' => $event->event_description,
                    'event_start_time' => $event->event_start_time,
                    'bands' => $event->services->map(function ($band) {
                        return $band->name;
                    })->toArray(),
                    'location' => $event->venues->first()->location ?? 'No location provided',
                    'ticket_url' => $event->ticket_url,
                    'on_the_door_ticket_price' => $event->on_the_door_ticket_price,
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $formattedEvents,
            ]);
        }

        return response()->json(['success' => true, 'events' => []]);
    }
}