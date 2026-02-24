<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreGradeRequest
 *
 * Validates request data for grade creation
 */
class StoreGradeRequest extends FormRequest
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
            'grade' => 'required|string|min:1|max:3',
            'minScore' => 'required|numeric',
            'maxScore' => 'required|numeric',
            'remark' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'grade.required' => 'Grade is required',
            'grade.min' => 'Grade must be at least 1 character',
            'grade.max' => 'Grade cannot exceed 3 characters',
            'minScore.required' => 'Minimum score is required',
            'minScore.numeric' => 'Minimum score must be a number',
            'maxScore.required' => 'Maximum score is required',
            'maxScore.numeric' => 'Maximum score must be a number',
            'remark.required' => 'Remark is required',
        ];
    }
}
