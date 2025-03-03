<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DesignStylesAndMediums extends Component
{
    public $styles;
    public $print;
    public $environments;

    /**
     * Create a new component instance.
     */
    public function __construct($styles = null, $print = null, $environments = null)
    {
        $this->styles = $styles;
        $this->print = $print;
        $this->environments = $environments;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.design-styles-and-mediums');
    }
}