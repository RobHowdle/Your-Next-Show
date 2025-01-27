<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\OtherService;
use Illuminate\Support\Facades\Storage;

class ServiceDataHelper
{
    // Getting Role User Data
    public function getArtistData(User $user)
    {
        $artist = $user->otherService("Artist")->first();

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
        $streamLinks = json_decode($artist->stream_urls, true);

        $streamPlatforms = [];
        $streamPlatformsToCheck = ['spotify', 'apple-music', 'youtube-music', 'amazon-music', 'bandcamp', 'soundcloud'];

        $members = is_array($artist->members) ? $artist->members : json_decode($artist->members, true);

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
            'platformsToCheck' => $platformsToCheck,
            'myEvents' => $myEvents,
            'genres' => $genres,
            'profileGenres' => $profileGenres,
            'isAllGenres' => $isAllGenres,
            'normalizedProfileGenres' => $normalizedProfileGenres,
            'bandTypes' => $bandTypes,
            'streamLinks' => $streamLinks,
            'streamPlatformsToCheck' => $streamPlatformsToCheck,
            'members' => $members
        ];
    }

    public function getDesignerData(User $user)
    {
        $designer = $user->otherService("Designer")->first();

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
            'packages' => $packages
        ];
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
        $packages = $photographer ? json_decode($photographer->packages) : [];
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
            'packages' => $packages
        ];
    }

    public function getVideographerData(User $user)
    {
        // $photographer = $user->otherService("Photographer")->first();
        // $serviceableId = $photographer->id;
        // $serviceableType = 'App\Models\OtherService';

        // // Basic Information
        // $photographerName = $photographer ? $photographer->name : '';
        // $photographerLocation = $photographer ? $photographer->location : '';
        // $photographerPostalTown = $photographer ? $photographer->postal_town : '';
        // $photographerLat = $photographer ? $photographer->latitude : '';
        // $photographerLong = $photographer ? $photographer->longitude : '';
        // $logo = $photographer && $photographer->logo_url
        // ? (filter_var($photographer->logo_url, FILTER_VALIDATE_URL) ? $photographer->logo_url : Storage::url($photographer->logo_url))
        // : asset('images/system/yns_no_image_found.png');
        // $contact_name = $photographer ? $photographer->contact_name : '';
        // $contact_number = $photographer ? $photographer->contact_number : '';
        // $contact_email = $photographer ? $photographer->contact_email : '';
        // $contactLinks = $photographer ? json_decode($photographer->contact_link, true) : [];

        // $platforms = [];
        // $platformsToCheck = ['website', 'facebook', 'twitter', 'instagram', 'snapchat', 'tiktok', 'youtube', 'bluesky'];

        // // Initialize the platforms array with empty strings for each platform
        // foreach ($platformsToCheck as $platform) {
        //     $platforms[$platform] = '';  // Set default to empty string
        // }

        // // Check if the contactLinks array exists and contains social links
        // if ($contactLinks) {
        //     foreach ($platformsToCheck as $platform) {
        //         // Only add the link if the platform exists in the $contactLinks array
        //         if (isset($contactLinks[$platform])) {
        //             $platforms[$platform] = $contactLinks[$platform];  // Store the link for the platform
        //         }
        //     }
        // }

        // $description = $photographer ? $photographer->description : '';
        // $genreList = file_get_contents(public_path('text/genre_list.json'));
        // $data = json_decode($genreList, true);
        // $genres = $data['genres'];
        // $photographerGenres = is_array($photographer->genre) ? $photographer->genre : json_decode($photographer->genre, true);
        // $portfolioLink = $photographer ? $photographer->portfolio_link : '';
        // $waterMarkedPortfolioImages = $photographer->portfolio_images;

        // if (!is_array($waterMarkedPortfolioImages)) {
        //     try {
        //         $waterMarkedPortfolioImages = json_decode($waterMarkedPortfolioImages, true);
        //     } catch (\Exception $e) {
        //         throw new \Exception("Portfolio images could not be converted to an array.");
        //     }
        // }

        // $groupedEnvironmentTypes = config('environment_types');

        // $environmentTypes = json_decode($photographer->environment_type, true);
        // $groupedData = [];

        // foreach ($groupedEnvironmentTypes as $groupName => $items) {
        //     foreach ($items as $item) {
        //         if ($environmentTypes && is_array($environmentTypes)) {
        //             $groupedData[$groupName][] = $item;
        //         }
        //     }
        // }

        // $workingTimes = is_array($photographer->working_times) ? $photographer->working_times : json_decode($photographer->working_times, true);
        // $genreList = file_get_contents(public_path('text/genre_list.json'));
        // $data = json_decode($genreList, true) ?? [];
        // $isAllGenres = in_array('All', $data);
        // $genres = $data['genres'];
        // $photographerGenres = is_array($photographer->genre) ? $photographer->genre : json_decode($photographer->genre, true);
        // $normalizedPhotographerGenres = [];
        // if ($photographerGenres) {
        //     foreach ($photographerGenres as $genreName => $genreData) {
        //         $normalizedPhotographerGenres[$genreName] = [
        //             'all' => $genreData['all'] ?? 'false',
        //             'subgenres' => isset($genreData['subgenres'][0])
        //             ? (is_array($genreData['subgenres'][0]) ? $genreData['subgenres'][0] : $genreData['subgenres'])
        //             : []
        //         ];
        //     }
        // }

        // $bandTypes = json_decode($photographer->band_type) ?? [];

        // return [
        //     'photographer' => $photographer,
        //     'photographerName' => $photographerName,
        //     'photographerLocation' => $photographerLocation,
        //     'photographerPostalTown' => $photographerPostalTown,
        //     'photographerLat' => $photographerLat,
        //     'photographerLong' => $photographerLong,
        //     'logo' => $logo,
        //     'contact_name' => $contact_name,
        //     'contact_email' => $contact_email,
        //     'contact_number' => $contact_number,
        //     'platforms' => $platforms,
        //     'platformsToCheck' => $platformsToCheck,
        //     'description' => $description,
        //     'genres' => $genres,
        //     'photographerGenres' => $photographerGenres,
        //     'portfolio_link' => $portfolioLink,
        //     'serviceableId' => $serviceableId,
        //     'serviceableType' => $serviceableType,
        //     'waterMarkedPortfolioImages' => $waterMarkedPortfolioImages,
        //     'environmentTypes' => $environmentTypes,
        //     'groups' => $groupedData,
        //     'workingTimes' => $workingTimes,
        //     'isAllGenres' => $isAllGenres,
        //     'photographerGenres' => $normalizedPhotographerGenres,
        //     'bandTypes' => $bandTypes,
        // ];
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
    public function getBandsData(int $promoterId): array
    {
        return OtherService::where('other_service_id', 4)
            ->whereHas('events', function ($query) use ($promoterId) {
                $query->whereHas('promoters', function ($q) use ($promoterId) {
                    $q->where('promoter_id', $promoterId);
                });
            })
            ->distinct()
            ->get()
            ->toArray();
    }

    public function getEnvironmentTypes(User $user): ?array
    {
        $photographer = OtherService::where('other_service_id', 1)
            ->whereHas('linkedUsers', fn($query) => $query->where('user_id', $user->id))
            ->first();

        return $photographer ? json_decode($photographer->environment_type, true) : null;
    }
}