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
use App\Helpers\VenueDataHelper;
use App\Models\UserModuleSetting;
use App\Helpers\ServiceDataHelper;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Helpers\PromoterDataHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\StoreUpdateBandTypes;
use App\Http\Requests\StoreUpdateBandGenres;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\BandProfileUpdateRequest;
use App\Http\Requests\VenueProfileUpdateRequest;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Requests\DesignerProfileUpdateRequest;
use App\Http\Requests\PromoterProfileUpdateRequest;
use App\Http\Requests\PhotographerProfileUpdateRequest;
use App\Http\Requests\StoreUpdatePackages;

class ProfileController extends Controller
{
    protected $passwordController;
    protected $serviceDataHelper;
    protected $venueDataHelper;
    protected $promoterDataHelper;

    protected function getUserId()
    {
        return Auth::id();
    }

    public function __construct(
        PromoterDataHelper $promoterDataHelper,
        VenueDataHelper $venueDataHelper,
        ServiceDataHelper $serviceDataHelper,
        PasswordController $passwordController
    ) {
        $this->passwordController = $passwordController;
        $this->serviceDataHelper = $serviceDataHelper;
        $this->venueDataHelper = $venueDataHelper;
        $this->promoterDataHelper = $promoterDataHelper;
    }

    private function getUniqueBandsForPromoterEvents($promoterId)
    {
        return $this->serviceDataHelper->getBandsData($promoterId);
    }

