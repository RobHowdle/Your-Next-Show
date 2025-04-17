<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use App\Models\ApiKey;
use App\Models\ApiKeys;
use App\Models\Promoter;
use App\Models\ServiceUser;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Models\UserModuleSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUpdatePackages;

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

            if (!$modelClass) {
                return response()->json(['success' => false, 'message' => 'Invalid dashboard type'], 400);
            }

            $newApiKey = ApiKey::create([
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
        $request->validate([
            'setting' => 'required|string',
            'enabled' => 'required|boolean',
            'userId' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->userId);

        $mailingPreferences = $user->mailing_preferences ?? [];

        // Simply update the specific setting
        $mailingPreferences[$request->setting] = $request->enabled;

        $user->update(['mailing_preferences' => $mailingPreferences]);

        return response()->json([
            'success' => true,
            'message' => 'Communications updated successfully'
        ]);
    }

    public function updateStylesAndPrint(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'styles' => 'required|array',
            'print' => 'required|array',
        ]);

        $user = User::findOrFail($request->userId);

        $user->update([
            'styles' => $request->styles,
            'print' => $request->print,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Styles and Print updated successfully'
        ]);
    }

    /**
     * Save packages for a user
     */
    public function updatePackages($dashboardType, StoreUpdatePackages $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            // Get the appropriate service based on dashboard type
            $service = match ($dashboardType) {
                'venue' => $user->venues()->first(),
                'promoter' => $user->promoters()->first(),
                'artist' => $user->otherService('Artist')->first(),
                'photographer' => $user->otherService('Photography')->first(),
                'designer' => $user->otherService('Designer')->first(),
                'videographer' => $user->otherService('Videography')->first(),
                default => null,
            };

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            // Check if jobs module is enabled for this user
            $jobsModuleEnabled = UserModuleSetting::where('user_id', $user->id)
                ->where('module_name', 'jobs')
                ->where('is_enabled', true)
                ->exists();

            if (!$jobsModuleEnabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jobs module is not enabled'
                ], 403);
            }

            // Structure packages data
            $packages = array_map(function ($package) {
                return [
                    'name' => $package['name'],
                    'description' => $package['description'],
                    'price' => $package['price'],
                    'features' => $package['features'] ?? [],
                    'is_active' => $package['is_active'] ?? true,
                ];
            }, $validated['packages']);

            // Update packages in the database
            $service->update([
                'packages' => json_encode($packages)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Packages updated successfully',
                'redirect' => route('profile.edit', [
                    'dashboardType' => $dashboardType,
                    'id' => $user->id
                ])
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving packages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save packages'
            ], 500);
        }
    }

    public function leaveService($dashboardType, Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            switch ($dashboardType) {
                case 'venue':
                    $service = $user->venues()->first();
                    $serviceType = 'App\Models\Venue';
                    break;
                case 'promoter':
                    $service = $user->promoters()->first();
                    $serviceType = 'App\Models\Promoter';
                    break;
                case 'artist':
                    $service = $user->otherService('Artist')->first();
                    $serviceType = 'App\Models\OtherService';
                    break;
                case 'designer':
                    $service = $user->otherService('Designer')->first();
                    $serviceType = 'App\Models\OtherService';
                    break;
                case 'photographer':
                    $service = $user->otherService('Photographer')->first();
                    $serviceType = 'App\Models\OtherService';
                    break;
                case 'videographer':
                    $service = $user->otherService('Videographer')->first();
                    $serviceType = 'App\Models\OtherService';
                    break;
            }

            $serviceUser = ServiceUser::where('user_id', $id)
                ->where('serviceable_type', $serviceType)
                ->where('serviceable_id', $service->id)
                ->first();

            if ($serviceUser) {
                // Delete the found service user relationship
                $serviceUser->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully left service'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }
        } catch (\Exception $e) {
            \Log::error('Error leaving service:', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to leave service'
            ], 500);
        }
    }

    public function searchClients(Request $request)
    {
        $search = $request->input('query');

        // Search users by first_name and last_name
        $users = User::where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
        })->get(['id', 'first_name', 'last_name'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => "{$user->first_name} {$user->last_name}",
                    'service_type' => 'User'
                ];
            });

        // Search venues
        $venues = Venue::where('name', 'LIKE', "%{$search}%")
            ->get()
            ->map(function ($venue) {
                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'service_type' => 'Venue'
                ];
            });

        // Search promoters
        $promoters = Promoter::where('name', 'LIKE', "%{$search}%")
            ->get()
            ->map(function ($promoter) {
                return [
                    'id' => $promoter->id,
                    'name' => $promoter->name,
                    'service_type' => 'Promoter'
                ];
            });

        // Search other services
        $otherServices = OtherService::where('name', 'LIKE', "%{$search}%")
            ->with('otherServiceList')
            ->get()
            ->map(function ($otherService) {
                return [
                    'id' => $otherService->id,
                    'name' => $otherService->name,
                    'service_type' => $otherService->otherServiceList->service_name ?? null,
                ];
            });

        // Merge all results
        $clients = collect()
            ->merge($users)
            ->merge($venues)
            ->merge($promoters)
            ->merge($otherServices);

        return response()->json($clients);
    }
}