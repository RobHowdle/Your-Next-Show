<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileSidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $dashboardType,
        public ?object $standardUserData = null
    ) {
        $this->dashboardType = $dashboardType;
        $this->standardUserData = $standardUserData;
    }

    /**
     * Get navigation items based on dashboard type.
     */
    public function getNavigationItems(): array
    {
        $items = [
            [
                'icon' => 'user',
                'label' => 'User Details',
                'tab' => 'profile'
            ],
            [
                'icon' => 'info-circle',
                'label' => 'Basic Information',
                'tab' => 'basic-info'
            ],
            [
                'icon' => 'pen',
                'label' => 'Description',
                'tab' => 'description'
            ],
        ];

        // Add role-specific navigation items
        switch ($this->dashboardType) {
            case 'venue':
                $items = array_merge($items, [
                    [
                        'icon' => 'building',
                        'label' => 'Venue Details',
                        'tab' => 'venue-details'
                    ],
                    [
                        'icon' => 'music',
                        'label' => 'In-House Gear',
                        'tab' => 'gear'
                    ]
                ]);
                break;
            case 'artist':
                $items = array_merge($items, [
                    [
                        'icon' => 'music',
                        'label' => 'Stream Links',
                        'tab' => 'stream-links'
                    ],
                    [
                        'icon' => 'users',
                        'label' => 'Band Members',
                        'tab' => 'members'
                    ]
                ]);
                break;
        }

        // Add settings as the last item
        $items[] = [
            'icon' => 'cog',
            'label' => 'Settings',
            'tab' => 'settings'
        ];

        return $items;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile-sidebar', [
            'navigationItems' => $this->getNavigationItems()
        ]);
    }
}
