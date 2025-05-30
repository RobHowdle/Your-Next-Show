<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VenueJourneyController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $venues = Venue::get();


        return view('admin.dashboards.venue.venue-journey', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'venues' => $venues,
        ]);
    }

    public function searchVenue(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $venues = Venue::where('name', 'like', '%' . $query . '%')
                ->get();
        } else {
            $venues = Venue::where('name', 'like', '%' . $query . '%')
                ->limit(8)
                ->get();
        }

        $html = '';
        foreach ($venues as $venue) {
            $html .= view('admin.dashboards.partials.venue-row', compact('venue'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function joinVenue($dashboardType, Request $request)
    {
        $serviceableId = $request->input('serviceable_id');

        $user = auth()->user();
        $venue = Venue::find($serviceableId);

        if (!$venue) {
            return response()->json([
                'success' => false,
                'message' => 'Venue not found'
            ], 404);
        }

        if ($user->venues()->where('venues.id', $serviceableId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already linked to this venue'
            ], 400);
        }

        // Check if this is the first user for this venue
        $isFirstUser = !$venue->users()->exists();

        // Get the appropriate role ID
        $roleId = $isFirstUser
            ? Role::where('name', 'service-owner')->first()->id
            : Role::where('name', 'service-member')->first()->id;

        // Attach user to venue with the role
        $user->venues()->attach($serviceableId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'role_id' => $roleId
        ]);

        $venue->update([
            'is_verified' => 1,
            'verified_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully linked!',
            'redirect' => route('dashboard', ['dashboardType' => $dashboardType])
        ], 200);
    }

    public function createVenue(Request $request)
    {
        $dashboardType = 'Venue';
        $platformsJson = determinePlatform($request->input('contact_link'));

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'postal_town' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'description' => 'required|string|max:255',
            'capacity' => 'required',
            'in_house_gear' => 'required',
            'contact_name' => 'required',
            'contact_number' => 'required',
            'contact_email' => 'required',
            'contact_link' => 'required',
        ]);

        try {
            $venue = Venue::create([
                'name' => $request->name,
                'location' => $request->location,
                'postal_town' => $request->postal_town,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'capacity' => $request->capacity,
                'in_house_gear' => $request->in_house_gear,
                'description' => $request->description,
                'contact_name' => $request->contact_name,
                'contact_number' => $request->contact_number,
                'contact_email' => $request->contact_email,
                'contact_link' => $platformsJson,
                'band_type'  =>  json_encode([]),
                'genre' => json_encode([]),
            ]);

            if (!$venue) {
                logger()->error('Failed to create venue');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create venue'
                ], 400);
            }

            $user = auth()->user();
            if (!$user) {
                logger()->error('No authenticated user found');
                return back()->withErrors(['error' => 'No authenticated user']);
            }

            $user->venues()->attach($venue->id, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return redirect()->route('dashboard', ['dashboardType' => $dashboardType])->with('success', 'Venue created successfully and joined!');
        } catch (\Exception $e) {
            logger()->error('Failed to create venue', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Something went wrong. We\'ve logged the error and will fix it soon.']);
        }
    }
}