<?php

namespace App\Services;

use Skiddle\Sdk\Client;
use Illuminate\Support\Facades\Http;
use App\Models\ApiKey;

class SkiddleService
{
    protected $client;
    protected $api;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client(
            $apiKey ? $apiKey->access_token : config('services.skiddle.api_key')
        );
    }

    public function getAuthorizationUrl()
    {
        return 'https://www.skiddle.com/api/v1/oauth2/authorize?' . http_build_query([
            'client_id' => config('services.skiddle.client_id'),
            'redirect_uri' => config('services.skiddle.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'read write',
        ]);
    }

    public function handleCallback($code)
    {
        $response = Http::post('https://www.skiddle.com/api/v1/oauth2/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => config('services.skiddle.client_id'),
            'client_secret' => config('services.skiddle.client_secret'),
            'redirect_uri' => config('services.skiddle.redirect_uri'),
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to get access token: ' . $response->body());
    }

    public function getUserEvents()
    {
        if (!$this->apiKey) {
            throw new \Exception('No access token available');
        }

        return $this->client->events()->getMyEvents();
    }

    public function searchEvents($query)
    {
        // Use the Skiddle SDK to search for events
        return $this->client->events()->search(['keyword' => $query]);
    }

    public function handleWebhook($event, $payload)
    {
        // Handle the webhook event using the Skiddle SDK
        switch ($event) {
            case 'order.created':
                $this->handleOrderCreated($payload);
                break;
            case 'order.updated':
                $this->handleOrderUpdated($payload);
                break;
            default:
                // Handle unknown event
                break;
        }
    }

    private function handleOrderCreated($payload)
    {
        // Logic to handle order created event
    }

    private function handleOrderUpdated($payload)
    {
        // Logic to handle order updated event
    }
}
