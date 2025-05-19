<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use What3words\Geocoder\Geocoder;
use App\Services\What3WordsService;
use Illuminate\Support\Facades\Http;

class What3WordsController extends Controller
{
    protected $what3wordsService;

    public function __construct(What3WordsService $what3wordsService)
    {
        $this->what3wordsService = $what3wordsService;
    }

    /**
     * Handle the What3Words address input and get suggestions.
     */
    public function suggest(Request $request)
    {
        $validated = $request->validate([
            'w3w' => 'required|string|min:7',
        ]);

        $address = $validated['w3w'];

        // Trigger the What3Words service to get suggestions
        $suggestedWords = $this->what3wordsService->getSuggestedWords($address);

        if ($suggestedWords) {
            return response()->json(['success' => true, 'suggestions' => $suggestedWords]);
        }

        return response()->json(['success' => false, 'suggestions' => 'No suggestions avaliable.']);
    }

    /**
     * Convert a regular address to What3Words format
     */
    public function convertToW3W(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|min:3',
        ]);

        $address = $validated['address'];

        \Log::info('Converting address to W3W', ['address' => $address]);

        try {
            // Check for API key
            $apiKey = config('services.what3words.api_key');
            $googleApiKey = config('services.google.maps_api_key');

            if (empty($apiKey) || empty($googleApiKey)) {
                \Log::error('Missing API keys', [
                    'w3w_key_exists' => !empty($apiKey),
                    'google_key_exists' => !empty($googleApiKey)
                ]);
                return response()->json(['success' => false, 'message' => 'API keys not configured']);
            }

            $api = new Geocoder($apiKey);

            // First convert the address to coordinates using geocoding
            $coordinates = $this->geocodeAddress($address);

            if (!$coordinates) {
                \Log::warning('Failed to geocode address', ['address' => $address]);
                return response()->json(['success' => false, 'message' => 'Could not convert this address']);
            }

            \Log::info('Geocoded address', ['coordinates' => $coordinates]);

            // Then convert the coordinates to 3 words
            $response = $api->convertTo3wa($coordinates['lat'], $coordinates['lng']);
            \Log::info('W3W API response', ['response' => $response]);

            if (isset($response['words'])) {
                return response()->json([
                    'success' => true,
                    'result' => [
                        'words' => $response['words'],
                        'nearestPlace' => $response['nearestPlace'] ?? 'Unknown location',
                        'coordinates' => $coordinates
                    ]
                ]);
            }

            \Log::warning('W3W API did not return words', ['response' => $response]);
            return response()->json(['success' => false, 'message' => 'No words returned from What3Words']);
        } catch (\Exception $e) {
            \Log::error('What3Words conversion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred']);
        }
    }

    /**
     * Geocode an address to latitude/longitude coordinates using Google Maps API
     * 
     * @param string $address The address to geocode
     * @return array|null Array containing lat/lng or null if geocoding failed
     */
    protected function geocodeAddress(string $address): ?array
    {
        try {
            $googleApiKey = config('services.google.maps_api_key');

            // URL encode the address
            $encodedAddress = urlencode($address);

            // Make API request to Google Maps Geocoding API
            $response = \Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'address' => $address,
                'key' => $googleApiKey,
            ]);

            $data = $response->json();

            // Log the response for debugging
            \Log::info('Google Geocoding response', ['response' => $data]);

            if ($response->successful() && isset($data['results'][0]['geometry']['location'])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'lat' => $location['lat'],
                    'lng' => $location['lng']
                ];
            }

            \Log::warning('Geocoding failed', [
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? 'No error message provided'
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Exception during geocoding', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}