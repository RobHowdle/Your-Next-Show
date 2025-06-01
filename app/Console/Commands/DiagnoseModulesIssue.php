<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\UserModuleSetting;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;

class DiagnoseModulesIssue extends Command
{
    protected $signature = 'diagnose:modules {--role=} {--fix}';
    protected $description = 'Diagnose issues with modules visibility for different roles';

    public function handle()
    {
        $roleName = $this->option('role');
        $shouldFix = $this->option('fix');

        $this->info("=== Modules Diagnostic Tool ===");

        // Get the database structure of user_module_settings
        $this->info("\nTable Structure:");
        $columns = Schema::getColumnListing('user_module_settings');
        foreach ($columns as $column) {
            $this->line(" - $column");
        }

        // Get all roles
        $roles = Role::all();
        $this->info("\nAnalyzing modules by role:");

        foreach ($roles as $role) {
            if ($roleName && $role->name !== $roleName) {
                continue;
            }

            $this->info("\n== Role: {$role->name} ==");

            // Get users with this role
            $users = User::role($role->name)->limit(3)->get();
            $this->line("Found {$users->count()} users with this role.");

            if ($users->isEmpty()) {
                continue;
            }

            foreach ($users as $user) {
                $this->line("\nUser ID: {$user->id} ({$user->email})");

                // Check roles
                $this->line("Assigned Roles: " . implode(', ', $user->getRoleNames()->toArray()));

                // Get modules
                $modules = UserModuleSetting::where('user_id', $user->id)->get();
                $this->line("Module Count: {$modules->count()}");

                // Show module details
                if ($modules->isEmpty()) {
                    $this->warn("  No modules found for this user!");

                    if ($shouldFix) {
                        $this->info("  Fixing missing modules for user {$user->id}...");
                        $this->createModulesForUser($user, $role);
                    }
                } else {
                    $modulesByName = [];
                    $hasIssues = false;

                    foreach ($modules as $module) {
                        $modulesByName[$module->module_name] = [
                            'id' => $module->id,
                            'enabled' => $module->is_enabled ? 'Yes' : 'No',
                            'serviceable_type' => $module->serviceable_type ?? 'NULL',
                            'serviceable_id' => $module->serviceable_id ?? 'NULL',
                        ];

                        // Check for potential issues
                        if (empty($module->serviceable_type) && !empty($module->serviceable_id)) {
                            $hasIssues = true;
                        }
                        if (!empty($module->serviceable_type) && empty($module->serviceable_id)) {
                            $hasIssues = true;
                        }
                    }

                    // Display modules in a table
                    $this->table(
                        ['Module', 'ID', 'Enabled', 'Serviceable Type', 'Serviceable ID'],
                        collect($modulesByName)->map(function ($data, $name) {
                            return [$name, $data['id'], $data['enabled'], $data['serviceable_type'], $data['serviceable_id']];
                        })->toArray()
                    );

                    if ($hasIssues && $shouldFix) {
                        $this->info("  Fixing module issues for user {$user->id}...");
                        $this->fixModulesForUser($user, $role);
                    }
                }
            }
        }

        return 0;
    }

    private function createModulesForUser($user, $role)
    {
        $allModules = ['events', 'todo_list', 'notes', 'finances', 'documents', 'users', 'reviews', 'jobs'];

        // Based on the role, determine which modules should be enabled
        $enabledModules = [];
        $serviceableType = '';

        switch ($role->name) {
            case "venue":
            case "promoter":
            case "artist":
                $enabledModules = $allModules;
                $serviceableType = 'App\Models\\' . ucfirst($role->name);
                break;

            case "photographer":
            case "designer":
            case "videographer":
                $enabledModules = ['todo_list', 'notes', 'finances', 'documents', 'reviews', 'jobs'];
                $serviceableType = 'App\Models\OtherService';
                break;

            case "standard":
                $enabledModules = ['events'];
                $serviceableType = 'App\Models\StandardUser';
                break;

            case "administrator":
                $enabledModules = $allModules;
                break;
        }

        // Create the modules
        foreach ($allModules as $module) {
            UserModuleSetting::create([
                'user_id' => $user->id,
                'module_name' => $module,
                'is_enabled' => in_array($module, $enabledModules),
                'serviceable_type' => $serviceableType,
                'serviceable_id' => $role->id, // Use role ID as fallback
            ]);
        }

        $this->info("  Created " . count($allModules) . " modules for user {$user->id}");
    }

    private function fixModulesForUser($user, $role)
    {
        $serviceableType = '';

        switch ($role->name) {
            case "venue":
            case "promoter":
            case "artist":
                $serviceableType = 'App\Models\\' . ucfirst($role->name);
                break;

            case "photographer":
            case "designer":
            case "videographer":
                $serviceableType = 'App\Models\OtherService';
                break;

            case "standard":
                $serviceableType = 'App\Models\StandardUser';
                break;
        }

        // Get all modules for this user
        $modules = UserModuleSetting::where('user_id', $user->id)->get();

        foreach ($modules as $module) {
            // Fix serviceable type/id consistency
            if ($role->name === 'artist') {
                $artistService = $user->otherService('Artist')->first();

                $module->serviceable_type = $serviceableType;
                $module->serviceable_id = $artistService ? $artistService->id : $role->id;
                $module->save();
            } else if (empty($module->serviceable_type) || empty($module->serviceable_id)) {
                $module->serviceable_type = $serviceableType;
                $module->serviceable_id = $role->id;
                $module->save();
            }
        }

        $this->info("  Fixed " . count($modules) . " modules for user {$user->id}");
    }
}
