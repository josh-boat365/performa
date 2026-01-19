<?php

namespace App\Http\Controllers;

use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Services\AppraisalApiService;
use App\Services\HrmsApiService;
use App\Exceptions\ApiException;

class ReportController extends Controller
{
    private AppraisalApiService $appraisalService;
    private HrmsApiService $hrmsService;

    public function __construct(AppraisalApiService $appraisalService, HrmsApiService $hrmsService)
    {
        $this->appraisalService = $appraisalService;
        $this->hrmsService = $hrmsService;
    }

    /**
     * Recursively convert associative arrays to objects
     * Indexed arrays (lists) are preserved as arrays for count() compatibility
     */
    private function arrayToObject($array)
    {
        if (!is_array($array)) {
            return $array;
        }

        // Check if this is an indexed array (sequential numeric keys starting from 0)
        // These should remain as arrays for count() and foreach to work properly
        if (array_is_list($array)) {
            return array_map(fn($item) => $this->arrayToObject($item), $array);
        }

        // Associative arrays become objects
        return (object) array_map(fn($item) => $this->arrayToObject($item), $array);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'batchId' => 'nullable',
            'departmentId' => 'nullable',
            'employeeId' => 'nullable',
            'branchId' => 'nullable',
        ]);

        // Filter non-empty parameters for the API request
        $filters = array_filter($request->only(['batchId', 'branchId', 'departmentId', 'employeeId']));
        $data = empty($filters) ? ['batchId' => ''] : $filters;

        // Fetch reports and convert to objects recursively
        $reportsData = [];
        try {
            $reportsData = $this->appraisalService->getReports($data);
        } catch (ApiException $e) {
            Log::error('API Error getting reports', ['message' => $e->getMessage()]);
        }

        if (!is_array($reportsData)) {
            $reportsData = [];
        }

        // Handle wrapped response (data key) or direct array
        if (isset($reportsData['data']) && is_array($reportsData['data'])) {
            $reportsData = $reportsData['data'];
        }

        // Log the raw response for debugging
        Log::info('Reports API Response', [
            'filter_data' => $data,
            'response_type' => gettype($reportsData),
            'response_count' => count($reportsData),
            'first_item' => isset($reportsData[0]) ? (array)$reportsData[0] : 'empty',
        ]);

        $reports = collect($reportsData)->map(fn($report) => $this->arrayToObject($report))->values();

        // Fetch all supporting data with individual error handling
        $batches = collect();
        try {
            $batches = collect($this->appraisalService->getAllBatches() ?? [])
                ->map(fn($batch) => [
                    'batchId' => $batch['id'] ?? 'N/A',
                    'batchName' => $batch['name'] ?? 'N/A',
                    'batchStatus' => $batch['status'] ?? 'N/A',
                ])->values();
        } catch (ApiException $e) {
            Log::warning('Failed to fetch batches for reports filter', ['message' => $e->getMessage()]);
        }

        $departments = collect();
        try {
            $departments = collect($this->hrmsService->getAllDepartments() ?? [])
                ->map(fn($department) => [
                    'departmentId' => $department['id'] ?? 'N/A',
                    'departmentName' => $department['name'] ?? 'N/A',
                ])->values();
        } catch (ApiException $e) {
            Log::warning('Failed to fetch departments for reports filter', ['message' => $e->getMessage()]);
        }

        $branches = collect();
        try {
            $branches = collect($this->hrmsService->getAllBranches() ?? [])
                ->map(fn($branch) => [
                    'branchId' => $branch['id'] ?? 'N/A',
                    'branchName' => $branch['name'] ?? 'N/A',
                ])->values();
        } catch (ApiException $e) {
            Log::warning('Failed to fetch branches for reports filter', ['message' => $e->getMessage()]);
        }

        $employees = collect();
        try {
            $employees = collect($this->hrmsService->getAllEmployees() ?? [])
                ->map(fn($employee) => [
                    'employeeId' => $employee['id'] ?? 'N/A',
                    'employeeStaffID' => $employee['staffNumber'] ?? 'N/A',
                    'employeeName' => trim(($employee['firstName'] ?? '') . ' ' . ($employee['surname'] ?? '')) ?: 'N/A',
                ])->values();
        } catch (ApiException $e) {
            Log::warning('Failed to fetch employees for reports filter', ['message' => $e->getMessage()]);
        }

        // Extract unique KPIs from reports data
        $kpis = $reports->flatMap(fn($report) => $report->employees ?? [])
            ->flatMap(fn($employee) => $employee->scores ?? [])
            ->map(fn($score) => [
                'kpiId' => $score->kpiId ?? 'N/A',
                'kpiName' => $score->kpiName ?? 'N/A',
            ])->unique('kpiId')->values();

        // Pass data to the view
        return view('reports.index', compact('reports', 'batches', 'branches', 'departments', 'employees', 'kpis'));
    }


    public function showEmployeeSummary($employeeId, $batchId)
    {
        try {
            $data = ['employeeId' => $employeeId, 'batchId' => $batchId];
            $employeeReportData = $this->appraisalService->getReports($data);
            $empData = $this->hrmsService->getAllEmployees();

            $employee = collect($employeeReportData ?? [])
                ->map(fn($report) => $this->arrayToObject($report))
                ->filter(fn($report) => isset($report->batchId) && $report->batchId == $batchId)
                ->values();

            $employeeData = collect($empData)->map(fn($emp) => $this->arrayToObject($emp))
                ->firstWhere('id', $employeeId);
            $employeeStaffNumber = $employeeData->staffNumber ?? 'N/A';
            $employeeBranch = $employeeData->branch->name ?? ($employeeData->branchName ?? 'N/A');

            return view('reports.employee_summary', compact('employee', 'employeeId', 'employeeStaffNumber', 'employeeBranch'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch employee summary', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', 'Failed to fetch employee summary. Please try again.');
        }
    }





    /**
     * Show the form for creating a new resource.
     */
    public function filter(Request $request)
    {
        try {
            $request->validate(
                [
                    'batchId' => 'nullable|int',
                    'departmentId' => 'nullable|int',
                    'kpiId' => 'nullable|int',
                    'employeeId' => 'nullable|int'
                ]
            );

            $data = [
                'batchId' => $request->batchId,
                'departmentId' => $request->departmentId,
                'kpiId' => $request->kpiId,
                'employeeId' => $request->employeeId,
            ];

            $reportsData = $this->appraisalService->getReports($data) ?? [];
            $reports = collect($reportsData)->map(fn($report) => $this->arrayToObject($report))->values();

            // Transform the reports into array of objects
            $formattedReports = $reports->map(function ($report) {
                return (object) [
                    'batchName' => $report->batchName ?? 'N/A',
                    'employeeName' => $report->employeeName ?? 'N/A',
                    'grade' => $report->grade ?? 'N/A',
                    'score' => $report->score ?? 'N/A',
                    'remark' => $report->remark ?? 'N/A',
                    'departmentName' => $report->departmentName ?? 'N/A',
                    'roleName' => $report->roleName ?? 'N/A',
                    'supervisorName' => $report->supervisorName ?? 'N/A',
                    'probeName' => $report->probeName ?? 'N/A',
                ];
            });

            return response()->json($formattedReports);
        } catch (ApiException $e) {
            Log::error('Failed to filter reports', ['message' => $e->getMessage()]);
            return response()->json([], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function generateEmployeePdf($employeeId)
    {
        try {
            // Prepare the request data
            $data = ['employeeId' => $employeeId];

            // Retrieve employee data
            $employee = $this->appraisalService->getReports($data);

            $pdf = PDF::loadView('reports.test-report', ['employee' => $employee])
                ->setPaper('a4', 'landscape')
                ->setOptions(['defaultFont' => 'arial']);

            return $pdf->stream('document.pdf');
        } catch (ApiException $e) {
            Log::error('Failed to generate employee PDF', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', 'Failed to generate PDF. Please try again.');
        }
    }

    public function generateEmployeePdfReport($employeeId)
    {
        try {
            // Prepare the request data
            $data = ['employeeId' => $employeeId];

            // Retrieve employee data and convert to objects
            $employeeResponseData = $this->appraisalService->getReports($data);
            $employeeResponseData = collect($employeeResponseData)->map(fn($report) => $this->arrayToObject($report))->values();

            // Handle cases where employee data is not available
            if (!$employeeResponseData || empty($employeeResponseData)) {
                return response()->json(['error' => 'Employee data not found or empty.'], 404);
            }

            // Initialize FPDF
            $fpdf = new Fpdf();
            $fpdf->AddPage();
            $fpdf->SetFont('Arial', '', 12);

            // Design header
            $fpdf->Image(public_path('bpsl_imgs/purple-logo-bpsl.png'), 10, 10, 30); // Company logo
            $fpdf->SetFont('Arial', 'B', 16);
            $fpdf->Cell(190, 10, 'Employee Appraisal Report', 0, 1, 'C');
            $fpdf->SetFont('Arial', '', 12);
            $fpdf->Cell(190, 10, now()->format('D M d Y g:i A'), 0, 1, 'C');
            $fpdf->Ln(10);

            // Iterate through employees using object syntax
            foreach ($employeeResponseData as $employeeData) {
                foreach ($employeeData->employees as $employee) {
                    // Employee Details Header
                    $fpdf->SetFont('Arial', 'B', 14);
                    $fpdf->Cell(190, 10, "Batch: {$employeeData->batchName}", 0, 1, 'C');
                    $fpdf->Ln(5);

                    // Employee Information
                    $fpdf->SetFont('Arial', '', 12);
                    $fpdf->Cell(95, 10, "Name: {$employee->employeeName}", 0, 0, 'L');
                    $fpdf->Cell(95, 10, "Employee ID: " . ($employee->staffNumber ?? '444'), 0, 1, 'L');
                    $fpdf->Cell(95, 10, "Role: {$employee->roleName}", 0, 0, 'L');
                    $fpdf->Cell(95, 10, "Branch: Head Office", 0, 1, 'L');
                    $fpdf->Cell(95, 10, "Department: {$employee->departmentName}", 0, 0, 'L');
                    $fpdf->Cell(95, 10, "Grade: " . ($employee->totalScore->grade ?? '___'), 0, 1, 'L');
                    $fpdf->Cell(95, 10, "Score: " . ($employee->totalScore->totalKpiScore ?? '___'), 0, 0, 'L');
                    $fpdf->Cell(95, 10, "Remark: " . ($employee->totalScore->remark ?? '___'), 0, 1, 'L');
                    $fpdf->Ln(10);

                    // Appraisal Questions and Scores
                    foreach ($employee->scores as $index => $score) {
                        $fpdf->SetFont('Arial', 'B', 12);
                        $fpdf->Cell(190, 10, "Question " . ($index + 1) . ": {$score->questionName}", 0, 1, 'L');
                        $fpdf->SetFont('Arial', '', 10);
                        $fpdf->MultiCell(190, 10, $score->questionDescription ?? '___', 0, 'L');
                        $fpdf->Ln(5);

                        $fpdf->Cell(95, 10, "Employee Section Score: " . ($score->sectionEmpScore ?? '___'), 0, 0, 'L');
                        $fpdf->Cell(95, 10, "Supervisor Section Score: " . ($score->sectionSupScore ?? '___'), 0, 1, 'L');

                        if ($score->metricEmpScore === null && isset($score->prob) && $score->prob) {
                            $fpdf->Cell(95, 10, "Probe Supervisor Score: " . ($score->sectionProbScore ?? '___'), 0, 1, 'L');
                        }

                        // Comments
                        $fpdf->SetFont('Arial', '', 10);
                        $fpdf->MultiCell(190, 10, "Employee Comment: " . ($score->employeeComment ?? '___'), 0, 'L');
                        $fpdf->MultiCell(190, 10, "Supervisor Comment: " . ($score->supervisorComment ?? '___'), 0, 'L');
                        if ($score->metricEmpScore === null && isset($score->prob) && $score->prob) {
                            $fpdf->MultiCell(190, 10, "Probe Supervisor Comment: " . ($score->probComment ?? '___'), 0, 'L');
                        }

                        $fpdf->Ln(10);
                    }

                    // Separator between employees
                    $fpdf->Ln(5);
                    $fpdf->Cell(190, 0, '', 'T', 1, 'C'); // Horizontal line
                    $fpdf->Ln(10);
                }
            }

            // Output PDF
            $fpdf->Output('I', 'Employee_Appraisal_Report.pdf');
        } catch (ApiException $e) {
            Log::error('Failed to generate PDF report', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', 'Failed to generate PDF report. Please try again.');
        }
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
