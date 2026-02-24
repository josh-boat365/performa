<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateGradeRequest
 *
 * Validates request data for grade updates
 */
class UpdateGradeRequest extends FormRequest
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
            'grade' => 'string|min:1|max:3',
            'minScore' => 'numeric',
            'maxScore' => 'numeric',
            'remark' => 'string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'grade.min' => 'Grade must be at least 1 character',
            'grade.max' => 'Grade cannot exceed 3 characters',
            'minScore.numeric' => 'Minimum score must be a number',
            'maxScore.numeric' => 'Maximum score must be a number',
        ];
    }
}
