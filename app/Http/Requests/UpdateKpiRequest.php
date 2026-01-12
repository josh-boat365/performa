<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateKpiRequest
 *
 * Validates request data for updating a KPI
 */
class UpdateKpiRequest extends FormRequest
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
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|string',
            'active' => 'sometimes|required|integer',
            'batchId' => 'sometimes|required|integer',
            'empRoleId' => 'sometimes|required|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [];
    }
}
