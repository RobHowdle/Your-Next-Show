<?php

namespace App\Services;

use App\Models\ApiKey;

class SkiddleService
{
    protected $apiKey;

    public function __construct(ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function handleWebhook($event, $payload)
    {
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

    public function searchEvents($query)
    {
        // Logic to search events using Skiddle API
    }
}
