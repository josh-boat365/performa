<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\HandlesApiResponses;

class SupervisorScoreController extends Controller
{
    use HandlesApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        $accessToken = session('api_token');

        $responseSup = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi/PendingSupervisorScoringKpi');

        // Check for session expiration (401 Unauthorized)
        if ($responseSup->status() === 401) {
            return $this->sessionExpiredRedirect();
        }

        $responseProb = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi/PendingProbScoringKpi');

        if ($responseSup->successful()) {
            $employeeSupDetails = $responseSup->object();
        }

        if ($responseProb->successful()) {
            $employeeProbDetails = $responseProb->object();
        }
        // dd($employeeProbDetails);



        return view('dashboard.supervisor.show-employee', compact('employeeSupDetails', 'employeeProbDetails'));
    }



    public function edit(Request $request, $kpiId, $batchId, $employeeId)
    {
        return $this->editKpi($request, $kpiId, $batchId, $employeeId, 'supervisor');
    }


    public function editProb(Request $request, $kpiId, $batchId, $employeeId)
    {
        return $this->editKpi($request, $kpiId, $batchId, $employeeId, 'prob');
    }



    public function editKpi(Request $request, $kpiId, $batchId, $employeeId, $type)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Get the access token from the session
        $accessToken = session('api_token');

        $data = [
            'employeeId' => (int) $employeeId,
            'kpiId' => (int) $kpiId,
            'batchId' => (int) $batchId
        ];

        // dd($data);

        // Determine endpoint based on type
        $endpoint = $type === 'prob'
            ? "http://192.168.1.200:5123/Appraisal/Kpi/GetProbScoringKpi"
            : "http://192.168.1.200:5123/Appraisal/Kpi/GetSupervisorScoringKpi";

        try {
            $response = Http::withToken($accessToken)->put($endpoint, $data);

            if ($response->successful()) {
                $kpis = $response->object() ?? [];
                $appraisal = collect() ?? [];

                // Process each KPI
                foreach ($kpis as $kpi) {
                    if ($kpi->kpiActive) {
                        // Filter active sections
                        $activeSections = collect($kpi->sections)->filter(function ($section) {
                            return $section->sectionActive;
                        });

                        // Transform sections to include metrics, even if none are active
                        $activeSections->transform(function ($section) {
                            // Filter metrics within the section
                            $section->metrics = collect($section->metrics)->filter(function ($metric) {
                                return $metric->metricActive;
                            });
                            return $section;
                        });

                        // Add the KPI and its sections to the appraisal
                        $appraisal->push((object) [
                            'kpi' => $kpi,
                            'activeSections' => $activeSections
                        ]);

                        // Only check status for supervisor type
                        if ($type !== 'prob' && !empty($kpi->sections)) {
                            $firstSection = $kpi->sections[0];
                            $kpiStatus = $firstSection->sectionEmpScore->status ?? 'PENDING';
                        }
                    }
                }

                //Employee Grade and Supervisor Grade for Employee
                $submittedEmployeeGrade = GetKpiGradeController::getGrade($kpiId, $batchId, $employeeId)->submittedEmployeeGrade;
                $supervisorGradeForEmployee = GetKpiGradeController::getGrade($kpiId, $batchId, $employeeId)->supervisorGradeForEmployee;


                // Determine view based on type
                $view = $type === 'prob'
                    ? "dashboard.probe-supervisor.score-employee-form"
                    : "dashboard.supervisor.score-employee-form";

                // Prepare view data
                $viewData = $type === 'prob'
                    ? compact('appraisal', 'employeeId', 'submittedEmployeeGrade', 'supervisorGradeForEmployee')
                    : compact('appraisal', 'kpiStatus', 'employeeId', 'submittedEmployeeGrade', 'supervisorGradeForEmployee');

                return view($view, $viewData);
            } else {
                // Check for session expiration (401 Unauthorized)
                if ($response->status() === 401) {
                    return $this->sessionExpiredRedirect();
                }
                Log::error('Failed to retrieve KPIs', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve Employee Appraisal, <b>Contact Application Support for Assistance</b>');
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving KPIs : editKpi function', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }






    /**
     * Store a newly created resource in storage.
     */
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

            // Initialize the payload variable
            $payload = null; // Initialize as null

            // Check for metric score input
            if ($request->input('metricSupScore')) {
                $request->validate([
                    'scoreId' => 'nullable|numeric|min:0',
                    'metricSupScore' => 'nullable|numeric',
                    'supervisorComment' => 'nullable|string',
                ]);

                // Ensure employeeComment is not an empty string
                $supervisorComment = $request->input('supervisorComment', '');

                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'metricSupScore' => (float) $request->input('metricSupScore'),
                    'supervisorComment' => $supervisorComment,
                ];

                // dd($payload);

                $successMessage = 'Metric score, metric comment submitted successfully!';
            }

            // Check for section score input
            if ($request->input('sectionSupScore')) {
                $request->validate([
                    'scoreId' => 'nullable|numeric|min:0',
                    'sectionSupScore' => 'nullable|numeric',
                    'supervisorComment' => 'nullable|string',
                ]);

                $supervisorComment = $request->input('supervisorComment', '');


                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'sectionSupScore' => (float) $request->input('sectionSupScore'),
                    'supervisorComment' => $supervisorComment,
                ];

                $successMessage = 'Section score, section comment submitted successfully!';
            }

            // Check if the payload is null (i.e., no score was submitted)
            if (is_null($payload)) {
                return back()->with('toast_error', 'No scores submitted. Please provide a score.');
            }

            // Retrieve the access token
            $accessToken = session('api_token');

            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/Score/supervisor-score', $payload);

            // Check for session expiration (401 Unauthorized)
            if ($response->status() === 401) {
                Log::warning('API returned 401 - Token may be expired');
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your session has expired. Please log in again.',
                        'session_expired' => true,
                        'redirect' => route('login')
                    ], 401);
                }
                return redirect()->route('login')->with('toast_warning', 'Your session has expired. Please log in again.');
            }

            // Check if the API call was successful
            if ($response->successful()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMessage,
                    ]);
                }
                return back()->with('toast_success', $successMessage);
            } else {
                // Log the error if the API response is not successful
                Log::error('API Response Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to submit score. Please try again.'
                    ], $response->status());
                }
                return back()->with('toast_error', 'Failed to submit score. Please try again.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Log exception and notify the user
            Log::error('API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again.',
                ], 500);
            }
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }


    public function probScore(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }


        try {
            // dd($request);
            // Initialize a variable for the success message
            $successMessage = '';

            // Initialize the payload variable
            $payload = null; // Initialize as null

            // Check for metric score input
            if ($request->input('metricProbScore')) {
                $request->validate([
                    'scoreId' => 'nullable|numeric|min:0',
                    'metricProbScore' => 'nullable|numeric',
                    'probComment' => 'nullable|string',
                ]);

                $probComment = $request->input('probComment', '');


                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'metricProbScore' => (float) $request->input('metricProbScore'),
                    'probComment' => $probComment,
                ];

                // dd($payload);

                $successMessage = 'Metric score, metric comment submitted successfully!';
            }

            // Check for section score input
            if ($request->input('sectionProbScore')) {
                $request->validate([
                    'scoreId' => 'nullable|numeric|min:0',
                    'sectionProbScore' => 'nullable|numeric',
                    'probComment' => 'nullable|string',
                ]);

                $probComment = $request->input('probComment', '');


                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'sectionProbScore' => (float) $request->input('sectionProbScore'),
                    'probComment' => $probComment,
                ];

                $successMessage = 'Section score, section comment submitted successfully!';
            }

            // Check if the payload is null (i.e., no score was submitted)
            if (is_null($payload)) {
                return back()->with('toast_error', 'No scores submitted. Please provide a score.');
            }

            // Retrieve the access token
            $accessToken = session('api_token');

            // Submit the data to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/Score/prob-score', $payload);

            // Check if the response is successful
            if ($response->status() === 200) {
                // dd($response);
                // Return success message
                return back()->with('toast_success', $successMessage);
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
}
