<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BandProfileUpdateRequest extends FormRequest
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
            'logo_url' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'location' => 'nullable|string',
            'postal_town' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'other_service_id' => "nullable|exists:other_services_list,id",
            'about' => 'nullable|string',
            'packages' => "nullable|array",
            'environment_type' => "nullable|array",
            'working_times' => "nullable|array",
            'members' => "nullable|string",
            'default_platform' => "nullable|string",
            'stream_links' => "nullable|array",
            'band_types' => 'nullable|array',
            'genres' => 'nullable|array',
            'contact_name' => 'nullable|string',
            'contact_number' => ['nullable', 'regex:/^(?:0|\+44)(?:\d\s?){9,10}$/'],
            'contact_email' => 'nullable|email',
            'contact_links.*.*' => 'nullable|url',
            'portfolio_link' => 'nullable|url',
            'services' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*.name' => 'nullable|string',
            'members.*.role' => 'nullable|string',
            'members.*.bio' => 'nullable|string',
            'members.*.profile_pic' => 'nullable|string',
            'member_pic.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'preferred_contact' => 'nullable|string',
        ];
    }
}
