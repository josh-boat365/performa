<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Codedge\Fpdf\Fpdf\Fpdf;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    /**
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'batchId' => 'nullable',
            'departmentId' => 'nullable',
            'kpiId' => 'nullable',
            'employeeId' => 'nullable',
        ]);

        // Retrieve access token from session
        $accessToken = session('api_token');

        // Filter non-empty parameters for the API request
        $filters = array_filter($request->only(['batchId', 'departmentId', 'kpiId', 'employeeId']));
        $data = empty($filters) ? ['batchId' => ''] : $filters;

        // Make the API request to fetch appraisal reports
        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        // Handle unsuccessful response
        if (!$response->successful()) {
            Log::error('Failed to fetch appraisal reports', ['response' => $response->body()]);
            return view('reports.index', [
                'reports' => collect(),
                'batches' => [],
                'departments' => [],
                'employees' => [],
                'kpis' => [],
            ]);
        }

        // Collect response data
        $reports = collect($response->object() ?? []);

        // Process employees data to avoid redundant operations
        $employeesData = $reports->flatMap(fn($report) => $report->employees ?? []);

        // Extract and group batch data
        $batches = $reports->map(fn($report) => [
            'batchId' => $report->batchId ?? 'N/A',
            'batchName' => $report->batchName ?? 'N/A',
        ])->unique('batchId')->values();

        // Fetch departments data
        $responseDepartments = Http::withToken($accessToken)
            ->get("http://192.168.1.200:5124/HRMS/Department");

        // Handle unsuccessful response for departments
        if (!$responseDepartments->successful()) {
            Log::error('Failed to fetch departments', ['response' => $responseDepartments->body()]);
            $departments = collect(); // Default to empty collection if the request fails
        } else {
            $departments = collect($responseDepartments->object())->map(fn($department) => [
                'departmentId' => $department->id,
                'departmentName' => $department->name,
            ])->values();
        }

        // Fetch employees data
        $responseEmployees = Http::withToken($accessToken)
            ->get("http://192.168.1.200:5124/HRMS/Employee");

        // Handle unsuccessful response for employees
        if (!$responseEmployees->successful()) {
            Log::error('Failed to fetch employees', ['response' => $responseEmployees->body()]);
            $employees = collect(); // Default to empty collection if the request fails
        } else {
            $employees = collect($responseEmployees->object())->map(fn($employee) => [
                'employeeId' => $employee->id ?? 'N/A', // Use null coalescing to provide a default value
                'employeeStaffID' => $employee->staffNumber ?? 'N/A', // Use null coalescing to provide a default value
                'employeeName' => trim(($employee->firstName ?? '') . ' ' . ($employee->surname ?? '')) ?: 'N/A',
                // Ensure both firstName and surname are checked
            ])->values();
        }

        // Extract unique KPIs from employees data
        $kpis = $employeesData->flatMap(fn($employee) => $employee->scores ?? [])
            ->map(fn($score) => [
                'kpiId' => $score->kpiId ?? 'N/A',
                'kpiName' => $score->kpiName ?? 'N/A',
            ])->unique('kpiId')->values();

        // Pass data to the view
        return view('reports.index', compact('reports', 'batches', 'departments', 'employees', 'kpis'));
    }



    public function showEmployeeSummary($employeeId)
    {
        // Retrieve access token from session
        $accessToken = session('api_token');


        // Prepare the request data
        $data = ['employeeId' => $employeeId];

        // Make the API request
        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);


        // Fetch employee data based on the employeeId
        $employee = $response->object();

        // dd($employee);



        return view('reports.employee_summary', compact('employee', 'employeeId'));
    }





    /**
     * Show the form for creating a new resource.
     */
    public function filter(Request $request)
    {
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

    public function generateEmployeePdfxx($employeeId)
    {
        // Retrieve employee data
        $accessToken = session('api_token');
        $data = ['employeeId' => $employeeId];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        $employeeData = $response->json();

        // Handle cases where employee data is not available
        if (!$employeeData || empty($employeeData)) {
            return response()->json(['error' => 'Employee data not found or empty.'], 404);
        }

        // Initialize FPDF
        $fpdf = new Fpdf();
        $fpdf->AddPage();
        $fpdf->SetFont('Arial', 'B', 16);

        // Design header
        $fpdf->Image(public_path('bpsl_imgs/purple-logo-bpsl.png'), 10, 10, 30); // Company logo
        $fpdf->Cell(190, 10, 'Employee Appraisal Report', 0, 1, 'C');
        $fpdf->SetFont('Arial', '', 12);
        $fpdf->Cell(190, 10, now()->format('D M d Y g:i A'), 0, 1, 'C');
        $fpdf->Ln(10);

        // dd($employeeData);
        // Display Employee Information
        foreach ($employeeData[0]['employees'] as $employee) {
            $fpdf->SetFont('Arial', 'B', 14);
            $fpdf->Cell(190, 10, "Employee: {$employee['employeeName']}", 0, 1, 'L');
            $fpdf->Ln(5);

            $fpdf->SetFont('Arial', '', 12);
            $fpdf->Cell(95, 10, "Employee ID: {444}", 0, 0, 'L');
            $fpdf->Cell(95, 10, "Role: {$employee['roleName']}", 0, 1, 'L');
            $fpdf->Cell(95, 10, "Department: {$employee['departmentName']}", 0, 0, 'L');
            $fpdf->Cell(95, 10, "Branch: Head Office", 0, 1, 'L');
            $fpdf->Ln(5);

            $fpdf->Cell(95, 10, "Grade: " . (isset($employee['totalScore']['grade']) ? $employee['totalScore']['grade'] : '___'), 0, 0, 'L');
            $fpdf->Cell(95, 10, "Score: " . (isset($employee['totalScore']['totalKpiScore']) ? $employee['totalScore']['totalKpiScore'] : '___'), 0, 1, 'L');
            $fpdf->Cell(190, 10, "Remark: " . (isset($employee['totalScore']['remark']) ? $employee['totalScore']['remark'] : '___'), 0, 1, 'L');
            $fpdf->Ln(10);

            // Appraisal Questions and Scores
            foreach ($employee['scores'] as $index => $score) {
                $fpdf->SetFont('Arial', 'B', 12);
                $fpdf->Cell(190, 10, "Question " . ($index + 1) . ": {$score['questionName']}", 0, 1, 'L');
                $fpdf->SetFont('Arial', '', 10);
                $fpdf->MultiCell(190, 10, $score['questionDescription'] ?? '___', 0, 'L');
                $fpdf->Ln(5);

                $fpdf->Cell(95, 10, "Employee Section Score: " . (isset($score['metricEmpScore']) ? $score['metricEmpScore'] : '___'), 0, 0, 'L');
                $fpdf->Cell(95, 10, "Employee Metric Score: " . (isset($score['sectionEmpScore']) ? $score['sectionEmpScore'] : '___'), 0, 1, 'L');
                $fpdf->Cell(95, 10, "Supervisor Section Score: " . (isset($score['sectionSupScore']) ? $score['sectionSupScore'] : '___'), 0, 0, 'L');
                $fpdf->Cell(95, 10, "Supervisor Metric Score: " . (isset($score['metricSupScore']) ? $score['metricSupScore'] : '___'), 0, 1, 'L');
                $fpdf->Ln(5);
            }

            // Add a separator
            $fpdf->Ln(5);
            $fpdf->Cell(190, 0, '', 'T', 1, 'C'); // Horizontal line
            $fpdf->Ln(10);
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