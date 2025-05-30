<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Standard role permissions
        $this->assignVenuePermissions();
        $this->assignPromoterPermissions();
        $this->assignArtistPermissions();
        $this->assignPhotographerPermissions();
        $this->assignVideographerPermissions();
        $this->assignDesignerPermissions();
        $this->assignStandardUserPermissions();
        $this->assignServiceRolePermissions();
        $this->assignAdministratorPermissions();
    }

    private function assignVenuePermissions(): void
    {
        $role = Role::findByName('venue');
        $permissions = [
            'manage_venue',
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
            'manage_jobs'
        ];
        $role->syncPermissions($permissions);
    }

    private function assignServiceRolePermissions(): void
    {
        // Service Owner
        $serviceOwner = Role::findByName('service-owner');
        $serviceOwner->syncPermissions(Permission::all());

        // Service Manager
        $serviceManager = Role::findByName('service-manager');
        $managerPermissions = [
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
        ];
        $serviceManager->syncPermissions($managerPermissions);

        // Service Member
        $serviceMember = Role::findByName('service-member');
        $memberPermissions = [
            'view_service_users',
            'view_documents',
            'upload_documents',
            'view_events',
            'create_events'
        ];
        $serviceMember->syncPermissions($memberPermissions);
    }

    private function assignPromoterPermissions(): void
    {
        $role = Role::findByName('promoter');
        $permissions = [
            'manage_promoter',
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
            'manage_jobs'
        ];

        $role->syncPermissions($permissions);
    }

    private function assignArtistPermissions(): void
    {
        $role = Role::findByName('artist');
        $permissions = [
            'manage_band',
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
            'manage_jobs'
        ];
        $role->syncPermissions($permissions);
    }

    private function assignPhotographerPermissions(): void
    {
        $role = Role::findByName('photographer');
        $permissions = [
            'manage_photographer',
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
            'manage_jobs'
        ];
        $role->syncPermissions($permissions);
    }

    private function assignVideographerPermissions(): void
    {
        $role = Role::findByName('videographer');
        $permissions = [
            'manage_videographer',
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
            'manage_jobs'
        ];
        $role->syncPermissions($permissions);
    }

    private function assignDesignerPermissions(): void
    {
        $role = Role::findByName('designer');
        $permissions = [
            'manage_designer',
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
            'manage_jobs'
        ];
        $role->syncPermissions($permissions);
    }

    private function assignStandardUserPermissions(): void
    {
        $role = Role::findByName('standard');
        $permissions = [
            'manage_standard_user',
            'view_events'
        ];
        $role->syncPermissions($permissions);
    }

    private function assignAdministratorPermissions(): void
    {
        $role = Role::findByName('administrator');
        $role->syncPermissions(Permission::all());
    }
}