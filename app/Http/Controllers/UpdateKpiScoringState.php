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

        // Validate the incoming request data
        $request->validate([
            'employeeId' => 'required|integer',
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'status' => 'required|string',
        ]);

        // Prepare the data to be sent to the API
        $data = [
            'employeeId' => (int) $request->input('employeeId'),
            'kpiId' => (int) $request->input('kpiId'),
            'batchId' => (int) $request->input('batchId'),
            'status' => $request->input('status'),
        ];
        // dd($data);
        try {
            // Retrieve the access token
            $accessToken = session('api_token');

            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/Score/update-score-status', $data);

            // Check if the response is successful
            if ($response->successful()) {
                // Tailor the success message based on the status
                $status = $request->input('status');
                $successMessage = '';

                switch ($status) {
                    case 'REVIEW':
                        $successMessage = 'Appraisal submitted for review successfully.';
                        break;
                    case 'COMPLETED':
                        $successMessage = 'Appraisal marked as completed successfully.';
                        break;
                    case 'CONFIRMATION':
                        $successMessage = 'Appraisal confirmed successfully.';
                        break;
                    case 'PROBLEM':
                        $successMessage = 'Appraisal status updated to problem successfully.';

                        break;
                    default:
                        $successMessage = 'Appraisal status updated successfully.';
                        break;
                }

                // Display success message using SweetAlert
                return redirect()->route('supervisor.index')->with('toast_success', $successMessage);
            } else {
                // Log the error if the response is not successful
                Log::error('API Submit, Review Response Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Handle the case where the API response is not successful
                return back()->with('toast_error', 'Failed to submit KPI for review. Please try again.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the API request
            // Log exception and notify the user
            Log::error('API Exception, Submit, Review Response Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('toast_error', 'An error occurred while submitting the KPI. Please try again.');
        }
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
