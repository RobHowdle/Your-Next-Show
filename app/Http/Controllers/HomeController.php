<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\OtherService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Venues
        $actualCount = Venue::count();
        $roundedCount = floor($actualCount / 10) * 10;
        $venues = $roundedCount . '+';

        // Artists
        $actualCount = OtherService::bands()->count();
        $roundedCount = floor($actualCount / 10) * 10;
        $artists = $roundedCount . '+';

        // Locations
        $actualCount = Venue::select('location')->distinct()->count();
        $roundedCount = floor($actualCount / 10) * 10;
        $locations = $roundedCount . '+';

        return view('welcome', compact('venues', 'artists', 'locations'));
    }
}
