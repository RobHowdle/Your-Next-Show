<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PasswordStrengthChecker extends Component
{
    /**
     * The ID of the password input field.
     *
     * @var string
     */
    public string $inputId;

    /**
     * Create a new component instance.
     */
    public function __construct(string $inputId = 'password')
    {
        $this->inputId = $inputId;
    }

    /**
     * Get the requirements for password validation.
     *
     * @return array
     */
    public function getRequirements(): array
    {
        return [
            'length' => [
                'text' => 'Min 8 characters',
                'regex' => '/.{8,}/'
            ],
            'uppercase' => [
                'text' => 'At least 1 uppercase letter (A-Z)',
                'regex' => '/[A-Z]/'
            ],
            'lowercase' => [
                'text' => 'At least 1 lowercase letter (a-z)',
                'regex' => '/[a-z]/'
            ],
            'number' => [
                'text' => 'At least 1 number (0-9)',
                'regex' => '/[0-9]/'
            ],
            'special' => [
                'text' => 'At least 1 special character (@$!%*?&)',
                'regex' => '/[@$!%*?&]/'
            ]
        ];
    }

    /**
     * Get the strength levels for password validation.
     *
     * @return array
     */
    public function getStrengthLevels(): array
    {
        return [
            'weak' => [
                'color' => 'bg-red-500',
                'width' => 'w-1/4'
            ],
            'fair' => [
                'color' => 'bg-yellow-500',
                'width' => 'w-2/4'
            ],
            'good' => [
                'color' => 'bg-blue-500',
                'width' => 'w-3/4'
            ],
            'strong' => [
                'color' => 'bg-green-500',
                'width' => 'w-full'
            ]
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.password-strength-checker', [
            'requirements' => $this->getRequirements(),
            'strengthLevels' => $this->getStrengthLevels()
        ]);
    }
}