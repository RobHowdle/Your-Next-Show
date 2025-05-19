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
        // Get the standard rules that apply to every submission
        $rules = [
            // Common fields that should always be validated if present
            'name' => 'nullable|string|max:255',
            'contact_number' => ['nullable', 'regex:/^(?:0|\+44)(?:\d\s?){9,10}$/'],
            'contact_email' => 'nullable|email',
            'preferred_contact' => 'nullable|string',
            'logo_url' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'inHouseGear' => 'nullable|string',
            'deposit_required' => 'nullable|in:yes,no',
            'deposit_amount' => 'nullable|numeric',
            'band_types' => 'nullable|array',
            'genres' => 'nullable|array',
            'additionalInfo' => 'nullable|string',
        ];

        // If this is the basic information form submission
        if ($this->has('location') || $this->has('contact_name') || $this->has('contact_links')) {
            $rules = array_merge($rules, [
                'location' => 'required|string',
                'postal_town' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'contact_name' => 'required|string',
                'contact_links' => 'sometimes|array',
                'contact_links.*' => 'sometimes|array',
                'contact_links.*.*' => 'sometimes|nullable|string|url',
            ]);
        }

        // If this is the description form submission
        if ($this->has('description')) {
            $rules['description'] = 'required|string';
        }

        // If this is the venue details form (capacity, etc.)
        if ($this->has('capacity') || $this->has('w3w')) {
            $rules['capacity'] = 'nullable|numeric';
            $rules['venue.w3w'] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Get the validation attributes for the request.
     *
     * @return array<string, string>
     */

    public function messages()
    {
        return [
            // Basic Info
            'name' => 'Please provide a valid venue name',
            'location' => 'Please provide a valid location',
            'postal_town' => 'Please provide a valid postal town',
            'latitude' => 'Please provide a valid latitude',
            'longitude' => 'Please provide a valid longitude',
            'contact_name' => 'Please provide a contact name',
            'contact_email' => 'Please provide a valid email address',
            'contact_number' => 'Please provide a valid contact number',
            'contact_links.*.*.url' => 'Please provide a valid URL',
            'logo_url.image' => 'Please provide a valid image',
            'logo_url.mimes' => 'Please provide a valid image type (jpeg, jpg, png, webp, svg)',
            'logo_url.max' => 'Too Big! (that\'s what she said) Max size is 5MB',

            // Description
            'description' => 'Please provide a valid description',

            // Genre & Artist Types
            'band_types' => 'Please provide valid band types',
            'genres' => 'Please provide valid genres',

            // Venue Details
            'capacity' => 'Please provide a valid capacity',
            'w3w' => 'Please provide a valid What3Words address',

            // In House Gear
            'in_house_gear' => 'Please provide valid in-house gear',
            'deposit_required.required' => 'Please specify if a deposit is required',
            'deposit_required.in' => 'Deposit required must be either yes or no'
        ];
    }
}
