<?php

return [
    'modules' => [
        'finances' => [
            'name' => 'Finances',
            'description' => 'This module provides the user with the ability to create and export various finances records such as budgets as well as seeing real time data of income, outgoings and profits.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [
                'currency' => 'GPB',
            ],
            'dependencies' => [],
        ],
        'events' => [
            'name' => 'Events',
            'description' => 'This module provides the user with the ability to create events as well as track ticket sales, attendance and reviews of the event.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
        'todo_list' => [
            'name' => 'Todo List',
            'description' => 'This module provides the user with the ability to create and manage a todo list of tasks they need to complete.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
        'reviews' => [
            'name' => 'Reviews',
            'description' => 'This module provides the user with the ability to approve, display, hide and remove reviews left by people who have interacted with them.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
        'notes' => [
            'name' => 'Notes',
            'description' => 'This module provides the user with the ability to create and manage notes with the ability to convert the note to a todo list item.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
        'documents' => [
            'name' => 'Documents',
            'description' => 'This module provides the user with the ability to upload various documents such as images, word documents, pdfs etc.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
        'users' => [
            'name' => 'Users',
            'description' => 'This module provides the user with the ability to manage users linked to their service.',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
        'jobs' => [
            'name' => 'Jobs',
            'description' => 'This module provides the user with the ability to create and manage jobs linked to their service',
            'enabled' => true,
            'version' => '1.0',
            'author' => 'Robert Howdle',
            'settings' => [],
            'dependencies' => [],
        ],
    ],
];
