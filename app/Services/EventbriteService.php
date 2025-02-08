<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EventbriteService
{
    protected $baseUrl = 'https://www.eventbriteapi.com/v3';
    protected $privateToken;

    public function __construct(ApiKey $apiKey)
    {
        $this->privateToken = config('services.eventbrite.key');
    }

    public function searchEvents(string $query = '')
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->privateToken
            ];

            // Get organization ID first
            $organizationId = $this->getOrganizationId($headers);

            if (!$organizationId) {
                throw new \Exception('No organization ID found');
            }

            Log::debug('Making Eventbrite API request', [
                'organizationId' => $organizationId,
                'query' => $query
            ]);

            // Search for events within the organization
            // Note: We're using name_filter instead of q for searching
            $response = Http::withHeaders($headers)
                ->get("{$this->baseUrl}/organizations/{$organizationId}/events", [
                    'status' => 'live',
                    'name_filter' => $query, // Changed from 'q' to 'name_filter'
                    'expand' => 'ticket_availability,venue',
                    'order_by' => 'start_asc' // Sort by start date ascending
                ]);

            if (!$response->successful()) {
                Log::error('Eventbrite API response error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to fetch events: ' . $response->body());
            }

            $events = $response->json('events', []);

            return collect($events)->map(function ($event) {
                $startDateTime = new \DateTime($event['start']['local']);
                $endDateTime = new \DateTime($event['end']['local']);

                return [
                    'id' => $event['id'],
                    'name' => $event['name']['text'],
                    'date' => $startDateTime->format('Y-m-d'),
                    'start_time' => $startDateTime->format('H:i'),
                    'end_time' => $endDateTime->format('H:i'),
                    'url' => $event['url'],
                    'venue' => $event['venue']['name'] ?? 'No venue specified',
                    'tickets_available' => $event['ticket_availability']['has_available_tickets'] ?? false,
                    'status' => $event['status'],
                    'description' => $event['summary'] ?? 'No description available',
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Eventbrite API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function getOrganizationId(array $headers)
    {
        try {
            $response = Http::withHeaders($headers)
                ->get("{$this->baseUrl}/users/me/organizations");

            if (!$response->successful()) {
                Log::error('Failed to fetch organization ID', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to fetch organization ID: ' . $response->body());
            }

            $data = $response->json();
            return $data['organizations'][0]['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('Error getting organization ID', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
