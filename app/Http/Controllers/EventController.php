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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Jobs\SyncGoogleCalendarEvents;
use App\Http\Requests\StoreUpdateEventRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;

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
                'showLoadMorePast' => false,
                'totalUpcomingCount' => 0,
                'message' => 'No events available for your role.',
            ]);
        }

        // Fetching upcoming events based on user role
        $upcomingEventsQuery = Event::query()
            ->where('event_date', '>', now())
            ->whereNull('deleted_at')
            ->orderBy('event_date', 'asc');

        // Fetching past events based on user role
        $pastEventsQuery = Event::query()
            ->where('event_date', '<=', now())
            ->whereNull('deleted_at')
            ->orderBy('event_date', 'desc');


        // Fetching events based on user role
        if ($role === "promoter") {
            $upcomingEventsQuery->where(function ($query) use ($user, $service) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('promoters', function ($q) use ($service) {
                        $q->where('promoter_id', $service->id);
                    });
            });

            $pastEventsQuery->where(function ($query) use ($user, $service) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('promoters', function ($q) use ($service) {
                        $q->where('promoter_id', $service->id);
                    });
            });
        } elseif ($role === "artist") {
            $upcomingEventsQuery->where(function ($query) use ($user, $service) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('bands', function ($q) use ($service) {
                        $q->where('band_id', $service->id);
                    });
            });

            $pastEventsQuery->where(function ($query) use ($user, $service) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('bands', function ($q) use ($service) {
                        $q->where('band_id', $service->id);
                    });
            });
        } elseif ($role === "venue") {
            $upcomingEventsQuery->where(function ($query) use ($user, $service) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('venues', function ($q) use ($service) {
                        $q->where('venue_id', $service->id);
                    });
            });

            $pastEventsQuery->where(function ($query) use ($user, $service) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('venues', function ($q) use ($service) {
                        $q->where('venue_id', $service->id);
                    });
            });
        } else {
            $upcomingEventsQuery->where('user_id', $user->id);
            $pastEventsQuery->where('user_id', $user->id);
        }

        // Get counts and paginated results
        $totalUpcomingCount = $upcomingEventsQuery->count();
        $initialUpcomingEvents = $upcomingEventsQuery->take(3)->get();

        $totalPastCount = $pastEventsQuery->count();
        $pastEvents = $pastEventsQuery->take(3)->get();

        $showLoadMoreUpcoming = $totalUpcomingCount > 3;
        $showLoadMorePast = $totalPastCount > 3;

        return view('admin.dashboards.show-events', [
            'user' => $user,
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'initialUpcomingEvents' => $initialUpcomingEvents,
            'pastEvents' => $pastEvents,
            'showLoadMoreUpcoming' => $showLoadMoreUpcoming,
            'showLoadMorePast' => $showLoadMorePast,
            'totalUpcomingCount' => $totalUpcomingCount,
            'totalPastCount' => $totalPastCount,
        ]);
    }

    public function loadMorePastEvents(Request $request, $dashboardType)
    {
        $page = $request->get('page', 1);
        $pastEvents = Event::where('event_date', '<=', now())
            ->orderBy('event_date', 'desc')
            ->paginate(3, ['*'], 'page', $page);

        $html = '';
        foreach ($pastEvents as $event) {
            $html .= view('admin.dashboards.partials.event_card', [
                'event' => $event,
                'dashboardType' => $dashboardType
            ])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMorePages' => $pastEvents->hasMorePages()
        ]);
    }

    public function loadMoreUpcomingEvents(Request $request, $dashboardType)
    {
        $page = $request->get('page', 1);
        $upcomingEvents = Event::where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->paginate(3, ['*'], 'page', $page);

        $html = '';
        foreach ($upcomingEvents as $event) {
            $html .= view('admin.dashboards.partials.event_card', [
                'event' => $event,
                'dashboardType' => $dashboardType
            ])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMorePages' => $upcomingEvents->hasMorePages()
        ]);
    }

    public function createNewEvent($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['roles', 'promoters', 'venues', 'otherService']);

        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $genres = collect($data['genres'] ?? [])->pluck('name')->toArray();

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
            'genres' => $genres,
            'serviceData' => $serviceData,
            'profileData' => [
                'apiKeys' => $serviceData['apiKeys'] ?? [],
                'hasMultiplePlatforms' => $hasMultiplePlatforms,
                'singlePlatform' => $singlePlatform
            ]
        ]);
    }

    public function storeNewEvent($dashboardType, StoreUpdateEventRequest $request)
    {
        $modules = collect(session('modules', []));

        try {
            DB::beginTransaction();
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

            // Handle pending opportunities if they exist
            if ($request->has('pending_opportunities')) {
                $opportunities = $request->input('pending_opportunities');

                // If it's a JSON string, decode it
                if (is_string($opportunities)) {
                    $opportunities = json_decode($opportunities, true);
                }

                // Ensure we have an array
                if (!is_array($opportunities)) {
                    $opportunities = [];
                }

                if (!empty($opportunities)) {
                    $opportunityController = app(OpportunityController::class);

                    foreach ($opportunities as $index => $opportunityData) {
                        try {
                            $mergedGenresArray = array_merge(
                                $opportunityData['main_genres'] ?? [],
                                $opportunityData['subgenres'] ?? []
                            );
                            \Log::info($posterUrl);
                            $opportunityData = array_merge($opportunityData, [
                                'event_id' => $event->id,
                                'serviceable_id' => $user->id,
                                'serviceable_type' => get_class($user),
                                'related_type' => 'App\Models\Event',
                                'related_id' => $event->id,
                                'title' => $event->event_name,
                                'type' => $opportunityData['type'] ?? null,
                                'position_type' => $opportunityData['position_type'] ?? null,
                                'genres' => $mergedGenresArray ?? [],
                                'performance_start_time' => $opportunityData['performance_start_time'],
                                'performance_end_time' => $opportunityData['performance_end_time'],
                                'set_length' => $opportunityData['set_length'],
                                'application_deadline' => $opportunityData['application_deadline'],
                                'additional_info' => $opportunityData['additional_info'] ?? null,
                                'use_related_poster' => $opportunityData['poster_type'] === 'event',
                                'excluded_entities' => $opportunityData['excluded_entities'] ?? [],
                                'poster_url' => $opportunityData['poster_type'] === 'event' ? $posterUrl : null,
                            ]);

                            // Create opportunity
                            $result = $opportunityController->createEventOpportunity($opportunityData);
                        } catch (\Exception $e) {
                            \Log::error('Failed to create opportunity', [
                                'error' => $e->getMessage(),
                                'data' => $opportunityData
                            ]);
                            throw $e;
                        }
                    }
                } else {
                    \Log::info('No valid opportunities to process');
                }
            }

            // Update has_opportunities check
            $hasOpportunities = false;
            if ($request->has('pending_opportunities')) {
                $opps = $request->input('pending_opportunities');
                if (is_string($opps)) {
                    $opps = json_decode($opps, true);
                }
                $hasOpportunities = !empty($opps);
            }
            DB::commit();

            \Log::info('Transaction committed successfully', [
                'event_id' => $event->id,
                'has_opportunities' => $hasOpportunities,
                'opportunities_data' => $opportunities
            ]);

            return response()->json([
                'success' => true,
                'message' => $hasOpportunities
                    ? 'Event and opportunities created successfully'
                    : 'Event created successfully',
                'redirect_url' => route('admin.dashboard.show-event', [
                    'dashboardType' => $dashboardType,
                    'id' => $event->id
                ])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating event: ' . $e->getMessage(), [
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

    public function showEvent($dashboardType, $id)
    {
        $modules = collect(session('modules', []));

        $user = Auth::user()->load(['roles', 'otherService']);
        $role = $user->roles->first()->name;
        $event = Event::with(['bands', 'promoters', 'venues', 'services', 'opportunities'])->findOrFail($id);

        // Check if user has permission to view the event
        if (!$this->canViewEvent($user, $event)) {
            return response()->view('errors.403', [
                'dashboardType' => $dashboardType,
                'modules' => $modules
            ], 403);
        }

        // View Tracker
        $this->trackEventView($event, request());

        // Generate mock stats data if user is event creator
        $mockStats = null;
        if ($event->user_id === Auth::id() || $user->isLinkedToEvent($event)) {
            $mockStats = [
                'ticketsSold' => rand(50, 150),
                'ticketsAvailable' => 200,
                'totalRevenue' => rand(500, 2000),
                'pageViews' => $event->unique_views_count,
                'hasNewStats' => (bool)rand(0, 1),
                'salesData' => collect([
                    ['date' => '2024-03-01', 'count' => rand(5, 15)],
                    ['date' => '2024-03-08', 'count' => rand(10, 25)],
                    ['date' => '2024-03-15', 'count' => rand(15, 35)],
                    ['date' => '2024-03-22', 'count' => rand(20, 45)],
                ]),
                'trafficSources' => $this->getTrafficSourceCounts($event)
            ];
        }

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
            'stats' => $mockStats
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

            // Load and decode genres JSON
            $genreList = file_get_contents(public_path('text/genre_list.json'));
            $data = json_decode($genreList, true) ?? [];
            $genres = collect($data['genres'] ?? [])->pluck('name')->toArray();

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
                    'genres' => $genres
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
                'band_ids' => json_encode($bandsArray),
                'genre' => json_encode($validatedData['genres'] ?? [])
            ];

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
                'message' => 'Event deleted successfully',
                'redirect_url' => route('admin.dashboard.show-events', ['dashboardType' => $dashboardType])

            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Event not found.',
            'redirect_url' => route('admin.dashboard.show-events', ['dashboardType' => $dashboardType])

        ], 404);
    }

    public function getPublicEvents(Request $request)
    {
        // Load and decode genres JSON
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];

        // Extract genre names from the structure
        $genres = collect($data['genres'] ?? [])->pluck('name')->toArray();

        // Get events query
        $events = Event::query()
            ->with('venues', 'bands', 'promoters', 'services')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc');

        // Apply filters
        if ($request->filled('genre')) {
            $events->where('genre', $request->genre);
        }

        if ($request->filled('type')) {
            $events->where('band_type', $request->band_type);
        }

        if ($request->filled('location')) {
            $events->whereHas('venue', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->location . '%')
                    ->orWhere('postcode', 'like', '%' . $request->location . '%');
            });
        }

        $events = $events->paginate(12)->withQueryString();

        return view('events', compact('events', 'genres'));
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

    public function filter(Request $request)
    {
        $events = Event::query()
            ->with(['venues'])
            ->when($request->filled('genre'), function ($query) use ($request) {
                $query->whereJsonContains('genre', $request->genre);
            })
            ->when($request->filled('band_type') && $request->band_type !== 'all', function ($query) use ($request) {
                $query->where('band_type', $request->band_type);
            })
            ->when($request->filled('location'), function ($query) use ($request) {
                $query->whereHas('venues', function ($q) use ($request) {
                    $q->where('location', 'like', '%' . $request->location . '%');
                });
            })
            ->orderBy('event_date')
            ->paginate(9);

        return view('partials.event-grid', compact('events'))->render();
    }

    public function uploadPoster(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|max:10240', // 10MB max
            ]);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store in temporary location
            $path = $file->storeAs('temp/posters', $fileName, 'public');

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PRIVATE FUNCTIONS
     */

    /**
     * Track event view
     */
    private function trackEventView($event, Request $request)
    {
        // Generate a visitor ID based on IP and user agent
        $visitorId = md5($request->ip() . $request->userAgent());

        try {
            // Get referrer information
            $referrerUrl = $request->header('referer');
            $referrerType = $this->determineReferrerType($referrerUrl);

            $event->views()->firstOrCreate(
                [
                    'visitor_id' => $visitorId,
                    'ip_address' => $request->ip()
                ],
                [
                    'referrer_url' => $referrerUrl,
                    'referrer_type' => $referrerType
                ]
            );
        } catch (\Exception $e) {
            // Handle any potential errors silently
            \Log::error('Error tracking view: ' . $e->getMessage());
        }
    }

    /**
     * Determine the referrer type based on the URL
     */
    private function determineReferrerType($referrerUrl)
    {
        if (!$referrerUrl) {
            return 'direct';
        }

        $referrerDomain = parse_url($referrerUrl, PHP_URL_HOST);

        // Define patterns for different referrer types
        $patterns = [
            'facebook' => ['facebook.com', 'fb.com', 'messenger.com'],
            'instagram' => ['instagram.com'],
            'x' => ['twitter.com', 'x.com'],
            'google' => ['google.com', 'google.co.uk'],
            'bing' => ['bing.com'],
            'tiktok' => ['tiktok.com'],
            'whatsapp' => ['whatsapp.com'],
            'snapchat' => ['snapchat.com'],
            'youtube' => ['youtube.com', 'youtu.be'],
            'linkedin' => ['linkedin.com'],
            'instagram' => ['instagram.com'],
        ];

        // Check each pattern
        foreach ($patterns as $type => $domains) {
            foreach ($domains as $domain) {
                if (str_contains($referrerDomain, $domain)) {
                    return $type;
                }
            }
        }

        // Check if it's internal
        if (str_contains($referrerDomain, parse_url(config('app.url'), PHP_URL_HOST))) {
            return 'internal';
        }

        return 'other';
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

    /**
     * Get traffic source categories
     */
    private function getTrafficSourceCategories()
    {
        return [
            'social_media' => [
                'facebook',
                'instagram',
                'twitter',
                'x.com',
                'tiktok'
            ],
            'search' => [
                'google',
                'bing',
                'yahoo'
            ],
            'direct' => [
                'direct'
            ],
            'internal' => [
                'internal'
            ]
        ];
    }

    /**
     * Get traffic source counts
     */
    private function getTrafficSourceCounts($event)
    {
        $sources = $event->views()
            ->select('referrer_type', DB::raw('count(*) as count'))
            ->groupBy('referrer_type')
            ->get();

        $trafficSourceCounts = [
            'social_media' => 0,
            'search' => 0,
            'direct' => 0,
            'internal' => 0,
            'other' => 0
        ];

        foreach ($sources as $source) {
            $categorized = false;
            foreach ($this->getTrafficSourceCategories() as $category => $sourceTypes) {
                if (in_array($source->referrer_type, $sourceTypes)) {
                    $trafficSourceCounts[$category] += $source->count;
                    $categorized = true;
                    break;
                }
            }
            if (!$categorized) {
                $trafficSourceCounts['other'] += $source->count;
            }
        }

        return collect([
            ['source' => 'Direct', 'count' => $trafficSourceCounts['direct']],
            ['source' => 'Social Media', 'count' => $trafficSourceCounts['social_media']],
            ['source' => 'Search', 'count' => $trafficSourceCounts['search']],
            ['source' => 'Internal', 'count' => $trafficSourceCounts['internal']],
            ['source' => 'Other', 'count' => $trafficSourceCounts['other']]
        ]);
    }

    private function canViewEvent($user, $event): bool
    {
        // Event creator can always view
        if ($event->user_id === $user->id) {
            return true;
        }

        // Use the new method to check if user is linked to event
        return $user->isLinkedToEvent($event);
    }
}