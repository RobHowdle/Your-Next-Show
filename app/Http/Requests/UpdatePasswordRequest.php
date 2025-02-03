<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules;
use App\Rules\CompromisedPassword;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'password.uncompromised' => 'We\'ve detected this password has been compromised in a data breach. Please choose a different password.',
        ];
    }
}
