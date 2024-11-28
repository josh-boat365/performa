<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalKpiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $accessToken = session('api_token');

        if (!$accessToken) {
            return redirect()->route('login')->with('toast_error', 'We can not find session, please login again'); // Redirect to login if token is missing
        }

        // Centralized API calls

        $responseKpis = $this->fetchApiData($accessToken, 'http://192.168.1.200:5123/Appraisal/Kpi');



        // Filter the KPIs to include only those with active state of true or false
        $activeKpis = collect($responseKpis)->filter(function ($kpi) {
            return $kpi->type === 'GLOBAL' && $kpi->type === 'PROBATION';
        });

        // Sort the KPIs to place the newly created one first
        $activeKpis = $activeKpis->sortByDesc('createdAt');

        // Paginate the KPIs to display 25 per page
        $activeKpis = $this->paginate($activeKpis, 25, $request);



        return view('global-kpi.index-kpi', compact('activeKpis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $accessToken = session('api_token');

        $responseRoles = $this->fetchApiData($accessToken, 'http://192.168.1.200:5124/HRMS/emprole');
        $responseBatches = $this->fetchApiData($accessToken, 'http://192.168.1.200:5123/Appraisal/batch');

        // Extracting data
        $batch_data = $responseBatches ?? [];

        $uniqueRoles = [];

        if ($responseRoles) {
            $rolesWithDepartments = collect($responseRoles);


            $uniqueRoles = $rolesWithDepartments->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            })->unique('id')->values()->toArray();
        }


        return view('global-kpi.create-kpi', compact('batch_data', 'uniqueRoles',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            'empRoleId' => 'required|integer',
        ]);

        // Prepare the data for KPI creation
        $kpiData = $request->only([
            'name',
            'description',
            'type',
            'batchId',
            'empRoleId'
        ]);
        $kpiData['active'] = (bool) $request->input('active'); // Cast to boolean

        // Send the request to the API
        $response = $this->sendApiRequest('http://192.168.1.200:5123/Appraisal/Kpi', $kpiData, 'POST');

        // Check the response and redirect
        if ($response->success) {
            return redirect()->route('kpi.index')->with('toast_success', 'Global KPI created successfully');
        }

        // Log errors (if any)
        Log::error('Failed to create Global KPI', [
            'status' => $response->status ?? 'N/A',
            'response' => $response->data ?? 'No response received',
        ]);

        return redirect()->back()->with('toast_error', 'Sorry, failed to create KPI');
    }

    /**
     * Display the specified resource.
     */  public function show(string $id)
    {
        try {
            // Fetch data using helper method
            $responseRoles = $this->fetchShowApiData('http://192.168.1.200:5124/HRMS/emprole');
            $responseBatches = $this->fetchShowApiData('http://192.168.1.200:5123/Appraisal/batch');
            $responseKpi = $this->fetchShowApiData('http://192.168.1.200:5123/Appraisal/Kpi/' . $id);

            // Extract and process data
            $batch_data = $responseBatches ?? [];
            $kpi_data = $responseKpi ?? null;

            // $uniqueDepartments = [];
            $uniqueRoles = [];

            if ($responseRoles) {
                $rolesWithDepartments = collect($responseRoles);

                // $uniqueDepartments = $rolesWithDepartments->pluck('department')->unique()->toArray();
                $uniqueRoles = $rolesWithDepartments->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                })->unique('id')->values()->toArray();
            }

            if ($kpi_data) {
                return view('global-kpi.edit', compact('kpi_data', 'uniqueRoles', 'batch_data'));
            }

            Log::error('Failed to fetch Global KPI', [
                'id' => $id,
                'response' => $responseKpi,
            ]);
            return redirect()->back()->with('toast_error', 'Global KPI does not exist');
        } catch (\Exception $e) {
            Log::error('Exception occurred while fetching Global KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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

    private function fetchApiData(string $accessToken, string $url)
    {
        $response = Http::withToken($accessToken)->get($url);

        return $response->successful() ? $response->object() : null;
    }

    private function sendApiRequest(string $url, array $data, string $method = 'POST')
    {
        $accessToken = session('api_token');

        try {
            $response = Http::withToken($accessToken)->{$method}($url, $data);

            return (object) [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->successful() ? $response->object() : $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('API Request Error', [
                'url' => $url,
                'method' => $method,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return (object) [
                'success' => false,
                'status' => null,
                'data' => null,
            ];
        }
    }


    protected function paginate(array|Collection $items, int $perPage, Request $request): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        if (!$items instanceof Collection) {
            $items = collect($items);
        }

        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
