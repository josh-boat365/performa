<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SubmitEmployeeScoreRequest
 *
 * Validates request data for employee score submission
 */
class SubmitEmployeeScoreRequest extends FormRequest
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
            'sectionEmpScoreId' => 'nullable|integer|min:0',
            'sectionEmpScore' => 'nullable|numeric|min:0|max:100',
            'sectionId' => 'required_if:sectionEmpScore,*|integer|min:1',
            'metricEmpScoreId' => 'nullable|integer|min:0',
            'metricEmpScore' => 'nullable|numeric|min:0|max:100',
            'metricId' => 'required_if:metricEmpScore,*|integer|min:1',
            'employeeComment' => 'nullable|string|max:1000',
            'kpiType' => 'nullable|string|in:REGULAR,PROBATION,GLOBAL',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'sectionId.required_if' => 'Section is required when submitting section score',
            'sectionEmpScore.numeric' => 'Section score must be a number',
            'sectionEmpScore.min' => 'Section score cannot be less than 0',
            'sectionEmpScore.max' => 'Section score cannot exceed 100',
            'metricId.required_if' => 'Metric is required when submitting metric score',
            'metricEmpScore.numeric' => 'Metric score must be a number',
            'metricEmpScore.min' => 'Metric score cannot be less than 0',
            'metricEmpScore.max' => 'Metric score cannot exceed 100',
            'employeeComment.max' => 'Comment cannot exceed 1000 characters',
            'kpiType.in' => 'KPI type must be: REGULAR, PROBATION, or GLOBAL',
        ];
    }
}
