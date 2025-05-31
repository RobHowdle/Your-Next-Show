<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\UserModuleSetting;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Auth\RegisteredUserController;

class UpdateAllUserSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-settings {--user=} {--role=} {--modules} {--mail} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user settings including modules and mailing preferences';

    /**
     * The RegisteredUserController instance.
     *
     * @var RegisteredUserController
     */
    protected $registeredUserController;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RegisteredUserController $registeredUserController)
    {
        parent::__construct();
        $this->registeredUserController = $registeredUserController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Determine which users to update
        $userId = $this->option('user');
        $roleName = $this->option('role');
        $updateModules = $this->option('modules') || $this->option('all');
        $updateMail = $this->option('mail') || $this->option('all');

        if (!$updateModules && !$updateMail) {
            $this->error('Please specify what to update: --modules, --mail, or --all');
            return 1;
        }

        // Query users based on options
        $query = User::query();

        // Filter by specific user ID if provided
        if ($userId) {
            $query->where('id', $userId);
        }

        // Filter by role if provided
        if ($roleName) {
            $query->role($roleName);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->error('No users found matching the criteria');
            return 1;
        }

        $this->info('Starting update for ' . $users->count() . ' users');
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            try {
                // Get user's role
                $userRole = $user->roles->first();

                if (!$userRole) {
                    $this->newLine();
                    $this->warn("User ID {$user->id} has no role assigned. Skipping.");
                    $errorCount++;
                    continue;
                }

                // Update modules if requested
                if ($updateModules) {
                    $this->setDefaultModules($user, $userRole->name);
                }

                // Update mailing preferences if requested
                if ($updateMail) {
                    $this->setDefaultMailingPreferences($user);
                }

                $successCount++;
            } catch (\Exception $e) {
                Log::error('Failed to update user settings', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed processing {$users->count()} users");
        $this->info("Success: {$successCount}");

        if ($errorCount > 0) {
            $this->warn("Errors: {$errorCount} - Check the log for details");
        }

        return 0;
    }

    /**
     * Set default modules for a user based on their role.
     * This method follows the exact implementation in RegisteredUserController.
     *
     * @param  \App\Models\User  $user
     * @param  string  $roleName
     * @return void
     */
    protected function setDefaultModules($user, $roleName)
    {
        // Define all available modules
        $allModules = ['events', 'todo_list', 'notes', 'finances', 'documents', 'users', 'reviews', 'jobs'];

        // Define default modules based on the user role
        $defaultModules = [];
        $serviceableType = '';
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $this->error("Role not found: {$roleName}");
            return;
        }

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

        $this->info("Setting modules for user {$user->id} ({$user->email}), role: {$role->name}");

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

                $this->line("  - Module '{$module}' " . (in_array($module, $defaultModules) ? 'enabled' : 'disabled'));
            } catch (\Exception $e) {
                $this->error("  - Failed to update module '{$module}': " . $e->getMessage());
                Log::error('Failed to create UserModuleSetting', [
                    'user_id' => $user->id,
                    'module_name' => $module,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Set default mailing preferences for a user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function setDefaultMailingPreferences($user)
    {
        try {
            // Retrieve the default communication preferences from the config file
            $defaultPreferences = Config::get('mailing_preferences.communication_preferences', []);

            // If config doesn't exist, use some sensible defaults
            if (empty($defaultPreferences)) {
                $defaultPreferences = [
                    'marketing' => 'Marketing updates',
                    'announcements' => 'Announcements',
                    'promotions' => 'Promotions',
                    'system' => 'System notifications'
                ];
            }

            // Set all preferences to true (enabled)
            $preferences = [];
            foreach ($defaultPreferences as $preferenceKey => $preference) {
                $preferences[$preferenceKey] = true; // Default all preferences to true
            }

            // Store the preferences as JSON
            $user->mailing_preferences = json_encode($preferences);
            $user->save();
        } catch (\Exception $e) {
            Log::error('Failed to update mailing preferences', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}