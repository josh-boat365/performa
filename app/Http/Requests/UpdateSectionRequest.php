<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateSectionRequest
 *
 * Validates request data for section updates
 */
class UpdateSectionRequest extends FormRequest
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
            'batchId' => 'sometimes|required|integer|min:1',
            'name' => 'sometimes|required|string|max:255',
            'section_order' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'batchId.required' => 'Batch is required',
            'batchId.integer' => 'Batch ID must be an integer',
            'batchId.min' => 'Batch ID must be at least 1',
            'name.required' => 'Section name is required',
            'name.max' => 'Section name cannot exceed 255 characters',
            'section_order.required' => 'Section order is required',
            'section_order.integer' => 'Section order must be an integer',
            'section_order.min' => 'Section order must be at least 1',
            'description.max' => 'Description cannot exceed 1000 characters',
        ];
    }
}
