<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ServiceRolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            // User management
            'invite_service_users',
            'remove_service_users',
            'edit_service_users',
            'view_service_users',

            // Service management
            'edit_service_details',
            'delete_service',
            'manage_service_settings',

            // Document management
            'upload_documents',
            'delete_documents',
            'edit_documents',

            // Calendar management
            'create_events',
            'edit_events',
            'delete_events',

            // Settings
            'manage_billing',
            'view_analytics',
            'export_data'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $ownerRole = Role::create(['name' => 'service-owner']);
        $managerRole = Role::create(['name' => 'service-manager']);
        $memberRole = Role::create(['name' => 'service-member']);

        // Owner gets all permissions
        $ownerRole->givePermissionTo(Permission::all());

        // Manager gets most permissions except critical ones
        $managerRole->givePermissionTo([
            'invite_service_users',
            'remove_service_users',
            'edit_service_users',
            'view_service_users',
            'edit_service_details',
            'manage_service_settings',
            'upload_documents',
            'delete_documents',
            'view_documents',
            'edit_documents',
            'create_events',
            'edit_events',
            'delete_events',
            'view_events',
            'view_analytics',
            'export_data'
        ]);

        // Member gets basic permissions
        $memberRole->givePermissionTo([
            'view_service_users',
            'view_documents',
            'upload_documents',
            'view_events',
            'create_events'
        ]);
    }
}