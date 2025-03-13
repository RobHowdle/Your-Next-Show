<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\UserService;
use App\Models\VenueReview;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Services\TodoService;
use App\Models\UserModuleSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    protected $todoService;

    protected function getUserId()
    {
        return Auth::id();
    }

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    public function index(Request $request)
    {
        $user = Auth::user()->load(['roles']);
        $roles = $user->roles->pluck('name');
        $roleName = $roles->first();
        $venues = Venue::all();
        $photographers = OtherService::photographers()->get();
        $videographers = OtherService::videographers()->get();
        $designers = OtherService::designers()->get();
        $promoters = Promoter::all();

        // Get existing services
        $promoter = $user->promoters()->first();
        $artist = $user->otherService("Artist")->first();
        $photographer = $user->otherService("Photographer")->first();
        $venue = $user->venues()->first();
        $designer = $user->otherService("Designer")->first();
        $videographer = $user->otherService("Videographer")->first();

        // Load genres from a JSON file
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'] ?? []; // Provide a fallback

        $modules = $this->getUserModules($user);
        session(['modules' => $modules]);


        // Determine dashboard type
        $dashboardType = lcfirst($roleName);

        // Role-based redirection
        switch ($roleName) {
            case 'promoter':
                if (!$promoter) {
                    return
                        redirect("/{$dashboardType}/promoter-journey")->with(['genres', $genres, 'dashboardType', $dashboardType, 'modules', $modules]);
                }
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType, 'modules', $modules]);
                break;
            case 'artist':
                if (!$artist) {
                    return
                        redirect("/{$dashboardType}/band-journey")->with(['genres', $genres, 'dashboardType', $dashboardType, 'modules', $modules]);
                }
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType, 'modules', $modules]);
                break;
            case 'venue':
                if (!$venue) {
                    return
                        redirect("/{$dashboardType}/venue-journey")->with(['venues', $venues, 'genres', $genres, 'dashboardType', $dashboardType, 'modules', $modules]);
                }
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType]);
                break;
            case 'photographer':
                if (!$photographer) {
                    return redirect("/{$dashboardType}/photographer-journey")->with(['photographers', $photographers, 'genres', $genres, 'dashboardType', $dashboardType, 'modules', $modules]);
                }
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType, 'modules', $modules]);
                break;
            case 'videographer':
                if (!$videographer) {
                    return redirect("/{$dashboardType}/videographer-journey")->with(['videographers', $videographers, 'genres', $genres, 'dashboardType', $dashboardType, 'modules', $modules]);
                }
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType, 'modules', $modules]);
                break;
            case 'designer':
                if (!$designer) {
                    return redirect("/{$dashboardType}/designer-journey")->with(['designers', $designers, 'genres', $genres, 'dashboardType', $dashboardType, 'modules', $modules]);
                }
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType, 'modules', $modules]);
                break;
            case 'standard':
                return redirect("/dashboard/{$dashboardType}")->with(['dashboardType', $dashboardType, 'modules', $modules]);
                break;
            case 'administrator':
                return redirect()->route('admin.dashboard');
            default:
                Log::warning("Unauthorized access attempt for role: {$roleName}");
                return abort(403); // Forbidden access if role is not recognized
        }
    }

    private function getUserModules($user)
    {
        $modules = $user->moduleSettings()->where('is_enabled', true)->get();

        $modulesArray = [];

        foreach ($modules as $module) {
            $modulesArray[$module->module_name] = ['is_enabled' => true];
        }

        // Return the modules array
        return $modulesArray;
    }
}
