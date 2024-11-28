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
                $activeBatches = array_filter($batches, function ($batch) {
                    return $batch['status'] === 'OPEN' && $batch['active'] === true;
                });

                return view('dashboard.show-batch', compact('activeBatches')); // Pass to view

            } else {
                // Log the error response
                Log::error('Failed to retrieve batches', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve batches');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while retrieving batches', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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
                $kpi = $response->json();

                $employeeKpi = [
                    'id' => $kpi[0]['kpiId'],
                    'kpi_name' => $kpi[0]['kpiName'],
                    'section_count' => count($kpi[0]['sections'])
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
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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
                $appraisal = $response->object();

                    // dd($appraisal);



                // Return the KPI names and section counts to the view
                return view("dashboard.employee-kpi-form", compact('appraisal'));
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
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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
                $appraisal = $response->object();

                // dd($appraisal);



                // Return the KPI names and section counts to the view
                return view("dashboard.employee-supervisor-kpi-score-form", compact('appraisal'));
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
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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

    public function supervisor()
    {

        return view('dashboard.supervisor');
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
