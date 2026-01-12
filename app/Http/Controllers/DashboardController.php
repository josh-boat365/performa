<?php

namespace App\Http\Controllers;

use App\Services\AppraisalApiService;
use App\Services\HrmsApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    private AppraisalApiService $appraisalService;
    private HrmsApiService $hrmsService;
    private GetKpiGradeController $gradeController;

    public function __construct(
        AppraisalApiService $appraisalService,
        HrmsApiService $hrmsService,
        GetKpiGradeController $gradeController
    ) {
        $this->appraisalService = $appraisalService;
        $this->hrmsService = $hrmsService;
        $this->gradeController = $gradeController;
    }

    /**
     * Display the main dashboard index
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            // Validate session token exists
            $accessToken = session('api_token');
            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            // Get all KPIs for employee
            $response = $this->appraisalService->getAllKpisForEmployee();
            $kpis = $response['data'] ?? $response ?? [];

            // dd($kpis);

            if (empty($kpis)) {

                return view("dashboard.index", $this->prepareViewData(null, null));
            }

            $firstKpiId = $kpis[0]['kpiId'] ?? null;


            if (!$firstKpiId) {

                return view("dashboard.index", $this->prepareViewData(null, null));
            }

            // Get details for first KPI
            $kpiDetailsResponse = $this->appraisalService->getKpiForEmployee($firstKpiId);
            $kpiDetails = $kpiDetailsResponse['data'] ?? $kpiDetailsResponse ?? [];

            if (empty($kpiDetails)) {
                return view("dashboard.index", $this->prepareViewData(null, null));
            }

            $employeeKpi = $this->processKpiDetails($kpiDetails);


            // Get employee grade
            $gradeDetails = $this->fetchEmployeeGrade(
                $employeeKpi['batch_id'] ?? '',
                $employeeKpi['employee_id'] ?? ''
            );




            return view("dashboard.index", $this->prepareViewData($employeeKpi, $gradeDetails));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve appraisal overview', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('toast_error', 'Failed to retrieve appraisal data. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in dashboard index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Display available batches for appraisal
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show()
    {
        try {
            $response = $this->appraisalService->getAllBatches();
            $batches = $response['data'] ?? $response ?? [];

            if (empty($batches)) {
                return view('dashboard.show-batch', ['activeBatches' => []]);
            }

            // Filter batches to get only those with status "OPEN" and active state true
            $filteredBatches = array_filter($batches, function ($batch) {
                return ($batch['status'] ?? null) === 'OPEN' && ($batch['active'] ?? false) === true;
            });

            $activeBatches = [];

            foreach ($filteredBatches as $batch) {
                $activeBatches = [
                    'id' => $batch['id'] ?? null,
                    'batch_name' => $batch['name'] ?? '',
                ];
            }

            return view('dashboard.show-batch', compact('activeBatches'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve batches', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve batches');
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving batches', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again');
        }
    }

    /**
     * Display KPI form for employee editing
     *
     * @param Request $request
     * @param int|string $id KPI ID
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editEmployeeKpi(Request $request, $id)
    {
        try {
            // Get KPIs for the specified employee
            $response = $this->appraisalService->getKpiForEmployee($id);
            $kpis = $response['data'] ?? $response ?? [];

            if (empty($kpis)) {
                return redirect()->back()->with('toast_error', 'No KPIs found for this employee');
            }

            // Initialize variables
            $appraisal = collect();
            $kpiId = $kpis[0]['kpiId'] ?? null;
            $batchId = null;
            $employeeId = null;
            $kpiStatus = 'PENDING';

            // Process each KPI
            foreach ($kpis as $kpi) {
                if ($kpi['kpiActive'] ?? false) {
                    // Filter active sections
                    $activeSections = collect($kpi['sections'] ?? [])->filter(function ($section) {
                        return $section['sectionActive'] ?? false;
                    });

                    $activeSections->transform(function ($section) {
                        $section['metrics'] = collect($section['metrics'] ?? [])->filter(function ($metric) {
                            return $metric['metricActive'] ?? false;
                        });
                        return $section;
                    });

                    $appraisal->push((object) [
                        'kpi' => (object) $kpi,
                        'activeSections' => $activeSections
                    ]);

                    // Collect statuses from sections
                    $statuses = [];
                    if (in_array($kpi['kpiType'] ?? null, ['GLOBAL', 'REGULAR'])) {
                        foreach ($kpi['sections'] ?? [] as $section) {
                            $status = $section['sectionEmpScore']['status'] ?? 'PENDING';
                            $statuses[] = $status;
                        }

                        $uniqueStatuses = array_unique($statuses);
                        $kpiStatus = count($uniqueStatuses) > 0 ? reset($uniqueStatuses) : 'PENDING';
                        $batchId = $kpi['batchId'] ?? null;
                        $employeeId = $kpi['employeeId'] ?? null;
                    }
                }
            }

            // Get employee total KPI score
            $gradeDetails = $this->fetchEmployeeTotalKpiScore($batchId, $employeeId);
            if ($gradeDetails) {
                $gradeDetails['status'] = $kpiStatus;
            } else {
                $gradeDetails = [
                    'kpiScore' => null,
                    'grade' => null,
                    'remark' => null,
                    'recommendation' => null,
                    'status' => $kpiStatus
                ];
            }

            // Get logged in user
            $loggedInUser = $this->getLoggedInUserInformation();
            $userId = $loggedInUser->id ?? null;

            // Get employee grades (using GetKpiGradeController service)
            $submittedEmployeeGrade = null;
            $supervisorGradeForEmployee = null;

            if ($kpiId && $batchId && $userId) {
                try {
                    $gradeInfo = $this->gradeController->getGrade($kpiId, $batchId, $userId);
                    $submittedEmployeeGrade = $gradeInfo->submittedEmployeeGrade ?? null;
                    $supervisorGradeForEmployee = $gradeInfo->supervisorGradeForEmployee ?? null;
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch grade information', ['error' => $e->getMessage()]);
                }
            }

            return view("dashboard.test-employee-kpi-form", compact(
                'appraisal',
                'batchId',
                'gradeDetails',
                'kpiStatus',
                'employeeId',
                'userId',
                'submittedEmployeeGrade',
                'supervisorGradeForEmployee'
            ));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve KPIs for editing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve KPI data');
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving KPIs for editing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again');
        }
    }

    /**
     * Display employee KPI information
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showEmployeeKpi()
    {
        try {
            // Get all KPIs for the employee
            $response = $this->appraisalService->getAllKpisForEmployee();
            $kpis = $response['data'] ?? $response ?? [];

            if (empty($kpis) || !isset($kpis[0]['kpiId'])) {
                return view("dashboard.show-employee-kpi", [
                    'employeeKpi' => null,
                    'gradeDetails' => null
                ]);
            }

            $firstKpiId = $kpis[0]['kpiId'];

            // Get detailed KPI information
            $kpiResponse = $this->appraisalService->getKpiForEmployee($firstKpiId);
            $kpiDetails = $kpiResponse['data'] ?? $kpiResponse ?? [];

            if (empty($kpiDetails)) {
                return view("dashboard.show-employee-kpi", [
                    'employeeKpi' => null,
                    'gradeDetails' => null
                ]);
            }

            // Calculate section counts and status
            $globalSectionCount = 0;
            $regularSectionCount = 0;
            $batchId = null;
            $employeeId = null;
            $kpiStatus = 'PENDING';

            foreach ($kpiDetails as $kpi) {
                if (($kpi['kpiType'] ?? null) === 'GLOBAL') {
                    $globalSectionCount += count($kpi['sections'] ?? []);
                }

                if (($kpi['kpiType'] ?? null) === 'REGULAR') {
                    $regularSectionCount += count($kpi['sections'] ?? []);
                }

                // Collect statuses
                if (in_array($kpi['kpiType'] ?? null, ['GLOBAL', 'REGULAR'])) {
                    $statuses = [];
                    foreach ($kpi['sections'] ?? [] as $section) {
                        $status = $section['sectionEmpScore']['status'] ?? 'PENDING';
                        $statuses[] = $status;
                    }

                    $uniqueStatuses = array_unique($statuses);
                    $kpiStatus = count($uniqueStatuses) > 0 ? reset($uniqueStatuses) : 'PENDING';
                    $batchId = $kpi['batchId'] ?? null;
                    $employeeId = $kpi['employeeId'] ?? null;
                }
            }

            $totalSectionCount = $globalSectionCount + $regularSectionCount;

            // Get employee grade details
            $gradeDetails = $this->fetchEmployeeTotalKpiScore($batchId, $employeeId);
            if ($gradeDetails) {
                $gradeDetails['status'] = $kpiStatus;
            } else {
                $gradeDetails = [
                    'kpiScore' => null,
                    'grade' => null,
                    'remark' => null,
                    'recommendation' => null,
                    'status' => $kpiStatus
                ];
            }

            // Prepare employee KPI information
            $employeeKpi = [
                'id' => $kpiDetails[0]['kpiId'] ?? null,
                'batch_id' => $kpiDetails[0]['batchId'] ?? null,
                'kpi_name' => $kpiDetails[0]['kpiName'] ?? 'N/A',
                'section_count' => $totalSectionCount
            ];

            return view("dashboard.show-employee-kpi", compact('employeeKpi', 'gradeDetails'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve employee KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve KPIs');
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving employee KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again');
        }
    }

    /**
     * Display supervisor KPI scoring form for employee
     *
     * @param Request $request
     * @param int|string $id KPI ID
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showEmployeeSupervisorKpiScore(Request $request, $id)
    {
        try {
            // Get KPIs for the specified employee
            $response = $this->appraisalService->getKpiForEmployee($id);
            $kpis = $response['data'] ?? $response ?? [];

            if (empty($kpis)) {
                return redirect()->back()->with('toast_error', 'No KPIs found');
            }

            $batchId = $kpis[0]['batchId'] ?? null;

            // Filter KPIs with active sections and metrics
            $appraisal = collect($kpis)->filter(function ($kpi) {
                if (!($kpi['kpiActive'] ?? false)) {
                    return false;
                }

                // Filter active sections
                $activeSections = collect($kpi['sections'] ?? [])->filter(function ($section) {
                    return $section['sectionActive'] ?? false;
                });

                if ($activeSections->isEmpty()) {
                    return false;
                }

                // Filter active metrics in sections
                $activeSections->transform(function ($section) {
                    $section['metrics'] = collect($section['metrics'] ?? [])->filter(function ($metric) {
                        return $metric['metricActive'] ?? false;
                    });
                    return $section;
                });

                return $activeSections->filter(function ($section) {
                    return collect($section['metrics'] ?? [])->isNotEmpty();
                })->isNotEmpty();
            })->map(function ($kpi) {
                return (object) $kpi;
            });

            return view("dashboard.employee-supervisor-kpi-score-form", compact('appraisal', 'batchId'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve KPIs for supervisor scoring', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve KPIs');
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving KPIs for supervisor scoring', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again');
        }
    }

    /**
     * Display probing scoring form for employee
     *
     * @param Request $request
     * @param int|string $id KPI ID
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showEmployeeProbe(Request $request, $id)
    {
        try {
            // Get KPIs for the specified employee
            $response = $this->appraisalService->getKpiForEmployee($id);
            $kpis = $response['data'] ?? $response ?? [];

            if (empty($kpis)) {
                return redirect()->back()->with('toast_error', 'No KPIs found');
            }

            // Initialize variables
            $appraisal = collect();
            $batchId = null;
            $kpiStatus = 'PENDING';

            // Process each KPI
            foreach ($kpis as $kpi) {
                if ($kpi['kpiActive'] ?? false) {
                    // Filter active sections
                    $activeSections = collect($kpi['sections'] ?? [])->filter(function ($section) {
                        return $section['sectionActive'] ?? false;
                    });

                    $activeSections->transform(function ($section) {
                        $section['metrics'] = collect($section['metrics'] ?? [])->filter(function ($metric) {
                            return $metric['metricActive'] ?? false;
                        });
                        return $section;
                    });

                    $appraisal->push((object) [
                        'kpi' => (object) $kpi,
                        'activeSections' => $activeSections
                    ]);

                    // Get status from first section
                    if (!empty($kpi['sections'])) {
                        $firstSection = $kpi['sections'][0];
                        $kpiStatus = $firstSection['sectionEmpScore']['status'] ?? 'PENDING';
                    }

                    $batchId = $kpi['batchId'] ?? null;
                }
            }

            // Get logged in user information
            $loggedInUser = $this->getLoggedInUserInformation();
            $employeeId = $loggedInUser->id ?? null;

            return view("dashboard.test-employee-probe-form", compact('appraisal', 'batchId', 'kpiStatus', 'employeeId'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve KPIs for probing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Sorry, failed to retrieve KPIs');
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving KPIs for probing', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again');
        }
    }

    /**
     * Display my KPIs page
     *
     * @return \Illuminate\View\View
     */
    public function my_kpis()
    {
        return view("dashboard.my-kpis");
    }

    /**
     * Display KPI setup page
     *
     * @return \Illuminate\View\View
     */
    public function kpi_setup()
    {
        return view("kpi-setup.kpi-setup");
    }

    /**
     * Display score setup page
     *
     * @return \Illuminate\View\View
     */
    public function score_setup()
    {
        return view("kpi-setup.score-setup");
    }

    /**
     * Get information about the currently logged-in user
     *
     * @return object|null User information object or null on failure
     */
    public function getLoggedInUserInformation()
    {
        try {
            $response = $this->hrmsService->getCurrentEmployeeInformation();
            $user = $response['data'] ?? null;
            return (object) $user;
        } catch (ApiException $e) {
            Log::warning('Failed to retrieve logged-in user information', [
                'message' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::warning('Exception occurred while retrieving user information', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Fetch employee grade details for a batch
     *
     * @param string|int $batchId The batch ID
     * @param string|int $employeeId The employee ID
     * @return array|null Grade details or null on failure
     */
    private function fetchEmployeeGrade($batchId, $employeeId)
    {
        if (empty($batchId) || empty($employeeId)) {

            return [
                'kpiScore' => null,
                'grade' => null,
                'remark' => null,
                'status' => '---'
            ];
        }



        try {

            // Use getEmployeeTotalKpiScore with PUT request (matches old implementation)
            $response = $this->appraisalService->getEmployeeTotalKpiScore([
                'batchId' => $batchId,
                'employeeId' => $employeeId
            ]);

            // Response is already the grade data, not wrapped in ['data']
            $grade = $response;

            if ($grade && !empty($grade)) {
                return [
                    'kpiScore' => $grade['totalKpiScore'] ?? null,
                    'grade' => $grade['grade'] ?? null,
                    'remark' => $grade['remark'] ?? null,
                    'recommendation' => $grade['recommendation'] ?? null,
                    'status' => $grade['status'] ?? '---'
                ];
            }
            return [
                'kpiScore' => null,
                'grade' => null,
                'remark' => null,
                'status' => '---'
            ];
        } catch (ApiException $e) {
            Log::warning('Failed to fetch employee grade', [
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'message' => $e->getMessage(),
            ]);
            return [
                'kpiScore' => null,
                'grade' => null,
                'remark' => null,
                'status' => '---'
            ];
        }
    }

    /**
     * Fetch employee total KPI score
     *
     * This method calls the employee-total-kpiscore endpoint to get the
     * calculated total KPI score for an employee in a batch.
     *
     * @param string|int $batchId The batch ID
     * @param string|int $employeeId The employee ID
     * @return array|null Grade details with total KPI score or null on failure
     */
    private function fetchEmployeeTotalKpiScore($batchId, $employeeId)
    {
        if (empty($batchId) || empty($employeeId)) {
            return [
                'kpiScore' => null,
                'grade' => null,
                'remark' => null,
                'recommendation' => null,
            ];
        }

        try {
            // Use the service method to get employee total KPI score
            $response = $this->appraisalService->getEmployeeTotalKpiScore([
                'batchId' => $batchId,
                'employeeId' => $employeeId
            ]);

            // Response is already the grade data, not wrapped in ['data']
            $grade = $response;

            if ($grade) {
                return [
                    'kpiScore' => $grade['totalKpiScore'] ?? null,
                    'grade' => $grade['grade'] ?? null,
                    'remark' => $grade['remark'] ?? null,
                    'recommendation' => $grade['recommendation'] ?? null,
                ];
            }
            return [
                'kpiScore' => null,
                'grade' => null,
                'remark' => null,
                'recommendation' => null,
            ];
        } catch (ApiException $e) {
            Log::warning('Failed to fetch employee total KPI score', [
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'message' => $e->getMessage(),
            ]);
            return [
                'kpiScore' => null,
                'grade' => null,
                'remark' => null,
                'recommendation' => null,
            ];
        }
    }

    /**
     * Process KPI details into standardized format
     *
     * @param array $kpiDetails Raw KPI details from API
     * @return array Processed KPI details
     */
    private function processKpiDetails(array $kpiDetails): array
    {
        if (empty($kpiDetails)) {
            return [];
        }

        $globalSectionCount = 0;
        $regularSectionCount = 0;
        $kpiStatus = 'PENDING';
        $batchId = $batchName = $employeeId = '';

        foreach ($kpiDetails as $kpi) {
            $sections = $kpi['sections'] ?? [];
            $sectionCount = count($sections);

            if ($kpi['kpiType'] === 'GLOBAL') {
                $globalSectionCount += $sectionCount;
            } elseif ($kpi['kpiType'] === 'REGULAR') {
                $regularSectionCount += $sectionCount;
            }

            if (!empty($sections)) {
                $firstSection = $sections[0];
                $kpiStatus = $firstSection['sectionEmpScore']['status'] ?? $kpiStatus;
            }

            $batchId = $kpi['batchId'] ?? $batchId;
            $batchName = $kpi['batchName'] ?? $batchName;
            $employeeId = $kpi['employeeId'] ?? $employeeId;
        }

        return [
            'id' => $kpiDetails[0]['kpiId'] ?? '---',
            'batch_id' => $batchId,
            'kpi_name' => $kpiDetails[0]['kpiName'] ?? '---',
            'batch_name' => $batchName,
            'section_count' => $globalSectionCount + $regularSectionCount,
            'employee_id' => $employeeId
        ];
    }

    /**
     * Prepare view data with KPI and grade information
     *
     * @param array|null $employeeKpi Employee KPI information
     * @param array|null $gradeDetails Grade and score details
     * @return array Formatted view data
     */
    private function prepareViewData(?array $employeeKpi, ?array $gradeDetails): array
    {
        return [
            'employeeKpi' => $employeeKpi ?? [
                'id' => null,
                'batch_id' => null,
                'employee_id' => null,
                'kpi_name' => 'No KPI assigned',
                'kpi_type' => 'REGULAR',
                'kpi_active' => false,
                'sections' => [],
                'kpi_status' => 'PENDING',
                'grade' => 'N/A',
                'score' => 0,
                'remark' => 'No data available',
            ],
            'gradeDetails' => $gradeDetails ?? [
                'submittedEmployeeGrade' => null,
                'supervisorGradeForEmployee' => null,
                'grade' => 'N/A',
                'score' => 0,
            ],
        ];
    }
}
