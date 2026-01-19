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

            // Get logged in user
            $loggedInUser = $this->getLoggedInUserInformation();
            $employeeId = $loggedInUser->id ?? null;

            if (!$employeeId) {
                return redirect()->route('login')->with('toast_error', 'Could not identify user. Please login again.');
            }

            // Get all batches
            $batchesResponse = $this->appraisalService->getAllBatches();
            $allBatches = $batchesResponse['data'] ?? $batchesResponse ?? [];

            if (empty($allBatches)) {
                return view("dashboard.index", $this->prepareViewData(null, null, []));
            }

            // Get current KPI (first KPI for employee)
            $kpisResponse = $this->appraisalService->getAllKpisForEmployee();
            $kpis = $kpisResponse['data'] ?? $kpisResponse ?? [];

            $currentBatchId = null;
            $currentKpiDetails = null;
            $employeeKpi = null;

            if (!empty($kpis) && isset($kpis[0]['kpiId'])) {
                $firstKpiId = $kpis[0]['kpiId'];
                $currentBatchId = $kpis[0]['batchId'] ?? null;

                // Get details for first KPI
                $kpiDetailsResponse = $this->appraisalService->getKpiForEmployee($firstKpiId, $currentBatchId);
                $currentKpiDetails = $kpiDetailsResponse['data'] ?? $kpiDetailsResponse ?? [];

                if (!empty($currentKpiDetails)) {
                    $employeeKpi = $this->processKpiDetails($currentKpiDetails);
                }
            }

            // Build batch scores collection - fetch scores for all batches
            $batchScores = collect();

            foreach ($allBatches as $batch) {
                $batchId = $batch['id'] ?? $batch['batchId'] ?? null;
                if (!$batchId) {
                    continue;
                }

                // Fetch employee scores for this batch
                $gradeDetails = $this->fetchEmployeeTotalKpiScore($batchId, $employeeId);

                $batchScores->push((object) [
                    'batchId' => $batchId,
                    'batchName' => $batch['name'] ?? $batch['batchName'] ?? 'Unknown',
                    'batchYear' => $batch['year'] ?? null,
                    'isCurrentBatch' => $batchId == $currentBatchId,
                    'kpiScore' => $gradeDetails['kpiScore'] ?? null,
                    'grade' => $gradeDetails['grade'] ?? null,
                    'remark' => $gradeDetails['remark'] ?? null,
                    'recommendation' => $gradeDetails['recommendation'] ?? null,
                    'status' => $gradeDetails ? 'COMPLETED' : 'PENDING'
                ]);
            }

            return view("dashboard.index", $this->prepareViewData($employeeKpi, null, $batchScores));
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
    public function editEmployeeKpi(Request $request, $id, $batchId)
    {
        try {
            // Get KPIs for the specified employee
            $response = $this->appraisalService->getKpiForEmployee($id, $batchId);
            $kpisData = $response['data'] ?? $response ?? [];

            // Convert all arrays to objects recursively (mimics old project's $response->object())
            $kpis = $this->arrayToObject($kpisData);
            // dd($kpis);

            if (empty($kpis)) {
                return redirect()->back()->with('toast_error', 'No KPIs found for this employee');
            }

            // Initialize an empty collection for active appraisals
            $appraisal = collect();

            $kpiId = $kpis[0]->kpiId ?? null;

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

                    // Collect statuses from sections
                    $statuses = [];
                    if (in_array($kpi->kpiType ?? null, ['GLOBAL', 'REGULAR'])) {
                        foreach ($kpi->sections as $section) {
                            $status = $section->sectionEmpScore->status ?? 'PENDING';
                            $statuses[] = $status;
                        }

                        $uniqueStatuses = array_unique($statuses);
                        $kpiStatus = count($uniqueStatuses) > 0 ? reset($uniqueStatuses) : 'PENDING';
                        $batchId = $kpi->batchId;
                        $employeeId = $kpi->employeeId;
                    }
                }
            }

            // Get employee total KPI score
            $gradeDetails = $this->fetchEmployeeTotalKpiScore($batchId ?? null, $employeeId ?? null);
            if ($gradeDetails) {
                $gradeDetails['status'] = $kpiStatus ?? 'PENDING';
            } else {
                $gradeDetails = [
                    'kpiScore' => null,
                    'grade' => null,
                    'remark' => null,
                    'recommendation' => null,
                    'status' => $kpiStatus ?? 'PENDING'
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

            return view("dashboard.employee-kpi-form", compact(
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
        // Debug: Log session data to trace toast messages
        Log::debug('showEmployeeKpi: Session data on page load', [
            'has_toast_success' => session()->has('toast_success'),
            'toast_success' => session('toast_success'),
            'has_toast_error' => session()->has('toast_error'),
            'toast_error' => session('toast_error'),
            'has_errors' => session()->has('errors'),
            'all_flash_keys' => array_keys(session()->all()),
        ]);

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
            $firstBatchId = $kpis[0]['batchId'] ?? null;

            // Get detailed KPI information
            $kpiResponse = $this->appraisalService->getKpiForEmployee($firstKpiId, $firstBatchId);
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

            // Filter for REGULAR KPI type
            $regularKpiDetails = collect($kpiDetails)->filter(function ($kpi) {
                return ($kpi['kpiType'] ?? null) === 'REGULAR';
            })->first();

            // Prepare employee KPI information
            $employeeKpi = [
                'id' => $regularKpiDetails['kpiId'] ?? null,
                'batch_id' => $regularKpiDetails['batchId'] ?? null,
                'kpi_name' => $regularKpiDetails['kpiName'] ?? 'N/A',
                'section_count' => $totalSectionCount
            ];

            return view("dashboard.show-employee-kpi", compact('employeeKpi', 'gradeDetails'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve employee KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Return view with empty data instead of redirecting
            return view("dashboard.show-employee-kpi", [
                'employeeKpi' => null,
                'gradeDetails' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Exception occurred while retrieving employee KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Return view with empty data instead of redirecting
            return view("dashboard.show-employee-kpi", [
                'employeeKpi' => null,
                'gradeDetails' => null
            ]);
        }
    }

    /**
     * Display supervisor KPI scoring form for employee
     *
     * @param Request $request
     * @param int|string $id KPI ID
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showEmployeeSupervisorKpiScore(Request $request, $id, $batchId)
    {
        try {
            // Get KPIs for the specified employee
            $response = $this->appraisalService->getKpiForEmployee($id, $batchId);
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
    public function showEmployeeProbe(Request $request, $id, $batchId)
    {
        try {
            // Get KPIs for the specified employee
            $response = $this->appraisalService->getKpiForEmployee($id, $batchId);
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
            $user = $response['data'] ?? $response ?? null;

            if (!$user || empty($user)) {
                Log::warning('No user data returned from HRMS service');
                return null;
            }

            // If user is an array, convert to object; if already object, return as is
            if (is_array($user)) {
                return (object) $user;
            }
            return $user;
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
            $payload = [
                'batchId' => (int) $batchId,
                'employeeId' => (int) $employeeId
            ];

            Log::debug('fetchEmployeeGrade - Requesting total KPI score', [
                'payload' => $payload,
                'payload_json' => json_encode($payload)
            ]);

            $response = $this->appraisalService->getEmployeeTotalKpiScore($payload);

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
    /**
     * Recursively convert arrays to objects (mimics $response->object())
     * Keeps arrays as arrays but converts objects/stdClass throughout the structure
     */
    private function arrayToObject($data)
    {
        if (is_array($data)) {
            // Check if it's an associative array (should become object)
            if ($this->isAssociativeArray($data)) {
                $obj = new \stdClass();
                foreach ($data as $key => $value) {
                    $obj->{$key} = $this->arrayToObject($value);
                }
                return $obj;
            }
            // Sequential array - keep as array but convert items
            return array_map(fn($item) => $this->arrayToObject($item), $data);
        } elseif (is_object($data)) {
            // Convert object properties recursively
            foreach ($data as $key => $value) {
                $data->{$key} = $this->arrayToObject($value);
            }
            return $data;
        }
        return $data;
    }

    /**
     * Check if an array is associative (has string keys)
     */
    private function isAssociativeArray(array $arr): bool
    {
        if (empty($arr)) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function fetchEmployeeTotalKpiScore($batchId, $employeeId)
    {
        if (empty($batchId) || empty($employeeId)) {
            return null;
        }

        try {
            // Use the service method to get employee total KPI score
            $payload = [
                'batchId' => (int) $batchId,
                'employeeId' => (int) $employeeId
            ];

            $response = $this->appraisalService->getEmployeeTotalKpiScore($payload);

            // Handle response that might be wrapped in 'data' key
            $grade = $response['data'] ?? $response;

            if (!empty($grade)) {
                return [
                    'kpiScore' => $grade['totalKpiScore'] ?? null,
                    'grade' => $grade['grade'] ?? null,
                    'remark' => $grade['remark'] ?? null,
                    'recommendation' => $grade['recommendation'] ?? null,
                ];
            }
            return null;
        } catch (ApiException $e) {
            // Handle 400 errors (no scores processed yet) gracefully
            if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'Total KPI Score')) {
                Log::debug('No KPI score found for batch/employee', [
                    'batchId' => $batchId,
                    'employeeId' => $employeeId,
                    'message' => $e->getMessage(),
                ]);
                return null;
            }

            Log::warning('Failed to fetch employee total KPI score', [
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'message' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::warning('Exception while fetching employee total KPI score', [
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'message' => $e->getMessage(),
            ]);
            return null;
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
     * @param \Illuminate\Support\Collection|array $batchScores Batch scores collection
     * @return array Formatted view data
     */
    private function prepareViewData(?array $employeeKpi, ?array $gradeDetails, $batchScores = null): array
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
            'batchScores' => $batchScores ?? collect(),
        ];
    }
}
