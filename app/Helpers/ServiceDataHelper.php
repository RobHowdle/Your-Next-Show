<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Event;
use App\Models\Document;
use PhpParser\Comment\Doc;
use App\Models\OtherService;
use Illuminate\Support\Facades\Storage;

class ServiceDataHelper
{
    // Getting Role User Data
    public function getArtistData(User $user)
    {
        $artist = $user->otherService("Artist")->first();

        $serviceableId = $artist->id;
        $serviceableType = 'App\Models\OtherService';

        $name = $artist ? $artist->name : '';
        $location = $artist ? $artist->location : '';
        $postalTown = $artist ? $artist->postal_town : '';
        $lat = $artist ? $artist->latitude : '';
        $long = $artist ? $artist->longitude : '';
        $logo = $artist && $artist->logo_url
            ? (filter_var($artist->logo_url, FILTER_VALIDATE_URL) ? $artist->logo_url : Storage::url($artist->logo_url))
            : asset('images/system/yns_no_image_found.png');
        $contact_name = $artist ? $artist->contact_name : '';
        $contact_number = $artist ? $artist->contact_number : '';
        $contact_email = $artist ? $artist->contact_email : '';
        $contactLinks = $artist ? json_decode($artist->contact_link, true) : [];

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

        $preferredContact = $artist ? $artist->preferred_contact : '';

        $description = $artist ? $artist->description : '';

        $myEvents = $artist ? $artist->events()->with('venues')->get() : collect();

        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $profileGenres = is_array($artist->genre) ? $artist->genre : json_decode($artist->genre, true);
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

        $bandTypes = json_decode($artist->band_type) ?? [];

        // Initialize stream links with default structure
        $streamLinksRaw = json_decode($artist->stream_urls, true);
        $streamLinks = $streamLinksRaw ?? [
            'spotify' => '',
            'apple-music' => '',
            'youtube-music' => '',
            'amazon-music' => '',
            'bandcamp' => '',
            'soundcloud' => ''
        ];

        $streamPlatforms = [];
        $streamPlatformsToCheck = ['spotify', 'apple-music', 'youtube-music', 'amazon-music', 'bandcamp', 'soundcloud'];

        $membersData = $artist->members;
        $members = [];

        if (!empty($membersData)) {
            // Convert to array if it's a string
            if (is_string($membersData)) {
                $decodedMembers = json_decode($membersData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $members = $decodedMembers; // Keep all properties including bio and profile_pic
                } else {
                    \Log::warning('Failed to decode band members JSON: ' . json_last_error_msg(), [
                        'raw_data' => $membersData
                    ]);
                }
            } else if (is_array($membersData)) {
                // Already an array, use it directly
                $members = $membersData;
            }
        }

        $documents = Document::where('serviceable_id', $artist->id)
            ->where('serviceable_type', get_class($artist))
            ->get();

        return [
            'artist' => $artist,
            'artistId' => $artist->id,
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
            'myEvents' => $myEvents,
            'genres' => $genres,
            'profileGenres' => $profileGenres,
            'isAllGenres' => $isAllGenres,
            'normalizedProfileGenres' => $normalizedProfileGenres,
            'bandTypes' => $bandTypes,
            'streamLinks' => $streamLinks,
            'streamPlatformsToCheck' => $streamPlatformsToCheck,
            'members' => $members,
            'preferred_contact' => $preferredContact,
            'serviceableId' => $serviceableId,
            'serviceableType' => $serviceableType,
            'documents' => $documents,
        ];
    }

