<?php

namespace Database\Factories;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'date_of_birth' => $this->faker->date(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'location' => $this->faker->address(),
            'postal_town' => $this->faker->city(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'mailing_preferences' => [
                'newsletter' => true,
                'marketing' => true,
                'updates' => true
            ],
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Assign role
            $role = Role::where('name', 'venue')->first();
            $user->assignRole($role);

            // Set default modules
            $this->setDefaultModules($user, $role->name);

            // Set mailing preferences
            $this->setDefaultMailingPreferences($user);
        });
    }

    protected function setDefaultModules($user, $roleName)
    {
        $allModules = ['events', 'todo_list', 'notes', 'finances', 'documents', 'users', 'reviews', 'jobs'];
        $serviceableType = 'App\Models\Venue';

        foreach ($allModules as $module) {
            \App\Models\UserModuleSetting::create([
                'user_id' => $user->id,
                'serviceable_id' => Role::where('name', $roleName)->first()->id,
                'serviceable_type' => $serviceableType,
                'module_name' => $module,
                'is_enabled' => true
            ]);
        }
    }

    protected function setDefaultMailingPreferences($user)
    {
        $preferences = [
            'newsletter' => true,
            'marketing' => true,
            'updates' => true
        ];

        $user->mailing_preferences = $preferences;
        $user->save();
    }
}