<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public function __construct(
        public string $name,
        public array $options,
        public array $selected = []
    ) {}

    public function render()
    {
        return view('components.select');
    }
}