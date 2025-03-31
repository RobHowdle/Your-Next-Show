<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Rob',
            'last_name' => 'Howdle',
            'date_of_birth' => Carbon::createFromFormat('d-m-Y', '08-10-1994'),
            'email' => 'rob@yournextshow.co.uk',
            'email_verified_at' => now(),
            'password' => Hash::make('yfT11LpReC43(dV(U5'),
        ]);
    }
}