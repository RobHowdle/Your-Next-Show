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

    public function getPromoterData(User $user, $dashboardType)
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

        // Get the social platforms config file - this contains all platform information
        $socialPlatformsConfig = config('social_platforms');

        // Initialize the platforms array with empty strings for each platform
        foreach (array_keys($socialPlatformsConfig) as $platform) {
            $platforms[$platform] = '';  // Set default to empty string
        }

        // Check if the contactLinks array exists and contains social links
        if ($contactLinks) {
            foreach (array_keys($socialPlatformsConfig) as $platform) {
                // Only add the link if the platform exists in the $contactLiIt nks array
                if (isset($contactLinks[$platform]) && !empty($contactLinks[$platform])) {
                    $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
                    $activePlatforms[] = $platform; // Track this platform as active
                }
            }
        }

        $preferredContact = $promoter ? $promoter->preferred_contact : '';

        // About Section
        $description = $promoter ? $promoter->description : '';

        // My Events
        $myEvents = $promoter ? $promoter->events()->with(['venues', 'bands'])->get() : collect();

        // Extract all unique artists from all events
        $allArtists = collect();
        foreach ($myEvents as $event) {
            // Use $event->bands() relationship method to get the artists
            foreach ($event->bands()->get() as $artist) {
                $allArtists->push($artist);
            }
        }
        $uniqueArtists = $allArtists->unique('id')->values();

        // Attach venues and events to each artist
        $uniqueArtists = $uniqueArtists->map(function ($artist) use ($myEvents) {
            // Find all events this artist performed at
            $artistEvents = $myEvents->filter(function ($event) use ($artist) {
                $eventArtists = $event->bands()->get();
                return $eventArtists->pluck('id')->contains($artist->id);
            });
            // Collect all unique venues from those events
            $venues = collect();
            foreach ($artistEvents as $event) {
                foreach ($event->venues as $venue) {
                    $venues->push($venue);
                }
            }
            $artist->venues = $venues->unique('id')->values();
            $artist->events = $artistEvents->values();
            return $artist;
        });

        // Venues
        $groupedVenues = $myEvents->groupBy(function ($event) {
            return $event->venues->first()->id ?? null;
        })->filter(function ($events, $venueId) {
            return $venueId !== null;
        })->map(function ($events, $venueId) use ($dashboardType) {
            $venue = $events->first()->venues->first();
            // Add eventsForJs for Alpine.js modal
            $eventsForJs = $events->map(function ($event) use ($dashboardType) {
                return [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'date' => $event->event_date,
                    'url' => route('admin.dashboard.show-event', [
                        'dashboardType' => $dashboardType,
                        'id' => $event->id
                    ]),
                ];
            })->values();
            return [
                'venue' => $venue,
                'event_count' => $events->count(),
                'events' => $events,
                'eventsForJs' => $eventsForJs,
            ];
        });

        // Group events by artist (for My Bands popup)
        $groupedArtists = $myEvents->flatMap(function ($event) {
            // Adjust 'artists' to your actual relationship name, e.g., 'bands'
            return collect($event->artists)->map(function ($artist) use ($event) {
                return [
                    'artist' => $artist,
                    'event' => $event
                ];
            });
        })->groupBy(function ($item) {
            return $item['artist']->id;
        })->map(function ($items, $artistId) use ($dashboardType) {
            $artist = $items->first()['artist'];
            $events = collect($items)->pluck('event')->unique('id')->values();
            $eventsForJs = $events->map(function ($event) use ($dashboardType) {
                return [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'date' => $event->event_date,
                    'url' => route('admin.dashboard.show-event', [
                        'dashboardType' => $dashboardType,
                        'id' => $event->id
                    ]),
                ];
            })->values();
            return [
                'artist' => $artist,
                'event_count' => $events->count(),
                'events' => $events,
                'eventsForJs' => $eventsForJs,
            ];
        });

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
            'platformsToCheck' => $socialPlatformsConfig,
            'preferred_contact' => $preferredContact,
            'groupedVenues' => $groupedVenues,
            'myEvents' => $myEvents,
            'uniqueArtists' => $uniqueArtists,
            'groupedArtists' => $groupedArtists,
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
