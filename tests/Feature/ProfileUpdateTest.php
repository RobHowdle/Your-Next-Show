<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;
use Database\Seeders\RoleSeeder;
use Database\Seeders\VenuesSeeder;
use Database\Seeders\PromoterSeeder;
use Database\Seeders\OtherServiceSeeder;
use Database\Seeders\OtherServicesListSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;
    use  WithFaker;

    protected $user;

    private function getRelationshipMethod(string $role): string
    {
        $relationships = [
            'venue' => 'venues',
            'promoter' => 'promoters',
            'photographer' => 'otherService',
            'videographer' => 'otherService',
            'designer' => 'otherService',
            'artist' => 'otherService'
        ];

        return $relationships[$role] ?? $role . 's';
    }

    public static function roleDataProvider(): array
    {
        return [
            'venue' => [
                'roleType' => 'venue',
                'modelClass' => Venue::class,
                'seederClass' => VenuesSeeder::class,
                'testingData' => [
                    'name' => 'Test Venue',
                    'contact_email' => 'venue@test.com',
                    'contact_number' => '+447111111111',
                    'location' => '123 Test Street',
                    'postal_town' => 'Test Town',
                    'contact_links' => [
                        'website' => 'https://example.com',
                        'facebook' => 'https://facebook.com/test',
                        'instagram' => 'https://instagram.com/test'
                    ]
                ]
            ],
            'promoter' => [
                'roleType' => 'promoter',
                'modelClass' => Promoter::class,
                'seederClass' => PromoterSeeder::class,
                'testingData' => [
                    'name' => 'Test Promoter',
                    'contact_email' => 'promoter@test.com',
                    'contact_number' => '+447111111111',
                    'location' => '456 Promoter Street',
                    'postal_town' => 'Promoter Town',
                    'contact_links' => [
                        'website' => 'https://example.com',
                        'facebook' => 'https://facebook.com/test',
                        'instagram' => 'https://instagram.com/test'
                    ]
                ]
            ],
            'photographer' => [
                'roleType' => 'photographer',
                'modelClass' => OtherService::class,
                'seederClass' => OtherServiceSeeder::class,
                'testingData' => [
                    'name' => 'Test Photographer',
                    'contact_email' => 'photographer@test.com',
                    'contact_number' => '+447111111111',
                    'location' => '789 Photo Street',
                    'postal_town' => 'Photo Town',
                    'contact_links' => [
                        'website' => 'https://example.com',
                        'facebook' => 'https://facebook.com/test',
                        'instagram' => 'https://instagram.com/test'
                    ],
                    'other_service_id' => 1
                ]
            ],
            'videographer' => [
                'roleType' => 'videographer',
                'modelClass' => OtherService::class,
                'seederClass' => OtherServiceSeeder::class,
                'testingData' => [
                    'name' => 'Test Videographer',
                    'contact_email' => 'videographer@test.com',
                    'contact_number' => '+447111111111',
                    'location' => '101 Video Street',
                    'postal_town' => 'Video Town',
                    'contact_links' => [
                        'website' => 'https://example.com',
                        'facebook' => 'https://facebook.com/test',
                        'instagram' => 'https://instagram.com/test'
                    ],
                    'other_service_id' => 2
                ]
            ],
            'designer' => [
                'roleType' => 'designer',
                'modelClass' => OtherService::class,
                'seederClass' => OtherServiceSeeder::class,
                'testingData' => [
                    'name' => 'Test Designer',
                    'contact_email' => 'designer@test.com',
                    'contact_number' => '+447111111111',
                    'location' => '202 Design Street',
                    'postal_town' => 'Design Town',
                    'contact_links' => [
                        'website' => 'https://example.com',
                        'facebook' => 'https://facebook.com/test',
                        'instagram' => 'https://instagram.com/test'
                    ],
                    'other_service_id' => 3
                ]
            ],
            'artist' => [
                'roleType' => 'artist',
                'modelClass' => OtherService::class,
                'seederClass' => OtherServiceSeeder::class,
                'testingData' => [
                    'name' => 'Test Artist',
                    'contact_email' => 'artist@test.com',
                    'contact_number' => '+447111111111',
                    'location' => '303 Artist Street',
                    'postal_town' => 'Artist Town',
                    'contact_links' => [
                        'website' => 'https://example.com',
                        'facebook' => 'https://facebook.com/test',
                        'instagram' => 'https://instagram.com/test'
                    ],
                    'other_service_id' => 4
                ]
            ]
        ];
    }

    /**
     * @dataProvider roleDataProvider
     */
    public function test_user_can_update_profile_for_role(
        string $roleType,
        string $modelClass,
        ?string $seederClass,
        array $testingData
    ) {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Seed all required data
        $this->seed(RoleSeeder::class);
        $this->seed(OtherServicesListSeeder::class);
        if ($seederClass) {
            $this->seed($seederClass);
        }

        // Create user and assign role
        $this->user = User::factory()->create();
        $roleModel = Role::where('name', $roleType)->first();
        $this->user->assignRole($roleModel);

        // Get first model from seeded data
        $model = $modelClass::first();
        if (!$model) {
            $this->fail("No {$roleType} found in database. Make sure seeder is working.");
        }

        // Get relationship method name
        $relationMethod = $this->getRelationshipMethod($roleType);

        // Attach the model to user using the correct relationship method
        $this->user->{$relationMethod}()->attach($model->id);

        // Store original data for verification
        $originalData = $model->toArray();
        $originalLinks = json_decode($model->contact_link, true) ?? [];

        $routeParams = [
            'dashboardType' => $roleType,
            'user' => $this->user->id,
        ];

        // Add the service-specific parameter
        if (in_array($roleType, ['photographer', 'videographer', 'designer', 'artist'])) {
            $routeParams['otherService'] = $model->id;  // For specialized services
        } else {
            $routeParams[$roleType] = $model->id;  // For venues/promoters
        }

        // Now dump the ACTUAL URL being used
        $url = route($roleType . '.update', $routeParams);
        dump("Final URL being accessed: " . $url);

        // Make the request with the complete route params
        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->put($url, $testingData);

        // Verify response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        // Refresh model
        $model->refresh();

        // Basic field assertions
        $this->assertEquals($testingData['name'], $model->name, 'Name not updated');
        $this->assertEquals($testingData['contact_email'], $model->contact_email, 'Email not updated');
        $this->assertEquals($testingData['contact_number'], $model->contact_number, 'Phone not updated');

        // Contact links assertions
        $actualLinks = json_decode($model->contact_link, true) ?? [];

        // Verify updated links
        foreach ($testingData['contact_links'] as $platform => $url) {
            $this->assertArrayHasKey(
                $platform,
                $actualLinks,
                "Platform $platform is missing from saved data"
            );
            $this->assertEquals(
                $url,
                $actualLinks[$platform],
                "URL for $platform not updated correctly"
            );
        }
    }

    /**
     * @dataProvider roleDataProvider
     */
    public function test_validation_fails_with_invalid_data_for_role(
        string $roleType,
        string $modelClass,
        ?string $seederClass,
        array $testingData
    ) {
        // Seed all required data
        $this->seed(RoleSeeder::class);
        $this->seed(OtherServicesListSeeder::class);
        if ($seederClass) {
            $this->seed($seederClass);
        }

        $routeParams = [
            'dashboardType' => $roleType,
            'user' => $this->user->id,
        ];

        // Create user and assign role
        $this->user = User::factory()->create();
        $roleModel = Role::where('name', $roleType)->first();
        $this->user->assignRole($roleModel);

        // Get first model from seeded data
        $model = $modelClass::first();
        if (!$model) {
            $this->fail("No {$roleType} found in database. Make sure seeder is working.");
        }

        if (in_array($roleType, ['photographer', 'videographer', 'designer', 'artist'])) {
            $routeParams['otherService'] = $model->id;  // Use 'otherService' as parameter name
        } else {
            $routeParams[$roleType] = $model->id;  // Normal parameter for venues/promoters
        }

        // Get relationship method name
        $relationMethod = $this->getRelationshipMethod($roleType);

        // Attach the model to user using the correct relationship method
        $this->user->{$relationMethod}()->attach($model->id);

        $invalidData = [
            'contact_email' => 'not-an-email',
            'contact_number' => 'not-a-number',
            'latitude' => 'not-a-latitude',
            'longitude' => 'not-a-longitude',
            'contact_links' => json_encode([
                'website' => 'not-a-url'
            ])
        ];

        $response = $this->actingAs($this->user)
            ->withHeaders(['Accept' => 'application/json'])
            ->put(route($roleType . '.update', $routeParams), $testingData);

        $response->assertJsonValidationErrors([
            'contact_email',
            'contact_number',
            'latitude',
            'longitude'
        ]);
    }

    // /** @test */
    // public function user_can_view_profile_edit_form()
    // {
    //     // Skip test if user not found
    //     if (!$this->user) {
    //         $this->markTestSkipped('User with ID 4 not found in database');
    //     }

    //     // Add debugging to see what's being sent
    //     $routeParams = [
    //         'dashboardType' => 'venue',
    //         'id' => $this->user->id
    //     ];

    //     $response = $this->actingAs($this->user)
    //         ->get(route('profile.edit', $routeParams));

    //     $response->assertStatus(200)
    //         ->assertViewIs('profile.edit')
    //         ->assertSee('User Profile Details');
    // }

    // /** @test */
    // public function user_can_update_all_basic_profile_fields()
    // {
    //     // Get the venue before update
    //     $venue = Venue::whereHas('linkedUsers', function ($query) {
    //         $query->where('user_id', $this->user->id);
    //     })->first();

    //     $updatedData = [
    //         'name' => $this->faker->company(),
    //         'contact_name' => $this->faker->name(),
    //         'contact_email' => $this->faker->email(),
    //         'contact_number' => '+44' . rand(7000000000, 7999999999),
    //         'location' => '123 Test Street',
    //         'postal_town' => 'Test Town',
    //         'latitude' => '51.5074',
    //         'longitude' => '-0.1278',
    //         'preferred_contact' => 'email',
    //         'description' => $this->faker->paragraph(),
    //         'contact_link' => [  // Not JSON encoded initially
    //             'website' => 'https://example.com',
    //             'facebook' => 'https://facebook.com/test',
    //             'twitter' => 'https://twitter.com/test',
    //             'x' => 'https://x.com/test',
    //             'youtube' => 'https://youtube.com/test',
    //             'instagram' => 'https://instagram.com/test'
    //         ]
    //     ];

    //     // JSON encode the contact links before the update
    //     $updatedData['contact_link'] = json_encode($updatedData['contact_link']);

    //     $response = $this->actingAs($this->user)
    //         ->put(route('venue.update', [
    //             'dashboardType' => 'venue',
    //             'user' => $this->user->id,
    //             'venue' => $venue->id
    //         ]), $updatedData);

    //     // Refresh venue from database
    //     $venue->refresh();

    //     // Compare contact links after decoding both to arrays
    //     $expectedLinks = json_decode($updatedData['contact_link'], true);
    //     $actualLinks = json_decode($venue->contact_link, true);

    //     // Sort both arrays by key for consistent comparison
    //     ksort($expectedLinks);
    //     ksort($actualLinks);

    //     // Assert each field was updated correctly
    //     $this->assertEquals($updatedData['name'], $venue->name, 'Venue name not updated');
    //     $this->assertEquals($updatedData['contact_name'], $venue->contact_name, 'Contact name not updated');
    //     $this->assertEquals($updatedData['contact_email'], $venue->contact_email, 'Contact email not updated');
    //     $this->assertEquals($updatedData['contact_number'], $venue->contact_number, 'Contact number not updated');
    //     $this->assertEquals($updatedData['location'], $venue->location, 'Location not updated');
    //     $this->assertEquals($updatedData['postal_town'], $venue->postal_town, 'Postal town not updated');
    //     $this->assertEquals($updatedData['latitude'], $venue->latitude, 'Latitude not updated');
    //     $this->assertEquals($updatedData['longitude'], $venue->longitude, 'Longitude not updated');
    //     $this->assertEquals($updatedData['preferred_contact'], $venue->preferred_contact, 'Preferred contact not updated');
    //     // Assert each social link individually for better error messages
    //     // Verify social links structure
    //     $actualLinks = json_decode($venue->contact_link, true);
    //     $this->assertIsArray($actualLinks, 'Contact links should be an array');
    //     $this->assertArrayHasKey('facebook', $actualLinks, 'Facebook link is missing');
    //     $this->assertArrayHasKey('instagram', $actualLinks, 'Instagram link is missing');
    //     $this->assertArrayHasKey('website', $actualLinks, 'Website link is missing');
    //     $this->assertArrayHasKey('youtube', $actualLinks, 'YouTube link is missing');
    //     $this->assertArrayHasKey('x', $actualLinks, 'X/Twitter link is missing');

    //     // Verify each link is a valid URL
    //     foreach ($actualLinks as $platform => $url) {
    //         $this->assertNotEmpty($url, "$platform URL should not be empty");
    //         $this->assertStringStartsWith('http', $url, "$platform URL should start with http");
    //     }
    //     $this->assertEquals($updatedData['description'], $venue->description, 'Description not updated');
    // }

    // /** @test */
    // public function user_cannot_update_profile_with_invalid_data()
    // {
    //     // Get the venue before update
    //     $venue = Venue::whereHas('linkedUsers', function ($query) {
    //         $query->where('user_id', $this->user->id);
    //     })->first();

    //     $invalidData = [
    //         'contact_email' => 'not-an-email', // Invalid email
    //         'contact_number' => 'not-a-number', // Invalid phone
    //         'latitude' => 'not-a-latitude', // Invalid latitude
    //         'longitude' => 'not-a-longitude', // Invalid longitude
    //         'contact_link' => json_encode([
    //             'website' => 'not-a-url',
    //             'facebook' => 'invalid-facebook-url',
    //             'instagram' => 'not-instagram-url'
    //         ])
    //     ];

    //     $response = $this->actingAs($this->user)
    //         ->withHeaders([
    //             'Accept' => 'application/json'
    //         ])
    //         ->put(route('venue.update', [
    //             'dashboardType' => 'venue',
    //             'user' => $this->user->id,
    //             'venue' => $venue->id
    //         ]), $invalidData);

    //     // Assert specific validation errors are returned
    //     $response->assertJsonValidationErrors([
    //         'contact_email' => 'The contact email field must be a valid email address.',
    //         'contact_number' => 'The contact number field format is invalid.',
    //         'latitude' => 'The latitude field must be a number.',
    //         'longitude' => 'The longitude field must be a number.'
    //     ]);

    //     // Verify the database wasn't updated
    //     $venue->refresh();

    //     // Verify original data is unchanged
    //     $this->assertNotEquals($invalidData['contact_email'], $venue->contact_email);
    //     $this->assertNotEquals($invalidData['contact_number'], $venue->contact_number);
    //     $this->assertNotEquals($invalidData['latitude'], $venue->latitude);
    //     $this->assertNotEquals($invalidData['longitude'], $venue->longitude);
    // }

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

    // /** @test */
    // public function validation_fails_with_invalid_email()
    // {
    //     // Get the venue before update
    //     $venue = Venue::whereHas('linkedUsers', function ($query) {
    //         $query->where('user_id', $this->user->id);
    //     })->first();

    //     $invalidData = [
    //         'contact_email' => 'invalid-email-format',
    //     ];

    //     $response = $this->actingAs($this->user)
    //         ->withHeaders([
    //             'Accept' => 'application/json'
    //         ])
    //         ->put(route('venue.update', [
    //             'dashboardType' => 'venue',
    //             'user' => $this->user->id,
    //             'venue' => $venue->id
    //         ]), $invalidData);

    //     // Assert validation errors
    //     $response->assertJsonValidationErrors([
    //         'contact_email' => 'The contact email field must be a valid email address.',
    //     ]);

    //     // Verify database wasn't updated
    //     $venue->refresh();
    //     $this->assertNotEquals($invalidData['contact_email'], $venue->contact_email);
    // }

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

    // /** @test */
    // public function location_data_is_properly_saved()
    // {
    //     // Get the venue before update
    //     $venue = Venue::whereHas('linkedUsers', function ($query) {
    //         $query->where('user_id', $this->user->id);
    //     })->first();

    //     $locationData = [
    //         'location' => '123 Test Street, London',
    //         'postal_town' => 'London',
    //         'latitude' => '51.5074',
    //         'longitude' => '-0.1278',
    //     ];

    //     $response = $this->actingAs($this->user)
    //         ->put(route('venue.update', [
    //             'dashboardType' => 'venue',
    //             'user' => $this->user->id,
    //             'venue' => $venue->id
    //         ]), $locationData);

    //     // Assert successful response
    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'success' => true,
    //             'message' => 'Profile updated successfully'
    //         ]);

    //     // Refresh venue from database
    //     $venue->refresh();

    //     // Assert each location field was updated correctly
    //     $this->assertEquals($locationData['location'], $venue->location, 'Location not updated');
    //     $this->assertEquals($locationData['postal_town'], $venue->postal_town, 'Postal town not updated');
    //     $this->assertEquals($locationData['latitude'], $venue->latitude, 'Latitude not updated');
    //     $this->assertEquals($locationData['longitude'], $venue->longitude, 'Longitude not updated');

    //     // Verify in database
    //     $this->assertDatabaseHas('venues', [
    //         'id' => $venue->id,
    //         'location' => $locationData['location'],
    //         'postal_town' => $locationData['postal_town'],
    //         'latitude' => $locationData['latitude'],
    //         'longitude' => $locationData['longitude'],
    //     ]);
    // }
}