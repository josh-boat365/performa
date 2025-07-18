<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateKpiScoringState extends Controller
{
    public function store(Request $request)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        // Enhanced validation with specific status values
        $request->validate([
            'employeeId' => 'required|integer',
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'status' => 'required|string|in:REVIEW,COMPLETED,CONFIRMATION,PROBLEM',
        ]);

        // Prepare the data
        $data = $request->only(['employeeId', 'kpiId', 'batchId', 'status']);

        try {
            // Retrieve and validate access token
            $accessToken = session('api_token');
            if (!$accessToken) {
                Log::warning('API token missing from session');
                return back()->with('toast_error', 'Session expired. Please login again.');
            }

            // Get API endpoint from config
            $apiEndpoint ='https://192.168.1.200:5123/Appraisal/Score/update-score-status';

            // Submit the data to the external API with timeout
            $response = Http::timeout(30)
                ->withToken($accessToken)
                ->put($apiEndpoint, $data);

            // Check if the response is successful
            if ($response->successful()) {
                $successMessage = $this->getSuccessMessage($request->input('status'));

                if( $request->input('status') === 'CONFIRMATION') {
                    // Additional logic for CONFIRMATION status if needed
                    return redirect()->route('supervisor.index')->with('toast_success', $successMessage);
                }

                return redirect()->back()->with('toast_success', $successMessage);
            } else {
                // Enhanced error logging
                Log::error('API Submit Response Error', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'request_data' => $data,
                    'user_id' => session('user_id'),
                ]);

                // Specific error messages based on status code
                $errorMessage = match ($response->status()) {
                    400 => 'Invalid data provided. Please check your input and try again.',
                    401 => 'Authentication failed. Please login again.',
                    403 => 'You do not have permission to perform this action.',
                    404 => 'Appraisal record not found.',
                    422 => 'The provided data is invalid. Please review and try again.',
                    500, 502, 503, 504 => 'Server error occurred. Please try again later.',
                    default => 'Failed to update appraisal status. Please try again.',
                };

                return back()->with('toast_error', $errorMessage);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Handle connection errors
            Log::error('API Connection Error', [
                'message' => $e->getMessage(),
                'request_data' => $data,
                'user_id' => session('user_id'),
            ]);
            return back()->with('toast_error', 'Network connection failed. Please check your connection and try again.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Handle request errors (timeout, etc.)
            Log::error('API Request Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request_data' => $data,
                'user_id' => session('user_id'),
            ]);
            return back()->with('toast_error', 'Request failed. Please try again later.');
        } catch (\Exception $e) {
            // Handle any other exceptions
            Log::error('Unexpected Error in Appraisal Update', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $data,
                'user_id' => session('user_id'),
            ]);
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Get success message based on status
     */
    private function getSuccessMessage($status)
    {
        $messages =  [
            'REVIEW' => 'Appraisal submitted for review successfully.',
            'COMPLETED' => 'Appraisal marked as completed successfully.',
            'CONFIRMATION' => 'Appraisal pushed to employee for confirmation successfully.',
            'PROBLEM' => 'Appraisal pushed to higher supervisor for review successfully.',
        ];

        return $messages[$status] ?? 'Appraisal status updated successfully.';
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'kpiId' => 'required|integer',
    //         'batchId' => 'required|integer',
    //         'status' => 'required|string|in:REVIEW,COMPLETED,CONFIRMATION,PROBLEM',
    //     ]);

    //     try {
    //         $response = Http::withToken(session('api_token'))
    //             ->put('http://192.168.1.200:5123/Appraisal/Score/update-score-status', [
    //                 'kpiId' => (int) $validated['kpiId'],
    //                 'batchId' => (int) $validated['batchId'],
    //                 'status' => $validated['status'],
    //             ]);

    //         if (!$response->successful()) {
    //             throw new \Exception("API request failed with status: {$response->status()}");
    //         }

    //         $messages = [
    //             'REVIEW' => 'Appraisal submitted for review successfully.',
    //             'COMPLETED' => 'Appraisal marked as completed successfully.',
    //             'CONFIRMATION' => 'Appraisal confirmed successfully.',
    //             'PROBLEM' => 'Appraisal status updated to problem successfully.',
    //         ];

    //         return redirect()->route('supervisor.index')
    //             ->with('toast_success', $messages[$validated['status']] ?? 'Appraisal status updated successfully.');
    //     } catch (\Exception $e) {
    //         Log::error('Appraisal status update failed', [
    //             'error' => $e->getMessage(),
    //             'data' => $validated,
    //         ]);

    //         return back()->with('toast_error', 'Failed to update appraisal status. Please try again.');
    //     }
    // }
}
