<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUpdateBandTypes extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'band_types.allTypes' => 'required|boolean',
            'band_types.bandTypes' => 'required|array',
            'band_types.bandTypes.*' => 'string'
        ];
    }

    public function messages()
    {
        return [
            'band_types.bandTypes.required' => 'You must select at least one artist type',
            'band_types.allTypes.required' => 'Please specify if all types are selected',
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
