<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use What3words\Geocoder\Geocoder;
use What3words\Geocoder\AutoSuggestOption;

class What3WordsService
{
    protected $apiKey;

    /**
     * Convert a regular address to What3Words format
     *
     * @param string $address
     * @return array|null
     */
    public function convertAddressToW3W(string $address): ?array
    {
        $api = new Geocoder(config('services.what3words.api_key'));

        try {
            // First convert the address to coordinates using a geocoding service
            $coordinates = $this->geocodeAddress($address);

            if (!$coordinates) {
                return null;
            }

            // Then convert the coordinates to 3 words
            $response = $api->convertTo3wa($coordinates['lat'], $coordinates['lng']);

            if (isset($response['words'])) {
                return [
                    'words' => $response['words'],
                    'nearestPlace' => $response['nearestPlace'] ?? 'Unknown location',
                    'coordinates' => [
                        'lat' => $coordinates['lat'],
                        'lng' => $coordinates['lng']
                    ]
                ];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('What3Words conversion error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Geocode an address to coordinates
     * 
     * @param string $address
     * @return array|null
     */
    protected function geocodeAddress(string $address): ?array
    {
        // For a basic implementation, you can use Google's Geocoding API
        // You'll need to add a Google API key to your config
        $googleApiKey = config('services.google.api_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $googleApiKey,
        ]);

        if ($response->successful() && isset($response['results'][0]['geometry']['location'])) {
            $location = $response['results'][0]['geometry']['location'];
            return [
                'lat' => $location['lat'],
                'lng' => $location['lng']
            ];
        }

        return null;
    }

    /**
     * Get What3Words address suggestions using autosuggest.
     *
     * @param string $words
     * @return array|null
     */
    public function getSuggestedWords(string $words): ?array
    {
        // Initialize Geocoder with the API key
        $api = new Geocoder(config('services.what3words.api_key'));

        // Define options for the API call
        $options = [
            AutoSuggestOption::clipTocountry("GB"),
            AutoSuggestOption::numberResults(5),
        ];

        // Call the autosuggest method using the correct Geocoder instance
        try {
            $response = $api->autosuggest($words, $options);

            // Check if suggestions exist
            if (isset($response['suggestions']) && is_array($response['suggestions'])) {
                return $response['suggestions'];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }

        // Return null if no suggestions are found
        return null;
    }
}