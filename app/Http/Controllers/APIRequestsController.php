<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use App\Models\ApiKeys;
use App\Models\Promoter;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Models\UserModuleSetting;
use Illuminate\Support\Facades\Auth;

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

    private function getModelClass($dashboardType)
    {
        $modelMap = [
            'promoter' => \App\Models\Promoter::class,
            'venue' => \App\Models\Venue::class,
            'artist' => \App\Models\OtherService::class,
            'photographer' => \App\Models\OtherService::class,
            'designer' => \App\Models\OtherService::class,
            'videographer' => \App\Models\OtherService::class,
        ];

        return $modelMap[strtolower($dashboardType)] ?? null;
    }

    public function updateAPI($dashboardType, Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'provider' => 'required|string',
                'api_key' => 'required|string',
                'api_secret' => 'nullable|string',
            ]);

            $modelClass = $this->getModelClass($dashboardType);
            // dd($modelClass);

            if (!$modelClass) {
                return response()->json(['success' => false, 'message' => 'Invalid dashboard type'], 400);
            }

            $newApiKey = ApiKeys::create([
                'serviceable_type' => $modelClass,
                'serviceable_id' => $validated['id'],
                'name' => 'API Key',
                'key_type' => $validated['provider'],
                'api_key' => $validated['api_key'],
                'api_secret' => $validated['api_secret'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'API Key created successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('API Key creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create API Key'
            ], 500);
        }
    }

    public function updateModule(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'module' => 'required|string',
            'enabled' => 'required|boolean',
            'userId' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->userId);

        // Update the module settings in the database
        $module = UserModuleSetting::where('user_id', $user->id)->where('module_name', $request->module)->first();

        if ($module) {
            $module->is_enabled = $request->enabled;
            $module->save();

            return response()->json(['success' => true, 'message' => 'Module updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Module not found.'], 404);
    }

    public function updateCommunications(Request $request)
    {
        // Validate with proper structure
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'settings' => 'required|array',
            'settings.*' => 'array',
            'settings.*.is_enabled' => 'required|boolean',
        ]);

        try {
            $user = User::findOrFail($request->userId);

            // Get current preferences or set defaults
            $preferences = $user->mailing_preferences ?? [
                'system_announcements' => ['is_enabled' => true],
                'legal_or_policy_updates' => ['is_enabled' => true],
                'account_notifications' => ['is_enabled' => true],
                'event_invitations' => ['is_enabled' => true],
                'surveys_and_feedback' => ['is_enabled' => true],
                'birthday_anniversary_holiday' => ['is_enabled' => true],
            ];

            // Ensure we have an array
            if (!is_array($preferences)) {
                $preferences = json_decode($preferences, true) ?? [];
            }

            // Update only the settings provided
            foreach ($request->settings as $key => $setting) {
                if (array_key_exists($key, $preferences)) {
                    $preferences[$key]['is_enabled'] = $setting['is_enabled'];
                }
            }

            // Save updates
            $user->mailing_preferences = $preferences;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Communication preferences updated successfully',
                'preferences' => $preferences
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update communication preferences',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
