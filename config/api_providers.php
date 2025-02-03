<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Define available API providers and their configurations
    |
    */

    'providers' => [
        'ticketing' => [
            'eventbrite' => [
                'name' => 'Eventbrite',
                'description' => 'Event ticketing and registration platform',
                'requires_secret' => true,
                'required_fields' => ['api_key', 'api_secret']
            ],
            'ticketmaster' => [
                'name' => 'Ticketmaster',
                'description' => 'Global ticketing and entertainment platform',
                'requires_secret' => true,
                'required_fields' => ['api_key', 'client_secret']
            ]
        ],

        'payment' => [
            'stripe' => [
                'name' => 'Stripe',
                'description' => 'Payment processing platform',
                'requires_secret' => true,
                'required_fields' => ['publishable_key', 'secret_key']
            ],
            'paypal' => [
                'name' => 'PayPal',
                'description' => 'Online payment system',
                'requires_secret' => true,
                'required_fields' => ['client_id', 'client_secret']
            ]
        ],

        'marketing' => [
            'mailchimp' => [
                'name' => 'Mailchimp',
                'description' => 'Email marketing platform',
                'requires_secret' => true,
                'required_fields' => ['api_key']
            ],
            'sendgrid' => [
                'name' => 'SendGrid',
                'description' => 'Email delivery service',
                'requires_secret' => true,
                'required_fields' => ['api_key']
            ]
        ],

        'social' => [
            'facebook' => [
                'name' => 'Facebook',
                'description' => 'Social media platform',
                'requires_secret' => true,
                'required_fields' => ['app_id', 'app_secret']
            ],
            'instagram' => [
                'name' => 'Instagram',
                'description' => 'Photo sharing platform',
                'requires_secret' => true,
                'required_fields' => ['access_token']
            ],
            'twitter' => [
                'name' => 'Twitter',
                'description' => 'Social networking service',
                'requires_secret' => true,
                'required_fields' => ['api_key', 'api_secret']
            ]
        ],

        'analytics' => [
            'google_analytics' => [
                'name' => 'Google Analytics',
                'description' => 'Web analytics service',
                'requires_secret' => false,
                'required_fields' => ['tracking_id']
            ]
        ]
    ]
];