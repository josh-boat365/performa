<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreKpiRequest
 *
 * Validates request data for creating a new KPI
 */
class StoreKpiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated and have a session token
        return session('api_token') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            'empRoleId' => 'required|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'KPI name is required',
            'type.required' => 'KPI type is required',
            'active.required' => 'Active status is required',
            'batchId.required' => 'Batch is required',
            'empRoleId.required' => 'Employee role is required',
        ];
    }
}
