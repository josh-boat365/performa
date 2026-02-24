<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateMetricRequest
 *
 * Validates request data for metric updates
 */
class UpdateMetricRequest extends FormRequest
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
            'sectionId' => 'sometimes|required|integer|min:1',
            'name' => 'sometimes|required|string|max:255',
            'metric_order' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'scale' => 'nullable|numeric|min:1|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'sectionId.required' => 'Section is required',
            'sectionId.integer' => 'Section ID must be an integer',
            'sectionId.min' => 'Section ID must be at least 1',
            'name.required' => 'Metric name is required',
            'name.max' => 'Metric name cannot exceed 255 characters',
            'metric_order.required' => 'Metric order is required',
            'metric_order.integer' => 'Metric order must be an integer',
            'metric_order.min' => 'Metric order must be at least 1',
            'description.max' => 'Description cannot exceed 1000 characters',
            'scale.numeric' => 'Scale must be a number',
            'scale.min' => 'Scale must be at least 1',
            'scale.max' => 'Scale cannot exceed 100',
        ];
    }
}
