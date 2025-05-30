<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public $href;
    public $id;
    public $label;
    public $type;

    /**
     * Create a new component instance.
     */
    public function __construct($href = null, $id = null, $label = 'Button', $type = "Button")
    {
        $this->href = $href;
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}