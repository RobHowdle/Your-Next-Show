<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notes extends Component
{
    public $dashboardType;

    public function __construct($dashboardType)
    {
        $this->dashboardType = $dashboardType;
    }

    public function render()
    {
        return view('components.notes');
    }
}