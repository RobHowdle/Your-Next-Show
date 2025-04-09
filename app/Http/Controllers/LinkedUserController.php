<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\ServiceUser;
use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LinkedUserController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function showUsers($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();

        // Get the user's role for the current service
        $serviceType = $user->getServiceType($dashboardType);
        $currentService = $user->getCurrentService($dashboardType);
        $userRole = null;

        if ($currentService) {
            // Get role from service_user table using role_id
            $serviceUser = DB::table('service_user')
                ->where('user_id', $user->id)
                ->where('serviceable_id', $currentService->serviceable_id)
                ->where('serviceable_type', $serviceType)
                ->whereNull('deleted_at')
                ->first();

            if ($serviceUser && $serviceUser->role_id) {
                $role = DB::table('roles')
                    ->where('id', $serviceUser->role_id)
                    ->first();

                $userRole = $role ? $role->name : null;
            }
        }

        return view('admin.dashboards.show-users', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'userRole' => $userRole
        ]);
    }

    public function getUsers($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['promoters', 'venues', 'otherService']);

        $relatedUsers = null;

        switch ($dashboardType) {
            case 'promoter':
                $relatedUsers = $user->promoters->load(['linkedUsers']);
                break;
            case 'venue':
                $relatedUsers = $user->venues->load(['linkedUsers']);
                break;
            case 'artist':
            case 'designer':
            case 'photographer':
            case 'videographer':
                $relatedUsers = $user->otherService($dashboardType)->load(['linkedUsers']);
                break;
        }

        return response()->json([
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'relatedUsers' => $relatedUsers,
            'modules' => $modules,
        ]);
    }

    public function newUser($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;
        $service = null;
        $currentServiceId = null;

        switch ($role) {
            case 'promoter':
                $service = $user->promoters()->first();
                $currentServiceId = $user->promoters->first()->id ?? null;
                break;

            case 'venue':
                $service = $user->venues()->first();
                $currentServiceId = $user->venues->first()->id ?? null;
                break;

            case 'artist':
                $service = $user->otherService("Artist")->first();
                $currentServiceId = $user->otherService("Artist")->first()->id ?? null;
                break;

            case 'photographer':
                $service = $user->otherService("Photograher")->first();
                $currentServiceId = $user->otherService("Photographer")->first()->id ?? null;
                break;

            case 'videographer':
                $service = $user->otherService("Videographer")->first();
                $currentServiceId = $user->otherService("Videographer")->first()->id ?? null;
                break;

            case 'designer':
                $service = $user->otherService("Designer")->first();
                $currentServiceId = $user->otherService("Designer")->first()->id ?? null;
                break;

            default:
                return null;
        }

        return view('admin.dashboards.new-user', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'service' => $service,
            'currentServiceId' => $currentServiceId,
            'modules' => $modules,
        ]);
    }

    public function searchUsers($dashboardType, Request $request)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $serviceId = null;
        $serviceType = null;
        if ($dashboardType == 'promoter') {
            $serviceType = 'App\Models\Promoter';
            $serviceId = $user->promoters->first()->id ?? null;
        } elseif ($dashboardType == 'artist') {
            $serviceType = 'App\Models\OtherService';
            $serviceId = $user->otherService("Artist")->first()->id ?? null;
        } elseif ($dashboardType == 'designer') {
            $serviceType = 'App\Models\OtherService';
            $serviceId = $user->otherService("Designer")->first()->id ?? null;
        } elseif ($dashboardType == 'photographer') {
            $serviceType = 'App\Models\OtherService';
            $serviceId = $user->otherService("Photographer")->first()->id ?? null;
        } elseif ($dashboardType == 'videographer') {
            $serviceType = 'App\Models\OtherService';
            $serviceId = $user->otherService("Videographer")->first()->id ?? null;
        } elseif ($dashboardType == 'venue') {
            $serviceType = 'App\Models\Venue';
            $serviceId = $user->venues->first()->id ?? null;
        }

        $linkedUserIds = ServiceUser::where('serviceable_id', $serviceId)
            ->where('serviceable_type', $serviceType)
            ->pluck('user_id')
            ->toArray();

        $users = User::where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('first_name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
        })
            ->whereNotIn('id', $linkedUserIds)
            ->get();

        $result = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ];
        });

        return response()->json([
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'result' => $result,
            'modules' => $modules,
        ]);
    }

    public function linkUser($dashboardType, $id, Request $request)
    {
        try {
            $userId = User::where('id', $id)->value('id');
            if (!$userId) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $currentServiceId = $request->currentServiceId;
            $user = User::find($userId);

            // Get service type
            $serviceType = $user->getServiceType($dashboardType);

            // Check if this is the first user for this service
            $isFirstUser = !ServiceUser::where('serviceable_id', $currentServiceId)
                ->where('serviceable_type', $serviceType)
                ->exists();

            // Determine role name
            $roleName = $isFirstUser ? 'service-owner' : 'service-member';

            // Get role ID from database
            $role = DB::table('roles')->where('name', $roleName)->first();

            if (!$role) {
                throw new \Exception("Role not found: {$roleName}");
            }

            // Assign Spatie role to user
            $user->assignRole($roleName);

            // Create service user record
            ServiceUser::create([
                'user_id' => $userId,
                'serviceable_id' => $currentServiceId,
                'serviceable_type' => $serviceType,
                'role_id' => $role->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => $isFirstUser ? 'User added as owner with full permissions.' : 'User added as member.',
                'role' => $roleName
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Link User Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while linking the user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(Request $request)
    {
        $modules = collect(session('modules', []));
        $relatedUser = DB::table('service_user')
            ->where('user_id', $request->user_id)
            ->where('serviceable_id', $request->service_id)
            ->first();

        if (!$relatedUser) {
            return response()->json(['success' => false, 'message' => 'Could not find user.']);
        }

        DB::table('service_user')
            ->where('user_id', $request->user_id)
            ->where('serviceable_id', $request->service_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'User successfully removed.']);
    }

    public function updateUserRole($dashboardType, Request $request)
    {
        try {
            $currentUser = Auth::user();
            $serviceType = $currentUser->getServiceType($dashboardType);
            $currentUserRole = $currentUser->getCurrentServiceRole($dashboardType);

            // Get the target user's current role
            $targetUserRole = DB::table('service_user')
                ->where('user_id', $request->user_id)
                ->where('serviceable_id', $request->service_id)
                ->where('serviceable_type', $serviceType)
                ->whereNull('deleted_at')
                ->join('roles', 'service_user.role_id', '=', 'roles.id')
                ->value('roles.name');

            // Validate permissions
            if (
                $currentUserRole === 'service-member' ||
                ($currentUserRole === 'service-manager' &&
                    ($targetUserRole === 'service-owner' || $request->role === 'service-owner'))
            ) {
                return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
            }

            // Get role ID
            $roleId = DB::table('roles')->where('name', $request->role)->value('id');
            if (!$roleId) {
                return response()->json(['message' => 'Invalid role specified.'], 400);
            }

            // Update role
            DB::table('service_user')
                ->where('user_id', $request->user_id)
                ->where('serviceable_id', $request->service_id)
                ->where('serviceable_type', $serviceType)
                ->update(['role_id' => $roleId]);

            // Update Spatie role
            $user = User::find($request->user_id);
            $user->syncRoles([$request->role]);

            return response()->json([
                'message' => 'User role updated successfully.',
                'role' => $request->role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the role.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}