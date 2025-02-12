<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenueProfileUpdateRequest extends FormRequest
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
            'w3w' => 'nullable|string',
            'capacity' => 'nullable|numeric',
            'inHouseGear' => 'nullable|string',
            'deposit_required' => 'nullable|in:yes,no',
            'deposit_amount' => 'nullable|numeric',
            'band_types' => 'nullable|array',
            'genres' => 'nullable|array',
            'contact_name' => 'nullable|string',
            'contact_number' => ['nullable', 'regex:/^(?:0|\+44)(?:\d\s?){9,10}$/'],
            'contact_email' => 'nullable|email',
            'contact_links.*.*' => 'nullable|url',
            'preferred_contact' => 'nullable|string',
            'description' => 'nullable|string',
            'additionalInfo' => 'nullable|string',
            'logo_url' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'deposit_required.required' => 'Please specify if a deposit is required',
            'deposit_required.in' => 'Deposit required must be either yes or no'
        ];
    }
}
