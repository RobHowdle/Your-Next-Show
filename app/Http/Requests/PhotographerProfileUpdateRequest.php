<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotographerProfileUpdateRequest extends FormRequest
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
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'about' => 'nullable|string',
            'genres' => 'nullable|array',
            'band_types' => 'nullable|array',
            'contact_name' => 'nullable|string',
            'contact_number' => ['nullable', 'regex:/^(?:0|\+44)(?:\d\s?){9,10}$/'],
            'contact_email' => 'nullable|email',
            'contact_links.*.*' => 'nullable|url',
            'portfolio_link' => 'nullable|url',
            'portfolio_image_path' => 'nullable',
            'working_times' => 'array',
            'working_times.*' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    // Allow string values for 'all-day' or 'unavailable'
                    if (is_string($value) && in_array($value, ['all-day', 'unavailable'], true)) {
                        return;
                    }

                    // Ensure value is an array with 'start' and 'end' if not a string
                    if (is_array($value)) {
                        if (!array_key_exists('start', $value) || !array_key_exists('end', $value)) {
                            $fail("$attribute must contain 'start' and 'end' if it is not 'all-day' or 'unavailable'.");
                        }
                    } else {
                        $fail("$attribute must be a valid status or a time range.");
                    }
                },
            ],
            'working_times.*.start' => 'nullable|required_with:working_times.*.end|date_format:H:i',
            'working_times.*.end' => 'nullable|required_with:working_times.*.start|date_format:H:i|after:working_times.*.start',
            // 'working_times' => 'array',
            // 'working_times.*' => 'nullable|string|in:all-day,unavailable',
            // 'working_times.Monday.start' => 'nullable|required_if:working_times.Monday,!=,all-day|required_if:working_times.Monday,!=,unavailable|date_format:H:i',
            // 'working_times.Monday.end' => 'nullable|required_if:working_times.Monday,!=,all-day|required_if:working_times.Monday,!=,unavailable|date_format:H:i',
            // 'working_times.Tuesday.start' => 'nullable|required_if:working_times.Tuesday,!=,all-day|required_if:working_times.Tuesday,!=,unavailable|date_format:H:i',
            // 'working_times.Tuesday.end' => 'nullable|required_if:working_times.Tuesday,!=,all-day|required_if:working_times.Tuesday,!=,unavailable|date_format:H:i',
            // 'working_times.Wednesday.start' => 'nullable|required_if:working_times.Wednesday,!=,all-day|required_if:working_times.Wednesday,!=,unavailable|date_format:H:i',
            // 'working_times.Wednesday.end' => 'nullable|required_if:working_times.Wednesday,!=,all-day|required_if:working_times.Wednesday,!=,unavailable|date_format:H:i',
            // 'working_times.Thursday.start' => 'nullable|required_if:working_times.Thursday,!=,all-day|required_if:working_times.Thursday,!=,unavailable|date_format:H:i',
            // 'working_times.Thursday.end' => 'nullable|required_if:working_times.Thursday,!=,all-day|required_if:working_times.Thursday,!=,unavailable|date_format:H:i',
            // 'working_times.Friday.start' => 'nullable|required_if:working_times.Friday,!=,all-day|required_if:working_times.Friday,!=,unavailable|date_format:H:i',
            // 'working_times.Friday.end' => 'nullable|required_if:working_times.Friday,!=,all-day|required_if:working_times.Friday,!=,unavailable|date_format:H:i',
            // 'working_times.Saturday.start' => 'nullable|required_if:working_times.Saturday,!=,all-day|required_if:working_times.Saturday,!=,unavailable|date_format:H:i',
            // 'working_times.Saturday.end' => 'nullable|required_if:working_times.Saturday,!=,all-day|required_if:working_times.Saturday,!=,unavailable|date_format:H:i',
            // 'working_times.Sunday.start' => 'nullable|required_if:working_times.Sunday,!=,all-day|required_if:working_times.Sunday,!=,unavailable|date_format:H:i',
            // 'working_times.Sunday.end' => 'nullable|required_if:working_times.Sunday,!=,all-day|required_if:working_times.Sunday,!=,unavailable|date_format:H:i',
        ];
    }
}
