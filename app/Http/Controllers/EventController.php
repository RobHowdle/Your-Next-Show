<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Promoter;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
        if ($role === "band") {
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
                    'message' => 'No events found for this band.',
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
                ->orderBy('event_date', 'asc')
                ->get();
        } elseif ($role === "band") {
            // Bands can see their own events or those associated with their promoter
            $upcomingEvents = Event::where('event_date', '>', now())
                ->where('user_id', $user->id) // events created by this band
                ->orWhereIn('id', function ($query) use ($service) {
                    $query->select('event_id')
                        ->from('event_band')
                        ->where('band_id', $service->id); // events associated with the promoter
                })
                ->orderBy('event_date', 'asc')
                ->get();
        } else {
            // Default case for any other roles (if necessary)
            $upcomingEvents = Event::where('event_date', '>', now())
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
        switch ($dashboardType) {
            case 'promoter':
                $role = $user->promoters()->first();
                break;
            case 'band':
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

        return view('admin.dashboards.new-event', [
            'role' => $role,
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
        ]);
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

    public function storeNewEvent($dashboardType, Request $request)
    {
        $modules = collect(session('modules', []));

        try {
            $validatedData = $request->validate([
                'event_name' => 'required|string',
                'event_date' => 'required|date_format:d-m-Y',
                'event_start_time' => 'required|date_format:H:i',
                'event_end_time' => 'nullable|date_format:H:i',
                'event_description' => 'nullable',
                'facebook_event_url' => 'nullable|url',
                'ticket_url' => 'nullable|url',
                'otd_ticket_price' => 'required|numeric',
                'venue_id' => 'required|integer|exists:venues,id',
                'headliner' => 'required|string',
                'headliner_id' => 'required|integer',
                'mainSupport' => 'required|string',
                'main_support_id' => 'required|integer',
                'band' => 'nullable|array',
                'band.*' => 'nullable|string',
                'band_id' => 'required|array',
                'band_id.*' => 'required|integer',
                'opener' => 'nullable|string',
                'opener_id' => 'required|integer',
                'poster_url' => 'required|image|mimes:jpeg,jpg,png,webp,svg|max:5120'
            ]);

            $user = Auth::user()->load('roles');
            $role = $user->getRoleNames()->first();

            $bandsArray = [];

            if ($request->has('promoter_id')) {
                $promoter = Promoter::find($request->promoter_id);
            }

            if (!empty($request->headliner)) {
                $bandsArray[] = ['role' => 'Headliner', 'band_id' => $request->headliner_id];
            }

            if (!empty($request->mainSupport)) {
                $bandsArray[] = ['role' => 'Main Support', 'band_id' => $request->main_support_id];
            }

            if (!empty($request->band_id)) {
                foreach ($request->band_id as $bandId) {
                    if (!empty($bandId)) {
                        $bandsArray[] = ['role' => 'Band', 'band_id' => $bandId];
                    }
                }
            }

            if (!empty($request->opener)) {
                $bandsArray[] = ['role' => 'Opener', 'band_id' => $request->opener_id];
            }

            // Correct Event Start Date/Time
            $event_date = Carbon::createFromFormat('d-m-Y H:i:s', $validatedData['event_date'] . ' 00:00:00')->format('Y-m-d H:i:s');

            // Poster Upload
            $posterUrl = null;

            if ($request->hasFile('poster_url')) {
                $eventPosterFile = $request->file('poster_url');

                $eventName = $request->input('event_name');
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
                'on_the_door_ticket_price' => $validatedData['otd_ticket_price'],
            ]);

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
            if (isset($promoter)) {
                $event->promoters()->attach(
                    $promoter->id,
                    [
                        'event_id' => $event->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
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
                    case 'Band':
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
        $modules = collect(session('modules', []));

        $promoter = Auth::user()->promoters()->first();

        $event = Event::with(['promoters', 'venues', 'services'])->findOrFail($id);
        $eventDate = Carbon::parse($event->event_date)->toDateString();
        $eventTime = $event->event_start_time;
        $combinedDateTime = Carbon::parse($eventDate . ' ' . $eventTime)->format('Y-m-d\TH:i');
        $formattedEndTime = \Carbon\Carbon::parse($event->event_end_time)->format('H:i');
        $formattedEventDate = \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i');

        $bandRoles = json_decode($event->band_ids, true);

        $headlinerId = null;
        $mainSupportId = null;
        $openerId = null;
        $bands = [];

        // Iterate through the decoded roles and IDs
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
                case 'Band':
                    $bands[] = $band['band_id'];
                    break;
            }
        }

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

        return view('admin.dashboards.edit-event', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'event' => $event,
            'combinedDateTime' => $combinedDateTime,
            'promoter' => $promoter,
            'formattedEndTime' => $formattedEndTime,
            'formattedEventDate' => $formattedEventDate,
            'headliner' => $headliner,
            'mainSupport' => $mainSupport,
            'bandObjects' => $bandObjects,
            'opener' => $opener,
        ]);
    }

    public function updateEvent($dashboardType, StoreUpdateEventRequest $request, $eventId)
    {
        $modules = collect(session('modules', []));

        try {
            $user = Auth::user()->load('roles');
            $role = $user->getRoleNames()->first();

            // Find the existing event
            $event = Event::findOrFail($eventId);

            $bandsArray = [];

            if ($request->has('promoter_id')) {
                $promoter = Promoter::find($request->promoter_id);
            }

            if (!empty($request->headliner)) {
                $bandsArray[] = ['role' => 'Headliner', 'band_id' => $request->headliner_id];
            }

            if (!empty($request->mainSupport)) {
                $bandsArray[] = ['role' => 'Main Support', 'band_id' => $request->main_support_id];
            }

            if (!empty($request->band_id)) {
                foreach ($request->band_id as $bandId) {
                    if (!empty($bandId)) {
                        $bandsArray[] = ['role' => 'Band', 'band_id' => $bandId];
                    }
                }
            }

            if (!empty($request->opener)) {
                $bandsArray[] = ['role' => 'Opener', 'band_id' => $request->opener_id];
            }

            // Correct Event Start Date/Time
            $event_date = Carbon::createFromFormat('d-m-Y H:i:s', $request->event_date . ' 00:00:00')->format('Y-m-d H:i:s');

            // Poster Upload
            $posterUrl = $event->poster_url;

            if ($request->hasFile('poster_url')) {
                $eventPosterFile = $request->file('poster_url');

                $eventName = $request->input('event_name');
                $posterExtension = $eventPosterFile->getClientOriginalExtension() ?: $eventPosterFile->guessExtension();
                $posterFilename = Str::slug($eventName) . '_poster.' . $posterExtension; // Adding '_poster' to the filename

                // Specify the destination directory
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

            // Update the event
            $event->update([
                'user_id' => $user->id,
                'name' => $request->event_name,
                'event_date' => $event_date,
                'event_start_time' => $request->event_start_time,
                'event_end_time' => $request->event_end_time,
                'event_description' => $request->event_description,
                'facebook_event_url' => $request->facebook_event_url,
                'poster_url' => $posterUrl,
                'band_ids' => json_encode($bandsArray),
                'ticket_url' => $request->ticket_url,
                'on_the_door_ticket_price' => $request->otd_ticket_price,
            ]);

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

            // Update Event Venue
            if (isset($validatedData['venue_id'])) {
                // If the venue has changed, detach the old one and attach the new one
                if ($event->venues()->pluck('event_venue.id')->first() !== $validatedData['venue_id']) {
                    $event->venues()->sync([$validatedData['venue_id'] => ['event_id' => $event->id]]);
                }
            }

            // Update Event Promoter
            if (isset($promoter)) {
                // If the promoter has changed, detach the old one and attach the new one
                if (!$event->promoters()->where('event_promoter.id', $promoter->id)->exists()) {
                    $event->promoters()->sync([$promoter->id => ['event_id' => $event->id]]);
                }
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
}
