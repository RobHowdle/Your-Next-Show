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
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
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
            'members_json' => 'nullable|string',
            'preferred_contact' => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate members_json if it exists
            if ($this->has('members_json')) {
                $membersJson = $this->input('members_json');
                $members = json_decode($membersJson, true);

                // Check if JSON is valid
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add('members_json', 'The members data is not valid JSON.');
                    return;
                }

                // Check if it's an array
                if (!is_array($members)) {
                    $validator->errors()->add('members_json', 'The members data must be an array.');
                    return;
                }

                // Validate each member
                foreach ($members as $index => $member) {
                    if (!isset($member['name']) || empty(trim($member['name']))) {
                        $validator->errors()->add("members_json", "Member #{$index} must have a name.");
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // Image validation messages
            'logo_url.image' => 'The band logo must be an image file.',
            'logo_url.mimes' => 'The band logo must be a file of type: jpeg, jpg, png, webp, or svg.',
            'logo_url.max' => 'The band logo must not be larger than 5MB.',

            // Member profile picture validation
            'member_pic.*.image' => 'Member profile pictures must be image files.',
            'member_pic.*.mimes' => 'Member profile pictures must be of type: jpeg, jpg, png, or webp.',
            'member_pic.*.max' => 'Member profile pictures must not be larger than 5MB.',

            // Members json validation
            'members_json.string' => 'The members data is not properly formatted.',

            // Members array validation
            'members.array' => 'The members data must be an array.',
            'members.*.name.string' => 'Member names must be text.',
            'members.*.role.string' => 'Member roles must be text.',
            'members.*.bio.string' => 'Member bios must be text.',

            // Contact validation
            'contact_number.regex' => 'Please enter a valid UK phone number.',
            'contact_email.email' => 'Please enter a valid email address.',
            'contact_links.*.*.url' => 'Social media links must be valid URLs.',
            'portfolio_link.url' => 'Portfolio link must be a valid URL.',
        ];
    }
}
