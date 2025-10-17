<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AppraisalScoreController extends Controller
{

    public function store(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        try {
            // Initialize a variable for the success message
            $successMessage = '';

            // Validate the request based on the input type
            if ($request->input('sectionEmpScore')) {
                $request->validate([
                    'sectionEmpScoreId' => 'nullable|numeric|min:0',
                    'sectionEmpScore' => 'nullable|numeric',
                    'sectionId' => 'required|integer',
                    'employeeComment' => 'nullable|string',
                    'kpiType' => 'nullable|string',
                ]);

                // Ensure employeeComment is not an empty string
                $employeeComment = $request->input('employeeComment', '');


                // Prepare the payload for the API request
                $payload = [
                    'id' => $request->input('sectionEmpScoreId') ?? null,
                    'sectionEmpScore' => (float) $request->input('sectionEmpScore', 0),
                    'sectionId' => (int) $request->input('sectionId', 0),
                    'employeeComment' => $employeeComment,
                    'kpiType' => $request->input('kpiType', ''),
                ];

                $successMessage = 'Section score, section comment submitted successfully!';
            }

            if ($request->input('metricEmpScore')) {
                $request->validate([
                    'metricEmpScoreId' => 'nullable|numeric|min:0',
                    'metricEmpScore' => 'nullable|numeric',
                    'metricId' => 'required|integer',
                    'sectionId' => 'required|integer',
                    'employeeComment' => 'nullable|string',
                    'kpiType' => 'nullable|string',
                ]);

                // Ensure employeeComment is not an empty string
                $employeeComment = $request->input('employeeComment', '');


                // Prepare the payload for the API request
                $payload = [
                    'id' => $request->input('metricEmpScoreId') ?? null,
                    'metricEmpScore' => (float) $request->input('metricEmpScore', 0),
                    'metricId' => (int) $request->input('metricId', 0),
                    'sectionId' => (int) $request->input('sectionId', 0),
                    'employeeComment' => $employeeComment,
                    'kpiType' => $request->input('kpiType', ''),
                ];

                $successMessage = 'Metric score, metric comment submitted successfully!';
            }

            // Retrieve the access token
            $accessToken = session('api_token');

            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->post('http://192.168.1.200:5123/Appraisal/Score/employee-score', $payload);

            // Check if the response is successful
            if ($response->status() === 200) {
                // Return success message
                // return back()->with('toast_success', $successMessage);
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                ]);
            } else {
                // Log the error if the response is not successful
                Log::error('API Response Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                // return back()->with('toast_error', 'Failed to submit score. Please check the logs for details.');
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Employee comment cannot be empty.'
                    ],
                    422
                );
            }
        } catch (\Exception $e) {
            // Log exception and notify the user
            Log::error('API Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 400);
        }
    }


    public function submitAppraisalForReview(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

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
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

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




    public function submitProbing(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }


        // dd($request);
        // Validate the incoming request data
        $request->validate([
            'scoreId' => 'nullable|integer', // Make scoreId nullable
            'employeeComment' => 'nullable|string',
            'sectionId' => 'nullable|integer',
            'metricId' => 'nullable|integer',
            'kpiType' => 'nullable|string',
        ]);

        // dd($request);

        // Retrieve the score ID and employee comment from the request
        $scoreId = $request->input('scoreId');
        $prob = $request->has('prob') ? true : false;
        $employeeComment = $request->input('employeeComment', '');

        // Prepare the access token
        $accessToken = session('api_token'); // Retrieve the access tok

        // If both scoreId and employeeComment are present
        if (
            $employeeComment == null
        ) {
            return back()->with('toast_error', 'Input a comment to probe');
        } else {
            // Prepare payload for the second API
            $payload = [
                'id' => (int) $scoreId,
                'sectionId' => (int) $request->input('sectionId', 0),
                'employeeComment' => $employeeComment,
                'kpiType' => $request->input('kpiType', ''),
            ];

            try {
                // Call the second API to update the employee comment
                $response = Http::withToken($accessToken)
                    ->put('http://192.168.1.200:5123/Appraisal/Score/employee-score', $payload);

                // Check if the response is successful
                if (!$response->successful()) {
                    // Log the error if the response is not successful
                    Log::error('API Submit, Confirmation Response Error for employee-score', [
                        'scoreId' => $scoreId,
                        'employeeComment' => $employeeComment,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return back()->with('toast_error', 'Failed to update employee comment. Please check the logs for details.');
                }
            } catch (\Exception $e) {
                // Handle any exceptions that occur during the API request
                Log::error('API Exception, Submit, employee-score Error', [
                    'scoreId' => $scoreId,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->with('toast_error', 'An error occurred while updating the employee comment.');
            }
        }


        // Prepare data for the first API
        $data = [
            'scoreId' => (int) $scoreId,
            'prob' => $prob,
        ];

        // dd($data);


        try {
            // Call the first API to update the score ID
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/Score/UpdateEmployeeScoreToProb', $data);

            // Check if the response is successful
            if ($response->successful()) {
                return back()->with('toast_success', 'Supervisor Score and Comment Submitted Successfully.');
            } else {
                // Log the error if the response is not successful
                Log::error('API Submit, Confirmation Response Error for UpdateEmployeeScoreToProb', [
                    'scoreId' => $scoreId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return back()->with('toast_error', 'Failed to update score ID. Please check the logs for details.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the API request
            Log::error('API Exception, Submit, UpdateEmployeeScoreToProb Error', [
                'scoreId' => $scoreId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('toast_error', 'An error occurred while updating the score ID .');
        }
    }
}
