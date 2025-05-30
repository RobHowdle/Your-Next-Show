<?php

return [
    'ticket_platforms' => [
        'eventbrite' => [
            'name' => 'Eventbrite',
            'description' => 'Connect your Eventbrite account to sync ticket sales and event data.',
            'documentation_url' => 'https://www.eventbrite.com/platform/api',
            'webhook_events' => ['order.placed', 'order.updated', 'order.refunded'],
            'enabled' => true,
        ],
        'ticketmaster' => [
            'name' => 'Ticketmaster',
            'description' => 'Integrate with Ticketmaster to manage your event listings and sales.',
            'documentation_url' => 'https://developer.ticketmaster.com/products-and-docs/apis/getting-started/',
            'webhook_events' => ['order.created', 'order.updated'],
            'enabled' => false,
        ],
        'fatsoma' => [
            'name' => 'Fatsoma',
            'description' => 'Connect your Fatsoma account to track ticket sales and promotions.',
            'documentation_url' => 'https://fatsoma.com/for-promoters',
            'webhook_events' => ['ticket.sold', 'ticket.refunded'],
            'enabled' => false,
        ],
        'skiddle' => [
            'name' => 'Skiddle',
            'description' => 'Integrate with Skiddle for ticket sales and event management.',
            'documentation_url' => 'https://www.skiddle.com/api/',
            'webhook_events' => ['order.created', 'order.updated'],
            'enabled' => false, // Set to false if not yet implemented
        ],
    ],

    // // Add other integration types here if needed
    // 'calendar' => [
    //     'google' => [
    //         'name' => 'Google Calendar',
    //         'enabled' => true,
    //     ],
    //     'outlook' => [
    //         'name' => 'Outlook Calendar',
    //         'enabled' => false,
    //     ],
    // ],
];
