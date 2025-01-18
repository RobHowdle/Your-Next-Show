<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateBandGenres extends FormRequest
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
            'genres' => 'required|array',
            'genres.*' => 'array',
            'genres.*.all' => 'required|boolean',
            'genres.*.subgenres' => 'required|array',
            'genres.*.subgenres.*' => 'string'
        ];
    }

    public function messages()
    {
        return [
            'genres.required' => 'Please select at least one genre',
            'genres.*.all.required' => 'Please specify if all subgenres are selected',
            'genres.*.subgenres.required' => 'Please select at least one subgenre',
            'genres.*.subgenres.*.string' => 'Invalid subgenre format'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
