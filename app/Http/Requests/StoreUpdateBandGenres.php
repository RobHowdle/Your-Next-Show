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
            'genres' => 'array|nullable',
            'genres.*' => 'array|nullable',
            'genres.*.all' => 'boolean|nullable',
            'genres.*.subgenres' => 'array|nullable',
            'genres.*.subgenres.*' => 'string|nullable'
        ];
    }

    public function messages()
    {
        return [
            'genres.array' => 'Invalid genres format',
            'genres.*.array' => 'Invalid genre format',
            'genres.*.subgenres.array' => 'Invalid subgenres format',
            'genres.*.subgenres.*.string' => 'Invalid subgenre format'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        \Log::info('Genre Request Data:', [
            'request' => $this->all(),
            'genres' => $this->input('genres')
        ]);
    }
}