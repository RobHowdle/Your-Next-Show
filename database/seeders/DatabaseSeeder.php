<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\VenuesSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\OtherServiceSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\OtherServicesListSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PermissionsSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            // VenueTestSeeder::class, // For Testing Data
            VenuesSeeder::class,
            // VenueExtraInfoSeeder::class, // For Testing Data
            // PromoterTestSeeder::class, // Foir Testing Data
            PromoterSeeder::class,
            // PromoterVenueTestPivotSeeder::class, // For Testing Data
            // PromoterReviewTestSeeder::class, // For Testing Data
            OtherServicesListSeeder::class,
            OtherServiceSeeder::class,
            // FinanceTestDataSeeder::class,
            // UserServiceSeeder::class,
            // TodoTestDataSeeder::class,
        ]);
        $this->call(VenuesTableSeeder::class);
        $this->call(PromotersTableSeeder::class);
        $this->call(OtherServicesListTableSeeder::class);
        $this->call(OtherServicesTableSeeder::class);
    }
}
