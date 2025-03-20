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

        // Get and parse genres with error handling
        try {
            $genreList = file_get_contents(public_path('text/genre_list.json'));
            $data = json_decode($genreList, true);

            // Ensure we have a valid array of genres
            $genres = isset($data['genres']) && is_array($data['genres'])
                ? array_values($data['genres'])
                : $this->getDefaultGenres();
        } catch (\Exception $e) {
            \Log::error('Error loading genres: ' . $e->getMessage());
            $genres = $this->getDefaultGenres();
        }

        return view('admin.dashboards.band.band-journey', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'bands' => $bands,
            'genres' => $genres
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

        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'postal_town' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'description' => 'required|string',
                'contact_name' => 'required',
                'contact_number' => 'required',
                'contact_email' => 'required|email',
                'contact_link' => 'required|string',
                'band_type' => 'required|string',
                'genres' => 'required|string'
            ]);

            // Format band type to match DB structure ["original-bands"]
            $bandType = [$validated['band_type'] . '-bands'];

            // Process social media links
            $platformsJson = determinePlatform($validated['contact_link']);

            // Process genres to match DB structure {"Metal": {"all": "false", "subgenres": []}}
            $genres = json_decode($validated['genres'], true);
            if (!is_array($genres)) {
                $genres = [];
            }

            $formattedGenres = [];
            foreach ($genres as $genre) {
                $formattedGenres[$genre] = [
                    'all' => 'false',
                    'subgenres' => []
                ];
            }

            // Create new band in the OtherService model
            $band = OtherService::create([
                'name' => $validated['name'],
                'location' => $validated['location'],
                'other_service_id' => 4,
                'postal_town' => $validated['postal_town'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'description' => $validated['description'],
                'contact_name' => $validated['contact_name'],
                'contact_number' => $validated['contact_number'],
                'contact_email' => $validated['contact_email'],
                'contact_link' => $platformsJson,
                'services' => 'Artist',
                'band_type' => json_encode($bandType),
                'genre' => json_encode($formattedGenres),
            ]);

            if (!$band) {
                throw new \Exception('Band creation failed');
            }

            // Associate the user with the new band
            $user = auth()->user();
            if (!$user) {
                throw new \Exception('No authenticated user found');
            }

            // Attach the band to the user using the pivot table
            $user->otherService('artist')->attach($band->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \Log::info('Band created successfully', [
                'band_id' => $band->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully created and joined the new artist!',
                'redirect' => route('dashboard', ['dashboardType' => lcfirst($dashboardType)])
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in createBand:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create band profile: ' . $e->getMessage()
            ], 422);
        }
    }
}
