<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWeightRequest;
use App\Http\Requests\UpdateWeightRequest;
use App\Services\AppraisalApiService;
use App\Services\HrmsApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalWeightController extends Controller
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
            $weightsResponse = $this->appraisalService->getAllWeights();

            // Handle both wrapped and unwrapped responses
            if (is_array($weightsResponse) && isset($weightsResponse['data']) && is_array($weightsResponse['data'])) {
                $weights = $weightsResponse['data'];
            } elseif (is_array($weightsResponse)) {
                $weights = $weightsResponse;
            } else {
                $weights = [];
            }

            // Log for debugging
            Log::info('Global Weight Index - All Weights count', ['count' => count($weights)]);

            // Log first weight structure if there are any
            if (!empty($weights)) {
                Log::info('Global Weight Index - First Weight structure', [
                    'first_weight_keys' => array_slice(array_keys($weights[0]), 0, 10),
                    'has_kpi' => isset($weights[0]['kpi']),
                    'kpi_type' => $weights[0]['kpi']['type'] ?? 'NOT_FOUND'
                ]);
            }

            // Filter for GLOBAL/PROBATION KPIs that are active
            $activeWeights = collect($weights)->filter(function ($weight) {
                return ($weight['kpi']['type'] === 'GLOBAL' || $weight['kpi']['type'] === 'PROBATION')
                    && $weight['kpi']['active'] === true;
            });

            Log::info('Global Weight Index - Filtered Weights count', ['count' => $activeWeights->count()]);

            $sortedWeights = $activeWeights->sortByDesc('createdAt');
            $activeKpis = $this->paginate($sortedWeights->toArray(), 25, $request);

            return view('global-kpi.weight.index-weight', compact('activeKpis'));
        } catch (ApiException $e) {
            // Handle 404 gracefully
            if (strpos($e->getMessage(), '404') !== false) {
                Log::warning('Weight endpoint not available on API server', ['message' => $e->getMessage()]);
                return view('global-kpi.weight.index-weight', ['activeKpis' => []])
                    ->with('toast_warning', 'Weight management is currently unavailable. Please contact your administrator.');
            }

            Log::error('Failed to load weights', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $kpisResponse = $this->appraisalService->getAllKpis();
            $departmentsResponse = $this->hrmsService->getAllDepartments();

            // Handle both wrapped and unwrapped KPI responses
            if (is_array($kpisResponse) && isset($kpisResponse['data']) && is_array($kpisResponse['data'])) {
                $kpisData = $kpisResponse['data'];
            } elseif (is_array($kpisResponse)) {
                $kpisData = $kpisResponse;
            } else {
                $kpisData = [];
            }

            // Handle both wrapped and unwrapped department responses
            if (is_array($departmentsResponse) && isset($departmentsResponse['data']) && is_array($departmentsResponse['data'])) {
                $departmentsData = $departmentsResponse['data'];
            } elseif (is_array($departmentsResponse)) {
                $departmentsData = $departmentsResponse;
            } else {
                $departmentsData = [];
            }

            $kpis = collect($kpisData);
            $departments = collect($departmentsData);

            $activeKpis = $kpis->filter(function ($kpi) {
                return $kpi['active'] === true && ($kpi['type'] === 'GLOBAL' || $kpi['type'] === 'PROBATION');
            });

            return view('global-kpi.weight.create-weight', compact('activeKpis', 'departments'));
        } catch (ApiException $e) {
            Log::error('Failed to load KPIs for weight creation', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWeightRequest $request)
    {
        try {
            $this->appraisalService->createWeight([
                'kpiId' => $request->input('kpiId'),
                'departmentId' => $request->input('departmentId'),
                'weight' => (float) $request->input('weight'),
            ]);

            return redirect()->route('global.weight.index')->with('toast_success', 'Weight For Global KPI created successfully');
        } catch (ApiException $e) {
            Log::error('Failed to create Global Weight KPI', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $weightResponse = $this->appraisalService->getWeight($id);
            $kpisResponse = $this->appraisalService->getAllKpis();
            $departmentsResponse = $this->hrmsService->getAllDepartments();

            // Handle wrapped and unwrapped weight response
            if (isset($weightResponse['data'])) {
                $globalWeight = is_array($weightResponse['data']) ? $weightResponse['data'] : $weightResponse['data'];
            } else {
                $globalWeight = is_array($weightResponse) && !isset($weightResponse['data']) ? $weightResponse : null;
            }

            // Handle wrapped and unwrapped KPI responses
            if (is_array($kpisResponse) && isset($kpisResponse['data']) && is_array($kpisResponse['data'])) {
                $kpisData = $kpisResponse['data'];
            } elseif (is_array($kpisResponse)) {
                $kpisData = $kpisResponse;
            } else {
                $kpisData = [];
            }

            // Handle wrapped and unwrapped department responses
            if (is_array($departmentsResponse) && isset($departmentsResponse['data']) && is_array($departmentsResponse['data'])) {
                $departmentsData = $departmentsResponse['data'];
            } elseif (is_array($departmentsResponse)) {
                $departmentsData = $departmentsResponse;
            } else {
                $departmentsData = [];
            }

            $kpis = collect($kpisData);
            $departments = collect($departmentsData);

            if (!$globalWeight) {
                return redirect()->back()->with('toast_error', 'Global Weight does not exist.');
            }

            $activeKpis = $kpis->filter(function ($kpi) {
                return $kpi['active'] === true && ($kpi['type'] === 'GLOBAL' || $kpi['type'] === 'PROBATION');
            });

            return view('global-kpi.weight.edit-weight', compact('globalWeight', 'activeKpis', 'departments'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Global Weight', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWeightRequest $request, string $id)
    {
        try {
            $this->appraisalService->updateWeight($id, [
                'id' => $id,
                'kpiId' => $request->input('kpiId'),
                'departmentId' => $request->input('departmentId'),
                'weight' => (float) $request->input('weight'),
            ]);

            return redirect()->route('global.weight.index')->with('toast_success', 'Global Weight updated successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to update Global Weight', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->appraisalService->deleteWeight($id);
            return redirect()->back()->with('toast_success', 'Global Weight deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete Global Weight', ['message' => $e->getMessage()]);
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
