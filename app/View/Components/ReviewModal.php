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
        $slug = strtolower(str_replace(' ', '-', $this->service));

        return match ($this->serviceType) {
            'venue' => route('submit-venue-review', ['slug' => $slug]),
            'promoter' => route('submit-promoter-review', ['slug' => $slug]),
            'service' => route('submit-single-service-review', [
                'serviceType' => strtolower($this->serviceType),
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