    public function getDesignerData(User $user)
    {
        $designer = $user->otherService("Designer")->first();

        if ($designer) {
            $serviceableId = $designer->id;
            $serviceableType = 'App\Models\OtherService';

            // Basic Information
            $name = $designer ? $designer->name : '';
            $location = $designer ? $designer->location : '';
            $postalTown = $designer ? $designer->postal_town : '';
            $lat = $designer ? $designer->latitude : '';
            $long = $designer ? $designer->longitude : '';
            $logo = $designer && $designer->logo_url
                ? (filter_var($designer->logo_url, FILTER_VALIDATE_URL) ? $designer->logo_url : Storage::url($designer->logo_url))
                : asset('images/system/yns_no_image_found.png');

            $contact_name = $designer ? $designer->contact_name : '';
            $contact_email = $designer ? $designer->contact_email : '';
            $contact_number = $designer ? $designer->contact_number : '';
            $contactLinks = $designer ? json_decode($designer->contact_link, true) : [];

            $platforms = [];
            $platformsToCheck = ['website', 'facebook', 'x', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

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

            $preferredContact = $designer ? $designer->preferred_contact : '';
            $description = $designer ? $designer->description : '';
            $myEvents = $designer ? $designer->events()->with('venues')->get() : collect();

            // Genres
            $genreList = file_get_contents(public_path('text/genre_list.json'));
            $data = json_decode($genreList, true) ?? [];
            $isAllGenres = in_array('All', $data);
            $genres = $data['genres'];
            $profileGenres = is_array($designer->genre) ? $designer->genre : json_decode($designer->genre, true);
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

            $bandTypes = json_decode($designer->band_type) ?? [];

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
            $styles = is_array($designer->styles) ? $designer->styles : json_decode($designer->styles, true);
            $print = is_array($designer->print) ? $designer->print : json_decode($designer->print, true);
            $portfolioLink = $designer ? $designer->portfolio_link : '';
            $waterMarkedPortfolioImages = $designer->portfolio_images;

            if (!is_array($waterMarkedPortfolioImages)) {
                try {
                    $waterMarkedPortfolioImages = json_decode($waterMarkedPortfolioImages, true);
                } catch (\Exception $e) {
                    throw new \Exception("Portfolio images could not be converted to an array.");
                }
            }

            $packages = $designer ? json_decode($designer->packages) : [];

            return [
                'designer' => $designer,
                'designerId'  => $designer->id,
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
                'genres' => $genres,
                'profileGenres' => $profileGenres,
                'isAllGenres' => $isAllGenres,
                'normalizedProfileGenres' => $normalizedProfileGenres,
                'bandTypes' => $bandTypes,
                'portfolio_link' => $portfolioLink,
                'serviceableId' => $serviceableId,
                'serviceableType' => $serviceableType,
                'waterMarkedPortfolioImages' => $waterMarkedPortfolioImages,
                'environmentTypes' => $environmentTypes,
                'groups' => $groupedData,
                'workingTimes' => $workingTimes,
                'styles' => $styles,
                'print' => $print,
                'packages' => $packages,
                'preferred_contact' => $preferredContact,
                'myEvents' => $myEvents
            ];
        }
    }

    public function getPhotographerData(User $user)
    {
        $photographer = $user->otherService("Photographer")->first();

        $serviceableId = $photographer->id;
        $serviceableType = 'App\Models\OtherService';

        // Basic Information
        $name = $photographer ? $photographer->name : '';
        $location = $photographer ? $photographer->location : '';
        $postalTown = $photographer ? $photographer->postal_town : '';
        $lat = $photographer ? $photographer->latitude : '';
        $long = $photographer ? $photographer->longitude : '';
        // Handle logo URL safely
        $logo = asset('images/system/yns_no_image_found.png'); // Default image
        if ($photographer && !empty($photographer->logo_url)) {
            try {
                // Check if it's a full URL (including http/https)
                if (filter_var($photographer->logo_url, FILTER_VALIDATE_URL)) {
                    // Check URL scheme is present and valid to avoid ERR_NAME_NOT_RESOLVED
                    $parsedUrl = parse_url($photographer->logo_url);
                    if (isset($parsedUrl['scheme']) && in_array($parsedUrl['scheme'], ['http', 'https'])) {
                        $logo = $photographer->logo_url;
                    } else {
                        // If scheme is missing, default to https
                        $logo = 'https://' . ltrim($photographer->logo_url, '/');
                    }
                } else {
                    // Handle as local storage path
                    $logo = Storage::url($photographer->logo_url);
                }
            } catch (\Exception $e) {
                \Log::error('Error processing logo URL: ' . $e->getMessage(), [
                    'logo_url' => $photographer->logo_url ?? 'null'
                ]);
            }
        }
        $contact_name = $photographer ? $photographer->contact_name : '';
        $contact_number = $photographer ? $photographer->contact_number : '';
        $contact_email = $photographer ? $photographer->contact_email : '';
        $contactLinks = $photographer ? json_decode($photographer->contact_link, true) : [];

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

        $preferredContact = $photographer ? $photographer->preferred_contact : '';
        $description = $photographer ? $photographer->description : '';
        $myEvents = $photographer ? $photographer->events()->with('venues')->get() : collect();

        $genreList = file_get_contents(public_path('text/genre_list.json'));
        $data = json_decode($genreList, true) ?? [];
        $isAllGenres = in_array('All', $data);
        $genres = $data['genres'];
        $profileGenres = is_array($photographer->genre) ? $photographer->genre : json_decode($photographer->genre, true);
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

        $bandTypes = json_decode($photographer->band_type) ?? [];
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
        $profileGenres = is_array($photographer->genre) ? $photographer->genre : json_decode($photographer->genre, true);
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

        $bandTypes = json_decode($photographer->band_type) ?? [];
        // Ensure packages are properly decoded from JSON
        $packagesRaw = $photographer->packages;
        $packages = [];

        if (!empty($packagesRaw)) {
            if (is_string($packagesRaw)) {
                try {
                    // First try to decode as JSON
                    $decoded = json_decode($packagesRaw, true);

                    // If it's still a string (JSON-encoded twice), try decoding again
                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }

                    // If we ended up with null, set to empty array
                    if ($decoded === null) {
                        $packages = [];
                    } else {
                        $packages = $decoded;
                    }

                    // Normalize package data structure
                    $normalizedPackages = [];
                    foreach ($packages as $index => $package) {
                        // Convert stdClass to array if needed
                        if (is_object($package)) {
                            $package = (array)$package;
                        }

                        // Ensure job_type is set
                        if (!isset($package['job_type']) || empty($package['job_type'])) {
                            // Try to determine job type from context or set default
                            $package['job_type'] = $package['job_type'] ?? 'photography';
                        }

                        $normalizedPackages[$index] = $package;
                    }
                    $packages = $normalizedPackages;
                } catch (\Exception $e) {
                    \Log::error('Failed to decode packages: ' . $e->getMessage());
                    $packages = [];
                }
            } elseif (is_array($packagesRaw)) {
                $packages = $packagesRaw;
            }
        }

        $styles = is_array($photographer->styles) ? $photographer->styles : json_decode($photographer->styles, true);

        return [
            'photographer' => $photographer,
            'photographerId'  => $photographer->id,
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
            'genres' => $genres,
            'profileGenres' => $profileGenres,
            'isAllGenres' => $isAllGenres,
            'normalizedProfileGenres' => $normalizedProfileGenres,
            'bandTypes' => $bandTypes,
            'portfolio_link' => $portfolioLink,
            'serviceableId' => $serviceableId,
            'serviceableType' => $serviceableType,
            'waterMarkedPortfolioImages' => $waterMarkedPortfolioImages,
            'environmentTypes' => $environmentTypes,
            'groups' => $groupedData,
            'workingTimes' => $workingTimes,
            'styles' => $styles,
            'packages' => $packages,
            'preferred_contact' => $preferredContact,
            'myEvents' => $myEvents
        ];
    }

