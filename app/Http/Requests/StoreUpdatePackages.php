<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdatePackages extends FormRequest
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
            'packages' => 'required|array',
            'packages.*.title' => 'required|string|max:255',
            'packages.*.description' => 'required|string',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.job_type' => 'string',
            'packages.*.items' => 'array',
            'packages.*.items.*' => 'string|max:255',
            'packages.*.lead_time' => 'numeric',
            'packages.*.lead_time_unit' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'packages.required' => 'At least one package is required.',
            'packages.*.title.required' => 'Package title is required.',
            'packages.*.description.required' => 'Package description is required.',
            'packages.*.price.required' => 'Package price is required.',
            'packages.*.price.numeric' => 'Package price must be a number.',
            'packages.*.price.min' => 'Package price cannot be negative.',
            'packages.*.items.array' => 'Package items must be a list.',
            'packages.*.items.*.string' => 'Package items must be text.'
        ];
    }
}