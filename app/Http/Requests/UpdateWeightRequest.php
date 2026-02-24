<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateWeightRequest
 *
 * Validates request data for weight updates
 */
class UpdateWeightRequest extends FormRequest
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
            'kpiId' => 'sometimes|required|integer',
            'departmentId' => 'sometimes|required|integer',
            'weight' => 'sometimes|required|numeric',
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
