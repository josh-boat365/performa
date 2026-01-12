<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use App\Services\ApiDataService;
use App\Services\HrmsApiService;
use App\Http\Requests\StoreKpiRequest;
use App\Http\Requests\UpdateKpiRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use App\Services\AppraisalApiService;
use Illuminate\Pagination\LengthAwarePaginator;


class KpiController extends Controller
{
    /**
     * @var AppraisalApiService
     */
    protected AppraisalApiService $appraisalService;

    /**
     * @var HrmsApiService
     */
    protected HrmsApiService $hrmsService;

    /**
     * Create a new controller instance
     */
    public function __construct(
        AppraisalApiService $appraisalService,
        HrmsApiService $hrmsService
    ) {
        $this->appraisalService = $appraisalService;
        $this->hrmsService = $hrmsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            // Set access token for API services
            $this->appraisalService->setAccessToken($accessToken);
            $this->hrmsService->setAccessToken($accessToken);

            // Fetch data from APIs
            $allKpisResponse = $this->appraisalService->getAllKpis();
            $allKpis = $allKpisResponse['data'] ?? $allKpisResponse ?? [];
            $currentUser = $this->hrmsService->getCurrentEmployeeInformation();
            $employeesResponse = $this->hrmsService->getAllEmployees();
            $employees = $employeesResponse['data'] ?? $employeesResponse ?? [];

            $userId = $currentUser['id'] ?? null;

            // Filter KPIs: type = REGULAR, managed by current user
            $activeKpis = ApiDataService::filterKpisByTypeAndStatus($allKpis, 'REGULAR')
                ->filter(function ($kpi) use ($userId) {
                    return ApiDataService::isKpiManagedByUser($kpi, $userId);
                })
                ->sortByDesc('createdAt')
                ->values();

            // Paginate the results
            $activeKpis = ApiDataService::paginateCollection($activeKpis, 25, $request->get('page', 1));

            return view('kpi-setup.index', compact('activeKpis', 'currentUser', 'employees'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch KPIs', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load KPIs. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'An unexpected error occurred.');
        }
    }


    /**
     * Show the form for creating a new KPI
     */
    public function create()
    {
        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);
            $this->hrmsService->setAccessToken($accessToken);

            // Fetch required data
            $rolesResponse = $this->hrmsService->getAllRoles();
            $allRoles = $rolesResponse['data'] ?? $rolesResponse ?? [];
            $batchesResponse = $this->appraisalService->getAllBatches();
            $allBatches = $batchesResponse['data'] ?? $batchesResponse ?? [];
            $currentUser = $this->hrmsService->getCurrentEmployeeInformation();

            $userId = $currentUser['id'] ?? null;

            // Filter open batches
            $openBatches = ApiDataService::filterByStatus($allBatches, 'OPEN');

            // Extract roles managed by current user
            $managedRoles = ApiDataService::extractUserRoles($allRoles, $userId);

            return view('kpi-setup.create', [
                'batch_data' => $openBatches,
                'uniqueRoles' => $managedRoles,
            ]);
        } catch (ApiException $e) {
            Log::error('Failed to load KPI creation form', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load form. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@create', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'An unexpected error occurred.');
        }
    }

    /**
     * Store a newly created KPI
     */
    public function store(StoreKpiRequest $request)
    {
        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);

            // Prepare and send KPI creation request
            $kpiData = [
                'name' => $request->input('name'),
                'description' => $request->input('description') ?? '',
                'type' => $request->input('type'),
                'active' => (bool) $request->input('active'),
                'batchId' => $request->input('batchId'),
                'empRoleId' => $request->input('empRoleId'),
            ];

            $response = $this->appraisalService->createKpi($kpiData);

            return redirect()->route('kpi.index')->with('toast_success', 'KPI created successfully');
        } catch (ApiException $e) {
            Log::error('Failed to create KPI', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to create KPI. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'An unexpected error occurred.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);
            $this->hrmsService->setAccessToken($accessToken);

            // Fetch required data
            $rolesResponse = $this->hrmsService->getAllRoles();
            $allRoles = $rolesResponse['data'] ?? $rolesResponse ?? [];
            $batchesResponse = $this->appraisalService->getAllBatches();
            $allBatches = $batchesResponse['data'] ?? $batchesResponse ?? [];
            $kpi_data = $this->appraisalService->getKpi($id);
            $currentUser = $this->hrmsService->getCurrentEmployeeInformation();

            $userId = $currentUser['id'] ?? null;

            // Extract roles managed by current user
            $uniqueRoles = ApiDataService::extractUserRoles($allRoles, $userId);
            $batch_data = $allBatches ?? [];

            if ($kpi_data) {
                return view('kpi-setup.edit', compact('kpi_data', 'uniqueRoles', 'batch_data'));
            }

            Log::error('Failed to fetch KPI', ['id' => $id]);
            return redirect()->back()->with('toast_error', 'KPI does not exist');
        } catch (ApiException $e) {
            Log::error('Failed to fetch KPI', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load KPI. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@show', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again.');
        }
    }


    public function update_state(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'active' => 'required|integer',
        ]);

        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);

            // Prepare the data for the KPI state update
            $kpiData = [
                'id' => $id,
                'active' => $request->input('active') == 1 ? true : false, // Convert to boolean
            ];

            // Update KPI activation using the service
            $response = $this->appraisalService->updateKpi($id, $kpiData);

            return redirect()->route('kpi.index')->with('toast_success', 'KPI state updated successfully');
        } catch (ApiException $e) {
            Log::error('Failed to update KPI state', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to update KPI state. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@update_state', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again.');
        }
    }

    public function update_status(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'status' => 'required|string|in:PENDING,OPEN,CLOSED,COMPLETED',
        ]);

        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);

            // Prepare the data for the KPI status update
            $kpiData = [
                'id' => $id,
                'status' => $request->input('status'),
            ];

            // Update KPI status using the service
            $response = $this->appraisalService->updateKpi($id, $kpiData);

            return redirect()->route('kpi.index')->with('toast_success', 'KPI status updated successfully');
        } catch (ApiException $e) {
            Log::error('Failed to update KPI status', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to update KPI status. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@update_status', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKpiRequest $request, $id)
    {
        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);

            // Prepare the data for the KPI update
            $kpiData = [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description') ?? '',
                'type' => $request->input('type'),
                'active' => (bool) $request->input('active'),
                'batchId' => $request->input('batchId'),
                'empRoleId' => $request->input('empRoleId'),
            ];

            // Update the KPI using the service
            $response = $this->appraisalService->updateKpi($id, $kpiData);

            return redirect()->route('kpi.index')->with('toast_success', 'KPI updated successfully');
        } catch (ApiException $e) {
            Log::error('Failed to update KPI', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to update KPI. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@update', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again.');
        }
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $accessToken = session('api_token');

            if (!$accessToken) {
                return redirect()->route('login')->with('toast_error', 'Session not found. Please login again.');
            }

            $this->appraisalService->setAccessToken($accessToken);

            // Delete the KPI using the service
            $response = $this->appraisalService->deleteKpi($id);

            return redirect()->route('kpi.index')->with('toast_success', 'KPI deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete KPI', [
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to delete KPI. Please try again.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in KpiController@destroy', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again.');
        }
    }
}
