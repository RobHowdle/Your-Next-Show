<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GoogleAddressPicker extends Component
{
    public $id;
    public $name;
    public $label;
    public $placeholder;
    public $value;
    public $latitude;
    public $longitude;
    public $dataId;
    public $postalTown;

    public function __construct(
        string $id,
        string $name,
        string $label,
        string $placeholder,
        string $postalTown,
        $value = null,
        $latitude = null,
        $longitude = null,
        $dataId
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->dataId = $dataId;
        $this->postalTown = $postalTown;
    }

    public function render()
    {
        return view('components.google-address-picker');
    }
}