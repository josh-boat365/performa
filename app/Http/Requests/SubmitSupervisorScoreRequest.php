<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SubmitSupervisorScoreRequest
 *
 * Validates request data for supervisor score submission
 */
class SubmitSupervisorScoreRequest extends FormRequest
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
            'scoreId' => 'required|integer|min:1',
            'sectionSupScore' => 'nullable|numeric|min:0',
            'metricSupScore' => 'nullable|numeric|min:0',
            'supervisorComment' => 'nullable|string|max:1000',
            // Legacy field names for backward compatibility
            'sectionId' => 'nullable|integer|min:1',
            'sectionSupervisorScore' => 'nullable|numeric|min:0|max:100',
            'sectionSupervisorScoreId' => 'nullable|integer|min:0',
            'metricId' => 'nullable|integer|min:1',
            'metricSupervisorScore' => 'nullable|numeric|min:0|max:100',
            'metricSupervisorScoreId' => 'nullable|integer|min:0',
            'kpiType' => 'nullable|string|in:REGULAR,PROBATION,GLOBAL',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'scoreId.required' => 'Employee score not found. The employee must submit their self-evaluation first.',
            'scoreId.integer' => 'Invalid score reference',
            'scoreId.min' => 'Employee score not found. The employee must submit their self-evaluation first.',
            'sectionSupScore.numeric' => 'Section score must be a number',
            'sectionSupScore.min' => 'Section score cannot be less than 0',
            'metricSupScore.numeric' => 'Metric score must be a number',
            'metricSupScore.min' => 'Metric score cannot be less than 0',
            'sectionId.required_if' => 'Section is required when submitting section score',
            'sectionSupervisorScore.numeric' => 'Section score must be a number',
            'sectionSupervisorScore.min' => 'Section score cannot be less than 0',
            'sectionSupervisorScore.max' => 'Section score cannot exceed 100',
            'metricId.required_if' => 'Metric is required when submitting metric score',
            'metricSupervisorScore.numeric' => 'Metric score must be a number',
            'metricSupervisorScore.min' => 'Metric score cannot be less than 0',
            'metricSupervisorScore.max' => 'Metric score cannot exceed 100',
            'supervisorComment.max' => 'Comment cannot exceed 1000 characters',
            'kpiType.in' => 'KPI type must be: REGULAR, PROBATION, or GLOBAL',
        ];
    }
}
