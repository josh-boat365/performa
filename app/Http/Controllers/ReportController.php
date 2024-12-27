<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use Codedge\Fpdf\Fpdf\Fpdf;
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
                'employeeName' => trim(($employee->firstName ?? '') . ' ' . ($employee->surname ?? '')) ?: 'N/A', // Ensure both firstName and surname are checked
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



        return view('reports.employee_summary', compact('employee'));
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
        // Retrieve employee data (same logic as before)
        $accessToken = session('api_token');
        $data = ['employeeId' => $employeeId];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        $employee = $response->object();

        // Initialize FPDF
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add Title
        $pdf->Cell(0, 10, 'Employee Performance Summary', 0, 1, 'C');

        foreach ($employee as $employeeData) {
            foreach ($employeeData->employees as $emp) {
                // Add Employee Details
                $pdf->SetFont('Arial', '', 12);
                $pdf->Ln(10);
                $pdf->Cell(40, 10, 'Name: ' . $emp->employeeName);
                $pdf->Ln(7);
                $pdf->Cell(40, 10, 'Department: ' . $emp->departmentName);
                $pdf->Ln(7);
                $pdf->Cell(40, 10, 'Role: ' . $emp->roleName);
                $pdf->Ln(7);
                $pdf->Cell(40, 10, 'Grade Score: ' . ($emp->totalScore->totalKpiScore ?? 'N/A'));
                $pdf->Ln(7);
                $pdf->Cell(40, 10, 'Grade: ' . ($emp->totalScore->grade ?? 'N/A'));
                $pdf->Ln(7);
                $pdf->Cell(40, 10, 'Remark: ' . ($emp->totalScore->remark ?? 'N/A'));

                // Add Scores Table Header
                $pdf->Ln(10);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(10, 10, '#', 1, 0, 'C');
                $pdf->Cell(50, 10, 'KPI', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Section Score', 1, 0, 'C');
                $pdf->Cell(30, 10, 'Metric Score', 1, 0, 'C');
                $pdf->Cell(70, 10, 'Comments', 1, 1, 'C');

                // Add Scores Data
                $pdf->SetFont('Arial', '', 12);
                foreach ($emp->scores as $index => $score) {
                    $pdf->Cell(10, 10, $index + 1, 1, 0, 'C');
                    $pdf->Cell(50, 10, $score->kpiName ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(30, 10, $score->sectionSupScore ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(30, 10, $score->metricSupScore ?? 'N/A', 1, 0, 'C');
                    $pdf->Cell(70, 10, $score->employeeComment ?? 'N/A', 1, 1, 'C');
                }
            }
        }

        // Output the PDF
        $pdf->Output();
        exit;
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
