<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Venue;
use App\Models\VenueReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VenueDashboardController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $venue = $user->load('venues');
        $role = $venue->roles->first()->name;

        $pendingReviews = VenueReview::with('venue')->where('review_approved', '0')->whereNull('deleted_at')->count();
        $todoItemsCount = $venue->venues()->with(['todos' => function ($query) {
            $query->where('completed', 0)->whereNull('deleted_at');
        }])->get()->pluck('todos')->flatten()->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $eventsCount = $venue->venues()
            ->with(['events' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('event_date', [$startOfWeek, $endOfWeek]);
            }])
            ->get()
            ->pluck('events')
            ->flatten()
            ->count();


        return view('admin.dashboards.venue-dash', [
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

    public function searchVenue(Request $request)
    {
        $query = $request->input('query');
        $promoters = Venue::where('name', 'LIKE', '%' . $query . '%')->get();

        return response()->json([
            'results' => $promoters,
            'count' => $promoters->count()
        ]);
    }

    public function linkVenue(Request $request)
    {
        $serviceableId = $request->input('serviceable_id');
        $serviceableType = 'App\Models\Venue';

        $user = auth()->user();
        $promoter = Venue::find($serviceableId);

        if (!$promoter) {
            return response()->json(['error' => 'Venue not found'], 404);
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
}