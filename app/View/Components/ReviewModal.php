<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReviewModal extends Component
{
    public $title;
    public $serviceType;
    public $profileId;
    public $service;
    /**
     * Create a new component instance.
     */
    public function __construct($title, $serviceType, $profileId, $service)
    {
        $this->title = $title;
        $this->serviceType = $serviceType;
        $this->profileId = $profileId;
        $this->service = $service;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.review-modal');
    }
}