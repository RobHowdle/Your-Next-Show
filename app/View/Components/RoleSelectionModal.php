<?php

namespace App\View\Components;

use Closure;
use App\Models\Role;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class RoleSelectionModal extends Component
{
    public $roles;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Get roles excluding service roles and admin
        $this->roles = Role::query()
            ->whereNotIn('name', ['service-owner', 'service-manager', 'service-member', 'administrator'])
            ->orderByRaw("CASE 
                WHEN name = 'venue' THEN 1
                WHEN name = 'promoter' THEN 2
                WHEN name = 'artist' THEN 3
                WHEN name = 'photographer' THEN 4
                WHEN name = 'videographer' THEN 5
                WHEN name = 'designer' THEN 6
                ELSE 7 END")
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->name,
                    'name' => $role->display_name,
                    'description' => $role->description,
                    'icon' => $role->icon
                ];
            });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.role-selection-modal', [
            'roles' => $this->roles,
        ]);
    }
}