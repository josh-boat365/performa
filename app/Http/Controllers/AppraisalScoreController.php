<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AppraisalScoreController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Initialize a variable for the success message
            $successMessage = '';

            // Initialize variables to track progress
            $totalSections = 0; // Total sections for the KPI
            $completedSections = 0; // Completed sections based on submitted scores


            // Validate the request based on the input type
            if ($request->input('sectionEmpScore')) {
                $request->validate([
                    'sectionEmpScoreId' => 'nullable|numeric|min:0',
                    'sectionEmpScore' => 'nullable|numeric',
                    'sectionId' => 'required|integer',
                    'employeeComment' => 'nullable|string',
                ]);

                // Prepare the payload for the API request
                $payload = [
                    'id' => $request->input('sectionEmpScoreId') ?? null,
                    'sectionEmpScore' => (float) $request->input('sectionEmpScore', 0),
                    'sectionId' => (int) $request->input('sectionId', 0),
                    'employeeComment' => $request->input('employeeComment', ''),
                ];

                $successMessage = 'Section score, section comment submitted successfully!';
                $totalSections = 1; // Assuming one section is being processed
                $completedSections = (float) $request->input('sectionEmpScore', 0) > 0 ? 1 : 0; // Mark as completed if score is greater than 0
            }

            if ($request->input('metricEmpScore')) {
                $request->validate([
                    'metricEmpScoreId' => 'nullable|numeric|min:0',
                    'metricEmpScore' => 'nullable|numeric',
                    'metricId' => 'required|integer',
                    'sectionId' => 'required|integer',
                    'employeeComment' => 'nullable|string',
                ]);

                // Prepare the payload for the API request
                $payload = [
                    'id' => $request->input('metricEmpScoreId') ?? null,
                    'metricEmpScore' => (float) $request->input('metricEmpScore', 0),
                    'metricId' => (int) $request->input('metricId', 0),
                    'sectionId' => (int) $request->input('sectionId', 0),
                    'employeeComment' => $request->input('employeeComment', ''),
                ];

                $successMessage = 'Metric score, metric comment submitted successfully!';
                $totalSections = 1; // Assuming one section is being processed
                $completedSections = (float) $request->input('metricEmpScore', 0) > 0 ? 1 : 0; // Mark as completed if score is greater than 0
            }

            // Retrieve the access token
            $accessToken = session('api_token');

            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->post(
                    'http://192.168.1.200:5123/Appraisal/Score/employee-score',
                    $payload
                );


            // Check if the response is successful
            if ($response->status() === 200) {
                // Calculate progress
                $progress = $totalSections > 0 ? ($completedSections / $totalSections) * 100 : 0;

                session([
                    'progress' => $progress
                ]);

                $appraisalProgress = session('progress');



                // Return success message and progress
                return back()->with('toast_success', $successMessage); // Send progress back to the frontend
            } else {
                // Log the error if the response is not successful
                Log::error('API Response Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return back()->with('toast_error', 'Failed to submit score. Please check the logs for details.');
            }
        } catch (\Exception $e) {
            // Log exception and notify the user
            Log::error('API Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function submitAppraisalForReview(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'status' => 'required|string',
        ]);

        // dd($request);

        // Prepare the data to be sent to the API
        $data = [
            'kpiId' => (int) $request->input('kpiId'),
            'batchId' => (int) $request->input('batchId'),
            'status' => $request->input('status'),
        ];

        try {
            // Make the API request
            // Retrieve the access token
            $accessToken = session('api_token');

            // dd($data);
            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/Score/update-score-status', $data);
            // dd($response);
            // Check if the response is successful
            if ($response->successful()) {
                // Display success message using SweetAlert
                return back()->with('toast_success', 'KPI submitted for review successfully.');
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
            Log::error('API Exception, Submit, Review Response Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }

    }
    public function acceptAppraisalReview(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'kpiId' => 'required|integer',
            'batchId' => 'required|integer',
            'status' => 'required|string',
        ]);

        // dd($request);

        // Prepare the data to be sent to the API
        $data = [
            'kpiId' => (int) $request->input('kpiId'),
            'batchId' => (int) $request->input('batchId'),
            'status' => $request->input('status'),
        ];

        try {
            // Make the API request
            // Retrieve the access token
            $accessToken = session('api_token');

            // dd($data);
            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/Score/update-score-status', $data);

            // Check if the response is successful
            if ($response->successful()) {
                // Display success message using SweetAlert
                return back()->with('toast_success', 'KPI submitted for review successfully.');
            } else {
                // Log the error if the response is not successful
                Log::error('API Submit, Confirmation Response Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Handle the case where the API response is not successful
                return back()->with('toast_error', 'Failed to submit KPI for review. Please try again.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the API request
            // Log exception and notify the user
            Log::error('API Exception, Submit, Confirmation Response Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }


    }

    public function submitProbing(Request $request){

        dd($request->all());


        $request->validate([

        ]);
    }
}
