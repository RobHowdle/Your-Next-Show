<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles array
        $roles = [
            [
                'name' => 'standard',
                'guard_name' => 'web',
                'display_name' => 'Music Fan',
                'description' => 'Discover and attend events',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
            ],
            [
                'name' => 'venue',
                'guard_name' => 'web',
                'display_name' => 'Venue Owner',
                'description' => 'List and manage your venue',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'
            ],
            [
                'name' => 'promoter',
                'guard_name' => 'web',
                'display_name' => 'Promoter',
                'description' => 'Organise and promote your events',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />'
            ],
            [
                'name' => 'artist',
                'guard_name' => 'web',
                'display_name' => 'Artist',
                'description' => 'Manage your band, find gigs and get yourself out there',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 4v16m0 0l4-4m-4 4l-4-4m8 0H8m8 0h2a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v12a2 2 0 002 2h2" />
                          </svg>'
            ],
            [
                'name' => 'photographer',
                'guard_name' => 'web',
                'display_name' => 'Photographer',
                'description' => 'Capture and share your pictures, get hired for real work',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />'
            ],
            [
                'name' => 'videographer',
                'guard_name' => 'web',
                'display_name' => 'Videographer',
                'description' => 'Capture and share your videos, get hired for real work',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />'
            ],
            [
                'name' => 'designer',
                'guard_name' => 'web',
                'display_name' => 'Designer',
                'description' => 'Create and share your designs, get hired for real work',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />'
            ],
            [
                'name' => 'administrator',
                'guard_name' => 'web',

                'display_name' => 'Administrator',
                'description' => 'Manage the entire system',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 4v16m0 0l4-4m-4 4l-4-4m8 0H8m8 0h2a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v12a2 2 0 002 2h2" />
                          </svg>'
            ],
            // Service roles
            [
                'name' => 'service-owner',
                'guard_name' => 'web',
                'display_name' => 'Service Owner',
                'description' => 'Full control over the service',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
              </svg>'
            ],
            [
                'name' => 'service-manager',
                'guard_name' => 'web',
                'display_name' => 'Service Manager',
                'description' => 'Manage service operations',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>'
            ],
            [
                'name' => 'service-member',
                'guard_name' => 'web',
                'display_name' => 'Service Member',
                'description' => 'Basic service access',
                'icon' => '<svg class="mb-3 h-8 w-8 text-yns_yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>'
            ]
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'guard_name' => $roleData['guard_name'],
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'icon' => $roleData['icon']
                ]
            );
        }
    }
}