<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Promoter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoterJourneyController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $promoters = Promoter::get();

        return view('admin.dashboards.promoter.promoter-journey', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'promoters' => $promoters,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $promoters = Promoter::where('name', 'like', '%' . $query . '%')
                ->get();
        } else {
            $promoters = Promoter::where('name', 'like', '%' . $query . '%')
                ->limit(8)
                ->get();
        }

        $html = '';
        foreach ($promoters as $promoter) {
            $html .= view('admin.dashboards.partials.promoter-row', compact('promoter'))->render();
        }

        return response()->json(['html' => $html]);
    }


    public function joinPromoter($dashboardType, Request $request)
    {
        $promoterId = $request->input('serviceable_id');
        $user = Auth::user();

        // Check if the band exists
        $promoter = Promoter::find($promoterId);

        if (!$promoter) {
            return response()->json([
                'success' => false,
                'message' => 'The promoter does not exist.'
            ], 404);
        }

        // Check if the user is already part of the band
        if ($user->promoters()->where('serviceable_id', $promoterId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this artist.'
            ], 400);
        }

        // Add the user to the band
        $user->promoters()->attach($promoterId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully linked!',
            'redirect' => route('dashboard', ['dashboardType' => $dashboardType])
        ], 200);
    }


    public function createPromoter(Request $request)
    {
        $dashboardType = 'Promoter';
        $platformsJson = determinePlatform($request->input('contact_link'));
        // Validate and create a new promoter
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

        try {
            // Create new promoter
            $promoter = Promoter::create([
                'name' => $request->name,
                'location' => $request->location,
                'postal_town' => $request->postal_town,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'description' => $request->description,
                'contact_name' => $request->contact_name,
                'contact_number' => $request->contact_number,
                'contact_email' => $request->contact_email,
                'contact_link' => $platformsJson,
                'band_type'  =>  json_encode([]),
                'genre' => json_encode([]),
            ]);

            if (!$promoter) {
                logger()->error('Promoter creation failed');
                return back()->withErrors(['error' => 'Failed to create the promoter']);
            }

            // Associate the user with the new promoter
            $user = auth()->user();
            if (!$user) {
                logger()->error('No authenticated user found');
                return back()->withErrors(['error' => 'No authenticated user']);
            }

            $user->promoters()->attach($promoter->id);

            return redirect()->route('dashboard', $dashboardType)->with('success', 'Successfully created and joined the new promoter!');
        } catch (\Exception $e) {
            logger()->error('Error in createPromoter:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Something went wrong. We\'ve logged the error and will fix it soon.']);
        }
    }
}