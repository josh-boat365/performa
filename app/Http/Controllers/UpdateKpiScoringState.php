<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateKpiScoringState extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employeeId' => 'required|integer',
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'supervisorId' => 'nullable|integer',
            'supervisorRecommendation' => 'nullable|string',
            'status' => 'required|string|in:REVIEW,COMPLETED,CONFIRMATION,PROBLEM',
        ]);

        $batchId = $validated['batchId'];

        try {
            // Validate session
            $sessionValidation = ValidateSessionController::validateSession();
            if ($sessionValidation) {
                return $sessionValidation;
            }

            $apiToken = session('api_token');
            $baseApiUrl = 'http://192.168.1.200:5123/Appraisal';

            // Submit supervisor recommendation if comment is provided (regardless of status)
            if (!empty($validated['supervisorRecommendation']) && !empty($validated['supervisorId'])) {
                $recommendationData = [
                    'employeeId' => (int) $validated['employeeId'],
                    'kpiId' => (int) $validated['kpiId'],
                    'batchId' => (int) $validated['batchId'],
                    'supervisorId' => (int) $validated['supervisorId'],
                    'supervisorComment' => $validated['supervisorRecommendation'],
                ];

                $recommendationResponse = Http::withToken($apiToken)
                    ->post("{$baseApiUrl}/Recommendation", $recommendationData);

                if (!$recommendationResponse->successful()) {
                    throw new \Exception("API request (recommendation) failed with status: {$recommendationResponse->status()}");
                }
            }

            // Always submit status update (for all statuses)
            $statusData = [
                'employeeId' => (int) $validated['employeeId'],
                'kpiId' => (int) $validated['kpiId'],
                'batchId' => (int) $validated['batchId'],
                'status' => $validated['status'],
            ];

    

            $statusResponse = Http::withToken($apiToken)
                ->put("{$baseApiUrl}/Score/update-score-status", $statusData);

            if (!$statusResponse->successful()) {
                throw new \Exception("API request (status update) failed with status: {$statusResponse->status()}");
            }

            // Success messages for each status
            $messages = [
                'REVIEW' => 'Appraisal submitted for review successfully.',
                'COMPLETED' => 'Appraisal marked as completed successfully.',
                'CONFIRMATION' => 'Appraisal pushed to employee for confirmation successfully.',
                'PROBLEM' => 'Appraisal pushed to higher supervisor for review successfully.',
            ];

            $successMessage = $messages[$validated['status']] ?? 'Appraisal status updated successfully.';

            // Redirect based on status
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
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API Connection Error : UpdateKpiScoringState', [
                'message' => $e->getMessage(),
                'request_data' => $statusData ?? $recommendationData ?? [],
            ]);
            return back()->with('toast_error', 'Network connection failed. Please check your connection and try again.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('API Request Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request_data' => $statusData ?? $recommendationData ?? [],
            ]);
            return back()->with('toast_error', 'Request failed. Please try again later.');
        } catch (\Exception $e) {
            Log::error('Unexpected Error in Appraisal Update', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $statusData ?? $recommendationData ?? [],
            ]);
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

}
