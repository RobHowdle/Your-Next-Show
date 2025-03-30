<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobsUpdateRequest extends FormRequest
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
            'client_id' => 'required',
            'client_name' => 'required|string',
            'client_service' => 'required|string',
            'package' => 'nullable|string',
            'job_priority' => 'required|string',
            'job_status' => 'required|string',
            'job_start_date' => 'required',
            'job_end_date' => 'required',
            'estimated_lead_time_value' => 'required|numeric',
            'estimated_lead_time_unit' => 'required',
            'scope' => 'nullable|string',
            'job_cost' => 'required|numeric',
            'job_scope_file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg,svg,zip|max:10240',
        ];
    }
}