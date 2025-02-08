<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Storage;

class VenueDataHelper
{
    protected $serviceDataHelper;

    public function __construct(ServiceDataHelper $serviceDataHelper)
    {
        $this->serviceDataHelper = $serviceDataHelper;
    }
    public function getVenueData(User $user)
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
        $depositRequired = $venue ? $venue->deposit_required : '';
        $depositAmont = $venue ? $venue->deposit_amount : '';

        // My Events
        $myEvents = $venue ? $venue->events()->with('venues')->get() : collect();
        $uniqueBands = $this->serviceDataHelper->getBandsData('Venue', $venue->id);

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
        $apiProviders = config('api_providers.providers');
        $apiKeys = ApiKey::where('serviceable_id', $venue->id)->where('serviceable_type', get_class($venue))->get();

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
                        'expires_at' => $apiKey->expires_at,
                    ];
                }
            });
        }
        return [
            'venue' => $venue,
            'venueId' => $venue->id,
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
            'depositRequired' => $depositRequired,
            'depositAmount' => $depositAmont,
            'apiProviders' => $apiProviders,
            'apiKeys' => $apiKeys,
        ];
    }
}
