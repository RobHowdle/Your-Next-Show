<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venue;
use App\Models\ApiKeys;
use App\Models\Promoter;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use App\Models\UserModuleSetting;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\StoreUpdateBandTypes;
use App\Http\Requests\StoreUpdateBandGenres;
use App\Http\Requests\BandProfileUpdateRequest;
use App\Http\Requests\VenueProfileUpdateRequest;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Requests\PromoterProfileUpdateRequest;
use App\Http\Requests\PhotographerProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;

class ProfileController extends Controller
{
    protected $passwordController;

    protected function getUserId()
    {
        return Auth::id();
    }

    public function __construct(PasswordController $passwordController)
    {
        $this->passwordController = $passwordController;
    }

    /**
     * Display the user's profile form.
     */
    public function edit($dashboardType, $userId): View
    {
        $user = User::where('id', $userId)->first();
        $roles = Role::where('name', '!=', 'administrator')->get();
        $userRole = $user->roles;

        // Initialize promoter variables
        $promoterData = [];
        $bandData = [];
        $venueData = [];
        $photographerUserData = [];
        $standardUserData = [];
        $designerData = [];

        // check dashboard type and get the data
        if ($dashboardType === 'promoter') {
            $promoterData = $this->getPromoterData($user);
        } elseif ($dashboardType === 'artist') {
            $bandData = $this->getBandData($user);
        } elseif ($dashboardType === 'venue') {
            $venueData = $this->getvenueData($user);
        } elseif ($dashboardType === 'photographer') {
            $photographerUserData = $this->getPhotographerData($user);
        } elseif ($dashboardType === 'standard') {
            $standardUserData = $this->getStandardUserData($user);
        } elseif ($dashboardType === 'designer') {
            $designerData = $this->getDesignerData($user);
        }

        $modulesWithSettings = $this->getModulesWithSettings($userId, $dashboardType);
        $communicationSettings = $this->getCommunicationSettings($userId, $dashboardType);

        return view('profile.edit', [
            'user' => $user,
            'dashboardType' => $dashboardType,
            'modules' => $modulesWithSettings,
            'promoterData' => $promoterData,
            'bandData' => $bandData,
            'venueData' => $venueData,
            'photographerUserData' => $photographerUserData,
            'standardUserData' => $standardUserData,
            'designerData' => $designerData,
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
            'userFirstName' => $user->first_name,
            'userLastName' => $user->last_name,
            'userEmail' => $user->email,
            'userDob' => $user->date_of_birth,
            'userLocation' => $user->location,
            'userPostalTown' => $user->postal_town,
            'userLat' => $user->latitude,
            'userLong' => $user->longitude,
            // 'modulesWithSettings' => $modulesWithSettings,
            'communications' => $communicationSettings,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update($dashboardType, ProfileUpdateRequest $request, $userId)
    {
        dd('update user');
        try {
            $user = User::findOrFail($userId);
            $userData = $request->validated();

            // Handle password update separately
            if (!empty($userData['password'])) {
                $passwordRequest = new UpdatePasswordRequest();
                $passwordRequest->setUserResolver(function () use ($user) {
                    return $user;
                });

                $passwordRequest->merge([
                    'password' => $userData['password'],
                    'password_confirmation' => $userData['password_confirmation']
                ]);

                if (!$this->passwordController) {
                    $this->passwordController = app(PasswordController::class);
                }

                $passwordResponse = $this->passwordController->update($passwordRequest);

                if ($passwordResponse->getStatusCode() !== 302) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password update failed'
                    ], 422);
                }
            }

            // Remove password fields from general update
            unset($userData['password']);
            unset($userData['password_confirmation']);

            if (isset($userData['userFirstName']) || isset($userData['userLastName'])) {
                $user->first_name = $userData['userFirstName'];
                $user->last_name = $userData['userLastName'];
            }

            if (isset($userData['userDob'])) {
                $user->date_of_birth = $userData['userDob'];
            }

            if (isset($userData['userEmail'])) {
                $user->email = $userData['userEmail'];
            }

            if (isset($userData['latitude']) && isset($userData['postal_town']) && isset($userData['longitude']) && isset($userData['location'])) {
                $user->location = $userData['location'];
                $user->postal_town = $userData['postal_town'];
                $user->latitude = $userData['latitude'];
                $user->longitude = $userData['longitude'];
            }

            if ($request->has('role') && $user->hasRole($request->role)) {
                $user->syncRoles([$request->role]);
            }

            $user->fill($userData);

            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile Update Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePromoter($dashboardType, PromoterProfileUpdateRequest $request, $user)
    {
        // Fetch the user
        $user = User::findOrFail($user);
        $userId = $user->id;
        $userData = $request->validated();

        if ($dashboardType == 'promoter') {
            // Fetch the promoter associated with the user via the service_user pivot table
            $promoter = Promoter::whereHas('linkedUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

            // Check if the promoter exists
            if ($promoter) {
                // Promoter Name
                if (isset($userData['name']) && $promoter->name !== $userData['name']) {
                    $promoter->update(['name' => $userData['name']]);
                }

                // Contact Name
                if (isset($userData['contact_name']) && $promoter->contact_name !== $userData['contact_name']) {
                    $promoter->update(['contact_name' => $userData['contact_name']]);
                }
                // Location
                if (isset($userData['location']) && isset($userData['latitude']) && isset($userData['longitude']) && isset($userData['postal_town'])) {
                    $promoter->update([
                        'location' => $userData['location'],
                        'latitude' => $userData['latitude'],
                        'longitude' => $userData['longitude'],
                        'postal_town' => $userData['postal_town'],
                    ]);
                }

                // Contact Email
                if (isset($userData['contact_email']) && $promoter->contact_email !== $userData['contact_email']) {
                    $promoter->update(['contact_email' => $userData['contact_email']]);
                }

                // Contact Number 
                if (isset($userData['contact_number']) && $promoter->contact_number !== $userData['contact_number']) {
                    $promoter->update(['contact_number' => $userData['contact_number']]);
                }

                // Contact Links
                if (isset($userData['contact_links']) && is_array($userData['contact_links'])) {
                    // Start with the existing `contact_links` array or an empty array if it doesn't exist
                    $updatedLinks = !empty($promoter->contact_link) ? json_decode($promoter->contact_link, true) : [];

                    // Iterate through the `contact_link` array from the request data
                    foreach ($userData['contact_links'] as $platform => $links) {
                        // Ensure we're setting only non-empty values
                        $updatedLinks[$platform] = !empty($links[0]) ? $links[0] : null;
                    }

                    // Filter out null values to remove platforms with no links
                    $updatedLinks = array_filter($updatedLinks);

                    // Encode the array back to JSON for storage and update the promoter record
                    $promoter->update(['contact_link' => json_encode($updatedLinks)]);
                }

                // About
                if (isset($userData['description']) && $promoter->description !== $userData['description']) {
                    $promoter->update(['description' => $userData['description']]);
                }

                // My Venues
                if (isset($userData['myVenues']) && $promoter->my_venues !== $userData['myVenues']) {
                    $promoter->update(['my_venues' => $userData['myVenues']]);
                }

                // Logo
                if (isset($userData['logo_url'])) {
                    $promoterLogoFile = $userData['logo_url'];

                    // Generate the file name
                    $promoterName = $request->input('name');
                    $promoterLogoExtension = $promoterLogoFile->getClientOriginalExtension() ?: $promoterLogoFile->guessExtension();
                    $promoterLogoFilename = Str::slug($promoterName) . '.' . $promoterLogoExtension;

                    // Store file in public disk
                    $path = $promoterLogoFile->storeAs('public/images/venue_logos', $promoterLogoFilename);

                    // Generate proper URL for public access
                    $logoUrl = asset(str_replace('public', 'storage', $path));

                    // Update database with relative path
                    $promoter->update(['logo_url' => $logoUrl]);
                }

                // Return success message with redirect
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])
                ]);
            } else {
                // Handle case where no promoter is linked to the user
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'Profile Failed to update'
                    ],
                    404
                );
            }
        }
    }

    public function updateVenue($dashboardType, VenueProfileUpdateRequest $request, $user)
    {
        $user = User::findOrFail($user);
        $userId = $user->id;
        $userData = $request->validated();

        if ($dashboardType == 'venue') {
            // Fetch the promoter associated with the user via the service_user pivot table
            $venue = Venue::whereHas('linkedUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

            // Check if the venue exists
            if ($venue) {
                // venue Name
                if (isset($userData['name']) && $venue->name !== $userData['name']) {
                    $venue->update(['name' => $userData['name']]);
                }

                // Contact Name
                if (isset($userData['contact_name']) && $venue->contact_name !== $userData['contact_name']) {
                    $venue->update(['contact_name' => $userData['contact_name']]);
                }

                // Location
                if (isset($userData['location']) && isset($userData['latitude']) && isset($userData['longitude']) && isset($userData['postal_town'])) {
                    $venue->update([
                        'location' => $userData['location'],
                        'latitude' => $userData['latitude'],
                        'longitude' => $userData['longitude'],
                        'postal_town' => $userData['postal_town'],
                    ]);
                }

                //W3W
                if (isset($userData['w3w'])) {
                    $venue->update(['w3w' => $userData['w3w']]);
                }

                // Contact Email
                if (isset($userData['contact_email']) && $venue->contact_email !== $userData['contact_email']) {
                    $venue->update(['contact_email' => $userData['contact_email']]);
                }

                // Contact Number 
                if (isset($userData['contact_number']) && $venue->contact_number !== $userData['contact_number']) {
                    $venue->update(['contact_number' => $userData['contact_number']]);
                }

                // Contact Links
                if (isset($userData['contact_links']) && is_array($userData['contact_links'])) {
                    // Start with the existing `contact_links` array or an empty array if it doesn't exist
                    $updatedLinks = !empty($venue->contact_link) ? json_decode($venue->contact_link, true) : [];

                    // Iterate through the `contact_link` array from the request data
                    foreach ($userData['contact_links'] as $platform => $links) {
                        // Ensure we're setting only non-empty values
                        $updatedLinks[$platform] = !empty($links[0]) ? $links[0] : null;
                    }

                    // Filter out null values to remove platforms with no links
                    $updatedLinks = array_filter($updatedLinks);

                    // Encode the array back to JSON for storage and update the promoter record
                    $venue->update(['contact_link' => json_encode($updatedLinks)]);
                }

                // About
                if (isset($userData['description']) && $venue->description !== $userData['description']) {
                    $venue->update(['description' => $userData['description']]);
                }

                // My Venues
                if (isset($userData['myVenues']) && $venue->my_venues !== $userData['myVenues']) {
                    $venue->update(['my_venues' => $userData['myVenues']]);
                }

                // In House Gear
                if (isset($userData['inHouseGear']) && $venue->in_house_gear !== $userData['inHouseGear']) {
                    $venue->update(['in_house_gear' => $userData['inHouseGear']]);
                }

                // Deposit Required
                if (isset($userData['deposit_required']) && $venue->deposit_required !== $userData['deposit_required']) {
                    $venue->update(['deposit_required' => $userData['deposit_required']]);
                }

                // Deposit Amount
                if (isset($userData['deposit_amount']) && $venue->deposit_amount !== $userData['deposit_amount']) {
                    $venue->update(['deposit_amount' => $userData['deposit_amount']]);
                }

                // Logo
                if (isset($userData['logo_url'])) {
                    $venueLogoFile = $userData['logo_url'];

                    // Generate the file name
                    $venueName = $request->input('name');
                    $venueLogoExtension = $venueLogoFile->getClientOriginalExtension() ?: $venueLogoFile->guessExtension();
                    $venueLogoFilename = Str::slug($venueName) . '.' . $venueLogoExtension;

                    // Store file in public disk
                    $path = $venueLogoFile->storeAs('public/images/venue_logos', $venueLogoFilename);

                    // Generate proper URL for public access
                    $logoUrl = asset(str_replace('public', 'storage', $path));

                    // Update database with relative path
                    $venue->update(['logo_url' => $logoUrl]);
                }

                // Capacity
                if (isset($userData['capacity'])) {
                    $venue->update(['capacity' => $userData['capacity']]);
                }

                // Additional Info
                if (isset($userData['additionalInfo'])) {
                    $venue->update(['additional_info' => $userData['additionalInfo']]);
                }

                // Return success message with redirect
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])
                ]);
            } else {
                // Handle case where no promoter is linked to the user
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'Profile Failed to update'
                    ],
                    404
                );
            }
        }
    }

    public function updateBand($dashboardType, BandProfileUpdateRequest $request, $user)
    {
        // Fetch the user
        $user = User::findOrFail($user);
        $userId = $user->id;
        $userData = $request->validated();

        if ($dashboardType == 'artist') {
            // Fetch the promoter associated with the user via the service_user pivot table
            $band = OtherService::where('other_service_id', 4)->whereHas('linkedUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

            // Check if the promoter exists
            if ($band) {
                // Promoter Name
                if (isset($userData['name']) && $band->name !== $userData['name']) {
                    $band->update(['name' => $userData['name']]);
                }
                // Contact Name
                if (isset($userData['contact_name']) && $band->contact_name !== $userData['contact_name']) {
                    $band->update(['contact_name' => $userData['contact_name']]);
                }
                // Location


                // Contact Email
                if (isset($userData['contact_email']) && $band->contact_email !== $userData['contact_email']) {
                    $band->update(['contact_email' => $userData['contact_email']]);
                }

                // Contact Number 
                if (isset($userData['contact_number']) && $band->contact_number !== $userData['contact_number']) {
                    $band->update(['contact_number' => $userData['contact_number']]);
                }

                // Contact Links
                if (isset($userData['contact_links']) && is_array($userData['contact_links'])) {
                    // Start with the existing `contact_links` array or an empty array if it doesn't exist
                    $updatedLinks = !empty($band->contact_link) ? json_decode($band->contact_link, true) : [];

                    // Iterate through the `contact_link` array from the request data
                    foreach ($userData['contact_links'] as $platform => $links) {
                        // Ensure we're setting only non-empty values
                        $updatedLinks[$platform] = !empty($links[0]) ? $links[0] : null;
                    }

                    // Filter out null values to remove platforms with no links
                    $updatedLinks = array_filter($updatedLinks);

                    // Encode the array back to JSON for storage and update the promoter record
                    $band->update(['contact_link' => json_encode($updatedLinks)]);
                }

                // Stream Links
                if (isset($userData['stream_links'])) {
                    // Decode the stored stream URLs from JSON to an array
                    $storedStreamLinks = json_decode($band->stream_urls, true);

                    // Ensure $userData['stream_links'] is an array
                    if (is_array($userData['stream_links'])) {
                        // If the stored stream links are different from the user input, update the database
                        if ($storedStreamLinks !== $userData['stream_links']) {
                            // Encode the user input as JSON and update
                            $band->update(['stream_urls' => json_encode($userData['stream_links'])]);
                        }
                    } else {
                        // If $userData['stream_links'] is not an array, handle the error
                        dd('Error: $userData[\'stream_links\'] is not an array');
                    }
                }

                // About
                if (isset($userData['about']) && $band->description !== $userData['about']) {
                    $band->update(['description' => $userData['about']]);
                }

                // Members
                if (isset($userData['members'])) {
                    $storedMembers = json_decode($band->members, true);
                    if ($storedMembers !== $userData['members']) {
                        $band->update(['members' => json_encode($userData['members'])]);
                    }
                }

                // Genres
                if (isset($userData['genres'])) {
                    $storedGenres = json_decode($band->genre, true);
                    if ($storedGenres !== $userData['genres']) {
                        $band->update(['genre' => json_encode($userData['genres'])]);
                    }
                }

                // Logo
                if (isset($userData['logo_url'])) {
                    $bandLogoFile = $userData['logo_url'];

                    // Generate the file name
                    $bandName = $request->input('name');
                    $bandLogoExtension = $bandLogoFile->getClientOriginalExtension() ?: $bandLogoFile->guessExtension();
                    $bandLogoFilename = Str::slug($bandName) . '.' . $bandLogoExtension;

                    // Store the file
                    $bandLogoFile->move(storage_path('app/public/images/band_logos'), $bandLogoFilename);

                    // Get the URL to the file
                    $logoUrl = Storage::url('images/band_logos/' . $bandLogoFilename);

                    // Update database
                    $band->update(['logo_url' => $logoUrl]);
                }

                // Return success message with redirect
                return redirect()->route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])->with('status', 'profile-updated');
            } else {
                // Handle case where no promoter is linked to the user
                return response()->json(['error' => 'Band not found'], 404);
            }
        }
    }

    public function updatePhotographer($dashboardType, PhotographerProfileUpdateRequest $request, $user)
    {
        try {
            // Validate dashboard type
            if ($dashboardType !== 'photographer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid dashboard type'
                ], 400);
            }

            // Find user with error handling
            try {
                $user = User::findOrFail($user);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $userData = $request->validated();

            // Find photographer with error handling
            $photographer = OtherService::where('other_service_id', 1)
                ->whereHas('linkedUsers', fn($query) => $query->where('user_id', $user->id))
                ->first();

            if (!$photographer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photographer not found'
                ], 404);
            }

            DB::beginTransaction();
            try {
                // Update basic fields
                $fieldsToUpdate = ['name', 'contact_name', 'contact_email', 'contact_number', 'description'];
                $updates = [];
                foreach ($fieldsToUpdate as $field) {
                    if (isset($userData[$field]) && $photographer->$field !== $userData[$field]) {
                        $updates[$field] = $userData[$field];
                    }
                }

                if (!empty($updates)) {
                    $photographer->update($updates);
                }

                // Contact Links
                if (isset($userData['contact_links'])) {
                    $this->updateJsonField($photographer, 'contact_link', $userData['contact_links']);
                }

                // Genres
                if (isset($userData['genres'])) {
                    $this->updateJsonField($photographer, 'genre', $userData['genres']);
                }

                // Logo Upload
                if (isset($userData['logo_url'])) {
                    try {
                        $logoPath = $this->uploadLogo($userData['logo_url'], $userData['name']);
                        $photographer->update(['logo_url' => $logoPath]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to upload logo: ' . $e->getMessage()
                        ], 500);
                    }
                }

                // Working Times
                if (isset($userData['working_times'])) {
                    $weekDaysOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

                    // Sort and validate working times
                    $sortedWorkingTimes = array_merge(
                        array_flip($weekDaysOrder),
                        array_intersect_key($userData['working_times'], array_flip($weekDaysOrder))
                    );

                    $sortedWorkingTimes = array_filter($sortedWorkingTimes, fn($value) => $value !== null);

                    // Validate time ranges
                    foreach ($sortedWorkingTimes as $day => $time) {
                        if (is_array($time)) {
                            $start = $time['start'] ?? null;
                            $end = $time['end'] ?? null;

                            if ($start && $end) {
                                if ($start >= $end) {
                                    DB::rollBack();
                                    return response()->json([
                                        'success' => false,
                                        'message' => "Start time must be earlier than end time for $day."
                                    ], 422);
                                }
                            }
                        }
                    }

                    $photographer->update(['working_times' => json_encode($sortedWorkingTimes)]);
                }

                DB::commit();

                return redirect()
                    ->route('profile.edit', [
                        'dashboardType' => $dashboardType,
                        'id' => $user->id
                    ])
                    ->with('success', 'Photographer profile updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update photographer profile: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper for JSON Fields
    protected function updateJsonField($photographer, $field, $data)
    {
        $existingData = json_decode($photographer->$field, true) ?? [];
        $updatedData = array_merge($existingData, $data);
        $photographer->update([$field => json_encode(array_filter($updatedData))]);
    }

    // Helper for Logo Upload
    protected function uploadLogo($file, $name)
    {
        $filename = Str::slug($name) . '.' . $file->getClientOriginalExtension();
        $file->move(storage_path('app/public/images/photographer_logos'), $filename);
        return Storage::url('images/photographer_logos/' . $filename);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    protected function getCommunicationSettings($userId, $dashboardType)
    {
        $user = User::findOrFail($userId);
        // Get default preferences from config
        $defaultPreferences = config('mailing_preferences.communication_preferences');
        if (!$defaultPreferences) {
            \Log::error('Mailing preferences config not loaded');
            return [];
        }

        // Get user preferences from JSON field and ensure it's an array
        $userPreferences = is_array($user->mailing_preferences)
            ? $user->mailing_preferences
            : json_decode($user->mailing_preferences, true) ?? [];

        $communicationSettings = [];

        // Map each preference to include name, description and enabled status
        foreach ($defaultPreferences as $key => $preference) {
            $communicationSettings[$key] = [
                'name' => $preference['name'],
                'description' => $preference['description'],
                'is_enabled' => isset($userPreferences[$key])
                    ? ($userPreferences[$key] === true ? 1 : 0)
                    : ($preference['enabled'] ? 1 : 0)
            ];
        }

        return $communicationSettings;
    }

    protected function getModulesWithSettings($userId, $dashboardType)
    {
        // Load all modules from config
        $modules = config('modules.modules');

        $modulesWithSettings = UserModuleSetting::where('user_id', $userId)
            ->get()
            ->mapWithKeys(function ($module) {
                return [$module->module_name => [
                    'is_enabled' => $module->is_enabled,
                    'description' => $module->description
                ]];
            })->toArray();

        return $modulesWithSettings;
    }

    // Get Data for Dashboard Type User
    private function getPromoterData(User $user)
    {
        $promoter = $user->promoters()->first();

        // Basic Information
        $name = $promoter ? $promoter->name : '';
        $location = $promoter ? $promoter->location : '';
        $postalTown = $promoter ? $promoter->postal_town : '';
        $lat = $promoter ? $promoter->latitude : '';
        $long = $promoter ? $promoter->longitude : '';
        $logo = $promoter && $promoter->logo_url
            ? (filter_var($promoter->logo_url, FILTER_VALIDATE_URL) ? $promoter->logo_url : Storage::url($promoter->logo_url))
            : asset('images/system/yns_no_image_found.png');

        $contact_name = $promoter ? $promoter->contact_name : '';
        $contact_email = $promoter ? $promoter->contact_email : '';
        $contact_number = $promoter ? $promoter->contact_number : '';
        $contactLinks = $promoter ? json_decode($promoter->contact_link, true) : [];

        $platforms = [];
        $platformsToCheck = ['facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // Initialize the platforms array with empty strings for each platform
        foreach ($platformsToCheck as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach ($platformsToCheck as $platform) {
                // Only add the link if the platform exists in the $contactLinks array
                if (isset($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                }
            }
        }

        // About Section
        $description = $promoter ? $promoter->description : '';

        // My Venues
        $myVenues = $promoter ? $promoter->my_venues : '';

        // My Events
        $myEvents = $promoter ? $promoter->events()->with('venues')->get() : collect();
        $uniqueBands = $this->getUniqueBandsForPromoterEvents($promoter->id);

        // Genres
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $profileGenres = is_array($promoter->genre) ? $promoter->genre : json_decode($promoter->genre, true);
        $normalizedProfileGenres = [];
        if ($profileGenres) {
            foreach ($profileGenres as $genreName => $genreData) {
                $normalizedProfileGenres[$genreName] = [
                    'all' => $genreData['all'] ?? 'false',
                    'subgenres' => isset($genreData['subgenres'][0])
                        ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
                        : []
                ];
            }
        }

        $bandTypes = json_decode($promoter->band_type) ?? [];
        $apiProviders = config('api_providers.providers');
        $apiKeys = ApiKeys::where('serviceable_id', $promoter->id)->where('serviceable_type', get_class($promoter))->get();

        if ($apiKeys) {
            $apiKeys = $apiKeys->map(function ($apiKey) {
                if ($apiKey->is_active) {
                    return [
                        'id' => $apiKey->id,
                        'type' => $apiKey->key_type,
                        'key' => $apiKey->api_key,
                        'secret' => $apiKey->api_secret,
                    ];
                }
            });
        }

        return [
            'promoter' => $promoter,
            'promoterId' => $promoter->id,
            'name' => $name,
            'location' => $location,
            'postalTown' => $postalTown,
            'lat' => $lat,
            'long' => $long,
            'logo' => $logo,
            'description' => $description,
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_number' => $contact_number,
            'platforms' => $platforms,
            'platformsToCheck' => $platformsToCheck,
            'myVenues' => $myVenues,
            'myEvents' => $myEvents,
            'uniqueBands' => $uniqueBands,
            'genres' => $genres,
            'profileGenres' => $profileGenres,
            'isAllGenres' => $isAllGenres,
            'normalizedProfileGenres' => $normalizedProfileGenres,
            'bandTypes' => $bandTypes,
            'apiProviders' => $apiProviders,
            'apiKeys' => $apiKeys,
        ];
    }

    private function getVenueData(User $user)
    {
        $venue = $user->venues()->first();

        // Basic Information
        $name = $venue ? $venue->name : '';
        $location = $venue ? $venue->location : '';
        $postalTown = $venue ? $venue->postal_town : '';
        $lat = $venue ? $venue->latitude : '';
        $long = $venue ? $venue->longitude : '';
        $w3w = $venue ? $venue->w3w : '';
        $logo = $venue && $venue->logo_url
            ? (filter_var($venue->logo_url, FILTER_VALIDATE_URL) ? $venue->logo_url : Storage::url($venue->logo_url))
            : asset('images/system/yns_no_image_found.png');

        $capacity = $venue ? $venue->capacity : '';
        $contact_name = $venue ? $venue->contact_name : '';
        $contact_number = $venue ? $venue->contact_number : '';
        $contact_email = $venue ? $venue->contact_email : '';
        $contactLinks = $venue ? json_decode($venue->contact_link, true) : [];

        $platforms = [];
        $platformsToCheck = ['facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // Initialize the platforms array with empty strings for each platform
        foreach ($platformsToCheck as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach ($platformsToCheck as $platform) {
                // Only add the link if the platform exists in the $contactLinks array
                if (isset($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                }
            }
        }

        // About Section
        $description = $venue ? $venue->description : '';

        // In House Gear
        $inHouseGear = $venue ? $venue->in_house_gear : '';

        // My Events
        $myEvents = $venue ? $venue->events()->with('venues')->get() : collect();
        $uniqueBands = $this->getUniqueBandsForPromoterEvents($venue->id);

        // Genres
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $profileGenres = is_array($venue->genre) ? $venue->genre : json_decode($venue->genre, true);
        $normalizedProfileGenres = [];
        if ($profileGenres) {
            foreach ($profileGenres as $genreName => $genreData) {
                $normalizedProfileGenres[$genreName] = [
                    'all' => $genreData['all'] ?? 'false',
                    'subgenres' => isset($genreData['subgenres'][0])
                        ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
                        : []
                ];
            }
        }

        $bandTypes = json_decode($venue->band_type) ?? [];
        $additionalInfo = $venue ? $venue->additional_info : '';

        return [
            'venue' => $venue,
            'name' => $name,
            'location' => $location,
            'postalTown' => $postalTown,
            'lat' => $lat,
            'long' => $long,
            'w3w' => $w3w,
            'logo' => $logo,
            'description' => $description,
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_number' => $contact_number,
            'platforms' => $platforms,
            'platformsToCheck' => $platformsToCheck,
            'inHouseGear' => $inHouseGear,
            'myEvents' => $myEvents,
            'uniqueBands' => $uniqueBands,
            'genres' => $genres,
            'profileGenres' => $profileGenres,
            'isAllGenres' => $isAllGenres,
            'normalizedProfileGenres' => $normalizedProfileGenres,
            'bandTypes' => $bandTypes,
            'capacity' => $capacity,
            'additionalInfo' => $additionalInfo,
        ];
    }

    private function getBandData(User $user)
    {
        $band = $user->otherService("Artist")->first();

        $name = $band ? $band->name : '';
        $location = $band ? $band->location : '';
        $logo = $band ? $band->logo_url : 'images/system/yns_logo.png';
        $phone = $band ? $band->contact_number : '';
        $contact_name = $band ? $band->contact_name : '';
        $contact_email = $band ? $band->contact_email : '';
        $contact_number = $band ? $band->contact_number : '';
        $contactLinks = $band ? json_decode($band->contact_link, true) : [];

        $platforms = [];
        $platformsToCheck = ['website', 'facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // Initialize the platforms array with empty strings for each platform
        foreach ($platformsToCheck as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach ($platformsToCheck as $platform) {
                // Only add the link if the platform exists in the $contactLinks array
                if (isset($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                }
            }
        }

        $about = $band ? $band->description : '';
        $myEvents = $band ? $band->events()->with('venues')->get() : collect();
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $artistGenres = is_array($band->genre) ? $band->genre : json_decode($band->genre, true);
        $normalizedArtistGenres = [];

        if ($artistGenres) {
            foreach ($artistGenres as $genreName => $genreData) {
                $normalizedArtistGenres[$genreName] = [
                    'all' => $genreData['all'] ?? 'false',
                    'subgenres' => isset($genreData['subgenres'][0])
                        ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
                        : []
                ];
            }
        }

        $bandTypes = json_decode($band->band_type) ?? [];
        $streamLinks = json_decode($band->stream_urls, true);

        $streamPlatforms = [];
        $streamPlatformsToCheck = ['spotify', 'apple-music', 'youtube-music', 'amazon-music', 'bandcamp', 'soundcloud'];

        $members = is_array($band->members) ? $band->members : json_decode($band->members, true);

        return [
            'artist' => $band,
            'name' => $name,
            'location' => $location,
            'logo' => $logo,
            'phone' => $phone,
            'about' => $about,
            'myEvents' => $myEvents,
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_number' => $contact_number,
            'platforms' => $platforms,
            'platformsToCheck' => $platformsToCheck,
            'genres' => $genres,
            'artistGenres' => $artistGenres,
            'isAllGenres' => $isAllGenres,
            'designerGenres' => $normalizedArtistGenres,
            'bandTypes' => $bandTypes,
            'streamLinks' => $streamLinks,
            'streamPlatformsToCheck' => $streamPlatformsToCheck,
            'members' => $members
        ];
    }

    private function getPhotographerData(User $user)
    {
        $photographer = $user->otherService("Photographer")->first();
        $serviceableId = $photographer->id;
        $serviceableType = 'App\Models\OtherService';

        // Basic Information
        $photographerName = $photographer ? $photographer->name : '';
        $photographerLocation = $photographer ? $photographer->location : '';
        $photographerPostalTown = $photographer ? $photographer->postal_town : '';
        $photographerLat = $photographer ? $photographer->latitude : '';
        $photographerLong = $photographer ? $photographer->longitude : '';
        $logo = $photographer && $photographer->logo_url
            ? (filter_var($photographer->logo_url, FILTER_VALIDATE_URL) ? $photographer->logo_url : Storage::url($photographer->logo_url))
            : asset('images/system/yns_no_image_found.png');
        $contact_name = $photographer ? $photographer->contact_name : '';
        $contact_number = $photographer ? $photographer->contact_number : '';
        $contact_email = $photographer ? $photographer->contact_email : '';
        $contactLinks = $photographer ? json_decode($photographer->contact_link, true) : [];

        $platforms = [];
        $platformsToCheck = ['website', 'facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // Initialize the platforms array with empty strings for each platform
        foreach ($platformsToCheck as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach ($platformsToCheck as $platform) {
                // Only add the link if the platform exists in the $contactLinks array
                if (isset($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                }
            }
        }

        $description = $photographer ? $photographer->description : '';
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true);
        $genres = $data['genres'];
        $photographerGenres = is_array($photographer->genre) ? $photographer->genre : json_decode($photographer->genre, true);
        $portfolioLink = $photographer ? $photographer->portfolio_link : '';
        $waterMarkedPortfolioImages = $photographer->portfolio_images;

        if (!is_array($waterMarkedPortfolioImages)) {
            try {
                $waterMarkedPortfolioImages = json_decode($waterMarkedPortfolioImages, true);
            } catch (\Exception $e) {
                throw new \Exception("Portfolio images could not be converted to an array.");
            }
        }

        $groupedEnvironmentTypes = config('environment_types');

        $environmentTypes = json_decode($photographer->environment_type, true);
        $groupedData = [];

        foreach ($groupedEnvironmentTypes as $groupName => $items) {
            foreach ($items as $item) {
                if ($environmentTypes && is_array($environmentTypes)) {
                    $groupedData[$groupName][] = $item;
                }
            }
        }

        $workingTimes = is_array($photographer->working_times) ? $photographer->working_times : json_decode($photographer->working_times, true);
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $photographerGenres = is_array($photographer->genre) ? $photographer->genre : json_decode($photographer->genre, true);
        $normalizedPhotographerGenres = [];
        if ($photographerGenres) {
            foreach ($photographerGenres as $genreName => $genreData) {
                $normalizedPhotographerGenres[$genreName] = [
                    'all' => $genreData['all'] ?? 'false',
                    'subgenres' => isset($genreData['subgenres'][0])
                        ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
                        : []
                ];
            }
        }

        $bandTypes = json_decode($photographer->band_type) ?? [];

        return [
            'photographer' => $photographer,
            'photographerName' => $photographerName,
            'photographerLocation' => $photographerLocation,
            'photographerPostalTown' => $photographerPostalTown,
            'photographerLat' => $photographerLat,
            'photographerLong' => $photographerLong,
            'logo' => $logo,
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_number' => $contact_number,
            'platforms' => $platforms,
            'platformsToCheck' => $platformsToCheck,
            'description' => $description,
            'genres' => $genres,
            'photographerGenres' => $photographerGenres,
            'portfolio_link' => $portfolioLink,
            'serviceableId' => $serviceableId,
            'serviceableType' => $serviceableType,
            'waterMarkedPortfolioImages' => $waterMarkedPortfolioImages,
            'environmentTypes' => $environmentTypes,
            'groups' => $groupedData,
            'workingTimes' => $workingTimes,
            'isAllGenres' => $isAllGenres,
            'photographerGenres' => $normalizedPhotographerGenres,
            'bandTypes' => $bandTypes,
        ];
    }

    private function getStandardUserData(User $user)
    {
        $standardUser = $user->standardUser()->first();
        $serviceableId = $standardUser->id;
        $serviceableType = 'App\Models\StandardUser';

        $name = $standardUser ? $standardUser->name : '';
        $location = $standardUser ? $standardUser->location : '';
        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $standardUserGenres = is_array($standardUser->genre) ? $standardUser->genre : json_decode($standardUser->genre, true);
        $normalizedStandardUserGenres = [];

        foreach ($standardUserGenres as $genreName => $genreData) {
            $normalizedStandardUserGenres[$genreName] = [
                'all' => $genreData['all'] ?? 'false',
                'subgenres' => isset($genreData['subgenres'][0])
                    ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
                    : []
            ];
        }

        $bandTypes = json_decode($standardUser->band_type) ?? [];

        return [
            'standardUser' => $standardUser,
            'name' => $name,
            'location' => $location,
            'genres' => $genres,
            'genres' => $genres,
            'isAllGenres' => $isAllGenres,
            'standardUserGenres' => $normalizedStandardUserGenres,
            'bandTypes' => $bandTypes
        ];
    }

    private function getDesignerData(User $user)
    {
        $designer = $user->otherService("Designer")->first();

        $serviceableId = $designer->id;
        $serviceableType = 'App\Models\OtherService';

        // Basic Information
        $designerName = $designer ? $designer->name : '';
        $designerLocation = $designer ? $designer->location : '';
        $designerPostalTown = $designer ? $designer->postal_town : '';
        $designerLat = $designer ? $designer->latitude : '';
        $designerLong = $designer ? $designer->longitude : '';
        $logo = $designer && $designer->logo_url
            ? (filter_var($designer->logo_url, FILTER_VALIDATE_URL) ? $designer->logo_url : Storage::url($designer->logo_url))
            : asset('images/system/yns_no_image_found.png');

        $contact_name = $designer ? $designer->contact_name : '';
        $contact_email = $designer ? $designer->contact_email : '';
        $contact_number = $designer ? $designer->contact_number : '';
        $contactLinks = $designer ? json_decode($designer->contact_link, true) : [];

        $platforms = [];
        $platformsToCheck = ['website', 'facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // Initialize the platforms array with empty strings for each platform
        foreach ($platformsToCheck as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach ($platformsToCheck as $platform) {
                // Only add the link if the platform exists in the $contactLinks array
                if (isset($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                }
            }
        }

        $description = $designer ? $designer->description : '';

        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $designerGenres = is_array($designer->genre) ? $designer->genre : json_decode($designer->genre, true);
        $normalizedDesignerGenres = [];
        if ($designerGenres) {
            foreach ($normalizedDesignerGenres as $genreName => $genreData) {
                $normalizedPromoterGenres[$genreName] = [
                    'all' => $genreData['all'] ?? 'false',
                    'subgenres' => isset($genreData['subgenres'][0])
                        ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
                        : []
                ];
            }
        }

        $bandTypes = json_decode($designer->band_type) ?? [];

        $portfolioLink = $designer ? $designer->portfolio_link : '';
        $waterMarkedPortfolioImages = $designer->portfolio_images;

        // Ensure it's an array
        if (!is_array($waterMarkedPortfolioImages)) {
            try {
                $waterMarkedPortfolioImages = json_decode($waterMarkedPortfolioImages, true);
            } catch (\Exception $e) {
                throw new \Exception("Portfolio images could not be converted to an array.");
            }
        }

        $groupedEnvironmentTypes = config('environment_types');
        $environmentTypes = json_decode($designer->environment_type, true);
        $groupedData = [];

        foreach ($groupedEnvironmentTypes as $groupName => $items) {
            foreach ($items as $item) {
                if (in_array($item, $environmentTypes)) {
                    $groupedData[$groupName][] = $item;
                }
            }
        }

        $workingTimes = is_array($designer->working_times) ? $designer->working_times : json_decode($designer->working_times, true);

        return [
            'designer' => $designer,
            'designerId'  => $designer->id,
            'designerName' => $designerName,
            'designerLocation' => $designerLocation,
            'designerPostalTown' => $designerPostalTown,
            'designerLat' => $designerLat,
            'designerLong' => $designerLong,
            'logo' => $logo,
            'description' => $description,
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_number' => $contact_number,
            'platforms' => $platforms,
            'platformsToCheck' => $platformsToCheck,
            'genres' => $genres,
            'designerGenres' => $designerGenres,
            'portfolio_link' => $portfolioLink,
            'serviceableId' => $serviceableId,
            'serviceableType' => $serviceableType,
            'waterMarkedPortfolioImages' => $waterMarkedPortfolioImages,
            'environmentTypes' => $environmentTypes,
            'groups' => $groupedData,
            'workingTimes' => $workingTimes,
            'isAllGenres' => $isAllGenres,
            'designerGenres' => $normalizedDesignerGenres,
            'bandTypes' => $bandTypes,
        ];
    }

    public function addRole(Request $request)
    {
        try {
            // Retrieve the user
            $user = User::findOrFail($request->id);
            // \Log::info('User found: ', [$user]);

            // Validate the incoming request
            $request->validate([
                'roleId' => 'required|exists:roles,id', // Ensure roleId is valid
            ]);

            // Retrieve the role
            $role = Role::find($request->roleId);
            // \Log::info('Role found: ', [$role]);

            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
            }

            // Check if the user already has the role
            if ($user->hasRole($role->name)) {
                return response()->json(['success' => false, 'message' => 'User already has this role.'], 400);
            }

            // Add the new role to the user
            $user->assignRole($role->name);  // This adds the role, doesn't replace existing roles

            // Return the response with success message and role name
            return response()->json([
                'success' => true,
                'message' => 'Role added successfully.',
                'newRoleName' => $role->name
            ]);
        } catch (\Exception $e) {
            // Log the error and return a response
            // \Log::error('Error adding role: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the role.'
            ], 500);
        }
    }

    public function deleteRole(Request $request)
    {
        try {
            // Retrieve the user
            $user = User::findOrFail($request->id);
            // \Log::info('User found: ', [$user]);

            // Validate the incoming request
            $request->validate([
                'roleId' => 'required|exists:roles,id', // Ensure roleId is valid
            ]);

            // Retrieve the role
            $role = Role::find($request->roleId);
            // \Log::info('Role found: ', [$role]);

            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
            }

            // Check if the user has the role
            if (!$user->hasRole($role->name)) {
                return response()->json(['success' => false, 'message' => 'User does not have this role.'], 400);
            }

            // Remove the role from the user
            $user->removeRole($role->name);  // This removes the role from the user

            // Return the response with success message and role name
            return response()->json([
                'success' => true,
                'message' => 'Role removed successfully.',
                'removedRoleName' => $role->name
            ]);
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error removing role: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the role.'
            ], 500);
        }
    }

    /**
     * Get a unique list of bands linked to events associated with the given promoter.
     *
     * @param  int  $promoterId
     * @return \Illuminate\Support\Collection
     */
    private function getUniqueBandsForPromoterEvents($promoterId)
    {
        // Fetch unique bands linked to events that the promoter is associated with
        return OtherService::where('other_service_id', 4)
            ->whereHas('events', function ($query) use ($promoterId) {
                $query->whereHas('promoters', function ($q) use ($promoterId) {
                    $q->where('promoter_id', $promoterId);
                });
            })
            ->distinct()
            ->get();
    }

    /**
     * Uploading Portfolio Sample Images
     */
    public function uploadPortfolioImages($dashboardType, Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if the file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $userId = auth()->id();
            $user = auth()->user();

            // Create a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Set custom directory for storing the file
            if ($dashboardType !== 'promoter' && $dashboardType !== 'venue') {
                $customPath = "images/other_services/{$dashboardType}/portfolio_images/{$userId}";
            } else {
                $customPath = "images/{$dashboardType}/portfolio_images/{$userId}";
            }

            // Store the image in the specified path
            try {
                $path = $file->storeAs($customPath, $filename);

                // Add watermark to the image
                $watermarkedImagePath = $this->addWatermarkToImage($path, $userId);

                // Return the response with success status and file path
                return response()->json(['success' => true, 'path' => $watermarkedImagePath]);
            } catch (\Exception $e) {
                // Catch any exceptions during file storage and log the error
                // \Log::error('File upload failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'File upload failed.'], 500);
            }
        }

        // Return an error response if no file is uploaded
        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    public function addWatermarkToImage($imagePath, $userId)
    {
        // Get the real path of the uploaded image
        $imageRealPath = storage_path('app/public/' . $imagePath);

        // Load the uploaded image using GD
        if (!file_exists($imageRealPath)) {
            throw new \Exception('Image file not found: ' . $imageRealPath);
        }

        $image = imagecreatefromjpeg($imageRealPath);

        $watermarkText = 'Your Next Show';
        $fontPath = public_path('fonts/ralewaysemibold.ttf'); // Ensure this font exists
        $textColor = imagecolorallocate($image, 255, 255, 255); // White color for watermark

        // Get image dimensions
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $fontSize = max(10, min(ceil($imageHeight * 0.05), ceil($imageWidth * 0.05))); // Scale font size, with a minimum of 10px

        // Calculate the bounding box for the text with the dynamic font size
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $watermarkText);
        $textWidth = $textBox[2] - $textBox[0];
        $textHeight = $textBox[1] - $textBox[7];

        // Calculate the position to center the text
        $xStart = ($imageWidth / 2) - ($textWidth / 2);
        $yStart = ($imageHeight / 2) + ($textHeight / 2);

        // Add the watermark text
        imagettftext($image, $fontSize, 0, $xStart, $yStart, $textColor, $fontPath, $watermarkText);
        $directoryPath = $imagePath . '/watermarked/' . $userId;

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        // Save the watermarked image
        $watermarkedImagePath = $directoryPath . "/watermark_{$userId}.jpg";
        imagejpeg($image, $watermarkedImagePath);

        // Clean up
        imagedestroy($image);

        // Return the relative path of the saved watermarked image
        return $watermarkedImagePath;
    }

    public function savePortfolio($dashboardType, $userId, Request $request)
    {

        $user = User::find($userId); // Retrieve user by ID
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'portfolio_image_path' => 'nullable|json',
            'portfolio_link' => 'nullable|string'
        ]);

        $portfolioImages = $validated['portfolio_image_path'];

        // If portfolio images are passed as a string, decode them into an array
        if (is_string($portfolioImages)) {
            $portfolioImages = json_decode($portfolioImages, true); // Decode JSON into an array
        }

        // Ensure portfolio images are an array
        if (!is_array($portfolioImages)) {
            return response()->json(['success' => false, 'message' => 'Invalid portfolio images format.'], 400);
        }

        $otherServiceUser = $user->otherService(strtoupper((string) $dashboardType))->first();

        if ($otherServiceUser) {
            // Save portfolio images as an array in the database
            $otherServiceUser->update([
                'portfolio_link' => $validated['portfolio_link'],
                'portfolio_images' => $portfolioImages, // Directly save the array
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Portfolio Updated',
                'redirect_url' => route('profile.edit', [
                    'dashboardType' => $dashboardType,
                    'id' => $user->id,
                ]),
            ]);
        }

        return redirect()->route('profile.edit', [
            'dashboardType' => $dashboardType,
            'id' => $user->id
        ])->with('status', 'Error saving portfolio');
    }

    public function updateEnvironmentTypes(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'environment_types' => 'required|array',
                'environment_types.*' => 'string|distinct',
            ]);

            // Get photographer
            $user = auth()->user();
            $photographer = OtherService::where('other_service_id', 1)
                ->whereHas('linkedUsers', fn($query) => $query->where('user_id', $user->id))
                ->first();

            if (!$photographer) {
                return redirect()->back()->with('error', 'Photographer profile not found.');
            }

            DB::beginTransaction();
            try {
                // Get current environment types
                $currentTypes = $photographer->environment_type ?
                    json_decode($photographer->environment_type, true) : [];

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON in existing environment types');
                }

                // Update environment types
                $selectedTypes = $request->input('environment_types', []);
                $updatedTypes = array_values(array_unique(array_merge($currentTypes, $selectedTypes)));

                $photographer->environment_type = json_encode($updatedTypes);
                $photographer->save();

                DB::commit();

                return redirect()
                    ->route('profile.edit', ['dashboardType' => 'photographer'])
                    ->with('success', 'Environment types updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Environment types update failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to update environment types.');
            }
        } catch (\Exception $e) {
            \Log::error('Environment types validation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Invalid environment types data.');
        }
    }

    /**
     * Save Genres
     */
    public function saveGenres($dashboardType, Request $request)
    {
        dd($request->all());
        \Log::info('Save Genres Request:', [
            'dashboardType' => $dashboardType,
            'requestData' => $request->all(),
        ]);

        $validated = $request;
        $user = User::where('id', Auth::user()->id)->first();

        // Get correct user type
        switch ($dashboardType) {
            case 'promoter':
                $userType = $user->promoters()->first();
                break;
            case 'artist':
                $userType = $user->otherService('Artist')->first();
                break;
            case 'venue':
                $userType = $user->venues()->first();
                break;
            case 'photography':
                $userType = $user->otherService('Photography')->first();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid dashboard type'
                ]);
        }

        if (!$userType) {
            return response()->json([
                'success' => false,
                'message' => 'User type not found'
            ]);
        }

        // Get existing genres from DB
        $existingGenres = is_array($userType->genre)
            ? $userType->genre
            : json_decode($userType->genre, true) ?? [];

        // Get new genres from request
        $newGenres = $validated['genres'];

        // Merge genres, preserving existing ones unless explicitly changed
        $updatedGenres = array_replace_recursive($existingGenres, $newGenres);

        // Save merged genres
        $userType->update(['genre' => $updatedGenres]);

        return response()->json([
            'success' => true,
            'message' => 'Genres successfully updated',
            'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])

        ]);
    }

    public function saveBandTypes($dashboardType, StoreUpdateBandTypes $request)
    {
        $validated = $request->validated();

        $bandTypesData = $validated['band_types'];
        $user = User::where('id', Auth::user()->id)->first();

        if (!$bandTypesData['allTypes'] && empty($bandTypesData['bandTypes'])) {
            return response()->json([
                'success' => false,
                'message' => 'At least one artist type is required'
            ], 422);
        }

        // Ensure the correct user is selected based on dashboard type
        switch ($dashboardType) {
            case 'promoter':
                $userType = $user->promoters()->first();
                break;
            case 'artist':
                $userType = $user->otherService('Artist')->first();
                break;
            case 'venue':
                $userType = $user->venues()->first();
                break;
            case 'photographer':
                $userType = $user->otherService('Photography')->first();
                break;
            case 'designer':
                $userType = $user->otherService('Designer')->first();
                break;
            case 'videographer':
                $userType = $user->otherService('Videography')->first();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid dashboard type'
                ]);
        }

        if (!$userType) {
            return response()->json([
                'success' => false,
                'message' => 'User type not found'
            ]);
        }

        // Structure the data for storage
        if ($bandTypesData['allTypes']) {
            $dataToStore = ['all'];
        } else {
            $dataToStore = array_values($bandTypesData['bandTypes']);
        }

        $userType->update(['band_type' => json_encode($dataToStore)]);

        return response()->json([
            'success' => true,
            'message' => 'Band Types successfully updated',
            'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])

        ]);
    }
}
