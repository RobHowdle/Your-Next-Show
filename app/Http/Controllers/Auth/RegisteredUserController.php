<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\View\View;
use App\Models\StandardUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use App\Models\UserModuleSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\RegisterUserRequest;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::where('name', '!=', 'administrator')->get();
        return view('auth.register', ['roles' => $roles]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(RegisterUserRequest $request): JsonResponse
    {
        $adminRoleId = Role::where('name', 'administrator')->pluck('id')->first();

        // Check if the selected role is not an administrator
        if ($request->has('role') && $adminRoleId) {
            $selectedRole = $request->input('role');

            if ($selectedRole != $adminRoleId) {
                try {
                    // Create the user
                    $user = User::create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'date_of_birth' => $request->date_of_birth,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                    ]);

                    // Assign the requested role
                    $role = Role::findOrFail($request->role);
                    $user->assignRole($role->name);

                    // Create Standard User Record if role type is Standard
                    if ($user->role('standard')) {
                        $standardUser = StandardUser::create([
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'location' => 'United Kingdom',
                            'postal_town' => 'United Kingdom',
                            'longitude' => '2.4333',
                            'latitude' => '53.5500',
                            'band_type' => json_encode([]),
                            'genre' => json_encode([])
                        ]);

                        $serviceUserRecord = DB::table('service_user')->insert([
                            'user_id' => $user->id,
                            'serviceable_id' => $standardUser->id,
                            'serviceable_type' => 'App\Models\StandardUser',
                            'role' => 'Standard',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }

                    // Set default modules and mailing preferences
                    $this->setDefaultModules($user, $role->name);
                    $this->setDefaultMailingPreferences($user);

                    // Fire the Registered event
                    event(new Registered($user));

                    // Log the user in
                    Auth::login($user);

                    $dashboardType = lcfirst($role->name);

                    // Success JSON response
                    return response()->json([
                        'success' => true,
                        'message' => 'Registration successful! Hang tight, we\'re making your dashboard!',
                        'redirect' => route('dashboard.index')  // Use the correct named route
                    ], 200);
                } catch (\Exception $e) {
                    Log::error('Registration failed:', [
                        'message' => $e->getMessage(),
                        'user_data' => [
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'email' => $request->email,
                        ],
                        'stack' => $e->getTraceAsString(),
                    ]);

                    // Error response for JSON requests
                    return response()->json([
                        'success' => false,
                        'message' => 'Registration failed. Please try again.'
                    ], 500);
                }
            } else {
                // Log and return error for users attempting to register as an admin
                $ipAddress = $request->ip();
                Log::error('User attempted to register with an admin role', ['ip_address' => $ipAddress]);

                // Error response for JSON requests
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot register as this role.'
                ], 403);
            }
        }

        // Error response for missing role in request
        return response()->json([
            'success' => false,
            'message' => 'Role selection is required.'
        ], 422);
    }


    protected function setDefaultModules($user, $roleName)
    {
        // Define all available modules
        $allModules = ['events', 'todo_list', 'notes', 'finances', 'documents', 'users', 'reviews', 'jobs'];

        // Define default modules based on the user role
        $defaultModules = [];
        $serviceableType = '';
        $role = Role::where('name', $roleName)->first();

        switch ($role->name) {
            case "venue":
            case "promoter":
            case "artist":
                $defaultModules = $allModules; // All modules for these roles
                $serviceableType = 'App\Models\\' . ucfirst($role->name);
                break;

            case "photographer":
            case "designer":
            case "videographer":
                $defaultModules = ['todo_list', 'notes', 'finances', 'documents', 'reviews', 'jobs'];
                $serviceableType = 'App\Models\OtherService';
                break;

            case "standard":
                $defaultModules = ['events'];
                $serviceableType = 'App\Models\StandardUser';
                break;

            case "administrator":
                $defaultModules = $allModules; // All modules for administrators
                break;
        }

        // Create module settings for all modules, enabling only default ones
        foreach ($allModules as $module) {
            try {
                $defaultSettings = UserModuleSetting::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'serviceable_id' => $role ? $role->id : null,
                        'serviceable_type' => $serviceableType,
                        'module_name' => $module,
                    ],
                    [
                        'is_enabled' => in_array($module, $defaultModules), // Enable if in default modules
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Failed to create UserModuleSetting', [
                    'user_id' => $user->id,
                    'module_name' => $module,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function setDefaultMailingPreferences($user)
    {
        // Retrieve the default communication preferences from the config file
        $defaultPreferences = Config::get('mailing_preferences.communication_preferences');

        // Set all preferences to true (enabled)
        $preferences = [];
        foreach ($defaultPreferences as $preferenceKey => $preference) {
            $preferences[$preferenceKey] = true; // Default all preferences to true
        }

        // Store the preferences as an array (Laravel will handle the JSON encoding automatically)
        $user->mailing_preferences = $preferences;
        $user->save();

        // Optionally, you can return a success response
        return response()->json([
            'message' => 'Default mailing preferences set successfully.'
        ]);
    }
}