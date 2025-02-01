<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration for all roles.
     */
    public function test_successful_registration()
    {
        // Create roles if not exists
        $roles = ['promoter', 'venue', 'artist', 'photographer', 'videographer', 'designer'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Test registration for each role
        foreach (Role::all() as $role) {
            $response = $this->postJson(route('register'), [
                'first_name' => "Test",
                'last_name' => "User",
                'date_of_birth' => '2000-01-01',
                'email' => "user_{$role->name}@example.com",
                'password' => 'SecurePass123!ABC1D',
                'password_confirmation' => 'SecurePass123!ABC1D',
                'role' => $role->id,
            ]);

            $response->assertStatus(201);

            $user = User::where('email', "user_{$role->name}@example.com")->first();
            $this->assertNotNull($user);
            $this->assertTrue($user->hasRole($role->id));

            // Optional: Output success message
            $this->info("Successfully registered user with role: {$role->name}");
        }
    }

    /**
     * Test validation failures.
     */
    public function test_registration_fails_due_to_missing_fields()
    {
        $response = $this->postJson(route('register'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'date_of_birth', 'email', 'password', 'role']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '2000-01-01',
            'email' => 'not-an-email',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_short_password()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '2000-01-01',
            'email' => 'user@example.com',
            'password' => '123',
            'password_confirmation' => '123',
            'role' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_when_passwords_do_not_match()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '2000-01-01',
            'email' => 'user@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPass!',
            'role' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_with_invalid_role()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '2000-01-01',
            'email' => 'user@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 999, // Invalid role ID
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        $user = User::where('email', 'user@example.com')->first();
        $this->assertNull($user); // Ensure user isn't created with an invalid role
    }

    public function test_registration_fails_with_password_containing_first_or_last_name()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '2000-01-01',
            'email' => 'user@example.com',
            'password' => 'Test123!',
            'password_confirmation' => 'Test123!',
            'role' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_for_underage_user()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => now()->subYears(17)->toDateString(),
            'email' => 'underage@example.com',
            'password' => 'SecurePass123!!',
            'password_confirmation' => 'SecurePass123!!',
            'role' => 1,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'underage@example.com', 'underage' => true]);
    }

    public function test_registration_passes_for_adult_user()
    {
        $response = $this->postJson(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => now()->subYears(18)->toDateString(),
            'email' => 'adult@example.com',
            'password' => 'SecurePass123!!!',
            'password_confirmation' => 'SecurePass123!!!',
            'role' => 1,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'adult@example.com', 'underage' => false]);
    }
}