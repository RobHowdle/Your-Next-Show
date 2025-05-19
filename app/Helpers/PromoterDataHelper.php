<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Storage;

class PromoterDataHelper
{
    protected $serviceDataHelper;

    public function __construct(ServiceDataHelper $serviceDataHelper)
    {
        $this->serviceDataHelper = $serviceDataHelper;
    }

    public function getPromoterData(User $user)
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
        $activePlatforms = [];
        $platformsToCheck = ['facebook', 'x', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // Initialize the platforms array with empty strings for each platform
        foreach ($platformsToCheck as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach ($platformsToCheck as $platform) {
                // Only add the link if the platform exists in the $contactLinks array
                if (isset($contactLinks[$platform]) && !empty($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                    $activePlatforms[] = $platform; // Track this platform as active
                }
            }
        }

        $preferredContact = $promoter ? $promoter->preferred_contact : '';


        // About Section
        $description = $promoter ? $promoter->description : '';

        // My Venues
        $myVenues = $promoter ? $promoter->my_venues : '';

        // My Events
        $myEvents = $promoter ? $promoter->events()->with('venues')->get() : collect();

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

        $bandTypes = json_decode($promoter->band_type, true) ?? [];
        $apiProviders = config('api_providers.providers');
        $apiKeys = ApiKey::where('serviceable_id', $promoter->id)->where('serviceable_type', get_class($promoter))->get();

        if ($apiKeys) {
            $apiKeys = $apiKeys->map(function ($apiKey) {
                if ($apiKey->is_active) {
                    return [
                        'id' => $apiKey->id,
                        'name' => $apiKey->name,
                        'type' => $apiKey->key_type,
                        'key' => $apiKey->api_key,
                        'secret' => $apiKey->api_secret,
                        'last_used_at' => $apiKey->last_used_at,
                        'is_active' => $apiKey->is_active,
                        'expires_at' => $apiKey->expires_at
                    ];
                }
            });
        }

        $packages = $promoter ? json_decode($promoter->packages) : [];

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
            'activePlatforms' => $activePlatforms,
            'platformsToCheck' => $platformsToCheck,
            'preferred_contact' => $preferredContact,
            'myVenues' => $myVenues,
            'myEvents' => $myEvents,
            // 'uniqueBands' => $uniqueBands,
            'genres' => $genres,
            'profileGenres' => $profileGenres,
            'isAllGenres' => $isAllGenres,
            'normalizedProfileGenres' => $normalizedProfileGenres,
            'bandTypes' => $bandTypes,
            'apiProviders' => $apiProviders,
            'apiKeys' => $apiKeys,
            'packages' => $packages,
        ];
    }
}