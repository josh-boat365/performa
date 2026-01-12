<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitEmployeeScoreRequest;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppraisalScoreController extends Controller
{
    private AppraisalApiService $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }

    public function store(SubmitEmployeeScoreRequest $request)
    {
        try {
            // Prepare score data based on which type is being submitted
            $scoreData = [];
            $successMessage = '';

            if ($request->filled('sectionEmpScore')) {
                $scoreData = [
                    'id' => $request->input('sectionEmpScoreId'),
                    'sectionEmpScore' => (float) $request->input('sectionEmpScore', 0),
                    'sectionId' => (int) $request->input('sectionId'),
                    'employeeComment' => $request->input('employeeComment', ''),
                    'kpiType' => $request->input('kpiType', ''),
                ];
                $successMessage = 'Section score and comment submitted successfully!';
            } elseif ($request->filled('metricEmpScore')) {
                $scoreData = [
                    'id' => $request->input('metricEmpScoreId'),
                    'metricEmpScore' => (float) $request->input('metricEmpScore', 0),
                    'metricId' => (int) $request->input('metricId'),
                    'sectionId' => (int) $request->input('sectionId'),
                    'employeeComment' => $request->input('employeeComment', ''),
                    'kpiType' => $request->input('kpiType', ''),
                ];
                $successMessage = 'Metric score and comment submitted successfully!';
            }

            // Submit the score via the service
            $this->appraisalService->submitEmployeeScore($scoreData);

            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        } catch (ApiException $e) {
            Log::error('Employee score submission failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to submit score. Please try again.',
                ],
                422
            );
        } catch (\Exception $e) {
            Log::error('Unexpected error during employee score submission', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 400);
        }
    }


    public function submitAppraisalForReview(Request $request)
    {
        try {
            $request->validate([
                'kpiId' => 'required|integer',
                'batchId' => 'required|integer',
                'status' => 'required|string',
            ]);

            $data = [
                'kpiId' => (int) $request->input('kpiId'),
                'batchId' => (int) $request->input('batchId'),
                'status' => $request->input('status'),
            ];

            // Submit the status update via the service
            $this->appraisalService->updateScoreStatus($data);

            return back()->with('toast_success', 'KPI submitted for review successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to submit appraisal for review', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'Failed to submit KPI for review. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during appraisal review submission', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function acceptAppraisalReview(Request $request)
    {
        try {
            $request->validate([
                'kpiId' => 'required|integer',
                'batchId' => 'required|integer',
                'status' => 'required|string',
            ]);

            $data = [
                'kpiId' => (int) $request->input('kpiId'),
                'batchId' => (int) $request->input('batchId'),
                'status' => $request->input('status'),
            ];

            // Submit the status update via the service
            $this->appraisalService->updateScoreStatus($data);

            return back()->with('toast_success', 'Appraisal review accepted successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to accept appraisal review', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'Failed to accept appraisal review. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during appraisal review acceptance', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function submitProbing(Request $request)
    {
        try {
            $request->validate([
                'scoreId' => 'required|integer',
                'employeeComment' => 'required|string|min:1',
                'sectionId' => 'nullable|integer',
                'metricId' => 'nullable|integer',
                'kpiType' => 'nullable|string',
            ]);

            $scoreId = $request->input('scoreId');
            $prob = $request->has('prob');
            $employeeComment = $request->input('employeeComment');

            // Prepare payload for employee comment update
            $commentPayload = [
                'id' => (int) $scoreId,
                'sectionId' => (int) $request->input('sectionId', 0),
                'employeeComment' => $employeeComment,
                'kpiType' => $request->input('kpiType', ''),
            ];

            // Update employee comment via the service
            $this->appraisalService->submitEmployeeScore($commentPayload);

            // Update score to probing state via the service
            $probingPayload = [
                'scoreId' => (int) $scoreId,
                'prob' => $prob,
            ];

            $this->appraisalService->updateEmployeeScoreToProbing($probingPayload);

            return back()->with('toast_success', 'Supervisor score and comment submitted successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to submit probing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'Failed to submit probing. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during probing submission', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }
}
