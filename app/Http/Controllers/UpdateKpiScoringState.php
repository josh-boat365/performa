<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateKpiScoringState extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'status' => 'required|string',
        ]);

        // Prepare the data to be sent to the API
        $data = [
            'kpiId' => (int) $request->input('kpiId'),
            'batchId' => (int) $request->input('batchId'),
            'status' => $request->input('status'),
        ];

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
                        $successMessage = 'KPI submitted for review successfully.';
                        break;
                    case 'COMPLETED':
                        $successMessage = 'KPI marked as completed successfully.';
                        break;
                    case 'CONFIRMATION':
                        $successMessage = 'KPI confirmed successfully.';
                        break;
                    case 'PROBLEM':
                        $successMessage = 'KPI status updated to problem successfully.';
                        break;
                    default:
                        $successMessage = 'KPI status updated successfully.';
                        break;
                }

                // Display success message using SweetAlert
                return back()->with('toast_success', $successMessage);
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
}
