<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->route('user')->id)],
            'date_of_birth' => ['required', 'date'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
            'location' => ['required', 'string'],
            'postal_town' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'mailing_preferences' => ['nullable', 'array'],
            'mailing_preferences.*' => ['string', Rule::in(array_keys(config('mailing_preferences.communication_preferences')))],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'roles.required' => 'At least one role must be assigned to the user.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
            'postal_town.required' => 'The city/town field is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'date_of_birth' => 'date of birth',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert mailing preferences array to associative array with boolean values
        if ($this->has('mailing_preferences')) {
            $preferences = array_fill_keys(
                array_keys(config('mailing_preferences.communication_preferences')),
                false
            );

            foreach ($this->input('mailing_preferences', []) as $key) {
                if (array_key_exists($key, config('mailing_preferences.communication_preferences'))) {
                    $preferences[$key] = true;
                }
            }

            $this->merge([
                'mailing_preferences' => $preferences
            ]);
        }
    }
}