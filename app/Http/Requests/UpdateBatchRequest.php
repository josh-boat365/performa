<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateBatchRequest
 *
 * Validates request data for updating an appraisal batch
 */
class UpdateBatchRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:2100',
            'active' => 'required|in:0,1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Batch name is required',
            'name.max' => 'Batch name cannot exceed 255 characters',
            'year.required' => 'Year is required',
            'year.integer' => 'Year must be an integer',
            'year.min' => 'Year must be at least 1900',
            'year.max' => 'Year cannot exceed 2100',
            'active.required' => 'Active status is required',
            'active.in' => 'Active status must be 0 or 1',
        ];
    }
}
