<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LoadingOverlay extends Component
{
    public function __construct(
        public string $text = 'LOADING...',
        public bool $showMusicLoader = true,
        public string $textClasses = 'text-lg text-white'
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.loading-overlay');
    }
}