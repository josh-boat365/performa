<?php

namespace App\Http\Controllers;

use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    /**
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $accessToken = session('api_token');

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

        // Use concurrent requests to fetch data in parallel
        $responses = Http::pool(fn(Pool $pool) => [
            $pool->withToken($accessToken)->timeout(30)->put("http://192.168.1.200:5123/Appraisal/Report", $data),
            $pool->withToken($accessToken)->timeout(15)->get("http://192.168.1.200:5123/Appraisal/Batch"),
            $pool->withToken($accessToken)->timeout(15)->get("http://192.168.1.200:5124/HRMS/Department"),
            $pool->withToken($accessToken)->timeout(15)->get("http://192.168.1.200:5124/HRMS/Branch"),
            $pool->withToken($accessToken)->timeout(15)->get("http://192.168.1.200:5124/HRMS/Employee"),
        ]);

        // Handle main reports response
        if (!$responses[0]->successful()) {
            Log::error('Failed to fetch appraisal reports', ['response' => $responses[0]->body()]);
            $reports = collect();

        } else {
            $reports = collect($responses[0]->object() ?? []);
        }

        // Process batch data
        if (!$responses[1]->successful()) {
            Log::error('Failed to fetch batches', ['response' => $responses[1]->body()]);
            $batches = collect();
        } else {
            $batches = collect($responses[1]->object())->map(fn($batch) => [
                'batchId' => $batch->id ?? 'N/A',
                'batchName' => $batch->name ?? 'N/A',
                'batchStatus' => $batch->status ?? 'N/A',
            ])->values();
        }

        // Process department data
        if (!$responses[2]->successful()) {
            Log::error('Failed to fetch departments', ['response' => $responses[2]->body()]);
            $departments = collect();
        } else {
            $departments = collect($responses[2]->object())->map(fn($department) => [
                'departmentId' => $department->id ?? 'N/A',
                'departmentName' => $department->name ?? 'N/A',
            ])->values();
        }

        // Process branch data
        if (!$responses[3]->successful()) {
            Log::error('Failed to fetch branches', ['response' => $responses[3]->body()]);
            $branches = collect();
        } else {
            $branches = collect($responses[3]->object())->map(fn($branch) => [
                'branchId' => $branch->id ?? 'N/A',
                'branchName' => $branch->name ?? 'N/A',
            ])->values();
        }

        // Process employee data
        if (!$responses[4]->successful()) {
            Log::error('Failed to fetch employees', ['response' => $responses[4]->body()]);
            $employees = collect();
        } else {
            $employees = collect($responses[4]->object())->map(fn($employee) => [
                'employeeId' => $employee->id ?? 'N/A',
                'employeeStaffID' => $employee->staffNumber ?? 'N/A',
                'employeeName' => trim(($employee->firstName ?? '') . ' ' . ($employee->surname ?? '')) ?: 'N/A',
            ])->values();
        }

        // dd($reports);

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


    public function showEmployeeSummary($employeeId)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Retrieve access token from session
        $accessToken = session('api_token');


        // Prepare the request data
        $data = ['employeeId' => $employeeId];

        // Make the API request
        // Fetch employee data based on the employeeId
        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        $responseEmployeeData = Http::withToken($accessToken)
            ->get("http://192.168.1.200:5124/HRMS/Employee");

        $empData = $responseEmployeeData->object();

        $employee = $response->object();

        //Get employee staff number
        $employeeData = collect($empData)->firstWhere('id', $employeeId);
        $employeeStaffNumber = $employeeData->staffNumber ?? 'N/A';
        $employeeBranch = $employeeData->branch->name ?? 'N/A';





        return view('reports.employee_summary', compact('employee', 'employeeId', 'employeeStaffNumber', 'employeeBranch'));
    }





    /**
     * Show the form for creating a new resource.
     */
    public function filter(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        $request->validate(
            [
                'batchId' => 'nullable|int',
                'departmentId' => 'nullable|int',
                'kpiId' => 'nullable|int',
                'employeeId' => 'nullable|int'
            ]
        );
        // dd($request);
        $accessToken = session('api_token');
        $data = [
            'batchId' => $request->batchId,
            'departmentId' => $request->departmentId,
            'kpiId' => $request->kpiId,
            'employeeId' => $request->employeeId,
        ];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        if (!$response->successful()) {
            return response()->json([]);
        }

        // Log the response for debugging
        Log::info('API Response, Filter:', (array) $response->object());

        $reports = collect($response->object() ?? []);

        // Transform the reports into an array of objects
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
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Retrieve employee data
        $accessToken = session('api_token');
        $data = ['employeeId' => $employeeId];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        $employee = $response->object();

        $pdf = PDF::loadView('reports.test-report', ['employee' => $employee])
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'arial']);

        return $pdf->stream('document.pdf');
    }
    public function generateEmployeePdfOld($employeeId)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Retrieve employee data
        $accessToken = session('api_token');
        $data = ['employeeId' => $employeeId];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        $employee = $response->object();

        // Initialize FPDF in Landscape Orientation
        $pdf = new Fpdf('L', 'mm', 'A4'); // 'L' for Landscape
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add Title
        $pdf->Cell(0, 10, 'Employee Performance Summary', 0, 1, 'C');

        foreach ($employee as $employeeData) {
            foreach ($employeeData->employees as $emp) {
                // Employee Header Information
                $pdf->Ln(10);
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(0, 10, 'Employee Summary', 0, 1, 'L');

                $pdf->SetFont('Arial', '', 12);
                $pdf->Ln(5);
                $pdf->Cell(50, 10, 'Name: ' . $emp->employeeName, 0, 0, 'L');
                $pdf->Cell(50, 10, 'Department: ' . $emp->departmentName, 0, 0, 'L');
                $pdf->Cell(50, 10, 'Role: ' . $emp->roleName, 0, 1, 'L');
                $pdf->Cell(50, 10, 'Grade Score: ' . ($emp->totalScore->totalKpiScore ?? 'N/A'), 0, 0, 'L');
                $pdf->Cell(50, 10, 'Grade: ' . ($emp->totalScore->grade ?? 'N/A'), 0, 0, 'L');
                $pdf->Cell(50, 10, 'Remark: ' . ($emp->totalScore->remark ?? 'N/A'), 0, 1, 'L');
                $pdf->Cell(50, 10, 'Batch: ' . $employeeData->batchName, 0, 0, 'L');
                $pdf->Cell(50, 10, 'Status: ' . $employeeData->status, 0, 0, 'L');
                $pdf->Cell(50, 10, 'Created On: ' . \Carbon\Carbon::parse($employeeData->createdAt)->format('d M, Y'), 0, 1, 'L');

                // Add a Separator Line
                $pdf->Ln(5);
                $pdf->Cell(0, 0, '', 'T', 1, 'C');

                // Appraisal Scores Table
                $pdf->Ln(10);
                $pdf->SetFont('Arial', 'B', 12);

                // Table Header
                $pdf->Cell(10, 10, '#', 1, 0, 'C');
                $pdf->Cell(50, 10, 'Question', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Section Sup Score', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Section Prob Score', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Metric Sup Score', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Metric Prob Score', 1, 0, 'C');
                $pdf->Cell(70, 10, 'Comments', 1, 1, 'C');

                $pdf->SetFont('Arial', '', 12);

                // Table Rows
                foreach ($emp->scores as $index => $score) {
                    $pdf->Cell(10, 10, $index + 1, 1, 0, 'C');
                    $pdf->Cell(50, 10, $score->questionName ?? 'N/A', 1, 0, 'L');
                    $pdf->Cell(30, 10, $score->sectionSupScore ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(30, 10, $score->sectionProbScore ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(30, 10, $score->metricSupScore ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(30, 10, $score->metricProbScore ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(
                        70,
                        10,
                        'Emp: ' . ($score->employeeComment ?? 'N/A') . "\n" .
                            'Sup: ' . ($score->supervisorComment ?? 'N/A') . "\n" .
                            'Prob: ' . ($score->probComment ?? 'N/A'),
                        1,
                        1,
                        'L'
                    );
                }
            }
        }

        // Output the PDF
        $pdf->Output();
        exit;
    }

    public function generateEmployeePdfReport($employeeId)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Retrieve employee data
        $accessToken = session('api_token');
        $data = ['employeeId' => $employeeId];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        $employeeResponseData = $response->object();

        // Handle cases where employee data is not available
        if (!$employeeResponseData || empty($employeeResponseData)) {
            return response()->json(['error' => 'Employee data not found or empty.'], 404);
        }

        // // Convert the response data into a collection
        // $employeeCollection = collect($employeeResponseData);

        // if (!$employeeCollection) {
        //     return response()->json(['error' => 'Invalid employee data structure.'], 500);
        // }

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

        // Iterate through employees using arrow syntax
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
