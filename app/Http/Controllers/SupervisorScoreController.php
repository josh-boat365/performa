<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SupervisorScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accessToken = session('api_token');

        $responseSup = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi/PendingSupervisorScoringKpi');


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


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $kpiId, $batchId)
    {
        // Get the access token from the session
        $accessToken = session('api_token');

        $data = [
            'kpiId' => (int) $kpiId,
            'batchId' => (int) $batchId
        ];


        try {

            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Kpi/GetSupervisorScoringKpi", $data);

            // dd($response);

            if ($response->successful()) {

                $kpis = $response->object();


                $appraisal = collect();

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
                            // Return the section regardless of whether it has active metrics
                            return $section;
                        });

                        // Add the KPI and its sections to the appraisal
                        $appraisal->push((object) [
                            'kpi' => $kpi,
                            'activeSections' => $activeSections
                        ]);

                        $firstSection = $kpi->sections[0];
                        $status = $firstSection->sectionEmpScore->status ?? 'PENDING';
                        $kpiStatus = $status;
                    }
                }


                return view("dashboard.supervisor.score-employee-form", compact('appraisal', 'kpiStatus'));
            } else {
                // Log the error response
                Log::error('Failed to retrieve KPIs', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve Employee Appraisal, <b>Contact Application Support for Assistance</b>');
            }
        } catch (\Exception $e) {

            Log::error('Exception occurred while retrieving KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }



    public function editProb(Request $request, $kpiId, $batchId)
    {
        // Get the access token from the session
        $accessToken = session('api_token');

        $data = [
            'kpiId' => (int) $kpiId,
            'batchId' => (int) $batchId
        ];


        try {

            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Kpi/GetProbScoringKpi", $data);


            if ($response->successful()) {

                $kpis = $response->object();


                $appraisal = collect();

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
                            // Return the section regardless of whether it has active metrics
                            return $section;
                        });

                        // Add the KPI and its sections to the appraisal
                        $appraisal->push((object) [
                            'kpi' => $kpi,
                            'activeSections' => $activeSections
                        ]);
                    }
                }

                // dd($appraisal);



                // Return the KPI names and section counts to the view
                return view("dashboard.probe-supervisor.score-employee-form", compact('appraisal'));
            } else {
                // Log the error response
                Log::error('Failed to retrieve KPIs', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve KPIs');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while retrieving KPIs', [
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

                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'metricSupScore' => (float) $request->input('metricSupScore'),
                    'supervisorComment' => $request->input('supervisorComment', ''),
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

                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'sectionSupScore' => (float) $request->input('sectionSupScore'),
                    'supervisorComment' => $request->input('supervisorComment', ''),
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

            // Check if the response is successful
            if ($response->status() === 200) {
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


    public function probScore(Request $request)
    {
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

                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'metricProbScore' => (float) $request->input('metricProbScore'),
                    'probComment' => $request->input('probComment', ''),
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

                // Prepare the payload for the API request
                $payload = [
                    'scoreId' => (int) $request->input('scoreId') ?? null,
                    'sectionProbScore' => (float) $request->input('sectionProbScore'),
                    'probComment' => $request->input('probComment', ''),
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



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