    private function getPortfolioData($service)
    {
        return $this->serviceDataHelper->getPortfolioData(auth()->user(), $service);
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
        $photographerData = [];
        $standardUserData = [];
        $designerData = [];
        $videographerData = [];

        // check dashboard type and get the data
        if ($dashboardType === 'promoter') {
            $promoterData = $this->getPromoterData($user);
        } elseif ($dashboardType === 'artist') {
            $bandData = $this->getOtherServicData($dashboardType, $user);
        } elseif ($dashboardType === 'venue') {
            $venueData = $this->getVenueData($user);
        } elseif ($dashboardType === 'photographer') {
            $photographerData = $this->getOtherServicData($dashboardType, $user);
        } elseif ($dashboardType === 'standard') {
            $standardUserData = $this->getStandardUserData($user);
        } elseif ($dashboardType === 'designer') {
            $designerData = $this->getOtherServicData($dashboardType, $user);
        } elseif ($dashboardType === 'videographer') {
            $videographerData = $this->getOtherServicData($dashboardType, $user);
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
            'photographerData' => $photographerData,
            'standardUserData' => $standardUserData,
            'designerData' => $designerData,
            'videographerData' => $videographerData,
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
            'communications' => $communicationSettings,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update($dashboardType, ProfileUpdateRequest $request, $userId)
    {
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

                if (isset($userData['preferred_contact'])) {
                    $promoter->update(['preferred_contact' => $userData['preferred_contact']]);
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

                if (isset($userData['preferred_contact'])) {
                    $venue->update(['preferred_contact' => $userData['preferred_contact']]);
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

                if (isset($userData['preferred_contact'])) {
                    $band->update(['preferred_contact' => $userData['preferred_contact']]);
                }

                if (isset($userData['stream_links'])) {
                    $streamLinks = $userData['stream_links'];
                    $defaultPlatform = $userData['default_platform'] ?? null;

                    // Clean null values and empty arrays
                    $cleanedLinks = array_filter($streamLinks, function ($platformLinks) {
                        return !empty($platformLinks[0]);
                    });

                    // Add default platform if set
                    if ($defaultPlatform) {
                        $cleanedLinks['default'] = $defaultPlatform;
                    }

                    // Update only if links have changed
                    $storedStreamLinks = json_decode($band->stream_urls, true) ?? [];
                    if ($storedStreamLinks !== $cleanedLinks) {
                        $band->update(['stream_urls' => json_encode($cleanedLinks)]);
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

                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])
                ]);
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

                if (isset($userData['preferred_contact'])) {
                    $photographer->update(['preferred_contact' => $userData['preferred_contact']]);
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

                return response()->json([
                    'success' => true,
                    'message' => 'Photographer profile updated successfully!',
                    'redirect' => route('profile.edit', [
                        'dashboardType' => $dashboardType,
                        'id' => $user->id
                    ])
                ]);
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

    public function updateDesigner($dashboardType, DesignerProfileUpdateRequest $request, $user)
    {
        $user = User::findOrFail($user);
        $userId = $user->id;
        $userData = $request->validated();

        if ($dashboardType == 'designer') {
            $designer = OtherService::designers()->whereHas('linkedUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

            if ($designer) {
                try {
                    // Designer Name
                    if (isset($userData['name']) && $designer->name !== $userData['name']) {
                        $designer->update(['name' => $userData['name']]);
                    }

                    // Contact Name
                    if (isset($userData['contact_name']) && $designer->contact_name !== $userData['contact_name']) {
                        $designer->update(['contact_name' => $userData['contact_name']]);
                    }

                    // Contact Email
                    if (isset($userData['contact_email']) && $designer->contact_email !== $userData['contact_email']) {
                        $designer->update(['contact_email' => $userData['contact_email']]);
                    }

                    // Contact Number
                    if (isset($userData['contact_number']) && $designer->contact_number !== $userData['contact_number']) {
                        $designer->update(['contact_number' => $userData['contact_number']]);
                    }

                    // Contact Links
                    if (isset($userData['contact_links']) && is_array($userData['contact_links'])) {
                        // Start with the existing `contact_links` array or an empty array if it doesn't exist
                        $updatedLinks = !empty($designer->contact_link) ? json_decode($designer->contact_link, true) : [];

                        // Iterate through the `contact_link` array from the request data
                        foreach ($userData['contact_links'] as $platform => $links) {
                            // Ensure we're setting only non-empty values
                            $updatedLinks[$platform] = !empty($links[0]) ? $links[0] : null;
                        }

                        // Filter out null values to remove platforms with no links
                        $updatedLinks = array_filter($updatedLinks);

                        // Encode the array back to JSON for storage and update the promoter record
                        $designer->update(['contact_link' => json_encode($updatedLinks)]);
                    }

                    // Location
                    if (isset($userData['location']) && isset($userData['latitude']) && isset($userData['longitude']) && isset($userData['postal_town'])) {
                        $designer->update([
                            'location' => $userData['location'],
                            'latitude' => $userData['latitude'],
                            'longitude' => $userData['longitude'],
                            'postal_town' => $userData['postal_town'],
                        ]);
                    }

                    // Description
                    if (isset($userData['description']) && $designer->description !== $userData['description']) {
                        $designer->update(['description' => $userData['description']]);
                    }

                    // Logo
                    if (isset($userData['logo_url'])) {
                        $logoPath = $this->uploadLogo($userData['logo_url'], $userData['name']);
                        $designer->update(['logo_url' => $logoPath]);
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

                        $designer->update(['working_times' => json_encode($sortedWorkingTimes)]);
                    }

                    // Styles
                    if (isset($userData['styles'])) {
                        $designer->update(['styles' => json_encode($userData['styles'])]);
                    }

                    // Print
                    if (isset($userData['print'])) {
                        $designer->update(['print' => json_encode($userData['print'])]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Profile updated successfully',
                        'redirect' => route('profile.edit', ['dashboardType' => $dashboardType, 'id' => $user->id])
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update profile: ' . $e->getMessage()
                    ], 500);
                }
            }
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
        return $this->promoterDataHelper->getPromoterData($user);
    }

    private function getVenueData(User $user)
    {
        return $this->venueDataHelper->getVenueData($user);
    }

    private function getOtherServicData($dashboardType, User $user)
    {
        switch ($dashboardType) {
            case 'artist':
                return $this->serviceDataHelper->getArtistData($user);
                break;
            case 'photographer':
                return $this->serviceDataHelper->getPhotographerData($user);
                break;
            case 'designer':
                return $this->serviceDataHelper->getDesignerData($user);
                break;
            case 'videographer':
                return $this->serviceDataHelper->getVideographerData($user);
                break;
        }
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

    public function addRole(Request $request)
    {
        try {
            // Retrieve the user
            $user = User::findOrFail($request->id);

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
        $environmentTypes = $this->serviceDataHelper->getEnvironmentTypes(auth()->user());

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