<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Note;
use App\Models\Todo;
use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Finance;
use App\Models\Promoter;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Models\PromoterReview;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class PromoterDashboardController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    /**
     * Return the Dashboard
     */
    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $promoter = $user->load('promoters');
        $role = $user->roles->first()->name;

        $pendingReviews = PromoterReview::with('promoter')->where('review_approved', '0')->whereNull('deleted_at')->count();
        $todoItemsCount = $promoter->promoters()->with(['todos' => function ($query) {
            $query->where('completed', 0)->whereNull('deleted_at');
        }])->get()->pluck('todos')->flatten()->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $eventsCount = $promoter->promoters()
            ->with(['events' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('event_date', [$startOfWeek, $endOfWeek]);
            }])
            ->get()
            ->pluck('events')
            ->flatten()
            ->count();

        return view('admin.dashboards.promoter-dash', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'pendingReviews' => $pendingReviews,
            'todoItemsCount' => $todoItemsCount,
            'eventsCount' => $eventsCount,
        ]);
    }

    /**
     * New Promoter - Linking to existing company/ create new one
     */
    public function searchExistingPromoters(Request $request)
    {
        $query = $request->input('query');
        $promoters = Promoter::where('name', 'LIKE', '%' . $query . '%')->get();

        return response()->json([
            'results' => $promoters,
            'count' => $promoters->count()
        ]);
    }

    public function linkToExistingPromoter(Request $request)
    {
        $serviceableId = $request->input('serviceable_id');
        $serviceableType = 'App\Models\Promoter';

        $user = auth()->user();
        $promoter = Promoter::find($serviceableId);

        if (!$promoter) {
            return response()->json(['error' => 'Promoter not found'], 404);
        }

        $existingUsersCount = DB::table('service_user')
            ->where('serviceable_id', $serviceableId)
            ->where('serviceable_type', $serviceableType)
            ->count();

        DB::table('service_user')->insert([
            'user_id' => $user->id,
            'serviceable_id' => $serviceableId,
            'serviceable_type' => $serviceableType,
            'role' => ($existingUsersCount == 0) ? 'Owner' : 'Standard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->load('roles');
        $userRole = $user->roles->first();


        if (!$userRole) {
            return response()->json(['error' => 'User role not found'], 404);
        }


        return response()->json([
            'redirect_url' => route('dashboard', ['dashboardType' => $userRole->name]),
            'message' => 'Successfully linked! Hold tight whilst we redirect you'
        ]);
    }

    public function showSinglePromoterEvent($id)
    {
        $promoter = Auth::user()->promoters()->first();
        $event = Event::with(['bands', 'promoters', 'venues'])->findOrFail($id);

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

        return view('admin.dashboards.promoter.promoter-show-single-event', compact(
            'promoter',
            'event',
            'headliner',
            'mainSupport',
            'otherBands',
            'opener',
            'eventStartTime',
            'eventEndTime'
        ));
    }

    public function editSinglePromoterEvent($id)
    {
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
                case 'Artist':
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

        return view('admin.dashboards.promoter.promoter-edit-event', [
            'event' => $event,
            'combinedDateTime' => $combinedDateTime,
            'promoter' => $promoter,
            'formattedEndTime' => $formattedEndTime,
            'formattedEventDate' => $formattedEventDate,
            'headliner' => $headliner,
            'mainSupport' => $mainSupport,
            'bandObjects' => $bandObjects,
            'opener' => $opener
        ]);
    }

    public function updateSinglePromoterEvent(Request $request, $id)
    {
        $promoter = Auth::user()->promoters()->first();

        $request->validate([
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
            'artist' => 'nullable|array',
            'band.*' => 'nullable|string',
            'band_id' => 'required|array',
            'band_id.*' => 'required|integer',
            'opener' => 'nullable|string',
            'opener_id' => 'required|integer',
            'poster_url' => 'required|image|mimes:jpeg,jpg,png,webp,svg|max:5120'
        ]);

        // Find the event to update
        $event = Event::findOrFail($id);

        // Only fill the fields that are present in the request
        $event->fill($request->only([
            'event_name',
            'event_start_time',
            'event_end_time',
            'event_description',
            'facebook_event_url',
            'ticket_url',
            'otd_ticket_price',
            'venue_id',
            'headliner',
            'headliner_id',
            'mainSupport',
            'main_support_id',
            'artist',
            'band.*',
            'band_id',
            'band_id.*',
            'opener',
            'opener_id',
            'poster_url'
        ]));

        // Poster Upload
        $posterUrl = null;

        if ($request->hasFile('poster_url')) {
            // Get the uploaded image file
            $eventPosterFile = $request->file('poster_url');

            // Generate a unique filename based on the event name and extension
            $eventName = $request->input('event_name');
            $posterExtension = $eventPosterFile->getClientOriginalExtension() ?: $eventPosterFile->guessExtension();
            $posterFilename = Str::slug($eventName) . '_poster.' . $posterExtension; // Adding '_poster' to the filename

            // Specify the destination directory
            $destinationPath = public_path('images/event_posters/' . $promoter->id); // Create a folder for the promoter

            // Check if the directory exists; if not, create it
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true); // Create directory with permissions
            }

            // Check if there is an existing poster to replace
            if ($event->poster_url) {
                // Generate old filename with date suffix
                $oldPosterFilename = pathinfo($event->poster_url, PATHINFO_FILENAME);
                $oldPosterExtension = pathinfo($event->poster_url, PATHINFO_EXTENSION);
                $dateSuffix = Carbon::now()->format('Y-m-d_H-i-s'); // Format: YYYY-MM-DD_HH-MM-SS
                $archivedPosterFilename = "{$oldPosterFilename}_old_{$dateSuffix}.{$oldPosterExtension}";

                // Move the old poster to the same directory with the new name
                File::move(public_path($event->poster_url), public_path('images/event_posters/' . $promoter->id . '/' . $archivedPosterFilename));
            }

            // Move the uploaded image to the specified directory
            $eventPosterFile->move($destinationPath, $posterFilename);

            // Construct the URL to the stored image
            $posterUrl = 'images/event_posters/' . $promoter->id . '/' . $posterFilename;
        }

        // Save the event only if there are changes
        if ($event->isDirty()) { // Checks if any attributes have been changed
            $event->save();
        }

        // Redirect back with a success message
        return redirect()->route('admin.dashboard.promoter.single-event.edit', $event->id)
            ->with('success', 'Event updated successfully!');
    }

    public function deleteSinglePromoterEvent($id)
    {
        $promoter = Auth::user()->promoters()->first();
        $event = Event::findOrFail($id);

        if ($event) {
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

    public function eventSelectVenue(Request $request)
    {
        $query = $request->input('query');

        if (!is_string($query) || strlen($query) < 3) {
            return response()->json([], 400); // Optionally return an empty array if query is too short
        }

        // Use the query builder to search directly in the database
        $venues = Venue::where('name', 'like', '%' . $query . '%')->get();

        return response()->json($venues);
    }

    /**
     * Functions for the Users Module on the Promoter Dashboard
     */
    public function promoterUsers()
    {
        $promoter = Auth::user()->promoters()->first();

        return view('admin.dashboards.promoter.promoter-users', compact('promoter'));
    }

    public function getPromoterusers()
    {
        $promoter = Auth::user()->promoters()->first();

        if ($promoter) {
            $users = $promoter->users;

            return response()->json($users);
        } else {
            return response()->json(['message' => 'No promoters associated with that user'], 404);
        }
    }

    public function newUser()
    {
        $promoter = Auth::user()->promoters()->first();

        return view('admin.dashboards.promoter.promoter-new-user', compact('promoter'));
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');

        // Adjust this query to fit your database schema
        $users = User::where('first_name', 'LIKE', "%{$query}%")
            // ->orWhere('email', 'LIKE', "%{$query}%")
            ->get();

        $promoterId = Auth::user()->promoters()->first()->id;

        $userLinks = DB::table('service_user')
            ->where('serviceable_id', $promoterId)
            ->where('serviceable_type', 'App\Models\Promoter')
            ->pluck('user_id')
            ->toArray();

        // Add linked status to each user
        $result = $users->map(function ($user) use ($userLinks) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'linked' => in_array($user->id, $userLinks),
            ];
        });

        return response()->json($result);
    }

    public function addUserToCompany(Request $request)
    {
        $userId = $request->input('user_id');
        $role = $request->input('role');
        $promoterId = $request->input('promoter_id');

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
            'promoter_id' => 'required|exists:promoters,id',
        ]);

        // Assuming you have a method to link the user to the promoter
        DB::table('service_user')->insert([
            'user_id' => $userId,
            'serviceable_id' => $promoterId,
            'serviceable_type' => 'App\Models\Promoter',
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return a success response with an appropriate HTTP status code
        return response()->json(['message' => 'User successfully added to the promotion company.'], 200);
    }

    public function deleteUserFromCompany(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'promoter_id' => 'required|exists:promoters,id',
            ]);

            if ((int) $validatedData['user_id'] === (int) Auth::user()->id) {
                return response()->json(['message' => 'You can\'t delete yourself. Please contact an administrator.'], 403);
            }

            // Perform the update operation
            DB::table('service_user')
                ->where('user_id', $validatedData['user_id'])
                ->where('serviceable_id', $validatedData['promoter_id'])
                ->update(['deleted_at' => now()]);


            return response()->json(['message' => 'User successfully removed from the promotion company.']);
        } catch (ValidationException $e) {

            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            return response()->json(['message' => 'An error occurred while removing the user.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Functions for the Budget Module on the Promoter Dashboard
     */
    public function promoterFinances()
    {
        $promoter = Auth::user()->load('promoters');

        return view('admin.dashboards.promoter.promoter-finances', compact('promoter'));
    }

    public function createNewPromoterBudget()
    {
        $promoter = Auth::user();

        return view('admin.dashboards.promoter.promoter-new-finance', compact('promoter'));
    }

    public function saveNewPromoterBudget(Request $request)
    {
        $promoter = Auth::user()->load('promoters');
        $promoterCompany = $promoter->promoters()->first();

        try {
            $validator = Validator::make($request->all(), [
                'desired_profit' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'budget_name' => 'required|string',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'link_to_event' => 'nullable|url',
                'income_presale' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'income_otd' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'income_other' => 'array|nullable',
                'income_other.*' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_venue' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_band' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_promotion' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_rider' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_other' => 'array|nullable',
                'outgoing_other.*' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'income_total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'profit_total' => 'required|numeric',
                'desired_profit_remaining' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 422);
            }

            $desiredProfit = $validator->validated()['desired_profit'];
            $budgetName = $validator->validated()['budget_name'];
            $dateFrom = $validator->validated()['date_from'];
            $dateTo = $validator->validated()['date_to'];
            $linkToEvent = $validator->validated()['link_to_event'];
            $incomePresale = $validator->validated()['income_presale'];
            $incomeOtd = $validator->validated()['income_otd'];
            $incomeOthers = $request->input('income_other', []);
            $outgoingVenue = $validator->validated()['outgoing_venue'];
            $outgoingBand = $validator->validated()['outgoing_band'];
            $outgoingPromotion = $validator->validated()['outgoing_promotion'];
            $outgoingRider = $validator->validated()['outgoing_rider'];
            $outgoingOthers = $request->input('outgoing_other', []);
            $incomeTotal = $validator->validated()['income_total'];
            $outgoingTotal = $validator->validated()['outgoing_total'];
            $profitTotal = $validator->validated()['profit_total'];
            $desiredProfitRemaining = $validator->validated()['desired_profit_remaining'];

            $incoming = json_encode([
                [
                    'field' => 'income_presale',
                    'value' => $incomePresale,
                ],
                [
                    'field' => 'income_otd',
                    'value' => $incomeOtd,
                ]
            ]);

            $outgoing = json_encode([
                [
                    'field' => 'outgoing_venue',
                    'value' => $outgoingVenue,
                ],
                [
                    'field' => 'outgoing_band',
                    'value' => $outgoingBand,
                ],
                [
                    'field' => 'outgoing_promotion',
                    'value' => $outgoingPromotion,
                ],
                [
                    'field' => 'outgoing_rider',
                    'value' => $outgoingRider,
                ],
            ]);

            $totalIncomeOther = array_sum($incomeOthers);
            $totalOutgoingOther = array_sum($outgoingOthers);
            $incomeOthersJson = json_encode($incomeOthers);
            $outgoingOthersJson = json_encode($outgoingOthers);

            $serviceableId = null;
            $serviceableType = null;

            if ($promoterCompany) {
                $serviceableId = $promoterCompany->id;
                $serviceableType = 'App\Models\Promoter';
            }

            $newPromoterBudget = Finance::create([
                'user_id' => $promoter->id,
                'serviceable_id' => $serviceableId,
                'serviceable_type' => $serviceableType,
                'finance_type' => 'Budget',
                'name' => $budgetName,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'external_link' => $linkToEvent,
                'incoming' => $incoming,
                'other_incoming' => $incomeOthersJson,
                'outgoing' => $outgoing,
                'other_outgoing' => $outgoingOthersJson,
                'desired_profit' => $desiredProfit,
                'total_incoming' => $incomeTotal,
                'total_outgoing' => $outgoingTotal,
                'total_profit' => $profitTotal,
                'total_remaining_to_desired_profit' => $desiredProfitRemaining,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your Budget Saved!',
                'view' => view(
                    'admin.dashboards.promoter.promoter-finances',
                    compact('promoter')
                )
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving budget:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getFinanceData(Request $request)
    {
        $serviceableId = $request->input('serviceable_id');
        $serviceableType = \App\Models\Promoter::class;;
        $filter = $request->input('filter');
        $date = $request->input('date');

        // Initialize response arrays
        $dates = [];
        $totalIncome = [];
        $totalOutgoing = [];
        $totalProfit = [];
        $financeRecords = [];

        // Based on the filter, adjust query for finances table
        $query = Finance::where('serviceable_id', $serviceableId)
            ->where('serviceable_type', $serviceableType);

        switch ($filter) {
            case 'day':
                $finances = Finance::where('serviceable_id', $serviceableId)
                    ->where('serviceable_type', $serviceableType)
                    ->whereDate('date_from', $date)
                    ->get();

                // Get totals for a single day
                $data = Finance::select(
                    DB::raw('SUM(total_incoming) as totalIncome'),
                    DB::raw('SUM(total_outgoing) as totalOutgoing'),
                    DB::raw('SUM(total_profit) as totalProfit')
                )
                    ->where('serviceable_id', $serviceableId)
                    ->where('serviceable_type', $serviceableType)
                    ->whereDate('date_from', $date)
                    ->first();

                $financeRecords = $finances->map(function ($finance) {
                    return [
                        'id' => $finance->id,
                        'name' => $finance->name,
                        'link' => route('promoter.dashboard.finances.show', $finance->id),
                    ];
                });

                // Format the data for response
                $response = [
                    'dates' => [$date], // Just the selected day
                    'totalIncome' => [$data->totalIncome ?: 0], // Wrap in array to match structure
                    'totalOutgoing' => [$data->totalOutgoing ?: 0],
                    'totalProfit' => [$data->totalProfit ?: 0],
                    'financeRecords' => $financeRecords,
                ];
                return response()->json($response);
                break;
            case 'week':
                $dates = explode(' to ', $date); // Split the date range

                if (count($dates) === 2) {
                    $startDate = Carbon::parse($dates[0])->startOfDay();
                    $endDate = Carbon::parse($dates[1])->endOfDay();

                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date_from', [$startDate, $endDate])
                            ->orWhereBetween('date_to', [$startDate, $endDate]);
                    });
                }

                $finances = $query->get();
                $financeRecords = $finances->map(function ($finance) {
                    return [
                        'id' => $finance->id,
                        'name' => $finance->name,
                        'link' => route('promoter.dashboard.finances.show', $finance->id),
                    ];
                });

                // Prepare the response with the appropriate data for your charts
                return response()->json([
                    'dates' => $finances->pluck('date_from'),
                    'totalIncome' => $finances->pluck('total_incoming'),
                    'totalOutgoing' => $finances->pluck('total_outgoing'),
                    'totalProfit' => $finances->pluck('total_profit'),
                    'financeRecords' => $financeRecords,
                ]);
                break;
            case 'month':
                // Gather data for each day of the month
                $startOfMonth = Carbon::parse($date)->startOfMonth();
                $endOfMonth = Carbon::parse($date)->endOfMonth();

                // Fetch daily totals for the month
                $data = Finance::select(DB::raw('DATE(date_from) as date, 
                SUM(total_incoming) as totalIncome,
                SUM(total_outgoing) as totalOutgoing,
                SUM(total_profit) as totalProfit'))
                    ->where('serviceable_id', $serviceableId)
                    ->where('serviceable_type', $serviceableType)
                    ->whereBetween('date_from', [$startOfMonth, $endOfMonth])
                    ->groupBy(DB::raw('DATE(date_from)'))
                    ->get();

                $finances = Finance::where('serviceable_id', $serviceableId)
                    ->where('serviceable_type', $serviceableType)
                    ->whereBetween('date_from', [$startOfMonth, $endOfMonth])
                    ->get();

                $financeRecords = $finances->map(function ($finance) {
                    return [
                        'id' => $finance->id,
                        'name' => $finance->name,
                        'link' => route('promoter.dashboard.finances.show', $finance->id),
                    ];
                });

                foreach ($data as $entry) {
                    $dates[] = $entry->date;
                    $totalIncome[] = $entry->totalIncome ?: 0; // Use 0 if null
                    $totalOutgoing[] = $entry->totalOutgoing ?: 0; // Use 0 if null
                    $totalProfit[] = $entry->totalProfit ?: 0; // Use 0 if null
                }

                // Format the data for response
                $response = [
                    'dates' => $data->pluck('date'),
                    'totalIncome' => $data->pluck('totalIncome'),
                    'totalOutgoing' => $data->pluck('totalOutgoing'),
                    'totalProfit' => $data->pluck('totalProfit'),
                    'financeRecords' => $financeRecords,
                ];
                return response()->json($response);
                break;
            case 'year':
                // Get start and end of the year
                $startOfYear = Carbon::parse($date)->startOfYear();
                $endOfYear = Carbon::parse($date)->endOfYear();

                // Fetch monthly totals for the year
                $data = Finance::select(DB::raw('MONTH(date_from) as month, 
                        SUM(total_incoming) as totalIncome,
                        SUM(total_outgoing) as totalOutgoing,
                        SUM(total_profit) as totalProfit'))
                    ->where('serviceable_id', $serviceableId)
                    ->where('serviceable_type', $serviceableType)
                    ->whereBetween('date_from', [$startOfYear, $endOfYear])
                    ->groupBy(DB::raw('MONTH(date_from)'))
                    ->get();

                // Format the response
                $response = [
                    'dates' => $data->pluck('month'),
                    'totalIncome' => $data->pluck('totalIncome'),
                    'totalOutgoing' => $data->pluck('totalOutgoing'),
                    'totalProfit' => $data->pluck('totalProfit'),
                ];
                return response()->json($response);
                break;
        }
        $finances = $query->get();

        // Calculate totals
        $totalIncome = $finances->sum('total_incoming');
        $totalOutgoing = $finances->sum('total_outgoing');
        $totalProfit = $finances->sum('total_profit');

        // Getting list of finance data for link generation
        $financeRecords = $finances->map(function ($finance) {
            return [
                'id' => $finance->id,
                'name' => $finance->name,
                'link' => route('promoter.dashboard.finances.show', $finance->id),
            ];
        });

        return response()->json([
            'totalIncome' => $totalIncome,
            'totalOutgoing' => $totalOutgoing,
            'totalProfit' => $totalProfit,
            'financeRecords' => $financeRecords,
        ]);
    }

    public function exportFinances(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|string',
            'filter' => 'required|string',
            'totalIncome' => 'required|string',
            'totalOutgoing' => 'required|string',
            'totalProfit' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the data from the request
        $dateRange = $request->input('date');
        $filterValue = $request->input('filter');
        $totalIncome = $request->input('totalIncome');
        $totalOutgoing = $request->input('totalOutgoing');
        $totalProfit = $request->input('totalProfit');

        // Convert inputs to arrays if necessary
        $totalIncome = is_array($totalIncome) ? $totalIncome : [$totalIncome];
        $totalOutgoing = is_array($totalOutgoing) ? $totalOutgoing : [$totalOutgoing];
        $totalProfit = is_array($totalProfit) ? $totalProfit : [$totalProfit];

        // Prepare the data for the PDF
        $data = [];

        if ($filterValue === 'day') {
            // Handle single day case
            $data[] = [
                'date' => $dateRange,
                'totalIncome' => $totalIncome[0],
                'totalOutgoing' => $totalOutgoing[0],
                'totalProfit' => $totalProfit[0],
            ];
        } elseif ($filterValue === 'week') {
            // Handle week case
            $dates = explode(' to ', $dateRange);

            if (count($dates) !== 2) {
                return response()->json(['errors' => ['Invalid date range format']], 422);
            }

            list($startDate, $endDate) = $dates;

            // Validate the dates
            if (!strtotime($startDate) || !strtotime($endDate)) {
                return response()->json(['errors' => ['Invalid date format']], 422);
            }

            $currentDate = strtotime($startDate);
            $endDateTimestamp = strtotime($endDate);

            while ($currentDate <= $endDateTimestamp) {
                $formattedDate = date('Y-m-d', $currentDate);
                $data[] = [
                    'date' => $formattedDate,
                    'totalIncome' => $totalIncome[0],
                    'totalOutgoing' => $totalOutgoing[0],
                    'totalProfit' => $totalProfit[0],
                ];
                $currentDate = strtotime('+1 day', $currentDate);
            }
        } elseif ($filterValue === 'month') {
            // Handle month case
            $month = $dateRange; // This should be in 'YYYY-MM' format
            $year = substr($month, 0, 4);
            $monthNumber = substr($month, 5, 2);

            // Get the total days in the month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $formattedDate = sprintf('%04d-%02d-%02d', $year, $monthNumber, $day);
                $data[] = [
                    'date' => $formattedDate,
                    'totalIncome' => $totalIncome[0],
                    'totalOutgoing' => $totalOutgoing[0],
                    'totalProfit' => $totalProfit[0],
                ];
            }
        } elseif ($filterValue === 'year') {
            // Handle year case
            $year = $dateRange; // This should be a year in 'YYYY' format

            // Validate the year
            if (!preg_match('/^\d{4}$/', $year)) {
                return response()->json(['errors' => ['Invalid year format']], 422);
            }

            // Loop through each month of the year
            for ($month = 1; $month <= 12; $month++) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $totalIncomeForMonth = $totalIncome[0]; // Adjust if you want to calculate per month
                $totalOutgoingForMonth = $totalOutgoing[0]; // Adjust if you want to calculate per month
                $totalProfitForMonth = $totalProfit[0]; // Adjust if you want to calculate per month

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $formattedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $data[] = [
                        'date' => $formattedDate,
                        'totalIncome' => $totalIncomeForMonth,
                        'totalOutgoing' => $totalOutgoingForMonth,
                        'totalProfit' => $totalProfitForMonth,
                    ];
                }
            }
        }

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.finances', compact('data'));
        $pdfContent = $pdf->output();

        // Return the PDF to the browser
        return response()->stream(function () use ($pdfContent) {
            echo $pdfContent;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="finances_graph_data.pdf"',
        ]);
    }

    public function showSingleFinance($id)
    {
        $promoter = Auth::user()->load('promoters');

        $finance = Finance::findOrFail($id)->load('user', 'serviceable');
        return view('admin.dashboards.promoter.promoter-show-single-finance', compact('finance', 'promoter'));
    }

    public function editSingleFinance($id)
    {
        $promoter = Auth::user()->load('promoters');
        $finance = Finance::findOrFail($id)->load('user', 'serviceable');

        return view('admin.dashboards.promoter..promoter-edit-single-finance', compact('finance', 'promoter'));
    }

    public function updateSingleFinance(Request $request, $id)
    {
        try {
            $promoter = Auth::user()->load('promoters');
            $finance = Finance::findOrFail($id)->load('user', 'serviceable');

            // Validate the input
            $validator = Validator::make($request->all(), [
                'desired_profit' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'name' => 'required|string',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'external_link' => 'nullable|url',
                'income_presale' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'income_otd' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'income_other' => 'array|nullable',
                'income_other.*' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_venue' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_band' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_promotion' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_rider' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_other' => 'array|nullable',
                'outgoing_other.*' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'income_total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'outgoing_total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
                'profit_total' => 'required|numeric',
                'desired_profit_remaining' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 422);
            }

            // Update the record with validated data
            $finance->update($validator);

            // Redirect back to the view page or another location
            return redirect()
                ->route('finances.show', $finance->id)
                ->with('success', 'Finance record updated successfully!')
                ->with('promoter', $promoter);
        } catch (\Exception $e) {
            Log::error('Error updating budget:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function exportSingleFinanceRecord($id)
    {
        $financeRecord = Finance::findOrFail($id);

        // Generate the PDF
        $pdf = PDF::loadView('pdf.finances-single', ['finance' => $financeRecord]);

        // Download the PDF file
        return $pdf->download('finance_record_' . $id . '.pdf');
    }

    /**
     * Functions for the Todo Module on the Promoter Dashboard
     */
    public function showPromoterTodos(Request $request)
    {
        $promoter = Auth::user()->load('promoters');

        // Get the promoter's company
        $promoterCompany = $promoter->promoters;
        $serviceableId = $promoterCompany->pluck('id');


        $perPage = 6;
        $page = $request->input('page', 1);

        // Fetch the todo items
        $todoItems = Todo::whereIn('serviceable_id', $serviceableId)
            ->where('completed', false)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return view('admin.dashboards.promoter.promoter-todo-list', compact('promoter', 'todoItems'));
    }

    public function getPromoterTodos(Request $request)
    {
        $promoter = Auth::user()->load('promoters');

        $promoterCompany = $promoter->promoters;
        $serviceableId = $promoterCompany->pluck('id');

        if ($promoterCompany->isEmpty()) {
            return response()->json([
                'view' => view('components.todo-items', ['todoItems' => collect()])->render(),
                'hasMore' => false,
            ]);
        }

        $perPage = 6;
        $page = $request->input('page', 1);

        $todoItems = Todo::whereIn('serviceable_id', $serviceableId)
            ->where('completed', false)
            ->where('completed_at', null)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return response()->json([
            'view' => view('components.todo-items', compact('todoItems'))->render(),
            'hasMore' => $todoItems->hasMorePages(),
        ]);
    }

    public function addNewTodoItem(Request $request)
    {
        $request->validate([
            'task' => 'required|string'
        ]);

        $todoItem = Todo::create([
            'user_id' => Auth::user()->id,
            'serviceable_id' => Auth::user()->promoters->first()->id,
            'serviceable_type' => 'App\Models\Promoter',
            'item' => $request->task,
        ]);

        return response()->json([
            'message' => 'Item Added Successfully',
            'todoItem' => $todoItem,
        ]);
    }

    public function completeTodoItem($id)
    {
        // Find the todo item by ID
        $todoItem = Todo::findOrFail($id);

        // Mark the item as completed
        $todoItem->completed = true;
        $todoItem->completed_at = now();
        $todoItem->save();

        // Return a success response
        return response()->json([
            'message' => 'Todo item marked as completed!',
            'todoItem' => $todoItem,
        ]);
    }

    public function deleteTodoItem($id)
    {
        // Find the todo item by ID
        $todoItem = Todo::findOrFail($id);

        // Delete the todo item
        $todoItem->delete();

        // Return a success response
        return response()->json([
            'message' => 'Todo item deleted successfully!',
        ]);
    }

    public function showCompletedTodoItems()
    {
        $promoter = Auth::user()->load('promoters');

        $promoterCompany = $promoter->promoters;
        $serviceableId = $promoterCompany->pluck('id');

        $completedTodos = Todo::whereIn('serviceable_id', $serviceableId)
            ->where('completed', true)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return response()->json([
            'view' => view('components.todo-items', ['todoItems' => $completedTodos])->render(),
            'hasMore' => $completedTodos->hasMorePages(),
        ]);
    }

    public function uncompleteTodoItem($id)
    {
        // Find the todo item by ID
        $todoItem = Todo::findOrFail($id);

        // Mark the item as completed
        $todoItem->completed = false;
        $todoItem->completed_at = null;
        $todoItem->save();

        // Return a success response
        return response()->json([
            'message' => 'Todo item marked as uncompleted!',
            'todoItem' => $todoItem,
        ]);
    }

    /**
     * Promoter Notes
     */
    public function showPromoterNotes(Request $request)
    {
        $promoter = Auth::user()->load(['promoters']);

        // Get the promoter's company
        $promoterCompany = $promoter->promoters;
        $serviceableId = $promoterCompany->pluck('id');

        $perPage = 6;
        $page = $request->input('page', 1);

        // Fetch the todo items
        $notes = Note::whereIn('serviceable_id', $serviceableId)
            ->where('completed', 0)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return view('admin.dashboards.promoter.promoter-notes', compact('promoter', 'notes'));
    }

    public function getPromoterNotes(Request $request)
    {
        $promoter = Auth::user()->load(['promoters']);

        $promoterCompany = $promoter->promoters;
        $serviceableId = $promoterCompany->pluck('id');

        if ($promoterCompany->isEmpty()) {
            return response()->json([
                'view' => view('components.note-items', ['notes' => collect()])->render(),
                'hasMore' => false,
            ]);
        }

        $perPage = 6;
        $page = $request->input('page', 1);

        $notes = Note::whereIn('serviceable_id', $serviceableId)
            ->where('completed', false)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return response()->json([
            'view' => view('components.note-items', compact('notes'))->render(),
            'hasMore' => $notes->hasMorePages(),
        ]);
    }

    public function completeNoteItem($id)
    {
        // Find the todo item by ID
        $note = Note::findOrFail($id);

        // Mark the item as completed
        $note->completed = true;
        $note->completed_at = now();
        $note->save();

        // Return a success response
        return response()->json([
            'message' => 'Note marked as completed!',
            'note' => $note,
        ]);
    }

    public function deleteNoteItem($id)
    {
        // Find the todo item by ID
        $note = Note::findOrFail($id);

        // Delete the todo item
        $note->delete();

        // Return a success response
        return response()->json([
            'message' => 'Note deleted successfully!',
        ]);
    }

    public function showCompletedNoteItems()
    {
        $promoter = Auth::user()->load(['promoters']);

        $promoterCompany = $promoter->promoters;
        $serviceableId = $promoterCompany->pluck('id');

        $completedNotes = Note::whereIn('serviceable_id', $serviceableId)
            ->where('completed', true)
            ->orderBy('created_at', 'DESC')
            ->paginate(6);

        return response()->json([
            'view' => view('components.note-items', ['notes' => $completedNotes])->render(),
            'hasMore' => $completedNotes->hasMorePages(),
        ]);
    }

    /**
     * Creating New Promoter
     */
    public function storeNewPromoter(Request $request)
    {
        try {
            // Validation
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'address-input' => 'required',
                'postal-town-input' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'promoter_logo' => 'nullable|mimes:jpeg,jpg,png,webp,svg|max:2048',
                'description' => 'required',
                'my_venues' => 'nullable',
                'genres' => 'required|array',
                'band_type' => 'required|array',
                'contact_name' => 'required_if:is_main_contact,false',
                'contact_number' => 'numeric|digits:11',
                'contact_email' => 'email|max:255',
                'contact_link' => 'nullable',
                'is_main_contact' => 'required|string'
            ]);

            $logoUrl = null; // Initialize logo URL

            if ($request->hasFile('promoter_logo')) {
                // Get the uploaded image file
                $promoterLogoFile = $request->file('promoter_logo');

                // Generate a unique filename based on the promoter's name and extension
                $promoterName = $request->input('name');
                $promoterLogoExtension = $promoterLogoFile->getClientOriginalExtension() ?: $promoterLogoFile->guessExtension();
                $promoterLogoFilename = Str::slug($promoterName) . '.' . $promoterLogoExtension;

                // Specify the destination directory within the public folder
                $destinationPath = 'images/promoters_logos';

                // Move the uploaded image to the specified directory
                $promoterLogoFile->move(public_path($destinationPath), $promoterLogoFilename);

                // Construct the URL to the stored image
                $logoUrl = $destinationPath . '/' . $promoterLogoFilename;
            }

            // Create or update the promoter
            if ($validatedData['is_main_contact'] == "true") {
                $user = Auth::user();
                $contactName = $user->name;
            } else {
                $contactName = $validatedData['contact_name'];
            }

            if (is_null($logoUrl)) {
                $logoUrl = asset('storage/images/system/yns_logo.png');
            }

            $contactLinks = explode(',', $validatedData['contact_link']);
            $contactLinks = array_map('trim', $contactLinks);
            $platformLinks = [];

            $platformsToCheck = ['facebook', 'x', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

            foreach ($contactLinks as $link) {
                $matchedPlatform = 'Unknown';

                foreach ($platformsToCheck as $platform) {
                    if (stripos($link, $platform) !== false) {
                        $matchedPlatform = $platform;
                        break;
                    }
                }

                if ($matchedPlatform === 'Unknown') {
                    $matchedPlatform = 'website';
                }

                $platformLinks[$matchedPlatform][] = trim($link);
            }

            // Save the promoter data to the database
            $promoter = Promoter::create([
                'name' => $validatedData['name'],
                'location' => $validatedData['address-input'],
                'postal_town' => $validatedData['postal-town-input'],
                'longitude' => $validatedData['longitude'],
                'latitude' => $validatedData['latitude'],
                'logo_url' => $logoUrl,
                'description' => $validatedData['description'],
                'my_venues' => json_encode($validatedData['my_venues']),
                'genre' => json_encode($validatedData['genres']),
                'band_type' => json_encode($validatedData['band_type']),
                'contact_name' => $contactName,
                'contact_number' => $validatedData['contact_number'],
                'contact_email' => $validatedData['contact_email'],
                'contact_link' => json_encode($platformLinks),
            ]);

            // Log success
            Log::info('Promoter created successfully', [
                'promoter_id' => $promoter->id,
                'name' => $validatedData['name'],
                'contact_name' => $contactName
            ]);

            $serviceableId = $promoter->id;
            $serviceableType = 'App\Models\Promoter';

            DB::table('service_user')->insert([
                'user_id' => $user->id,
                'serviceable_id' => $serviceableId,
                'serviceable_type' => $serviceableType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Promoter Link created successfully');

            // Redirect to the promoter dashboard with success message
            return redirect()->route('promoter.dashboard', compact('promoter'))
                ->with('success', 'Promoter created successfully.');
        } catch (ValidationException $e) {
            // Handle validation exceptions
            Log::error('Validation failed for promoter creation', [
                'errors' => $e->validator->errors(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Handle other exceptions
            Log::error('Failed to create promoter', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Failed to create promoter. Please try again.')
                ->withInput();
        }
    }

    /**
     * Promoter Reviews
     */
    public function getPromoterReviews($filter = 'all')
    {
        $promoter = Auth::user()->load(['promoters']);

        switch ($filter) {
            case 'pending':
                $filter = 'pending';
                break;
            case 'all':
                $filter = 'all';
                break;

                dd($filter);
        }

        return view('admin.dashboards.promoter.promoter-reviews', compact('promoter', 'filter'));
    }

    public function fetchReviews($filter = 'all')
    {
        // Fetch the reviews based on the filter
        $reviews = ($filter === 'pending')
            ? PromoterReview::where('review_approved', 0)->get()
            : PromoterReview::all();

        return response()->json(['reviews' => $reviews]);
    }

    public function showAllPromoterReviews()
    {
        $allReviews = PromoterReview::get();

        return response()->json(['reviews' => $allReviews]);
    }

    public function showPendingPromoterReviews()
    {
        $pendingReviews = PromoterReview::where('review_approved', 0)->get();

        return response()->json(['reviews' => $pendingReviews]);
    }

    public function approveDisplayPromoterReview($reviewId)
    {
        $review = PromoterReview::findOrFail($reviewId);

        if ($review) {
            $review->review_approved = true;
            $review->display = true;
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review displayed successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Review not found']);
    }

    public function approvePromoterReview($reviewId)
    {
        $review = PromoterReview::findOrFail($reviewId);

        if ($review) {
            $review->review_approved = true;
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review approved successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Review not found']);
    }

    public function displayPromoterReview($reviewId)
    {
        $review = PromoterReview::findOrFail($reviewId);

        if ($review) {
            $review->display = true;
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review displayed successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Review not found']);
    }

    public function hidePromoterReview($reviewId)
    {
        $review = PromoterReview::findOrFail($reviewId);

        if ($review) {
            $review->display = false;
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review hidden successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Review not found']);
    }

    public function unapprovePromoterReview($reviewId)
    {

        $review = PromoterReview::findOrFail($reviewId);

        if ($review) {
            $review->review_approved = false;
            $review->display = false;
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review unnapproved successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Review not found']);
    }

    public function deletePromoterReview($reviewId)
    {
        $review = PromoterReview::find($reviewId);
        if ($review) {
            $review->delete();
            return response()->json(['success' => true, 'message' => 'Review deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Review not found']);
    }
}