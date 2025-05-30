<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Main system permissions
            'manage_venue',
            'manage_promoter',
            'manage_band',
            'manage_photographer',
            'manage_videographer',
            'manage_designer',
            'manage_standard_user',
            'manage_modules',
            'view_finances',
            'manage_finances',
            'view_events',
            'manage_events',
            'view_todo_list',
            'manage_todo_list',
            'view_reviews',
            'manage_reviews',
            'view_notes',
            'manage_notes',
            'view_documents',
            'manage_documents',
            'view_users',
            'manage_users',
            'view_jobs',
            'manage_jobs',

            // Service-specific permissions
            'invite_service_users',
            'remove_service_users',
            'edit_service_users',
            'view_service_users',
            'edit_service_details',
            'delete_service',
            'manage_service_settings',
            'upload_documents',
            'delete_documents',
            'edit_documents',
            'create_events',
            'edit_events',
            'delete_events',
            'manage_billing',
            'view_analytics',
            'export_data'
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}