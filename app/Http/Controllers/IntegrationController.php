<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\EventbriteService;
use Illuminate\Support\Facades\Log;

class IntegrationController extends Controller
{
    // Creating/ Deleting Integrations
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'serviceableType' => 'required|string|in:promoter,venue,service',
                'serviceableId' => 'required|integer',
                'provider' => [
                    'required',
                    'string',
                    Rule::in(array_keys(config('integrations.ticket_platforms'))),
                ],
                'key_type' => 'required|string|in:api_key,webhook',
                'api_key' => 'required|string',
                'api_secret' => 'required|string',
            ]);

            $apiKey = new ApiKey([
                'serviceable_id' => $validated['serviceableId'],
                'serviceable_type' => match ($validated['serviceableType']) {
                    'promoter' => 'App\Models\Promoter',
                    'venue' => 'App\Models\Venue',
                    'service' => 'App\Models\OtherService',
                },
                'name' => $validated['provider'],
                'key_type' => $validated['key_type'],
                'api_key' => $validated['api_key'],
                'api_secret' => $validated['api_secret'],
                'is_active' => true,
                'last_used_at' => now(),
            ]);

            $apiKey->save();

            Log::info('Integration saved successfully', ['id' => $apiKey->id]);

            return response()->json([
                'message' => 'Integration saved successfully',
                'data' => $apiKey
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving integration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error saving integration',
                'errors' => [$e->getMessage()]
            ], 422);
        }
    }

    public function searchEvents(Request $request, $platform)
    {
        try {
            Log::info('Search Events Request', [
                'platform' => $platform,
                'query' => $request->query('query'),
                'user' => auth()->user()
            ]);

            // Validate platform is enabled in config
            if (!config("integrations.ticket_platforms.{$platform}.enabled")) {
                return response()->json(['error' => 'Platform not supported'], 400);
            }

            // Get the API key for the platform
            $apiKey = ApiKey::where('name', $platform)
                ->where('is_active', true)
                ->first();

            if (!$apiKey) {
                return response()->json(['error' => 'No active API key found'], 404);
            }

            // Get the appropriate service
            $service = match ($platform) {
                'eventbrite' => new EventbriteService($apiKey),
                default => throw new \Exception('Unsupported platform'),
            };

            // Search for events
            $events = $service->searchEvents($request->query('query', ''));

            return response()->json([
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching platform events', [
                'platform' => $platform,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to search events'], 500);
        }
    }
}
