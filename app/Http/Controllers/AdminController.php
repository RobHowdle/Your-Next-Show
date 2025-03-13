<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrator']);
    }

    public function index()
    {
        try {
            $data = [
                'totalUsers' => User::count(),
                'totalVenues' => Venue::count(),
                'totalPromoters' => Promoter::count(),
                'totalServices' => OtherService::count(),
                'venues' => Venue::latest()->take(10)->get(),
                'users' => User::with('roles')->latest()->take(10)->get(),
                'promoters' => Promoter::latest()->take(10)->get(),
                'services' => OtherService::latest()->take(10)->get(),
            ];

            return view('admin.dashboards.admin-dash', $data);
        } catch (\Exception $e) {
            Log::error('Error in admin dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error loading the dashboard.');
        }
    }

    public function users()
    {
        $users = User::with('roles')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function venues()
    {
        $venues = Venue::paginate(15);
        return view('admin.venues.index', compact('venues'));
    }

    public function promoters()
    {
        $promoters = Promoter::paginate(15);
        return view('admin.promoters.index', compact('promoters'));
    }

    public function services()
    {
        $services = OtherService::paginate(15);
        return view('admin.services.index', compact('services'));
    }

    // Users
    public function editUser(User $user)
    {
        // Check if user is super admin
        if ($user->isSuperAdmin() && $user->id !== auth()->id()) {
            return redirect()->back()->with('error', 'Cannot modify super admin account');
        }

        // Delegate to UserController
        return App::make(UserController::class)->edit($user);
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        try {
            $userController = App::make(UserController::class);
            $userController->update($request, $user);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            }

            return redirect()
                ->route('admin.users')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user'
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to update user')
                ->withInput();
        }
    }

    public function deleteUser(User $user)
    {
        // Check if user is super admin
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete super admin account');
        }

        // Check if user is trying to delete themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account');
        }

        // Delegate to UserController
        return App::make(UserController::class)->destroy($user);
    }

    public function resendVerification(User $user)
    {
        // Delegate to UserController
        return App::make(UserController::class)->resendVerification($user);
    }

    // Venues
    public function editVenue(Venue $venue)
    {
        // Delegate to VenueController
        return App::make(VenueController::class)->edit($venue);
    }
}