<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\JoinBandRequest;

class PhotographerJourneyController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;
        $photographers = OtherService::photographers()->get();

        return view('admin.dashboards.photographer.photographer-journey', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'photographers' => $photographers,
        ]);
    }

    public function searchPhotographer(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $photographers = OtherService::where('other_service_id', 1)
                ->where('name', 'like', '%' . $query . '%')
                ->get();
        } else {
            $photographers = OtherService::where('other_service_id', 1)
                ->limit(8)
                ->get();
        }

        $html = '';
        foreach ($photographers as $photographer) {
            $html .= view('admin.dashboards.partials.photographer-row', compact('photographer'))->render();
        }

        return response()->json(['html' => $html]);
    }


    public function joinPhotographer($dashboardType, Request $request)
    {
        $photographerId = $request->input('serviceable_id');
        $user = Auth::user();

        // Check if the photographer exists
        $photographer = OtherService::find($photographerId);

        if (!$photographer) {
            return response()->json([
                'success' => false,
                'message' => 'The photographer does not exist.'
            ], 404);
        }

        // Check if the user is already part of the photography company
        if ($user->otherService('photographer')->where('serviceable_id', $photographerId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of this photography company.'
            ], 400);
        }

        // Add the user to the photography company
        $user->otherService('photographer')->attach($photographerId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined!',
            'redirect' => route('dashboard', ['dashboardType' => $dashboardType]),
        ], 200);
    }


    public function createPhotographer(Request $request)
    {
        $dashboardType = 'Photographer';
        $platformsJson = determinePlatform($request->input('contact_link'));

        // Validate and create a new photographer
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

        // Create a new photographer in the OtherService model
        try {
            $photographer = OtherService::create([
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
                'portfoilio_link' => 'https://www.yournextshow.co.uk',
                'portfolio_images' => [],
                'other_service_id' => 1,
                'services' => 'Photography',
            ]);

            if (!$photographer) {
                logger()->error('Photographer creation failed');
                return back()->withErrors(['error' => 'Failed to create the photographer']);
            }
            $user = auth()->user();

            if (!$user) {
                logger()->error('No authenticated user found');
                return back()->withErrors(['error' => 'No authenticated user']);
            }

            $user->otherService()->attach($photographer->id, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return redirect()->route('dashboard', ['dashboardType' => $dashboardType])->with('success', 'Successfully created and joined the new photography company!');
        } catch (\Exception $e) {
            logger()->error('Photographer creation failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Something went wrong. We\'ve logged the error and will fix it soon.']);
        }
    }
}