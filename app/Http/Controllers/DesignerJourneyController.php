<?php

namespace App\Http\Controllers;

use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerJourneyController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $designer = OtherService::designers()->get();

        return view('admin.dashboards.designer.designer-journey', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'designer' => $designer,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $designers = OtherService::where('other_service_id', 3)
                ->where('name', 'like', '%' . $query . '%')
                ->get();
        } else {
            $designers = OtherService::where('other_service_id', 3)
                ->limit(8)
                ->get();
        }

        $html = '';
        foreach ($designers as $designer) {
            $html .= view('admin.dashboards.partials.designer-row', compact('designer'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function joinDesigner($dashboardType, Request $request)
    {
        $designerId = $request->input('serviceable_id');
        $user = Auth::user();

        // Check designer exists
        $designer = OtherService::find($designerId);

        if (!$designer) {
            return response()->json([
                'success' => false,
                'message' => 'The designer does not exist.'
            ], 400);
        }

        if ($user->otherService('designer')->where('serviceable_id', $designerId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already linked'
            ], 400);
        }

        $user->otherService('designer')->attach($designerId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully linked!',
            'redirect' => route('dashboard', ['dashboardType' => $dashboardType])
        ], 200);
    }

    public function createDesigner(Request $request)
    {
        $dashboardType = 'Designer';
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
            $designer = OtherService::create([
                'name' => $request->name,
                'location' => $request->location,
                'other_service_id' => 3,
                'postal_town' => $request->postal_town,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'description' => $request->description,
                'contact_name' => $request->contact_name,
                'contact_number' => $request->contact_number,
                'contact_email' => $request->contact_email,
                'contact_link' => $platformsJson,
                'services' => 'Designer',
                'band_type'  =>  [],
                'genre' => "[]",
            ]);

            if (!$designer) {
                logger()->error('Designer creation failed');
                return back()->withErrors(['error' => 'Failed to create the designer']);
            }

            // Associate the user with the new designer
            $user = auth()->user();
            if (!$user) {
                logger()->error('No authenticated user found');
                return back()->withErrors(['error' => 'No authenticated user']);
            }

            $user->otherService()->attach($designer->id);

            return redirect()->route('dashboard', $dashboardType)->with('success', 'Successfully created and joined the new designer!');
        } catch (\Exception $e) {
            logger()->error('Error in createDesigner:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Something went wrong. We\'ve logged the error and will fix it soon.']);
        }
    }
}