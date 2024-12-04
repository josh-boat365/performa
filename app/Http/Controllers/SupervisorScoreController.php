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

        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi/PendingSupervisorScoringKpi');

        if ($response->successful()) {
            $employeeKpiDetails = $response->object();
        }



        return view('dashboard.supervisor.show-employee', compact('employeeKpiDetails'));
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

        // dd($data);
        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Kpi/GetSupervisorScoringKpi", $data);

            // dd($response);
            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $kpi = $response->object();

                // dd($kpi);

                // Filter the KPIs to include only those with active state of true or false
                $appraisal = collect($kpi)->filter(function ($kpi) {
                    // Check if the KPI is active
                    if ($kpi->kpiActive) {
                        // Filter sections that are active
                        $activeSections = collect($kpi->sections)->filter(function ($section) {
                            return $section->sectionActive; // Only include active sections
                        });

                        // If there are no active sections, return false
                        if ($activeSections->isEmpty()) {
                            return false;
                        }

                        // Filter metrics within the active sections
                        $activeSections->transform(function ($section) {
                            $section->metrics = collect($section->metrics)->filter(function ($metric) {
                                return $metric->metricActive; // Only include active metrics
                            });

                            // Return the section only if it has active metrics
                            return $section->metrics->isNotEmpty() ? $section : null;
                        });

                        // Remove null sections (those without active metrics)
                        $activeSections = $activeSections->filter();

                        // Return true if there are any active sections with active metrics
                        return $activeSections->isNotEmpty();
                    }

                    return false; // If KPI is not active, return false
                });

                // dd($appraisal);



                // Return the KPI names and section counts to the view
                return view("dashboard.supervisor.score-employee-form", compact('appraisal'));
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
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again, <b>Or Contact IT</b>');
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
                    'metricSupScore' => (float) $request->input('metricSupScore', 0),
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
                    'sectionSupScore' => (float) $request->input('sectionSupScore', 0),
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
