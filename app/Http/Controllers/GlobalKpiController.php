<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalKpiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        $accessToken = session('api_token');

        if (!$accessToken) {
            return redirect()->route('login')->with('toast_error', 'We can not find session, please login again'); // Redirect to login if token is missing
        }

        // Centralized API calls

        $responseKpis = $this->fetchApiData($accessToken, 'http://192.168.1.200:5123/Appraisal/Kpi');

        // dd($responseKpis);


        // Filter the KPIs to include only those with active state of true or false
        $activeKpis = collect($responseKpis)->filter(function ($kpi) {
            return $kpi->type == 'GLOBAL' || $kpi->type == 'PROBATION';
        });

        // dd($activeKpis);

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
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }


        $accessToken = session('api_token');

        $responseRoles = $this->fetchApiData($accessToken, 'http://192.168.1.200:5124/HRMS/emprole');
        $responseBatches = $this->fetchApiData($accessToken, 'http://192.168.1.200:5123/Appraisal/batch');

        // Extracting data
        $batch_data = collect($responseBatches)->filter(fn($batch) => $batch->status === 'OPEN');

        $uniqueDepartments = [];
        // $uniqueRoles = [];

        if ($responseRoles) {
            $rolesWithDepartments = collect($responseRoles);

            // Extract and deduplicate departments and roles
            $uniqueDepartments = $rolesWithDepartments->pluck('department')->unique()->toArray();
            // $uniqueRoles = $rolesWithDepartments->map(function ($role) {
            //     return [
            //         'id' => $role->id,
            //         'name' => $role->name,
            //     ];
            // })->unique('id')->values()->toArray();
        }

        // dd($uniqueDepartments);

        return view('global-kpi.create-kpi', compact('batch_data', 'uniqueDepartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

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
            return redirect()->route('global.index')->with('toast_success', 'Global KPI created successfully');
        }

        // Log errors (if any)
        Log::error('Failed to create Global KPI', [
            'status' => $response->status ?? 'N/A',
            'response' => $response->data ?? 'No response received',
        ]);

        return redirect()->back()->with('toast_error', 'Sorry, failed to create KPI' . $response->body());
    }

    /**
     * Display the specified resource.
     */  public function show(string $id)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        try {
            $accessToken = session('api_token');

            // Fetch data using helper method
            // $responseRoles = Http::withToken($accessToken)
            //     ->get('http://192.168.1.200:5124/HRMS/emprole');

            $responseBatches = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/batch');


            $responseKpi = Http::withToken($accessToken)
                ->get("http://192.168.1.200:5123/Appraisal/Kpi/{$id}");

            // Extract and process data
            $batch_data = $responseBatches->object() ?? [];
            $kpi_data = $responseKpi->object() ?? null;


            // $uniqueRoles = $responseRoles->object();



            if ($kpi_data) {
                return view('global-kpi.edit-kpi', compact('kpi_data',  'batch_data'));
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
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            'empRoleId' => 'required|integer',
        ]);

        // Prepare the data for KPI update
        $kpiData = [
            'id' => $id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'active' => $request->input('active') == 1 ? true : false,
            'batchId' => $request->input('batchId'),
            'empRoleId' => $request->input('empRoleId'),
        ];

        // Send the request to the API to update the KPI
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Kpi";
        $response = $this->sendApiRequest($apiUrl, $kpiData, 'PUT');

        // Check the response and redirect
        if ($response->success) {
            return redirect()->route('global.index')->with('toast_success', 'Global KPI updated successfully');
        }

        // Log errors (if any)
        Log::error('Failed to update Global KPI', [
            'status' => $response->status ?? 'N/A',
            'response' => $response->data ?? 'No response received',
        ]);

        return redirect()->back()->with('toast_error', 'Sorry, failed to update Global KPI' . $response->body());
    }


    public function update_state(Request $request, string $id)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        // Validate the request data
        $request->validate([
            'active' => 'required|integer',
        ]);

        // dd($request);

        // Get the access token from the request or environment
        $accessToken = session('api_token');

        // Prepare the data for the batch state update
        $batchData = [
            'id' => $id,
            'active' => $request->input('active') == 1 ? true : false, // Convert to boolean
        ];

        // dd($batchData);

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/kpi/update-activation', $batchData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('global.index')->with('toast_success', 'Global Kpi state updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update batch', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update batch state');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating batch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }

    public function update_status(Request $request, string $id)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        try {
            // Validate the request data
            $request->validate([

                'status' => 'required|string',
            ]);

            // dd($id);

            // Get the access token from the request or environment
            $accessToken = session('api_token'); // Replace with your actual access token

            // Prepare the data for the batch update
            $batchData = [
                'id' => (int) $id,
                'type' => $request->input('status'),

            ];

            // dd($batchData);


            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Kpi/update-type/", $batchData);
            // dd($response);
            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('global.index')->with('toast_success', 'Global Kpi status updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update Global KPI Status', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update Global KPI Status');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating batch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        try {
            // Make the DELETE request to the external API
            $response = Http::withToken($accessToken)
                ->delete("http://192.168.1.200:5123/Appraisal/Kpi/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Global Kpi deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Global Kpi, there are Section <br> dependent on this Metric and can not be deleted, <b>DEACTIVATE INSTEAD</b>');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Global Kpi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }


    private function fetchApiData(string $accessToken, string $url)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        $response = Http::withToken($accessToken)->get($url);

        return $response->successful() ? $response->object() : null;
    }

    private function sendApiRequest(string $url, array $data, string $method = 'POST')
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

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
