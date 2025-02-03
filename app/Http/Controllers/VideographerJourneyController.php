<?php

namespace App\Http\Controllers;

use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideographerJourneyController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $videographer = OtherService::videographers()->get();

        return view('admin.dashboards.videographer.videographer-journey', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'videographer' => $videographer,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        if ($query) {
            $videographers = OtherService::where('other_service_id', 2)
                ->where('name', 'like', '%' . $query . '%')
                ->get();
        } else {
            $videographers = OtherService::where('other_service_id', 2)
                ->limit(8)
                ->get();
        }

        $html = '';
        foreach ($videographers as $videographer) {
            $html .= view('admin.dashboards.partials.videographer-row', compact('videographer'))->render();
        }

        return response()->json(['html' => $html]);
    }

    public function joinVideographer($dashboardType, Request $request)
    {
        $videographerId = $request->input('serviceable_id');
        $user = Auth::user();

        // Check designer exists
        $videographer = OtherService::find($videographerId);

        if (!$videographer) {
            return response()->json([
                'success' => false,
                'message' => 'The videographer does not exist.'
            ], 400);
        }

        if ($user->otherService('videography')->where('serviceable_id', $videographerId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already linked'
            ], 400);
        }

        $user->otherService('videography')->attach($videographerId, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully linked!',
            'redirect' => route('dashboard', ['dashboardType' => $dashboardType])
        ], 200);
    }
}