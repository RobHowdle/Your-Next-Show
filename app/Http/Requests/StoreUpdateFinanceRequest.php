<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateFinanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'desired_profit' => $this->desired_profit ?? 0,
            'income_presale' => $this->income_presale ?? 0,
            'income_otd' => $this->income_otd ?? 0,
            'income_other' => $this->income_other ?? [],
            'outgoing_venue' => $this->outgoing_venue ?? 0,
            'outgoing_band' => $this->outgoing_band ?? 0,
            'outgoing_promotion' => $this->outgoing_promotion ?? 0,
            'outgoing_rider' => $this->outgoing_rider ?? 0,
            'outgoing_other' => $this->outgoing_other ?? [],
            'income_total' => $this->income_total ?? 0,
            'outgoing_total' => $this->outgoing_total ?? 0,
            'profit_total' => $this->profit_total ?? 0,
            'desired_profit_remaining' => $this->desired_profit_remaining ?? 0,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'desired_profit' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'budget_name' => 'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'link_to_event' => 'nullable|url',
            'income_presale' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'income_otd' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'income_other' => 'nullable|array',
            'income_other.*' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'outgoing_venue' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'outgoing_band' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'outgoing_promotion' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'outgoing_rider' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'outgoing_other' => 'nullable|array',
            'outgoing_other.*' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'income_total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'outgoing_total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'profit_total' => 'required|numeric',
            'desired_profit_remaining' => 'nullable|numeric',
        ];
    }
}