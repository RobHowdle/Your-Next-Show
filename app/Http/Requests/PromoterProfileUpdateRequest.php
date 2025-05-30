<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoterProfileUpdateRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'postal_town' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'logo_url' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'band_types' => 'nullable|array',
            'genres' => 'nullable|array',
            'contact_name' => 'nullable|string',
            'contact_number' => ['nullable', 'regex:/^(?:0|\+44)(?:\d\s?){9,10}$/'],
            'contact_email' => 'nullable|email',
            'contact_links' => 'sometimes|array',
            'contact_links.*' => 'string|url',
            'description' => 'nullable|string',
            'preferred_contact' => 'nullable|string',
        ];
    }
}