<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Rules\CompromisedPassword;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'email' => ['required', 'email', 'lowercase', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                new CompromisedPassword(),
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'not_regex:/' . preg_quote($this->first_name) . '/i',
                'not_regex:/' . preg_quote($this->last_name) . '/i',
            ],
            'role' => [
                'required',
                Rule::exists('roles', 'name')->where(function ($query) {
                    return $query->where('name', '!=', 'administrator');
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name' => 'Please enter your first name.',
            'last_name' => 'Please enter your last name.',
            'date_of_birth' => 'Please enter your date of birth.',
            'role' => 'Please select a role.',
            'role.exists' => 'The selected role is invalid.',
            'password.uncompromised' => 'We\'ve detected this password has been compromised in a data breach. Please choose a different password.',
        ];
    }
}