    public function getVideographerData(User $user)
    {
        $videographer = $user->otherService("Videographer")->first();

        if ($videographer) {
            $serviceableId = $videographer->id;
            $serviceableType = 'App\Models\OtherService';

            // Basic Information
            $name = $videographer ? $videographer->name : '';
            $location = $videographer ? $videographer->location : '';
            $postalTown = $videographer ? $videographer->postal_town : '';
            $lat = $videographer ? $videographer->latitude : '';
            $long = $videographer ? $videographer->longitude : '';
            $logo = $videographer && $videographer->logo_url
                ? (filter_var($videographer->logo_url, FILTER_VALIDATE_URL) ? $videographer->logo_url : Storage::url($videographer->logo_url))
                : asset('images/system/yns_no_image_found.png');
            $contact_name = $videographer ? $videographer->contact_name : '';
            $contact_number = $videographer ? $videographer->contact_number : '';
            $contact_email = $videographer ? $videographer->contact_email : '';
            $contactLinks = $videographer ? json_decode($videographer->contact_link, true) : [];

            $platforms = [];
            $platformsToCheck = ['website', 'facebook', 'x', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

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

            $preferredContact = $videographer ? $videographer->preferred_contact : '';
            $description = $videographer ? $videographer->description : '';
            $myEvents = $videographer ? $videographer->events()->with('venues')->get() : collect();

            $genreList = file_get_contents(public_path('text/genre_list.json'));
            $data = json_decode($genreList, true) ?? [];
            $isAllGenres = in_array('All', $data);
            $genres = $data['genres'];
            $profileGenres = is_array($videographer->genre) ? $videographer->genre : json_decode($videographer->genre, true);
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

            $bandTypes = json_decode($videographer->band_type) ?? [];

            $groupedEnvironmentTypes = config('environment_types');
            $environmentTypes = json_decode($videographer->environment_type, true);
            $groupedData = [];

            foreach ($groupedEnvironmentTypes as $groupName => $items) {
                foreach ($items as $item) {
                    if ($environmentTypes && is_array($environmentTypes)) {
                        $groupedData[$groupName][] = $item;
                    }
                }
            }

            $workingTimes = is_array($videographer->working_times) ? $videographer->working_times : json_decode($videographer->working_times, true);
            $styles = is_array($videographer->styles) ? $videographer->styles : json_decode($videographer->styles, true);
            $portfolioLink = $videographer ? $videographer->portfolio_link : '';
            $waterMarkedPortfolioImages = $videographer->portfolio_images;

            if (!is_array($waterMarkedPortfolioImages)) {
                try {
                    $waterMarkedPortfolioImages = json_decode($waterMarkedPortfolioImages, true);
                } catch (\Exception $e) {
                    throw new \Exception("Portfolio images could not be converted to an array.");
                }
            }

            $packages = $videographer ? json_decode($videographer->packages) : [];

            return [
                'videographer' => $videographer,
                'videographerId'  => $videographer->id,
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
                'genres' => $genres,
                'profileGenres' => $profileGenres,
                'isAllGenres' => $isAllGenres,
                'normalizedProfileGenres' => $normalizedProfileGenres,
                'bandTypes' => $bandTypes,
                'portfolio_link' => $portfolioLink,
                'serviceableId' => $serviceableId,
                'serviceableType' => $serviceableType,
                'waterMarkedPortfolioImages' => $waterMarkedPortfolioImages,
                'environmentTypes' => $environmentTypes,
                'groups' => $groupedData,
                'workingTimes' => $workingTimes,
                'styles' => $styles,
                'packages' => $packages,
                'preferred_contact' => $preferredContact,
                'myEvents' => $myEvents
            ];
        }
    }

    public function getServiceByType(User $user, string $type): ?OtherService
    {
        return $user->otherService(strtoupper($type))->first();
    }

    public function getPortfolioData(User $user, string $type): ?array
    {
        $service = $this->getServiceByType($user, $type);
        if (!$service) return null;

        return [
            'portfolio_images' => $service->portfolio_images ?? [],
            'portfolio_link' => $service->portfolio_link ?? null
        ];
    }

    // Unique Bands for Venues and Promoters
    public function getBandsData($type, $id)
    {
        switch ($type) {
            case 'Venue':
                $events = Event::whereHas('venues', function ($query) use ($id) {
                    $query->where('venues.id', $id);
                })
                    ->orderBy('event_date', 'desc')
                    ->take(10)
                    ->get();

                $bandIds = [];
                foreach ($events as $event) {
                    if ($event->band_ids) {
                        $decodedIds = json_decode($event->band_ids, true);
                        if (is_array($decodedIds)) {
                            foreach ($decodedIds as $band) {
                                if (isset($band['band_id'])) {
                                    $bandIds[] = $band['band_id'];
                                }
                            }
                        }
                    }
                }

                return empty($bandIds) ? [] : OtherService::where('other_service_id', 4)
                    ->whereIn('id', array_values(array_unique($bandIds)))
                    ->limit(10)
                    ->get()
                    ->toArray();
                break;
            case 'Promoter':
                $events = Event::whereHas('promoters', function ($query) use ($id) {
                    $query->where('promoters.id', $id);
                })
                    ->orderBy('event_date', 'desc')
                    ->take(10)
                    ->get();

                $bandIds = [];
                foreach ($events as $event) {
                    if ($event->band_ids) {
                        $decodedIds = json_decode($event->band_ids, true);
                        if (is_array($decodedIds)) {
                            foreach ($decodedIds as $band) {
                                if (isset($band['band_id'])) {
                                    $bandIds[] = $band['band_id'];
                                }
                            }
                        }
                    }
                }

                return empty($bandIds) ? [] : OtherService::where('other_service_id', 4)
                    ->whereIn('id', array_values(array_unique($bandIds)))
                    ->limit(10)
                    ->get()
                    ->toArray();
                break;
        }
    }

    public function getEnvironmentTypes(User $user): ?array
    {
        $photographer = OtherService::where('other_service_id', 1)
            ->whereHas('linkedUsers', fn($query) => $query->where('user_id', $user->id))
            ->first();

        return $photographer ? json_decode($photographer->environment_type, true) : null;
    }
}