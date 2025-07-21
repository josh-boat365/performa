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
            'status' => 'required|string|in:REVIEW,COMPLETED,CONFIRMATION,PROBLEM',
        ]);

        try {

            // Validate session
            $sessionValidation = ValidateSessionController::validateSession();
            if ($sessionValidation) {
                return $sessionValidation;
            }

            $data = [
                'employeeId' => (int) $validated['employeeId'],
                'kpiId' => (int) $validated['kpiId'],
                'batchId' => (int) $validated['batchId'],
                'status' => $validated['status'],
            ];

            $response = Http::withToken(session('api_token'))
                ->put('http://192.168.1.200:5123/Appraisal/Score/update-score-status', $data);

            if (!$response->successful()) {
                throw new \Exception("API request (UpdateKpiScoringState Controller: update-score-status API)  failed with status: {$response->status()}");
            }

            $messages =  [
                'REVIEW' => 'Appraisal submitted for review successfully.',
                'COMPLETED' => 'Appraisal marked as completed successfully.',
                'CONFIRMATION' => 'Appraisal pushed to employee for confirmation successfully.',
                'PROBLEM' => 'Appraisal pushed to higher supervisor for review successfully.',
            ];

            if ($validated['status'] === 'CONFIRMATION') {
                // Additional logic for CONFIRMATION status if needed
                return redirect()->route('supervisor.index')
                    ->with('toast_success', $messages[$validated['status']] ?? 'Appraisal status updated successfully.');
            }

            return redirect()->back()->with('toast_success', $messages[$validated['status']] ?? 'Appraisal status updated successfully.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Handle connection errors
            Log::error('API Connection Error : UpdateKpiScoringState', [
                'message' => $e->getMessage(),
                'request_data' => $data,
            ]);
            return back()->with('toast_error', 'Network connection failed. Please check your connection and try again.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Handle request errors (timeout, etc.)
            Log::error('API Request Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request_data' => $data,

            ]);
            return back()->with('toast_error', 'Request failed. Please try again later.');
        } catch (\Exception $e) {
            // Handle any other exceptions
            Log::error('Unexpected Error in Appraisal Update', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $data,

            ]);
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }
}
