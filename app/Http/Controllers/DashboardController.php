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

        // dd($id);
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                // ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetAllKpiForBatch/{$id}");
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetAllKpiForEmployee");




            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $kpi = $response->json();

                // dd($kpis[0]['kpiId']);
                $responseKpis = Http::withToken($accessToken)
                    ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$kpi[0]['kpiId']}");

                $kpis = $responseKpis->json();

                // dd($kpis);

                if (empty($kpis)) {

                    $employeeKpi = null;
                } else {
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
                            $regularSectionCount += count($kpi['sections']);
                            $firstSection = $kpi['sections'][0];
                            $status = $firstSection['sectionEmpScore']['status'] ?? 'PENDING';



                            $kpiStatus = $status;
                            $batchId = $kpi['batchId'];
                            $batchName = $kpi['batchName'];
                            $employeeId = $kpi['employeeId'];
                        }
                    }

                    // Calculate the total section count
                    $totalSectionCount = $globalSectionCount + $regularSectionCount;


                    $grade_data = [
                        'batchId' => $batchId,
                        'employeeId' => $employeeId
                    ];


                    $employeeGrade =
                        Http::withToken($accessToken)
                        ->put("http://192.168.1.200:5123/Appraisal/Score/employee-total-kpiscore", $grade_data);



                    if ($employeeGrade->successful() && !empty($employeeGrade->object())) {
                        $grade = $employeeGrade->object();
                        $gradeDetails = [
                            'kpiScore' => $grade->totalKpiScore,
                            'grade' => $grade->grade,
                            'remark' => $grade->remark,
                            'status' => $kpiStatus
                        ];
                    } else {
                        $gradeDetails = [
                            'kpiScore' => null,
                            'grade' => null,
                            'remark' => null,
                            'status' => $kpiStatus
                        ];
                    }

                    // Prepare the result
                    $employeeKpi = [
                        'id' => $kpi['kpiId'],
                        'batch_id' => $kpi['batchId'],
                        'kpi_name' => $kpi['kpiName'],
                        'batch_name' => $batchName,
                        'section_count' => $totalSectionCount
                    ];
                }



                // dd($employeeKpi, $gradeDetails);





                // Return the KPI names and section counts to the view
                return view("dashboard.index", compact('employeeKpi', 'gradeDetails'));
            } else {
                // Log the error response
                Log::error('Failed to retrieve Appraisal Overview', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve Appraisal Overview');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while retrieving Appraisal Overview', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }

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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }

    public function editEmployeeKpi(Request $request, $id)
    {
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified employee ID
            $response = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$id}");

            // Check if the response is successful
            if (!$response->successful()) {
                Log::error('Failed to retrieve KPIs', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve Appraisal, <b>Contact Application Support for Assistance</b>');
            }

            // Decode the response into an object
            $kpis = $response->object();

            // Initialize an empty collection for active appraisals
            $appraisal = collect();


            // Process each KPI
            foreach ($kpis as $kpi) {
                if ($kpi->kpiActive) {
                    // Filter active sections
                    $activeSections = collect($kpi->sections)->filter(function ($section) {
                        return $section->sectionActive;
                    });

                    $activeSections->transform(function ($section) {
                        $section->metrics = collect($section->metrics)->filter(function ($metric) {
                            return $metric->metricActive;
                        });
                        return $section;
                    });


                    $appraisal->push((object) [
                        'kpi' => $kpi,
                        'activeSections' => $activeSections
                    ]);
                }
            }


            $grade_data = [];


            foreach ($kpis as $kpi) {

                if ($kpi->kpiType === "REGULAR") {

                    if (!empty($kpi->sections)) {

                        $firstSection = $kpi->sections[0];
                        $status = $firstSection->sectionEmpScore->status ?? 'PENDING';

                        $kpiStatus = $status;
                        $batchId = $kpi->batchId;
                        $employeeId = $kpi->employeeId;
                    }
                }
            }

            $grade_data = [
                'batchId' => $batchId,
                'employeeId' => $employeeId
            ];


            $employeeGrade =
                Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Score/employee-total-kpiscore", $grade_data);


            $batchId = $appraisal->isNotEmpty() ? $appraisal->first()->kpi->batchId : null;

            if ($employeeGrade->successful() && !empty($employeeGrade->object())) {
                $grade = $employeeGrade->object();
                $gradeDetails = [
                    'kpiScore' => $grade->totalKpiScore,
                    'grade' => $grade->grade,
                    'remark' => $grade->remark,
                    'status' => $kpiStatus
                ];
            } else {
                $gradeDetails = [
                    'kpiScore' => null,
                    'grade' => null,
                    'remark' => null,
                    'status' => $kpiStatus
                ];
            }

            // dd($gradeDetails);



            // Return the KPI names and section counts to the view
            return view("dashboard.test-employee-kpi-form", compact('appraisal', 'batchId', 'gradeDetails'));
        } catch (\Exception $e) {
            // Log the exception
            Log::error(
                'Exception occurred while retrieving KPIs',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }



    public function showEmployeeKpi()
    {
        // dd($id);
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified batch ID
            $response = Http::withToken($accessToken)
                // ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetAllKpiForBatch/{$id}");
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetAllKpiForEmployee");




            // Check if the response is successful
            if ($response->successful()) {
                // Decode the response into an array of KPIs
                $kpi = $response->json();

                // dd($kpis[0]['kpiId']);
                $responseKpis = Http::withToken($accessToken)
                    ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$kpi[0]['kpiId']}");

                $kpis = $responseKpis->json();

                // dd($kpis);

                if (empty($kpis)) {

                    $employeeKpi = null;
                } else {
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
                            $regularSectionCount += count($kpi['sections']);
                            $firstSection = $kpi['sections'][0];
                            $status = $firstSection['sectionEmpScore']['status'] ?? 'PENDING';



                            $kpiStatus = $status;
                            $batchId = $kpi['batchId'];
                            $employeeId = $kpi['employeeId'];
                        }
                    }

                    // Calculate the total section count
                    $totalSectionCount = $globalSectionCount + $regularSectionCount;


                    $grade_data = [
                        'batchId' => $batchId,
                        'employeeId' => $employeeId
                    ];


                    $employeeGrade =
                    Http::withToken($accessToken)
                    ->put("http://192.168.1.200:5123/Appraisal/Score/employee-total-kpiscore", $grade_data);



                    if ($employeeGrade->successful() && !empty($employeeGrade->object())) {
                        $grade = $employeeGrade->object();
                        $gradeDetails = [
                            'kpiScore' => $grade->totalKpiScore,
                            'grade' => $grade->grade,
                            'remark' => $grade->remark,
                            'status' => $kpiStatus
                        ];
                    } else {
                        $gradeDetails = [
                            'kpiScore' => null,
                            'grade' => null,
                            'remark' => null,
                            'status' => $kpiStatus
                        ];
                    }

                    // Prepare the result
                    $employeeKpi = [
                        'id' => $kpi['kpiId'],
                        'batch_id' => $kpi['batchId'],
                        'kpi_name' => $kpi['kpiName'],
                        'section_count' => $totalSectionCount
                    ];
                }



                // dd($employeeKpi, $gradeDetails);





                // Return the KPI names and section counts to the view
                return view("dashboard.show-employee-kpi", compact('employeeKpi', 'gradeDetails'));
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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }

    public function showEmployeeProbe(Request $request, $id)
    {
        // Get the access token from the session
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API to get KPIs for the specified employee ID
            $response = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/GetKpiForEmployee/{$id}");

            // Check if the response is successful
            if (!$response->successful()) {
                Log::error('Failed to retrieve KPIs', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve KPIs');
            }

            // Decode the response into an object
            $kpis = $response->object();

            // Initialize an empty collection for active appraisals
            $appraisal = collect();


            // Process each KPI
            foreach ($kpis as $kpi) {
                if ($kpi->kpiActive) {
                    // Filter active sections
                    $activeSections = collect($kpi->sections)->filter(function ($section) {
                        return $section->sectionActive;
                    });

                    $activeSections->transform(function ($section) {
                        $section->metrics = collect($section->metrics)->filter(function ($metric) {
                            return $metric->metricActive;
                        });
                        return $section;
                    });


                    $appraisal->push((object) [
                        'kpi' => $kpi,
                        'activeSections' => $activeSections
                    ]);
                }
            }



            // Get the batch ID from the first KPI if available
            $batchId = $appraisal->isNotEmpty() ? $appraisal->first()->kpi->batchId : null;

            // dd($appraisal);

            // Return the KPI names and section counts to the view
            return view("dashboard.test-employee-probe-form", compact('appraisal', 'batchId'));
        } catch (\Exception $e) {
            // Log the exception
            Log::error(
                'Exception occurred while retrieving KPIs',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
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
