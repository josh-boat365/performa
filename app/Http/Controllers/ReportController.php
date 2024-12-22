<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accessToken = session('api_token');
        $data = [
            'batchId' => '' // Fetch all batches
        ];

        $response = Http::withToken($accessToken)
            ->put("http://192.168.1.200:5123/Appraisal/Report", $data);

        if (!$response->successful()) {
            return view('reports.index', [
                'batches' => [],
                'departments' => [],
                'employees' => [],
                'kpis' => [],
            ]);
        }

        // Ensure response data is not null
        $reports = collect($response->object() ?? []);

        // Extract batches
        $batches = $reports->map(fn($report) => [
            'batchId' => $report->batchId ?? 'N/A',
            'batchName' => $report->batchName ?? 'N/A',
        ])->unique();

        // Extract departments
        $departments = $reports->flatMap(fn($report) => $report->employees ?? [])
            ->map(fn($employee) => [
                'departmentId' => $employee->departmentId ?? 'N/A',
                'departmentName' => $employee->departmentName ?? 'N/A',
            ])
            ->unique()
            ->values();

        // Extract employees
        $employees = $reports->flatMap(fn($report) => $report->employees ?? [])
            ->map(fn($employee) => [
                'employeeId' => $employee->employeeId ?? 'N/A',
                'employeeName' => $employee->employeeName ?? 'N/A',
                'roleName' => $employee->roleName ?? 'N/A',
            ])
            ->unique('employeeId');

        // Extract KPIs
        $kpis = $reports->flatMap(fn($report) => $report->employees ?? [])
            ->flatMap(fn($employee) => $employee->scores ?? [])
            ->map(fn($score) => [
                'kpiName' => $score->kpiName ?? 'N/A'
                ])
            ->unique()
            ->values();

        // Pass grouped data to the view
        return view('reports.index', compact('reports', 'batches', 'departments', 'employees', 'kpis'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
