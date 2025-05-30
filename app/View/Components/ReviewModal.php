<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReviewModal extends Component
{
    public function __construct(
        public string $title,
        public string $serviceType,
        public string $profileId,
        public string $service
    ) {}

    public function getFormAction(): string
    {
        $slug = strtolower(str_replace(' ', '-', $this->title)); // Use title for slug (service name)
        $serviceType = strtolower($this->service); // Use actual service type (e.g., artist, designer)

        return match ($this->serviceType) {
            'venue' => route('submit-venue-review', ['slug' => $slug]),
            'promoter' => route('submit-promoter-review', ['slug' => $slug]),
            'singleService' => route('submit-single-service-review', [
                'serviceType' => $serviceType,
                'name' => $slug
            ]),
            default => throw new \InvalidArgumentException("Unknown service type: {$this->serviceType}")
        };
    }

    public function render()
    {
        return view('components.review-modal');
    }
}
