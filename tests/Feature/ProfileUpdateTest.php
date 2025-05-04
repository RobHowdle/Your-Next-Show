<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Venue;
use Database\Seeders\RoleSeeder;
use Database\Seeders\VenuesSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;
    use  WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable RefreshDatabase to start with a clean state
        // $this->refreshDatabase();

        // Seed the roles table
        $this->seed(RoleSeeder::class);
        $this->seed(VenuesSeeder::class);

        // Check for role by name or display_name
        $role = Role::where('name', 'venue')
            ->orWhere('display_name', 'Venue')
            ->first();

        $venue = Venue::where('name', 'The Forum Music Studios')->first();


        // Create a user with factory and assign role
        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        // Create link between venue and user
        $this->user->venues()->attach($venue->id);
    }

    /** @test */
    public function user_can_view_profile_edit_form()
    {
        // Skip test if user not found
        if (!$this->user) {
            $this->markTestSkipped('User with ID 4 not found in database');
        }

        // Add debugging to see what's being sent
        $routeParams = [
            'dashboardType' => 'venue',
            'id' => $this->user->id
        ];

        $response = $this->actingAs($this->user)
            ->get(route('profile.edit', $routeParams));

        $response->assertStatus(200)
            ->assertViewIs('profile.edit')
            ->assertSee('User Profile Details');
    }

    /** @test */
    public function user_can_update_all_basic_profile_fields()
    {
        // Get the venue before update
        $venue = Venue::whereHas('linkedUsers', function ($query) {
            $query->where('user_id', $this->user->id);
        })->first();

        $updatedData = [
            'name' => $this->faker->company(),
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'contact_number' => '+44' . rand(7000000000, 7999999999),
            'location' => '123 Test Street',
            'postal_town' => 'Test Town',
            'latitude' => '51.5074',
            'longitude' => '-0.1278',
            'preferred_contact' => 'email',
            'description' => $this->faker->paragraph(),
            'contact_link' => [  // Not JSON encoded initially
                'website' => 'https://example.com',
                'facebook' => 'https://facebook.com/test',
                'twitter' => 'https://twitter.com/test',
                'x' => 'https://x.com/test',
                'youtube' => 'https://youtube.com/test',
                'instagram' => 'https://instagram.com/test'
            ]
        ];

        // JSON encode the contact links before the update
        $updatedData['contact_link'] = json_encode($updatedData['contact_link']);

        $response = $this->actingAs($this->user)
            ->put(route('venue.update', [
                'dashboardType' => 'venue',
                'user' => $this->user->id,
                'venue' => $venue->id
            ]), $updatedData);

        // Refresh venue from database
        $venue->refresh();

        // Compare contact links after decoding both to arrays
        $expectedLinks = json_decode($updatedData['contact_link'], true);
        $actualLinks = json_decode($venue->contact_link, true);

        // Sort both arrays by key for consistent comparison
        ksort($expectedLinks);
        ksort($actualLinks);

        // Assert each field was updated correctly
        $this->assertEquals($updatedData['name'], $venue->name, 'Venue name not updated');
        $this->assertEquals($updatedData['contact_name'], $venue->contact_name, 'Contact name not updated');
        $this->assertEquals($updatedData['contact_email'], $venue->contact_email, 'Contact email not updated');
        $this->assertEquals($updatedData['contact_number'], $venue->contact_number, 'Contact number not updated');
        $this->assertEquals($updatedData['location'], $venue->location, 'Location not updated');
        $this->assertEquals($updatedData['postal_town'], $venue->postal_town, 'Postal town not updated');
        $this->assertEquals($updatedData['latitude'], $venue->latitude, 'Latitude not updated');
        $this->assertEquals($updatedData['longitude'], $venue->longitude, 'Longitude not updated');
        $this->assertEquals($updatedData['preferred_contact'], $venue->preferred_contact, 'Preferred contact not updated');
        // Assert each social link individually for better error messages
        // Verify social links structure
        $actualLinks = json_decode($venue->contact_link, true);
        $this->assertIsArray($actualLinks, 'Contact links should be an array');
        $this->assertArrayHasKey('facebook', $actualLinks, 'Facebook link is missing');
        $this->assertArrayHasKey('instagram', $actualLinks, 'Instagram link is missing');
        $this->assertArrayHasKey('website', $actualLinks, 'Website link is missing');
        $this->assertArrayHasKey('youtube', $actualLinks, 'YouTube link is missing');
        $this->assertArrayHasKey('x', $actualLinks, 'X/Twitter link is missing');

        // Verify each link is a valid URL
        foreach ($actualLinks as $platform => $url) {
            $this->assertNotEmpty($url, "$platform URL should not be empty");
            $this->assertStringStartsWith('http', $url, "$platform URL should start with http");
        }
        $this->assertEquals($updatedData['description'], $venue->description, 'Description not updated');
    }

    /** @test */
    public function user_cannot_update_profile_with_invalid_data()
    {
        // Get the venue before update
        $venue = Venue::whereHas('linkedUsers', function ($query) {
            $query->where('user_id', $this->user->id);
        })->first();

        $invalidData = [
            'contact_email' => 'not-an-email', // Invalid email
            'contact_number' => 'not-a-number', // Invalid phone
            'latitude' => 'not-a-latitude', // Invalid latitude
            'longitude' => 'not-a-longitude', // Invalid longitude
            'contact_link' => json_encode([
                'website' => 'not-a-url',
                'facebook' => 'invalid-facebook-url',
                'instagram' => 'not-instagram-url'
            ])
        ];

        $response = $this->actingAs($this->user)
            ->withHeaders([
                'Accept' => 'application/json'
            ])
            ->put(route('venue.update', [
                'dashboardType' => 'venue',
                'user' => $this->user->id,
                'venue' => $venue->id
            ]), $invalidData);

        // Assert specific validation errors are returned
        $response->assertJsonValidationErrors([
            'contact_email' => 'The contact email field must be a valid email address.',
            'contact_number' => 'The contact number field format is invalid.',
            'latitude' => 'The latitude field must be a number.',
            'longitude' => 'The longitude field must be a number.'
        ]);

        // Verify the database wasn't updated
        $venue->refresh();

        // Verify original data is unchanged
        $this->assertNotEquals($invalidData['contact_email'], $venue->contact_email);
        $this->assertNotEquals($invalidData['contact_number'], $venue->contact_number);
        $this->assertNotEquals($invalidData['latitude'], $venue->latitude);
        $this->assertNotEquals($invalidData['longitude'], $venue->longitude);
    }

    /** @test */
    // public function user_can_update_password()
    // {
    //     $newPassword = 'NewPassword123!';

    //     $response = $this->actingAs($this->user)
    //         ->post(route('profile.update', [
    //             'dashboardType' => 'standard',
    //             'user' => $this->user->id
    //         ]), [
    //             'password' => $newPassword,
    //             'password_confirmation' => $newPassword,
    //         ]);

    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'success' => true,
    //             'message' => 'Profile updated successfully'
    //         ]);
    // }

    /** @test */
    public function validation_fails_with_invalid_email()
    {
        // Get the venue before update
        $venue = Venue::whereHas('linkedUsers', function ($query) {
            $query->where('user_id', $this->user->id);
        })->first();

        $invalidData = [
            'contact_email' => 'invalid-email-format',
        ];

        $response = $this->actingAs($this->user)
            ->withHeaders([
                'Accept' => 'application/json'
            ])
            ->put(route('venue.update', [
                'dashboardType' => 'venue',
                'user' => $this->user->id,
                'venue' => $venue->id
            ]), $invalidData);

        // Assert validation errors
        $response->assertJsonValidationErrors([
            'contact_email' => 'The contact email field must be a valid email address.',
        ]);

        // Verify database wasn't updated
        $venue->refresh();
        $this->assertNotEquals($invalidData['contact_email'], $venue->contact_email);
    }

    /** @test */
    // public function validation_fails_with_mismatched_passwords()
    // {
    //     $response = $this->actingAs($this->user)
    //         ->post(route('profile.update', [
    //             'dashboardType' => 'standard',
    //             'user' => $this->user->id
    //         ]), [
    //             'password' => 'NewPassword123!',
    //             'password_confirmation' => 'DifferentPassword123!',
    //         ]);

    //     $response->assertStatus(422)
    //         ->assertJsonValidationErrors(['password']);
    // }

    /** @test */
    public function location_data_is_properly_saved()
    {
        // Get the venue before update
        $venue = Venue::whereHas('linkedUsers', function ($query) {
            $query->where('user_id', $this->user->id);
        })->first();

        $locationData = [
            'location' => '123 Test Street, London',
            'postal_town' => 'London',
            'latitude' => '51.5074',
            'longitude' => '-0.1278',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('venue.update', [
                'dashboardType' => 'venue',
                'user' => $this->user->id,
                'venue' => $venue->id
            ]), $locationData);

        // Assert successful response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        // Refresh venue from database
        $venue->refresh();

        // Assert each location field was updated correctly
        $this->assertEquals($locationData['location'], $venue->location, 'Location not updated');
        $this->assertEquals($locationData['postal_town'], $venue->postal_town, 'Postal town not updated');
        $this->assertEquals($locationData['latitude'], $venue->latitude, 'Latitude not updated');
        $this->assertEquals($locationData['longitude'], $venue->longitude, 'Longitude not updated');

        // Verify in database
        $this->assertDatabaseHas('venues', [
            'id' => $venue->id,
            'location' => $locationData['location'],
            'postal_town' => $locationData['postal_town'],
            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
        ]);
    }
}