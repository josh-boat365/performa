<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreMetricRequest
 *
 * Validates request data for metric creation
 */
class StoreMetricRequest extends FormRequest
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
            'sectionId' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'score' => 'required|integer|min:1',
            'active' => 'required|in:0,1',
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
            'description.max' => 'Description cannot exceed 1000 characters',
            'score.required' => 'Metric score is required',
            'score.integer' => 'Metric score must be an integer',
            'score.min' => 'Metric score must be at least 1',
            'active.required' => 'Active status is required',
            'active.in' => 'Active status must be 0 or 1',
            'scale.numeric' => 'Scale must be a number',
            'scale.min' => 'Scale must be at least 1',
            'scale.max' => 'Scale cannot exceed 100',
        ];
    }
}
