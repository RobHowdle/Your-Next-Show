<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use App\Rules\CompromisedPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'userFirstName' => ['sometimes', 'string', 'max:255'],
            'userLastName' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'userDob' => ['sometimes', 'date'],
            'role' => ['sometimes', 'exists:App\Models\Role,id'],
            'location' => ['sometimes', 'string'],
            'postal_town' => ['sometimes', 'string'],
            'latitude' => ['sometimes', 'numeric'],
            'longitude' => ['sometimes', 'numeric'],
            'password' => [
                'nullable',
                'sometimes',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && empty($this->password_confirmation)) {
                        $fail('The password confirmation field is required when password is present.');
                    }
                }
            ],
            'password_confirmation' => [
                'nullable',
                'sometimes',
                'same:password',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && empty($this->password)) {
                        $fail('The password field is required when password confirmation is present.');
                    }
                }
            ],
        ];
    }

    /**
     * Get the validation attributes for the request.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // User Details
            'userFirstName.string' => 'The first name must be a string.',
            'userLastName.string' => 'The last name must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'userDob.date' => 'The date of birth must be a valid date.',
            'role.exists' => 'The selected role is invalid.',
            'location.string' => 'The location must be a string.',
            'postal_town.string' => 'The postal town must be a string.',
            'latitude.numeric' => 'The latitude must be a number.',
            'longitude.numeric' => 'The longitude must be a number.',
        ];
    }
}