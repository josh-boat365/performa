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
                    'kpiType' => 'nullable|string',
                ]);

                // Prepare the payload for the API request
                $payload = [
                    'id' => $request->input('sectionEmpScoreId') ?? null,
                    'sectionEmpScore' => (float) $request->input('sectionEmpScore', 0),
                    'sectionId' => (int) $request->input('sectionId', 0),
                    'employeeComment' => $request->input('employeeComment', ''),
                    'kpiType' => $request->input('kpiType', ''),
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
                    'kpiType' => 'nullable|string',
                ]);

                // Prepare the payload for the API request
                $payload = [
                    'id' => $request->input('metricEmpScoreId') ?? null,
                    'metricEmpScore' => (float) $request->input('metricEmpScore', 0),
                    'metricId' => (int) $request->input('metricId', 0),
                    'sectionId' => (int) $request->input('sectionId', 0),
                    'employeeComment' => $request->input('employeeComment', ''),
                    'kpiType' => $request->input('kpiType', ''),
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

    // public function submitProbing(Request $request)
    // {
    //     // Validate the incoming request data
    //     $request->validate([
    //         'scoreId' => 'required|array',
    //         'scoreId.*' => 'integer', // Ensure each scoreId is an integer
    //     ]);

    //     // Retrieve the score IDs from the request
    //     $scoreIds = $request->input('scoreId');

    //     // Prepare the data to be sent to the API
    //     $successCount = 0; // To count successful submissions
    //     $accessToken = session('api_token'); // Retrieve the access token

    //     foreach ($scoreIds as $scoreId) {
    //         // Prepare the data for the current scoreId
    //         $data = [
    //             'scoreId' => (int) $scoreId,
    //         ];

    //         try {
    //             // Submit the data to the external API
    //             $response = Http::withToken($accessToken)
    //                 ->put('http://192.168.1.200:5123/Appraisal/Score/UpdateEmployeeScoreToProb', $data);

    //             // Check if the response is successful
    //             if ($response->successful()) {
    //                 $successCount++;
    //             } else {
    //                 // Log the error if the response is not successful
    //                 Log::error('API Submit, Confirmation Response Error', [
    //                     'scoreId' => $scoreId,
    //                     'status' => $response->status(),
    //                     'body' => $response->body(),
    //                 ]);
    //             }
    //         } catch (\Exception $e) {
    //             // Handle any exceptions that occur during the API request
    //             Log::error('API Exception, Submit, Confirmation Response Error', [
    //                 'scoreId' => $scoreId,
    //                 'message' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString(),
    //             ]);
    //         }
    //     }

    //     // Check if all submissions were successful
    //     if ($successCount === count($scoreIds)) {
    //         return back()->with('toast_success', 'All Supervisor Scores Has Been Submitted For Review Successfully.');
    //     } else {
    //         return back()->with('toast_error', 'Some scores failed to submit. Please check the logs for details.');
    //     }
    // }


    // public function submitProbing(Request $request){
    //         dd($request->all());
    // }


    // public function submitProbing(Request $request)
    // {
    //     // Validate the incoming request data
    //     $request->validate([
    //         'scoreId' => 'nullable|integer', // Ensure scoreId is an integer
    //         'employeeComment' => 'nullable|string',
    //         'sectionId' => 'nullable|integer',
    //         'metricId' => 'nullable|integer',
    //         'kpiType' => 'nullable|string',
    //     ]);

    //     // Retrieve the score ID and employee comment from the request
    //     $scoreId = $request->input('scoreId');
    //     $employeeComment = $request->input('employeeComment');

    //     // Prepare the access token
    //     $accessToken = session('api_token'); // Retrieve the access token

    //     // Check if both scoreId and employeeComment are present
    //     if ($employeeComment !== null && $scoreId !== null) {
    //         // Prepare payload for the second API
    //         $payload = [
    //             'id' => (int) $scoreId,
    //             'sectionId' => (int) $request->input('sectionId', 0),
    //             'employeeComment' => $employeeComment,
    //             'kpiType' => $request->input('kpiType', ''),
    //         ];

    //         try {
    //             // Call the second API to update the employee comment
    //             $response = Http::withToken($accessToken)
    //             ->put('http://192.168.1.200:5123/Appraisal/Score/employee-score', $payload);

    //             // Check if the response is successful
    //             if (!$response->successful()) {
    //                 // Log the error if the response is not successful
    //                 Log::error('API Submit, Confirmation Response Error for employee comment to Probe', [
    //                     'scoreId' => $scoreId,
    //                     'employeeComment' => $employeeComment,
    //                     'status' => $response->status(),
    //                     'body' => $response->body(),
    //                 ]);
    //                 return back()->with('toast_error', 'Failed to submit employee comment to Probe. ');
    //             }
    //         } catch (\Exception $e) {
    //             // Handle any exceptions that occur during the API request
    //             Log::error('API Exception, Submit, employee-score Error', [
    //                 'scoreId' => $scoreId,
    //                 'message' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString(),
    //             ]);
    //             return back()->with('toast_error', 'An error occurred while submitting the employee comment.');
    //         }

    //         // Prepare data for the first API
    //         $data = [
    //             'scoreId' => (int) $scoreId,
    //         ];

    //         try {
    //             // Call the first API to update the score ID
    //             $response = Http::withToken($accessToken)
    //                 ->put('http://192.168.1.200:5123/Appraisal/Score/UpdateEmployeeScoreToProb', $data);

    //             // Check if the response is successful
    //             if ($response->successful()) {
    //                 return back()->with('toast_success', ' Submitted  to Probe Successfully.');
    //             } else {
    //                 // Log the error if the response is not successful
    //                 Log::error('API Submit, Confirmation Response Error for UpdateEmployeeScoreToProb', [
    //                     'scoreId' => $scoreId,
    //                     'status' => $response->status(),
    //                     'body' => $response->body(),
    //                 ]);
    //                 return back()->with('toast_error', 'Failed to submit supervisor score to Probe.');
    //             }
    //         } catch (\Exception $e) {
    //             // Handle any exceptions that occur during the API request
    //             Log::error('API Exception, Submit, UpdateEmployeeScoreToProb Error', [
    //                 'scoreId' => $scoreId,
    //                 'message' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString(),
    //             ]);
    //             return back()->with('toast_error', 'An error occurred while updating the supervisor score to Probe ');
    //         }
    //     } elseif ($employeeComment !== null
    //     ) {
    //         // If only employeeComment is present, call the second API
    //         $payload = [
    //             'id' => (int) $scoreId,
    //             'sectionId' => (int) $request->input('sectionId', 0),
    //             'employeeComment' => $employeeComment,
    //             'kpiType' => $request->input('kpiType', ''),
    //         ];

    //         try {
    //             // Call the second API to update the employee comment
    //             $response = Http::withToken($accessToken)
    //             ->put('http://192.168.1.200:5123/Appraisal/Score/employee-score', $payload);

    //             // Check if the response is successful
    //             if ($response->successful()) {
    //                 return back()->with('toast_success', 'Employee comment Saved for Probing successfully.');
    //             } else {
    //                 // Log the error if the response is not successful
    //                 Log::error('API Submit, Confirmation Response Error for employee comment', [
    //                     'scoreId' => $scoreId,
    //                     'employeeComment' => $employeeComment,
    //                     'status' => $response->status(),
    //                     'body' => $response->body(),
    //                 ]);
    //                 return back()->with('toast_error', 'Failed to save employee comment to Probe');
    //             }
    //         } catch (\Exception $e) {
    //             // Handle any exceptions that occur during the API request
    //             Log::error('API Exception, Submit, employee comment Error', [
    //                 'scoreId' => $scoreId,
    //                 'message' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString(),
    //             ]);
    //             return back()->with('toast_error', 'An error occurred while saving  the employee comment to Probe.');
    //         }
    //     } elseif ($scoreId !== null) {
    //         // If only scoreId is present, call the first API
    //         $data = [
    //             'scoreId' => (int) $scoreId,
    //         ];

    //         try {
    //             // Call the first API to update the score ID
    //             $response = Http::withToken($accessToken)
    //             ->put('http://192.168.1.200:5123/Appraisal/Score/UpdateEmployeeScoreToProb', $data);

    //             // Check if the response is successful
    //             if ($response->successful()) {
    //                 return back()->with('toast_success', 'Supervisor Score Saved for Probing Successfully.');
    //             } else {
    //                 // Log the error if the response is not successful
    //                 Log::error('API Submit, Confirmation Response Error for UpdateEmployeeScoreToProb', [
    //                     'scoreId' => $scoreId,
    //                     'status' => $response->status(),
    //                     'body' => $response->body(),
    //                 ]);
    //                 return back()->with('toast_error', 'Failed  to save Supervisor Score  to Probe.');
    //             }
    //         } catch (\Exception $e) {
    //             // Handle any exceptions that occur during the API request
    //             Log::error('API Exception, Submit, UpdateEmployeeScoreToProb Error', [
    //                 'scoreId' => $scoreId,
    //                 'message' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString(),
    //             ]);
    //             return back()->with('toast_error', 'An error occurred while saving Supervisor Score  to Probe.');
    //         }
    //     } else {
    //         return back()->with('toast_error', 'No valid data provided for Submission: <b>You Need to Select A Check Box To Confirm</b>');
    //     }
    // }




    public function submitProbing(Request $request)
    {
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
        $employeeComment = $request->input('employeeComment');

        // Prepare the access token
        $accessToken = session('api_token'); // Retrieve the access token

        // If both scoreId and employeeComment are present
        if (
            $employeeComment !== null && $scoreId !== null
        ) {
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

            // Prepare data for the first API
            $data = [
                'scoreId' => (int) $scoreId,
            ];

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
        } elseif ($employeeComment !== null) {
            // If only employeeComment is provided, prepare the payload for the comment API
            $payload = [
                'employeeComment' => $employeeComment,
                'sectionId' => (int) $request->input('sectionId', 0),
                'kpiType' => $request->input('kpiType', ''),
            ];

            try {
                // Call the API to submit the employee comment
                $response = Http::withToken($accessToken)
                    ->post('http://192.168.1.200:5123/Appraisal/Score/employee-score', $payload);

                // Check if the response is successful
                if ($response->successful()) {
                    return back()->with('toast_success', 'Employee Comment Submitted Successfully.');
                } else {
                    // Log the error if the response is not successful
                    Log::error('API Submit, Confirmation Response Error for submitComment', [
                        'employeeComment' => $employeeComment,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return back()->with('toast_error', 'Failed to submit employee comment. Please check the logs for details.');
                }
            } catch (\Exception $e) {
                // Handle any exceptions that occur during the API request
                Log::error('API Exception, Submit, submitComment Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->with('toast_error', 'An error occurred while submitting the employee comment.');
            }
        } elseif ($scoreId !== null) {
            // If only scoreId is provided, prepare the payload for the score ID API
            $data = [
                'scoreId' => (int) $scoreId,
            ];

            try {
                // Call the API to update the score ID
                $response = Http::withToken($accessToken)
                    ->put('http://192.168.1.200:5123/Appraisal/Score/UpdateEmployeeScoreToProb', $data);

                // Check if the response is successful
                if ($response->successful()) {
                    return back()->with('toast_success', 'Score ID Submitted Successfully.');
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
                return back()->with('toast_error', 'An error occurred while updating the score ID.');
            }
        } else {
            return back()->with('toast_error', 'No data provided to submit.');
        }
    }


    public function submitFinalProbing() {}
}
