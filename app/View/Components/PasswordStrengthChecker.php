<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use App\Rules\CompromisedPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class PasswordStrengthChecker extends Component
{
    private CompromisedPassword $compromisedRule;

    /**
     * The ID of the password input field.
     *
     * @var string
     */
    public string $inputId;
    private ?string $apiError = null;

    /**
     * Create a new component instance.
     */
    public function __construct(string $inputId = 'password')
    {
        $this->inputId = $inputId;
        $this->compromisedRule = new CompromisedPassword();
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
            ],
            'compromised' => [
                'text' => 'Must not be a compromised password',
                'check' => function ($password) {
                    try {
                        $result = $this->compromisedRule->passes('password', $password);
                        $this->apiError = null;
                        return $result;
                    } catch (\Exception $e) {
                        Log::error('Password check failed: ' . $e->getMessage());
                        $this->apiError = 'Unable to verify password security. Please try again later.';
                        return true; // Fail open for better UX
                    }
                }
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
            'strengthLevels' => $this->getStrengthLevels(),
            'apiError' => $this->apiError,
        ]);
    }
}