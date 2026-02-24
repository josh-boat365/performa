<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKpiRequest;
use App\Http\Requests\UpdateKpiRequest;
use App\Services\AppraisalApiService;
use App\Services\HrmsApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalKpiController extends Controller
{
    private AppraisalApiService $appraisalService;
    private HrmsApiService $hrmsService;

    public function __construct(AppraisalApiService $appraisalService, HrmsApiService $hrmsService)
    {
        $this->appraisalService = $appraisalService;
        $this->hrmsService = $hrmsService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Get all KPIs
            $response = $this->appraisalService->getAllKpis();

            // Log raw response for debugging
            Log::info('Global KPI Index - Raw response', [
                'response_type' => gettype($response),
                'response_keys' => is_array($response) ? array_keys($response) : 'N/A'
            ]);

            // Handle both wrapped and unwrapped responses
            if (is_array($response) && isset($response['data']) && is_array($response['data'])) {
                $kpis = $response['data'];
            } elseif (is_array($response)) {
                $kpis = $response;
            } else {
                $kpis = [];
            }

            // Log for debugging
            Log::info('Global KPI Index - All KPIs count', ['count' => count($kpis)]);

            // Log first KPI to see structure if there are any
            if (!empty($kpis)) {
                Log::info('Global KPI Index - First KPI structure', [
                    'first_kpi' => array_slice(array_keys($kpis[0]), 0, 10), // First 10 keys
                    'type_value' => $kpis[0]['type'] ?? 'NOT_FOUND'
                ]);
            }

            // Filter KPIs to include only those with type GLOBAL or PROBATION
            $activeKpis = collect($kpis)->filter(function ($kpi) {
                $type = $kpi['type'] ?? null;
                return $type === 'GLOBAL' || $type === 'PROBATION';
            });

            Log::info('Global KPI Index - Filtered KPIs count', [
                'count' => $activeKpis->count(),
                'filter_logic' => 'type === GLOBAL || type === PROBATION'
            ]);

            // Sort by creation date descending
            $activeKpis = $activeKpis->sortByDesc('createdAt');

            // Paginate
            $activeKpis = $this->paginate($activeKpis, 25, $request);

            return view('global-kpi.index-kpi', compact('activeKpis'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve KPIs', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Get batches and roles
            $batchesResponse = $this->appraisalService->getAllBatches();
            $rolesResponse = $this->hrmsService->getAllRoles();

            // Handle wrapped and unwrapped responses
            $batches = collect(
                (isset($batchesResponse['data']) && is_array($batchesResponse['data']))
                    ? $batchesResponse['data']
                    : $batchesResponse
            );
            $roles = collect(
                (isset($rolesResponse['data']) && is_array($rolesResponse['data']))
                    ? $rolesResponse['data']
                    : $rolesResponse
            );

            // Filter batches with OPEN status
            $batch_data = $batches->filter(fn($batch) => ($batch['status'] ?? null) === 'OPEN');

            // Extract unique departments
            $uniqueDepartments = $roles->pluck('department')->unique()->toArray();

            return view('global-kpi.create-kpi', compact('batch_data', 'uniqueDepartments'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve create form data', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKpiRequest $request)
    {
        try {
            $kpiData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'type' => $request->input('type'),
                'active' => (bool) $request->input('active'),
                'batchId' => (int) $request->input('batchId'),
                'empRoleId' => (int) $request->input('empRoleId'),
            ];

            $this->appraisalService->createKpi($kpiData);

            return redirect()->route('global.index')->with('toast_success', 'Global KPI created successfully');
        } catch (ApiException $e) {
            Log::error('Failed to create KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $batchesResponse = $this->appraisalService->getAllBatches();
            $kpiResponse = $this->appraisalService->getKpi($id);

            // Handle wrapped and unwrapped batches response
            $batch_data = (isset($batchesResponse['data']) && is_array($batchesResponse['data']))
                ? $batchesResponse['data']
                : $batchesResponse;

            // Handle wrapped and unwrapped KPI response
            if (isset($kpiResponse['data'])) {
                $kpi_data = is_array($kpiResponse['data']) ? $kpiResponse['data'] : $kpiResponse['data'];
            } else {
                $kpi_data = is_array($kpiResponse) && !isset($kpiResponse['data']) ? $kpiResponse : null;
            }

            if (!$kpi_data) {
                return redirect()->back()->with('toast_error', 'Global KPI does not exist');
            }

            return view('global-kpi.edit-kpi', compact('kpi_data', 'batch_data'));
        } catch (ApiException $e) {
            Log::error('Failed to retrieve KPI', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKpiRequest $request, string $id)
    {
        try {
            $kpiData = [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => $request->input('score'),
                'type' => $request->input('type'),
                'active' => (bool) $request->input('active'),
                'batchId' => (int) $request->input('batchId'),
                'departmentId' => (int) $request->input('departmentId'),
                'empRoleId' => (int) $request->input('empRoleId'),
            ];

            $this->appraisalService->updateKpi($id, $kpiData);

            return redirect()->route('global.index')->with('toast_success', 'Global KPI updated successfully');
        } catch (ApiException $e) {
            Log::error('Failed to update KPI', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    public function update_state(Request $request, string $id)
    {
        try {
            $request->validate([
                'active' => 'required|integer',
            ]);

            $data = [
                'id' => $id,
                'active' => (bool) $request->input('active'),
            ];

            $this->appraisalService->updateKpi($id, $data);

            return redirect()->route('global.index')->with('toast_success', 'Global KPI state updated successfully');
        } catch (ApiException $e) {
            Log::error('Failed to update KPI state', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    public function update_status(Request $request, string $id)
    {
        try {
            $request->validate([
                'status' => 'required|string',
            ]);

            $data = [
                'id' => $id,
                'type' => $request->input('status'),
            ];

            $this->appraisalService->updateKpi($id, $data);

            return redirect()->route('global.index')->with('toast_success', 'Global KPI status updated successfully');
        } catch (ApiException $e) {
            Log::error('Failed to update KPI status', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->appraisalService->deleteKpi($id);

            return redirect()->back()->with('toast_success', 'Global KPI deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete KPI', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', $e->getMessage());
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
