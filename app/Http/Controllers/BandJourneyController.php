<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\JoinBandRequest;

class BandJourneyController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $bands = OtherService::bands()->get();

        return view('admin.dashboards.band.band-journey', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'bands' => $bands,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $artists = OtherService::where('other_service_id', 4)
                ->where('name', 'like', '%' . $query . '%')
                ->get();
        } else {
            $artists = OtherService::where('other_service_id', 4)
                ->limit(8)
                ->get();
        }

        $html = '';
        foreach ($artists as $artist) {
            $html .= view('admin.dashboards.partials.band-row', compact('artist'))->render();
        }

        return response()->json(['html' => $html]);
    }


    public function joinBand($dashboardType, Request $request)
    {
        $artistId = $request->input('serviceable_id');
        $user = Auth::user();

        // Check if the band exists
        $band = OtherService::find($artistId);

        if (!$band) {
            return response()->json([
                'success' => false,
                'message' => 'The artist does not exist.'
            ], 404);
        }

        // Check if the user is already part of the band
        if ($user->otherService('artist')->where('serviceable_id', $artistId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this artist.'
            ], 400);
        }

        // Add the user to the band
        $user->otherService('artist')->attach($artistId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully linked!',
            'redirect' => route('dashboard', ['dashboardType' => $dashboardType])
        ], 200);
    }

    public function createBand(Request $request)
    {
        $dashboardType = 'Artist';
        $platformsJson = determinePlatform($request->input('contact_link'));
        // Validate and create a new band
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'postal_town' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'description' => 'required|string|max:255',
            'contact_name' => 'required',
            'contact_number' => 'required',
            'contact_email' => 'required',
            'contact_link' => 'required',
        ]);

        // Create new band in the OtherService model
        try {
            // Create new band in the OtherService model
            $band = OtherService::create([
                'name' => $request->name,
                'location' => $request->location,
                'other_service_id' => 4,
                'postal_town' => $request->postal_town,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'description' => $request->description,
                'contact_name' => $request->contact_name,
                'contact_number' => $request->contact_number,
                'contact_email' => $request->contact_email,
                'contact_link' => $platformsJson,
                'services' => 'Artist',
                'band_type'  =>  [],
                'genre' => "[]",
            ]);

            if (!$band) {
                logger()->error('Band creation failed');
                return back()->withErrors(['error' => 'Failed to create the band']);
            }

            // Associate the user with the new band
            $user = auth()->user();
            if (!$user) {
                logger()->error('No authenticated user found');
                return back()->withErrors(['error' => 'No authenticated user']);
            }

            $user->otherService()->attach($band->id);

            return redirect()->route('dashboard', $dashboardType)->with('success', 'Successfully created and joined the new artist!');
        } catch (\Exception $e) {
            logger()->error('Error in createBand:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Something went wrong. We\'ve logged the error and will fix it soon.']);
        }
    }
}