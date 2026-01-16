<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitSupervisorScoreRequest;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupervisorScoreController extends Controller
{
    private AppraisalApiService $appraisalService;
    private GetKpiGradeController $gradeController;

    public function __construct(
        AppraisalApiService $appraisalService,
        GetKpiGradeController $gradeController
    ) {
        $this->appraisalService = $appraisalService;
        $this->gradeController = $gradeController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $responseSup = $this->appraisalService->getPendingSupervisorScoringKpi();
            $responseProb = $this->appraisalService->getPendingProbScoringKpi();

            $employeeSupDetails = $responseSup['data'] ?? $responseSup ?? [];
            $employeeProbDetails = $responseProb['data'] ?? $responseProb ?? [];

            return view('dashboard.supervisor.show-employee', compact('employeeSupDetails', 'employeeProbDetails'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve supervisor scoring KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('toast_error', 'Failed to retrieve employee appraisals. Please contact support.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in supervisor index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
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
        try {
            $data = [
                'employeeId' => (int) $employeeId,
                'kpiId' => (int) $kpiId,
                'batchId' => (int) $batchId
            ];

            // Get KPI data based on type
            $response = $type === 'prob'
                ? $this->appraisalService->getProbScoringKpi($data)
                : $this->appraisalService->getSupervisorScoringKpi($data);

            $kpis = $response['data'] ?? $response ?? [];
            $appraisal = collect();
            $kpiStatus = 'PENDING'; // Initialize with default value

            // Process each KPI
            foreach ($kpis as $kpi) {
                if ($kpi['kpiActive'] ?? false) {
                    // Filter active sections
                    $activeSections = collect($kpi['sections'] ?? [])->filter(function ($section) {
                        return $section['sectionActive'] ?? false;
                    });

                    // Transform sections to include metrics and convert to objects
                    $activeSections = $activeSections->map(function ($section) {
                        // Filter active metrics and convert to objects
                        $metrics = collect($section['metrics'] ?? [])->filter(function ($metric) {
                            return $metric['metricActive'] ?? false;
                        })->map(function ($metric) {
                            // Convert metricEmpScore to object if exists
                            if (isset($metric['metricEmpScore'])) {
                                $metric['metricEmpScore'] = (object) $metric['metricEmpScore'];
                            }
                            return (object) $metric;
                        });

                        // Convert sectionEmpScore to object if exists
                        if (isset($section['sectionEmpScore'])) {
                            $section['sectionEmpScore'] = (object) $section['sectionEmpScore'];
                        }

                        $section['metrics'] = $metrics;
                        return (object) $section;
                    });

                    // Add the KPI and its sections to the appraisal
                    $appraisal->push((object) [
                        'kpi' => (object) $kpi,
                        'activeSections' => $activeSections
                    ]);

                    // Only check status for supervisor type
                    if ($type !== 'prob' && !empty($kpi['sections'])) {
                        $firstSection = $kpi['sections'][0];
                        $kpiStatus = $firstSection['sectionEmpScore']['status'] ?? 'PENDING';
                    }
                }
            }

            // Get grade information
            $gradeInfo = $this->gradeController->getGrade($kpiId, $batchId, $employeeId);
            $submittedEmployeeGrade = $gradeInfo->submittedEmployeeGrade;
            $supervisorGradeForEmployee = $gradeInfo->supervisorGradeForEmployee;

            // Determine view based on type
            $view = $type === 'prob'
                ? "dashboard.probe-supervisor.score-employee-form"
                : "dashboard.supervisor.score-employee-form";

            // Prepare view data
            $viewData = $type === 'prob'
                ? compact('appraisal', 'employeeId', 'kpiId', 'batchId', 'submittedEmployeeGrade', 'supervisorGradeForEmployee')
                : compact('appraisal', 'kpiStatus', 'employeeId', 'kpiId', 'batchId', 'submittedEmployeeGrade', 'supervisorGradeForEmployee');

            return view($view, $viewData);
        } catch (ApiException $e) {
            Log::error('Failed to retrieve KPI for editing', [
                'kpiId' => $kpiId,
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'type' => $type,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('toast_error', 'Failed to retrieve employee appraisal. Please contact support.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in supervisor editKpi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubmitSupervisorScoreRequest $request)
    {
        try {
            $scoreData = [];
            $successMessage = '';

            // Prepare score data based on which type is being submitted
            if ($request->filled('metricSupScore')) {
                $scoreData = [
                    'scoreId' => $request->input('scoreId'),
                    'metricSupScore' => (float) $request->input('metricSupScore'),
                    'supervisorComment' => $request->input('supervisorComment', ''),
                ];
                $successMessage = 'Metric score and comment submitted successfully!';
            } elseif ($request->filled('sectionSupScore')) {
                $scoreData = [
                    'scoreId' => $request->input('scoreId'),
                    'sectionSupScore' => (float) $request->input('sectionSupScore'),
                    'supervisorComment' => $request->input('supervisorComment', ''),
                ];
                $successMessage = 'Section score and comment submitted successfully!';
            }

            // Submit the score via the service
            $this->appraisalService->submitSupervisorScore($scoreData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                ]);
            }

            return back()->with('toast_success', $successMessage);
        } catch (ApiException $e) {
            Log::error('Supervisor score submission failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit score. Please try again.'
                ], 422);
            }

            return back()->with('toast_error', 'Failed to submit score. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during supervisor score submission', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
        try {
            $request->validate([
                'scoreId' => 'required|integer|min:1',
                'metricProbScore' => 'nullable|numeric|min:0|max:100',
                'sectionProbScore' => 'nullable|numeric|min:0|max:100',
                'probComment' => 'nullable|string|max:1000',
            ]);

            $scoreData = [];
            $successMessage = '';

            // Prepare score data based on which type is being submitted
            if ($request->filled('metricProbScore')) {
                $scoreData = [
                    'scoreId' => (int) $request->input('scoreId'),
                    'metricProbScore' => (float) $request->input('metricProbScore'),
                    'probComment' => $request->input('probComment', ''),
                ];
                $successMessage = 'Metric score and comment submitted successfully!';
            } elseif ($request->filled('sectionProbScore')) {
                $scoreData = [
                    'scoreId' => (int) $request->input('scoreId'),
                    'sectionProbScore' => (float) $request->input('sectionProbScore'),
                    'probComment' => $request->input('probComment', ''),
                ];
                $successMessage = 'Section score and comment submitted successfully!';
            }

            if (empty($scoreData)) {
                return back()->with('toast_error', 'No scores submitted. Please provide a score.');
            }

            // Submit the probing score via the service
            $this->appraisalService->submitProbScore($scoreData);

            return back()->with('toast_success', $successMessage);
        } catch (ApiException $e) {
            Log::error('Probing score submission failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'Failed to submit score. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during probing score submission', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }
}
