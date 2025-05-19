<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class W3WSearch extends Component
{
    /**
     * The input field ID
     *
     * @var string
     */
    public $id;

    /**
     * The input field name
     *
     * @var string
     */
    public $name;

    /**
     * The input field current value
     *
     * @var string|null
     */
    public $value;

    /**
     * The input label text
     *
     * @var string
     */
    public $label;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id = 'w3w',
        string $name = 'w3w',
        ?string $value = null,
        string $label = 'What3Words Address'
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.w3w-search');
    }
}