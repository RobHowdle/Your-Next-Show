<?php

return [
    'facebook' => [
        'name' => 'Facebook',
        'icon' => 'fab fa-facebook',
        'url_pattern' => 'https://facebook.com/{username}',
        'placeholder' => 'Enter your Facebook username or page URL',
        'validation' => [
            'pattern' => '^(https?:\/\/)?(www\.)?facebook.com\/[a-zA-Z0-9(\.\?)?]',
            'message' => 'Please enter a valid Facebook URL'
        ]
    ],
    'instagram' => [
        'name' => 'Instagram',
        'icon' => 'fab fa-instagram',
        'url_pattern' => 'https://instagram.com/{username}',
        'placeholder' => 'Enter your Instagram username',
        'validation' => [
            'pattern' => '^(https?:\/\/)?(www\.)?instagram.com\/[a-zA-Z0-9_\.]+\/?$',
            'message' => 'Please enter a valid Instagram URL'
        ]
    ],
    'x' => [
        'name' => 'X (formerly Twitter)',
        'icon' => 'fab fa-twitter',
        'url_pattern' => 'https://x.com/{username}',
        'placeholder' => 'Enter your X username',
        'validation' => [
            'pattern' => '^(https?:\/\/)?(www\.)?x.com\/[a-zA-Z0-9_]+\/?$',
            'message' => 'Please enter a valid X URL'
        ]
    ],
    'linkedin' => [
        'name' => 'LinkedIn',
        'icon' => 'fab fa-linkedin',
        'url_pattern' => 'https://linkedin.com/in/{username}',
        'placeholder' => 'Enter your LinkedIn profile URL',
        'validation' => [
            'pattern' => '^(https?:\/\/)?(www\.)?linkedin.com\/(in|company)\/[a-zA-Z0-9\-]+\/?$',
            'message' => 'Please enter a valid LinkedIn URL'
        ]
    ],
    'youtube' => [
        'name' => 'YouTube',
        'icon' => 'fab fa-youtube',
        'url_pattern' => 'https://youtube.com/{channel}',
        'placeholder' => 'Enter your YouTube channel URL',
        'validation' => [
            'pattern' => '^(https?:\/\/)?(www\.)?youtube.com\/(c\/|channel\/|user\/)?[a-zA-Z0-9\-\_]+\/?$',
            'message' => 'Please enter a valid YouTube URL'
        ]
    ],
    'tiktok' => [
        'name' => 'TikTok',
        'icon' => 'fab fa-tiktok',
        'url_pattern' => 'https://tiktok.com/@{username}',
        'placeholder' => 'Enter your TikTok username',
        'validation' => [
            'pattern' => '^(https?:\/\/)?(www\.)?tiktok.com\/@[a-zA-Z0-9_\.]+\/?$',
            'message' => 'Please enter a valid TikTok URL'
        ]
    ],
    'website' => [
        'name' => 'Website',
        'icon' => 'fas fa-globe',
        'url_pattern' => '{url}',
        'placeholder' => 'Enter your website URL',
        'validation' => [
            'pattern' => '^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$',
            'message' => 'Please enter a valid URL'
        ]
    ]
];