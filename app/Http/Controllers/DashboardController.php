<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;


class DashboardController extends Controller
{

    public function index()
    {

        $userName = session('user_name');
        $userEmail = session('user_email');


        return view("dashboard.index", compact('userEmail', 'userName'));
    }



    public function show()
    {

        $accessToken = session('api_token');

        try {
            $response = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/Batch');

            if ($response->successful()) {

                $batches = $response->json();


                // Filter batches to get only those with status "OPEN" and active state true
                $batch = array_filter($batches, function ($batch) {
                    return $batch['status'] === 'OPEN' && $batch['active'] === true;
                });

                $activeBatches = [];

                foreach ($batch as $activeBatch) {
                    // dd($activeBatch);
                    $activeBatches = [
                        'id' => $activeBatch['id'],
                        'batch_name' => $activeBatch['name'],
                    ];
                }


                // dd($activeBatch['id']);



                return view('dashboard.show-batch', compact('activeBatches')); // Pass to view

            } else {
                // Log the error response
                Log::error('Failed to retrieve Employee Appraisal', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve Appraisal');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while retrieving EMployee Appraisal', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
        }
    }


    public function showEmployeeKpi(Request $request, $id)
    {
        // dd($id);
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetAllKpiForBatch/{$id}");


            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $kpis = $response->json();

                // dd($kpis);
                $globalSectionCount = 0;
                $regularSectionCount = 0;

                // Loop through each KPI
                foreach ($kpis as $kpi) {
                    // Check if the KPI type is GLOBAL
                    if ($kpi['kpiType'] === 'GLOBAL') {
                        // Count the number of sections for GLOBAL KPI
                        $globalSectionCount += count($kpi['sections']);
                    }

                    // Check if the KPI type is REGULAR
                    if ($kpi['kpiType'] === 'REGULAR') {
                        // Count the number of sections for REGULAR KPI
                        $regularSectionCount += count($kpi['sections']);
                    }
                }

                // Calculate the total section count
                $totalSectionCount = $globalSectionCount + $regularSectionCount;

                // Prepare the result
                $employeeKpi = [
                    'id' => $kpi['kpiId'],
                    'batch_id' => $kpi['batchId'],
                    'kpi_name' => $kpi['kpiName'],
                    'section_count' => $totalSectionCount
                ];

                // dd($employeeKpi);



                // Return the KPI names and section counts to the view
                return view("dashboard.show-employee-kpi", compact('employeeKpi'));
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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
        }
    }

    public function editEmployeeKpi(Request $request, $id)
    {
        // dd($id);
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$id}");

            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $kpi = $response->object();

                // dd($kpi);
                $batchId = $kpi[0]->batchId;

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
                return view("dashboard.test-employee-kpi-form", compact('appraisal', 'batchId'));
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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
        }
    }





    public function showEmployeeSupervisorKpiScore(Request $request, $id)
    {
        // dd($id);
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$id}");

            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $kpi = $response->object();

                $batchId = $kpi[0]->batchId;

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



                // Return the KPI names and section counts to the view
                return view("dashboard.employee-supervisor-kpi-score-form", compact('appraisal', 'batchId'));
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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
        }
    }

    public function showEmployeeProbe(Request $request, $id)
    {
        // dd($id);
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$id}");

            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $appraisal = $response->object();

                // dd($appraisal);



                // Return the KPI names and section counts to the view
                return view("dashboard.employee-probe-form", compact('appraisal'));
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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
        }
    }



    private function fetchBatches()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Batch');

        // Check if the response is successful and not null
        if ($response->successful()) {
            return $response->json() ?? [];
        } else {
            // Log the error and return an empty array or handle accordingly
            Log::error('Failed to fetch batches', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return []; // Return an empty array if the fetch fails
        }
    }

    private function fetchKpis()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi');

        // Check if the response is successful and not null
        if ($response->successful()) {
            return $response->json() ?? [];
        } else {
            // Log the error and return an empty array or handle accordingly
            Log::error('Failed to fetch KPIs', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return []; // Return an empty array if the fetch fails
        }
    }

    private function fetchSections()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Section');

        // Check if the response is successful and not null
        if ($response->successful()) {
            return $response->json() ?? [];
        } else {
            // Log the error and return an empty array or handle accordingly
            Log::error('Failed to fetch sections', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return []; // Return an empty array if the fetch fails
        }
    }

    private function fetchMetrics()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Metric');

        // Check if the response is successful and not null
        if ($response->successful()) {
            return $response->json() ?? [];
        } else {
            // Log the error and return an empty array or handle accordingly
            Log::error('Failed to fetch metrics', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return []; // Return an empty array if the fetch fails
        }
    }


    private function organizeDataByRole($kpis, $sections, $metrics, $currentEmpRoleId)
    {
        // Initialize an array to hold organized data by role
        $organized = [];

        foreach ($kpis as $kpi) {
            // Check if the empRole ID matches the current user's role
            if ($kpi['empRole']['id'] === $currentEmpRoleId) {
                $roleId = $kpi['empRole']['id'];
                if (!isset($organized[$roleId])) {
                    $organized[$roleId] = [
                        'roleName' => $kpi['empRole']['name'], // Store role name
                        'kpis' => []
                    ];
                }

                // Initialize section count for the current KPI
                $sectionCount = 0;

                // Add the KPI to the organized data
                $organized[$roleId]['kpis'][$kpi['id']] = [
                    'name' => $kpi['name'],
                    'description' => $kpi['description'],
                    'active' => $kpi['active'],
                    'section_count' => 0, // Initialize section count
                    'sections' => []
                ];

                // Find sections related to the current KPI
                foreach ($sections as $section) {
                    if ($section['kpi']['id'] === $kpi['id']) {
                        // Increment the section count
                        $sectionCount++;

                        // Store section details
                        $organized[$roleId]['kpis'][$kpi['id']]['sections'][$section['id']] = [
                            'name' => $section['name'],
                            'description' => $section['description'],
                            'score' => $section['score'],
                            'active' => $section['active'],
                            'metrics' => []
                        ];

                        // Find metrics related to the current section
                        foreach ($metrics as $metric) {
                            if ($metric['section']['id'] === $section['id']) {
                                $organized[$roleId]['kpis'][$kpi['id']]['sections'][$section['id']]['metrics'][] = [
                                    'id' => $metric['id'],
                                    'name' => $metric['name'],
                                    'description' => $metric['description'],
                                    'score' => $metric['score'],
                                    'active' => $metric['active']
                                ];
                            }
                        }
                    }
                }

                // Store the section count in the KPI data
                $organized[$roleId]['kpis'][$kpi['id']]['section_count'] = $sectionCount;

                // dd($sectionCount);

                // Store the section count in the session
                Session::put("kpi_section_count", $sectionCount);
            }
        }

        return $organized;
    }




    public function my_kpis()
    {
        return view("dashboard.my-kpis");
    }


    public function kpi_setup()
    {
        return view("kpi-setup.kpi-setup");
    }
    public function score_setup()
    {
        return view("kpi-setup.score-setup");
    }
}
