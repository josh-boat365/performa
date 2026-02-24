<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreBatchRequest
 *
 * Validates request data for creating a new appraisal batch
 */
class StoreBatchRequest extends FormRequest
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
        ];
    }
}
