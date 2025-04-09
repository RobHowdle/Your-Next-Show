<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'on_the_door_ticket_price' => $this->on_the_door_ticket_price ?? 0,
        ]);

        // Better handling of pending opportunities
        try {
            $opportunities = $this->pending_opportunities;

            // If it's a JSON string, decode it
            if (is_string($opportunities)) {
                $decoded = json_decode($opportunities, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $opportunities = $decoded;
                }
            }

            // Ensure it's an array
            $opportunities = is_array($opportunities) ? $opportunities : [];

            // Log the opportunities data for debugging
            \Log::info('Processing opportunities in request', [
                'original' => $this->pending_opportunities,
                'processed' => $opportunities
            ]);

            $this->merge(['pending_opportunities' => $opportunities]);
        } catch (\Exception $e) {
            \Log::error('Error processing opportunities', [
                'error' => $e->getMessage(),
                'raw' => $this->pending_opportunities
            ]);
            $this->merge(['pending_opportunities' => []]);
        }

        // Remove any top-level opportunity fields that should only be in the array
        $fieldsToRemove = [
            'type',
            'position_type',
            'main_genres',
            'performance_start_time',
            'performance_end_time',
            'set_length',
            'poster_type',
            'additional_requirements'
        ];

        foreach ($fieldsToRemove as $field) {
            if ($this->has($field)) {
                $this->request->remove($field);
            }
        }
    }

    public function rules()
    {
        $rules = [
            'event_name' => 'required|string',
            'event_date' => 'required|date_format:d-m-Y',
            'event_start_time' => 'required|date_format:H:i',
            'event_end_time' => 'nullable|date_format:H:i',
            'promoter_ids' => 'nullable|array',
            'promoter_ids.*' => 'integer|exists:promoters,id',
            'event_description' => 'required|string',
            'facebook_event_url' => 'nullable|url',
            'ticket_url' => 'nullable|url',
            'on_the_door_ticket_price' => 'nullable|numeric',
            'venue_id' => 'required|integer|exists:venues,id',
            'headliner' => 'required|string',
            'headliner_id' => 'required|integer',
            'main_support' => 'nullable|string',
            'main_support_id' => 'nullable|integer',
            'bands' => 'nullable|string',
            'bands_ids' => 'nullable|array',
            'bands_ids.*' => 'nullable|integer',
            'opener' => 'nullable|string',
            'opener_id' => 'nullable|integer',
            'ticket_platform' => 'nullable',
            'genres' => 'nullable|array',
            // Opportunities validation
            'pending_opportunities' => ['nullable', 'array'],
            'pending_opportunities.*.type' => 'required|string|in:artist_wanted,venue_wanted,promoter_wanted,photographer_wanted,designer_wanted,videographer_wanted',
            'pending_opportunities.*.position_type' => 'nullable|string',
            'pending_opportunities.*.main_genres' => 'nullable|array',
            'pending_opportunities.*.main_genres.*' => 'string',
            'pending_opportunities.*.subgenres' => 'nullable|array',
            'pending_opportunities.*.subgenres.*.*' => 'string', // Changed from array to string
            'pending_opportunities.*.performance_start_time' => 'nullable|date_format:H:i',
            'pending_opportunities.*.performance_end_time' => 'nullable|date_format:H:i',
            'pending_opportunities.*.set_length' => 'nullable|string',
            'pending_opportunities.*.poster_type' => 'nullable|string|in:event,custom',
            'pending_opportunities.*.additional_requirements' => 'nullable|string',
        ];

        $rules['poster_url'] = [
            'nullable',
            'max:10240',
            function ($attribute, $value, $fail) {
                if ($this->file('poster_url')) {
                    if (!$value->isValid()) {
                        if ($value->getError() === UPLOAD_ERR_INI_SIZE) {
                            $maxSize = ini_get('upload_max_filesize');
                            $fail("The poster file size must not exceed {$maxSize}.");
                        } elseif (!in_array($value->extension(), ['jpeg', 'jpg', 'png', 'webp', 'svg'])) {
                            $fail('The poster must be a valid image file (jpeg, jpg, png, webp, svg).');
                        }
                    }
                } elseif (is_string($value)) {
                    if (!preg_match('/\.(jpeg|jpg|png|webp|svg)$/i', $value)) {
                        $fail('The poster URL must be a valid image URL.');
                    }
                }
            },
        ];

        // Add the required rule for poster_url only on create
        if ($this->isMethod('post')) {
            $rules['poster_url'][] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'poster_url.max' => 'The poster file size must not exceed 10MB.'
        ];
    }
}
