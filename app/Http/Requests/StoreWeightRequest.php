<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreWeightRequest
 *
 * Validates request data for weight creation
 */
class StoreWeightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return session('api_token') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'kpiId' => 'required|integer',
            'departmentId' => 'required|integer',
            'weight' => 'required|numeric',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'kpiId.required' => 'KPI is required',
            'departmentId.required' => 'Department is required',
            'weight.required' => 'Weight is required',
            'weight.numeric' => 'Weight must be a number',
        ];
    }
}
