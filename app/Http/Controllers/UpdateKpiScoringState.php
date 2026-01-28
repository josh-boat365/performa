<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;

class UpdateKpiScoringState extends Controller
{
    private AppraisalApiService $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employeeId' => 'required|integer',
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'supervisorId' => 'nullable|integer',
            'supervisorRecommendation' => 'nullable|string',
            'status' => 'required|string|in:REVIEW,COMPLETED,CONFIRMATION,PROBLEM,SCORING',
        ]);

        $batchId = $validated['batchId'];

        try {
            // Submit supervisor recommendation if comment is provided
            if (!empty($validated['supervisorRecommendation']) && !empty($validated['supervisorId'])) {
                $recommendationData = [
                    'employeeId' => (int) $validated['employeeId'],
                    'kpiId' => (int) $validated['kpiId'],
                    'batchId' => (int) $validated['batchId'],
                    'supervisorId' => (int) $validated['supervisorId'],
                    'supervisorComment' => $validated['supervisorRecommendation'],
                ];

                $this->appraisalService->submitRecommendation($recommendationData);
            }

            // Always submit status update
            $statusData = [
                'employeeId' => (int) $validated['employeeId'],
                'kpiId' => (int) $validated['kpiId'],
                'batchId' => (int) $validated['batchId'],
                'status' => $validated['status'],
            ];

            Log::info('Submitting appraisal status update', [
                'data' => $statusData,
                'target_status' => $validated['status']
            ]);

            $response = $this->appraisalService->updateScoreStatus($statusData);

            Log::info('Appraisal status update successful', [
                'data' => $statusData,
                'response' => $response
            ]);

            // Success messages for each status
            $messages = [
                'REVIEW' => 'Appraisal submitted for review successfully.',
                'COMPLETED' => 'Appraisal marked as completed successfully.',
                'CONFIRMATION' => 'Appraisal pushed to employee for confirmation successfully.',
                'PROBLEM' => 'Appraisal pushed to higher supervisor for review successfully.',
                'SCORING' => 'Appraisal pushed back to employee for scoring successfully.',
            ];

            $successMessage = $messages[$validated['status']] ?? 'Appraisal status updated successfully.';

            // Redirect based on status
            switch ($validated['status']) {
                case 'SCORING':
                    return redirect()
                        ->route('supervisor.index')
                        ->with('toast_success', $successMessage);
                        
                case 'CONFIRMATION':
                    return redirect()
                        ->route('supervisor.index')
                        ->with('toast_success', $successMessage);

                case 'REVIEW':
                case 'COMPLETED':
                    return redirect()
                        ->route('show.batch.kpi', ['batchId' => $batchId])
                        ->with('toast_success', $successMessage);

                case 'PROBLEM':
                    return redirect()
                        ->route('supervisor.index')
                        ->with('toast_success', $successMessage);

                default:
                    return redirect()
                        ->back()
                        ->with('toast_success', $successMessage);
            }
        } catch (ApiException $e) {
            // Check if this is an email service error - the submission went through but email failed
            $errorMessage = $e->getMessage();
            $isEmailServiceError = str_contains($errorMessage, 'IEmailService')
                || str_contains($errorMessage, 'EmailService')
                || str_contains($errorMessage, 'email service');

            if ($isEmailServiceError) {
                // Log the email error but treat as success since the appraisal was submitted
                Log::warning('Appraisal status updated but email notification failed', [
                    'message' => $errorMessage,
                    'data' => $validated ?? [],
                ]);

                // Success messages for each status
                $messages = [
                    'REVIEW' => 'Appraisal submitted for review successfully.',
                    'COMPLETED' => 'Appraisal marked as completed successfully.',
                    'CONFIRMATION' => 'Appraisal pushed to employee for confirmation successfully.',
                    'PROBLEM' => 'Appraisal pushed to higher supervisor for review successfully.',
                ];

                $successMessage = $messages[$validated['status']] ?? 'Appraisal status updated successfully.';

                // Redirect based on status (same as success flow)
                switch ($validated['status']) {
                    case 'CONFIRMATION':
                        return redirect()
                            ->route('supervisor.index')
                            ->with('toast_success', $successMessage);

                    case 'REVIEW':
                    case 'COMPLETED':
                        return redirect()
                            ->route('show.batch.kpi', ['batchId' => $batchId])
                            ->with('toast_success', $successMessage);

                    case 'PROBLEM':
                        return redirect()
                            ->route('supervisor.index')
                            ->with('toast_success', $successMessage);

                    default:
                        return redirect()
                            ->back()
                            ->with('toast_success', $successMessage);
                }
            }

            Log::error('Failed to update KPI scoring state', [
                'message' => $e->getMessage(),
                'status_code' => $e->getCode(),
                'data' => $validated ?? [],
            ]);
            return back()->with('toast_error', 'Failed to update appraisal status. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error updating KPI scoring state', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $validated ?? [],
            ]);
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }
}
