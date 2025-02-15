<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Promoter;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Helpers\CompareHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Jobs\SyncGoogleCalendarEvents;
use App\Http\Requests\StoreUpdateEventRequest;

class EventController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function showEvents($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['roles', 'otherService']);
        $role = $user->roles->first()->name;

        // Determine the service based on the user's role
        if (in_array($role, ["artist", "photographer", "videographer", "designer"])) {
            $service = $user->otherService(ucfirst($role))->first();
            if (is_null($service)) {
                return view('admin.dashboards.show-events', [
                    'user' => $user,
                    'userId' => $this->getUserId(),
                    'dashboardType' => $dashboardType,
                    'modules' => $modules,
                    'initialUpcomingEvents' => collect(),
                    'pastEvents' => collect(),
                    'showLoadMoreUpcoming' => false,
                    'hasMorePast' => false,
                    'totalUpcomingCount' => 0,
                    'message' => "No events found for this {$role}.",
                ]);
            }
        } elseif ($role === "promoter") {
            $service = $user->promoters()->first();
        } elseif ($role === "venue") {
            $service = $user->venues()->first();
        } else {
            $service = null;
        }

        if (is_null($service)) {
            return view('admin.dashboards.show-events', [
                'user' => $user,
                'userId' => $this->getUserId(),
                'dashboardType' => $dashboardType,
                'modules' => $modules,
                'initialUpcomingEvents' => collect(),
                'pastEvents' => collect(),
                'showLoadMoreUpcoming' => false,
                'hasMorePast' => false,
                'totalUpcomingCount' => 0,
                'message' => 'No events available for your role.',
            ]);
        }

        // Fetching events based on user role
        if ($role === "promoter") {
            // Promoter can see their own events and those created by users in their company
            $upcomingEvents = Event::where('event_date', '>', now())
                ->where('user_id', $user->id) // events created by this promoter
                ->orWhereIn('id', function ($query) use ($service) {
                    $query->select('event_id')
                        ->from('event_promoter')
                        ->where('promoter_id', $service->id); // events associated with the promoter
                })
                ->whereNull('deleted_at')
                ->orderBy('event_date', 'asc')
                ->get();
        } elseif ($role === "artist") {
            // Bands can see their own events or those associated with their promoter
            $upcomingEvents = Event::where('event_date', '>', now())
                ->where('user_id', $user->id) // events created by this band
                ->orWhereIn('id', function ($query) use ($service) {
                    $query->select('event_id')
                        ->from('event_band')
                        ->where('band_id', $service->id); // events associated with the promoter
                })
                ->whereNull('deleted_at')
                ->orderBy('event_date', 'asc')
                ->get();
        } elseif ($role === "venue") {
            // Promoter can see their own events and those created by users in their company
            $upcomingEvents = Event::where('event_date', '>', now())
                ->where('user_id', $user->id) // events created by this promoter
                ->orWhereIn('id', function ($query) use ($service) {
                    $query->select('event_id')
                        ->from('event_venue')
                        ->where('venue_id', $service->id); // events associated with the promoter
                })
                ->whereNull('deleted_at')
                ->orderBy('event_date', 'asc')
                ->get();
        } else {
            // Default case for any other roles (if necessary)
            $upcomingEvents = Event::where('event_date', '>', now())
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->orderBy('event_date', 'asc')
                ->get();
        }

        // Prepare upcoming events for the view
        $totalUpcomingCount = $upcomingEvents->count();
        $initialUpcomingEvents = $upcomingEvents->take(3);

        // Past events remain unchanged
        $totalPastCount = Event::where('event_date', '<=', now())->count();
        $pastEvents = Event::where('event_date', '<=', now())
            ->orderBy('event_date', 'desc')
            ->paginate(3);

        $showLoadMoreUpcoming = $totalUpcomingCount > 3;
        $hasMorePast = $totalPastCount > 3;

        return view('admin.dashboards.show-events', [
            'user' => $user,
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'initialUpcomingEvents' => $initialUpcomingEvents,
            'pastEvents' => $pastEvents,
            'showLoadMoreUpcoming' => $showLoadMoreUpcoming,
            'hasMorePast' => $hasMorePast,
            'totalUpcomingCount' => $totalUpcomingCount,
        ]);
    }

    public function loadMoreUpcomingEvents($dashboardType, Request $request)
    {
        $modules = collect(session('modules', []));

        $user = Auth::user()->load('roles');
        $role = $user->getRoleNames()->first();

        $userPromoter = $user->promoters()->first();

        $currentPage = $request->input('page', 1);

        $upcomingEvents = Event::where('event_date', '>', now())
            ->where(function ($query) use ($user, $userPromoter) {
                if ($userPromoter) {
                    $query->where('user_id', $user->id)
                        ->orWhereIn('user_id', function ($subquery) use ($userPromoter) {
                            $subquery->select('id')->from('users')
                                ->where('promoter_id', $userPromoter->id);
                        });
                } else {
                    $query->where('user_id', $user->id);
                }
            })
            ->orderBy('event_date')
            ->paginate(3, ['*'], 'page', $currentPage);

        $hasMorePages = $upcomingEvents->hasMorePages();

        $html = '';
        foreach ($upcomingEvents as $event) {
            $html .= view('admin.dashboards.partials.event_card', ['promoter' => $userPromoter, 'event' => $event])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMorePages' => $hasMorePages
        ]);
    }

    public function loadMorePastEvents(Request $request)
    {
        $modules = collect(session('modules', []));

        $promoter = Auth::user()->promoters()->first();

        $currentPage = $request->input('page', 1);

        $pastEvents = Event::where('event_date', '<', now())
            ->orderBy('event_date')
            ->paginate(3, ['*'], 'page', $currentPage);

        $hasMorePages = $pastEvents->hasMorePages();

        $html = '';
        foreach ($pastEvents as $event) {
            $html .= view('admin.dashboards.partials.event_card', ['promoter' => $promoter, 'event' => $event])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMorePages' => $hasMorePages
        ]);
    }

    public function createNewEvent($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['roles', 'promoters', 'venues', 'otherService']);

        $serviceData = [
            'promoter_id' => null,
            'promoter_name' => null,
            'venue_id' => null,
            'venue_name' => null,
            'id' => null,
            'name' => null,
            'apiKeys' => []
        ];

        // Get the service and API keys based on dashboard type
        switch ($dashboardType) {
            case 'promoter':
                $service = $user->promoters()->first();
                if ($service) {
                    $serviceData['promoter_id'] = $service->id;
                    $serviceData['promoter_name'] = $service->name;
                    $serviceData['apiKeys'] = $this->getServiceApiKeys($service);
                }
                break;

            case 'venue':
                $service = $user->venues()->first();
                if ($service) {
                    $serviceData['venue_id'] = $service->id;
                    $serviceData['venue_name'] = $service->name;
                    $serviceData['apiKeys'] = $this->getServiceApiKeys($service);
                }
                break;

            default:
                $service = $user->otherService('service')->first();
                if ($service) {
                    $serviceData['id'] = $service->id;
                    $serviceData['name'] = $service->name;
                    $serviceData['apiKeys'] = $this->getServiceApiKeys($service);
                }
        }

        $hasMultiplePlatforms = count($serviceData['apiKeys']) > 1;
        $singlePlatform = count($serviceData['apiKeys']) === 1 ? collect($serviceData['apiKeys'])->first() : null;

        return view('admin.dashboards.new-event', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'service' => $service,
            'serviceData' => $serviceData,
            'profileData' => [
                'apiKeys' => $serviceData['apiKeys'] ?? [],
                'hasMultiplePlatforms' => $hasMultiplePlatforms,
                'singlePlatform' => $singlePlatform
            ]
        ]);
    }

    /**
     * Get API keys for a service and filter based on config
     */
    private function getServiceApiKeys($service)
    {
        // Get enabled ticket platforms from config
        $enabledPlatforms = collect(config('integrations.ticket_platforms'))
            ->filter(function ($platform) {
                return $platform['enabled'] === true;
            })
            ->keys()
            ->toArray();

        // Get and format service API keys
        return $service->apiKeys()
            ->where('is_active', true)
            ->whereIn('name', $enabledPlatforms)
            ->get()
            ->map(function ($apiKey) {
                return [
                    'id' => $apiKey->id,
                    'name' => $apiKey->name,
                    'key_type' => $apiKey->key_type,
                    'display_name' => config("integrations.ticket_platforms.{$apiKey->name}.name"),
                    'description' => config("integrations.ticket_platforms.{$apiKey->name}.description")
                ];
            })
            ->toArray();
    }

    public function selectVenue($dashboardType, Request $request)
    {
        $modules = collect(session('modules', []));

        $query = $request->input('query');

        if (!is_string($query) || strlen($query) < 3) {
            return response()->json([], 400);
        }

        $venues = collect();

        if ($dashboardType === 'venue') {
            $user = Auth::user()->load('venues');
            $venue = $user->venues()->first();

            if ($venue) {
                $venues->push($venue);
            }
        } else {
            $venues = Venue::where('name', 'like', '%' . $query . '%')->get();
        }

        return response()->json($venues);
    }

    public function selectPromoter($dashboardType, Request $request)
    {
        $modules = collect(session('modules', []));

        $query = $request->input('query');

        if (!is_string($query) || strlen($query) < 3) {
            return response()->json([], 400);
        }

        $promoters = Promoter::where('name', 'like', '%' . $query . '%')->get();

        return response()->json($promoters);
    }

    public function storeNewEvent($dashboardType, StoreUpdateEventRequest $request)
    {
        $modules = collect(session('modules', []));

        try {
            $validatedData = $request->validated();

            $user = Auth::user()->load('roles');
            $role = $user->getRoleNames()->first();

            $bandsArray = [];

            if ($request->has('promoter_id')) {
                $promoter = Promoter::find($validatedData['promoter_id']);
            }

            if (!empty($validatedData['headliner'])) {
                $bandsArray[] = ['role' => 'Headliner', 'band_id' => $validatedData['headliner_id']];
            }

            if (!empty($validatedData['main_support'])) {
                $bandsArray[] = ['role' => 'Main Support', 'band_id' => $validatedData['main_support_id']];
            }

            if (!empty($validatedData['bands_ids'])) {
                foreach ($validatedData['bands_ids'] as $bandId) {
                    if (!empty($bandId)) {
                        $bandsArray[] = ['role' => 'Artist', 'band_id' => $bandId];
                    }
                }
            }

            if (!empty($validatedData['opener'])) {
                $bandsArray[] = ['role' => 'Opener', 'band_id' => $validatedData['opener_id']];
            }

            // Correct Event Start Date/Time
            $event_date = Carbon::createFromFormat('d-m-Y H:i:s', $validatedData['event_date'] . ' 00:00:00')->format('Y-m-d H:i:s');

            // Poster Upload
            $posterUrl = null;

            if ($request->hasFile('poster_url')) {
                $eventPosterFile = $request->file('poster_url');

                $eventName = $validatedData['event_name'];
                $posterExtension = $eventPosterFile->getClientOriginalExtension() ?: $eventPosterFile->guessExtension();
                $posterFilename = Str::slug($eventName) . '_poster.' . $posterExtension; // Adding '_poster' to the filename

                // Specify the destination directory, ensure the correct folder structure
                $destinationPath = public_path('images/event_posters/' . strtolower($role) . '/' . $user->id);

                // Check if the directory exists; if not, create it
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true); // Create directory with permissions
                }

                // Move the uploaded image to the specified directory
                $eventPosterFile->move($destinationPath, $posterFilename);

                // Construct the URL to the stored image
                $posterUrl = 'images/event_posters/' . strtolower($role) . '/' . $user->id . '/' . $posterFilename;
            }

            // Main Event Creation
            $event = Event::create([
                'user_id' => $user->id,
                'event_name' => $validatedData['event_name'],
                'event_date' => $event_date,
                'event_start_time' => $validatedData['event_start_time'],
                'event_end_time' => $validatedData['event_end_time'],
                'event_description' => $validatedData['event_description'],
                'facebook_event_url' => $validatedData['facebook_event_url'],
                'poster_url' => $posterUrl,
                'band_ids' => json_encode($bandsArray),
                'ticket_url' => $validatedData['ticket_url'],
                'on_the_door_ticket_price' => $validatedData['on_the_door_ticket_price'],
            ]);

            if (!empty($validatedData['ticket_platform'])) {
                $event->ticketPlatforms()->create([
                    'platform_name' => $request->ticket_platform,
                    'platform_event_id' => $request->platform_event_id,
                    'platform_event_url' => $request->platform_event_url,
                ]);
            }

            // Event Band Creation
            if (!empty($bandsArray)) {
                foreach ($bandsArray as $band) {
                    $event->services()->attach($band['band_id'], [
                        'event_id' => $event->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            // Event Venue Creation
            if (isset($validatedData['venue_id'])) {
                $event->venues()->attach(
                    $validatedData['venue_id'],
                    [
                        'event_id' => $event->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }

            // Event Promoter Creation
            if (isset($validatedData['promoter_ids'])) {
                foreach ($validatedData['promoter_ids'] as $promoterId) {
                    $event->promoters()->attach(
                        $promoterId,
                        [
                            'event_id' => $event->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
                }
            }

            // Add to internal calendar
            $calendarController = new CalendarController();
            $calendarRequest = new Request([
                'event_id' => $event->id,
                'title' => $event->event_name,
                'date' => Carbon::parse($event_date)->format('Y-m-d'),
                'start_time' => $validatedData['event_start_time'],
                'end_time' => $validatedData['event_end_time'],
                'location' => Venue::find($validatedData['venue_id'])->name ?? '',
                'description' => $validatedData['description'] ?? '',
                'calendar_service' => 'internal'
            ]);

            $calendarController->addEventToInternalCalendar($calendarRequest);

            // Check for Google Calendar integration
            if ($user->google_access_token) {
                $googleRequest = new Request([
                    'event_id' => $event->id,
                    'title' => $event->event_name,
                    'date' => Carbon::parse($event_date)->format('Y-m-d'),
                    'start_time' => $validatedData['event_start_time'],
                    'end_time' => $validatedData['event_end_time'],
                    'location' => Venue::find($validatedData['venue_id'])->name ?? '',
                    'description' => $validatedData['description'] ?? '',
                    'calendar_service' => 'google'
                ]);

                $calendarController->addEventToInternalCalendar($googleRequest);
                // \Log::info('Dispatching SyncGoogleCalendarEvents job');
                SyncGoogleCalendarEvents::dispatch($event, Auth::id())->delay(now()->addSeconds(20))->onQueue('default');
            }

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'redirect_url' => route('admin.dashboard.show-event', ['dashboardType' => $dashboardType, 'id' => $event->id])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage(), [
                'success' => false,
                'message' => 'Error creating event. Please try again.',
                'request' => $request->all(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'There was an error creating the event. Please try again.',
                'request' => $request->all(),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function showEvent($dashboardType, $id)
    {
        $modules = collect(session('modules', []));

        $user = Auth::user()->load(['roles', 'otherService']);
        $role = $user->roles->first()->name;
        $event = Event::with(['bands', 'promoters', 'venues', 'services'])->findOrFail($id);

        $bandRolesArray = json_decode($event->band_ids, true);

        $headliner = null;
        $mainSupport = null;
        $otherBands = [];
        $opener = null;

        $bandRoles = $event->bands()->get();

        foreach ($bandRolesArray as $bandRole) {
            $band = $bandRoles->firstWhere('id', $bandRole['band_id']);
            if ($band) {
                switch ($bandRole['role']) {
                    case 'Headliner':
                        $headliner = $band;
                        break;
                    case 'Main Support':
                        $mainSupport = $band;
                        break;
                    case 'Artist':
                        $otherBands[] = $band;
                        break;
                    case 'Opener':
                        $opener = $band;
                        break;
                }
            }
        }

        $eventStartTime = $event->event_start_time ? Carbon::parse($event->event_start_time)->format('g:i A') : null;
        $eventEndTime = $event->event_end_time ? Carbon::parse($event->event_end_time)->format('g:i A') : null;

        $isPastEvent = Carbon::now()->isAfter($event->event_date);

        return view('admin.dashboards.show-event', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'event' => $event,
            'isPastEvent' => $isPastEvent,
            'headliner' => $headliner,
            'mainSupport' => $mainSupport,
            'otherBands' => $otherBands,
            'opener' => $opener,
            'eventStartTime' => $eventStartTime,
            'eventEndTime' => $eventEndTime,
        ]);
    }

    public function editEvent($dashboardType, $id)
    {
        try {
            $modules = collect(session('modules', []));

            // Load event with relationships
            $event = Event::with(['promoters', 'venues', 'services'])->findOrFail($id);

            // Get venue with null check
            $venue = $event->venues->first();

            // Get promoters with null check
            $promoters = $event->promoters ?? collect();
            $promoterData = [];

            // Format dates and times
            $eventDate = Carbon::parse($event->event_date)->toDateString();

            // Process band roles
            $bandRoles = json_decode($event->band_ids, true) ?? [];
            $headlinerId = null;
            $mainSupportId = null;
            $openerId = null;
            $bands = [];

            foreach ($bandRoles as $band) {
                switch ($band['role']) {
                    case 'Headliner':
                        $headlinerId = $band['band_id'];
                        break;
                    case 'Main Support':
                        $mainSupportId = $band['band_id'];
                        break;
                    case 'Opener':
                        $openerId = $band['band_id'];
                        break;
                    case 'Artist':
                        $bands[] = $band['band_id'];
                        break;
                }
            }

            // Get band objects with null checks
            $headliner = $headlinerId ? OtherService::find($headlinerId) : null;
            $mainSupport = $mainSupportId ? OtherService::find($mainSupportId) : null;
            $opener = $openerId ? OtherService::find($openerId) : null;

            $bandObjects = [];
            foreach ($bands as $bandId) {
                $band = OtherService::find($bandId);
                if ($band) {
                    $bandObjects[] = $band;
                }
            }

            $user = Auth::user()->load(['roles', 'promoters', 'venues', 'otherService']);
            switch ($dashboardType) {
                case 'promoter':
                    $role = $user->promoters()->first();

                    if ($role) {
                        $promoterData = [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    };
                    break;
                case 'artist':
                    $role = $user->otherService('service')->first();
                    break;
                case 'designer':
                    $role = $user->otherService('service')->first();
                    break;
                case 'videographer':
                    $role = $user->otherService('service')->first();
                    break;
                case 'photographer':
                    $role = $user->otherService('service')->first();
                    break;
                case 'venue':
                    $role = $user->venues()->first();
                    break;
                default:
                    $role = 'guest';
                    break;
            }

            // Get existing poster URL
            $posterUrl = $event->poster_url;
            $hasPoster = !empty($posterUrl);

            return view(
                'admin.dashboards.edit-event',
                [
                    'userId' => $this->getUserId(),
                    'dashboardType' => $dashboardType,
                    'modules' => $modules,
                    'event' => $event,
                    'eventDate' => $eventDate,
                    'eventStartTime' => $event->event_start_time,
                    'eventEndTime' => $event->event_end_time,
                    'venue' => $venue,
                    'promoters' => $promoters,
                    'headliner' => $headliner,
                    'mainSupport' => $mainSupport,
                    'bandObjects' => $bandObjects,
                    'opener' => $opener,
                    'posterUrl' => $posterUrl,
                    'hasPoster' => $hasPoster,
                    'promoterData' => $promoterData,
                    'role' => $role,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error editing event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading event details');
        }
    }

    public function updateEvent($dashboardType, StoreUpdateEventRequest $request, $eventId)
    {

        $existingEvent = Event::find($eventId);
        $changes = CompareHelper::showEventChanges($existingEvent, $request->all());

        // dd($changes);
        $modules = collect(session('modules', []));
        try {
            $validatedData = $request->validated();
            // \Log::info('Validated data:', $validatedData);
            $user = Auth::user()->load('roles');
            $role = $user->getRoleNames()->first();

            // Find the existing event
            $event = Event::findOrFail($eventId);

            if (!empty($validatedData['promoter_ids'])) {
                $promoter = Promoter::find($validatedData['promoter_ids']);
            }

            $bandsArray = [];
            if (!empty($validatedData['headliner'])) {
                $bandsArray[] = ['role' => 'Headliner', 'band_id' => $validatedData['headliner_id']];
            }

            if (!empty($validatedData['main_support'])) {
                $bandsArray[] = ['role' => 'Main Support', 'band_id' => $validatedData['main_support_id']];
            }

            if (!empty($validatedData['bands_ids']) && is_array($validatedData['bands_ids'])) {
                foreach ($validatedData['bands_ids'] as $bandId) {
                    if (!empty($bandId)) {
                        $bandsArray[] = ['role' => 'Artist', 'band_id' => $bandId];
                    }
                }
            }

            if (!empty($validatedData['opener'])) {
                $bandsArray[] = ['role' => 'Opener', 'band_id' => $validatedData['opener_id']];
            }

            // \Log::info('Bands array:', $bandsArray);


            // Poster Upload
            $posterUrl = $event->poster_url;
            if ($request->hasFile('poster_url')) {
                $eventPosterFile = $request->file('poster_url');

                $eventName = $validatedData['event_name'];
                $posterExtension = $eventPosterFile->getClientOriginalExtension() ?: $eventPosterFile->guessExtension();
                $posterFilename = Str::slug($eventName) . '_poster.' . $posterExtension; // Adding '_poster' to the filename

                // Specify the destination directory, ensure the correct folder structure
                $destinationPath = public_path('images/event_posters/' . strtolower($role) . '/' . $user->id);

                // Check if the directory exists; if not, create it
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true); // Create directory with permissions
                }

                // Move the uploaded image to the specified directory
                $eventPosterFile->move($destinationPath, $posterFilename);

                // Construct the URL to the stored image
                $posterUrl = 'images/event_posters/' . strtolower($role) . '/' . $user->id . '/' . $posterFilename;
            }

            // Correct Event Start Date/Time
            $event_date = Carbon::createFromFormat('d-m-Y H:i:s', $validatedData['event_date'] . ' 00:00:00')->format('Y-m-d H:i:s');

            // Update event
            $updateData = [
                'event_name' => $validatedData['event_name'],
                'event_date' => $event_date,
                'event_start_time' => $validatedData['event_start_time'],
                'event_end_time' => $validatedData['event_end_time'],
                'event_description' => $validatedData['event_description'],
                'facebook_event_url' => $validatedData['facebook_event_url'],
                'ticket_url' => $validatedData['ticket_url'],
                'on_the_door_ticket_price' => $validatedData['on_the_door_ticket_price'],
                'band_ids' => json_encode($bandsArray)
            ];

            // \Log::info('Update data:', $updateData);


            // Update the event
            $event->update($updateData);

            // Sync Event Bands (attach or detach based on changes)
            if (!empty($bandsArray)) {
                $existingBandIds = $event->services()->pluck('band_id')->toArray();
                $newBandIds = array_column($bandsArray, 'band_id');

                // Attach new bands
                foreach (array_diff($newBandIds, $existingBandIds) as $bandId) {
                    $event->services()->attach($bandId, [
                        'event_id' => $event->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                // Detach removed bands
                foreach (array_diff($existingBandIds, $newBandIds) as $bandId) {
                    $event->services()->detach($bandId);
                }
            }

            // Update relationships
            if (isset($validatedData['venue_id'])) {
                $event->venues()->sync([$validatedData['venue_id']]);
            }

            if (!empty($validatedData['promoter_ids']) && is_array($validatedData['promoter_ids'])) {
                $event->promoters()->sync($validatedData['promoter_ids']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'redirect_url' => route('admin.dashboard.show-event', ['dashboardType' => $dashboardType, 'id' => $event->id])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage(), [
                'success' => false,
                'message' => 'Error updating event. Please try again.',
                'request' => $request->all(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'There was an error updating the event. Please try again.',
                'request' => $request->all(),
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteEvent($dashboardType, $eventId)
    {
        $modules = collect(session('modules', []));
        $event = Event::findOrFail($eventId);

        if ($event) {
            $event->eventPromoters()->delete();
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Event not found.'
        ], 404);
    }

    public function getPublicEvents()
    {
        //
    }

    public function getSinglePublicEvent($eventId)
    {
        $event = Event::with(['bands', 'promoters', 'venues', 'services'])->findOrFail($eventId);

        $bandRolesArray = json_decode($event->band_ids, true);

        $headliner = null;
        $mainSupport = null;
        $otherBands = [];
        $opener = null;

        $bandRoles = $event->bands()->get();

        foreach ($bandRolesArray as $bandRole) {
            $band = $bandRoles->firstWhere('id', $bandRole['band_id']);
            if ($band) {
                switch ($bandRole['role']) {
                    case 'Headliner':
                        $headliner = $band;
                        break;
                    case 'Main Support':
                        $mainSupport = $band;
                        break;
                    case 'Artist':
                        $otherBands[] = $band;
                        break;
                    case 'Opener':
                        $opener = $band;
                        break;
                }
            }
        }

        $eventStartTime = $event->event_start_time ? Carbon::parse($event->event_start_time)->format('g:i A') : null;
        $eventEndTime = $event->event_end_time ? Carbon::parse($event->event_end_time)->format('g:i A') : null;

        $isPastEvent = Carbon::now()->isAfter($event->event_date);

        return view('single-event', [
            'event' => $event,
            'isPastEvent' => $isPastEvent,
            'headliner' => $headliner,
            'mainSupport' => $mainSupport,
            'otherBands' => $otherBands,
            'opener' => $opener,
            'eventStartTime' => $eventStartTime,
            'eventEndTime' => $eventEndTime,
        ]);
    }
}
