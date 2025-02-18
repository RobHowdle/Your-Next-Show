<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\View\View;
use App\Models\StandardUser;
use App\Models\UserModuleSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Controllers\DashboardController;


class RegisteredUserController extends Controller
{
    protected $dashboardController;

    public function __construct(DashboardController $dashboardController)
    {
        $this->dashboardController = $dashboardController;
    }

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
        // Check if the selected role is not an administrator
        $adminRoleId = Role::where('name', 'administrator')->pluck('id')->first();

        if ($request->has('role') && $adminRoleId && $request->input('role') != $adminRoleId) {
            try {
                DB::beginTransaction();

                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'date_of_birth' => $request->date_of_birth,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                $role = Role::findOrFail($request->role);
                $user->assignRole($role->name);

                // Create standard user service if role is standard
                if ($role->name === 'standard') {
                    $this->createStandardUserService($user);
                }

                $this->setDefaultModules($user, $role->name);
                $this->setDefaultMailingPreferences($user);

                event(new Registered($user));
                Auth::login($user);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Let\'s set up your profile.',
                    'redirect' => route('dashboard.index')
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Registration failed:', [
                    'message' => $e->getMessage(),
                    'user_data' => $request->only(['first_name', 'last_name', 'email']),
                    'stack' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.'
                ], 500);
            }
        }

        if ($request->input('role') == $adminRoleId) {
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

    protected function createStandardUserService($user): void
    {
        $standardUser = StandardUser::create([
            'name' => $user->first_name . ' ' . $user->last_name,
            'location' => 'United Kingdom',
            'postal_town' => 'United Kingdom',
            'longitude' => '2.4333',
            'latitude' => '53.5500',
            'band_type' => json_encode([]),
            'genre' => json_encode([])
        ]);

        DB::table('service_user')->insert([
            'user_id' => $user->id,
            'serviceable_id' => $standardUser->id,
            'serviceable_type' => StandardUser::class,
            'role' => 'Standard',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
