<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreSectionRequest
 *
 * Validates request data for section creation
 */
class StoreSectionRequest extends FormRequest
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
            'description' => 'nullable|string|max:1000',
            'score' => 'required|integer|min:1',
            'kpiId' => 'required|integer|min:1',
            'active' => 'required|in:0,1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Section name is required',
            'name.max' => 'Section name cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
            'score.required' => 'Section score is required',
            'score.integer' => 'Section score must be an integer',
            'kpiId.required' => 'KPI is required',
            'active.required' => 'Section status is required',
        ];
    }
}
