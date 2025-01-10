<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class KpiController extends Controller
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

        $user = $this->fetchApiData($accessToken, 'http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');
        $employeeResponse = $this->fetchApiData($accessToken, 'http://192.168.1.200:5124/HRMS/Employee');

        $userDepartmentId = $user->department->id;
        // dd($userDepartmentId, $responseKpis);

        //$employees = $employeeResponse->pluck('id')->toArray(); //error cal to member function on array
        //new approach
        $employees = $employeeResponse;




        // Filter the KPIs to include only those with active state of true or false
        $activeKpis = collect($responseKpis)->filter(function ($kpi) use ($userDepartmentId) {
            return $kpi->type === 'REGULAR' && ($kpi->active == true || $kpi->active == false) && ($kpi->empRole->departmentId === $userDepartmentId);
        });

        // Sort the KPIs to place the newly created one first
        $activeKpis = $activeKpis->sortByDesc('createdAt');

        // Paginate the KPIs to display 25 per page
        $activeKpis = $this->paginate($activeKpis, 25, $request);



        return view('kpi-setup.index', compact('activeKpis', 'user', 'employees'));
    }

    // Add this method for pagination



    public function create()
    {

        $accessToken = session('api_token');

        $responseRoles = $this->fetchApiData($accessToken, 'http://192.168.1.200:5124/HRMS/emprole');
        $responseBatches = $this->fetchApiData($accessToken, 'http://192.168.1.200:5123/Appraisal/batch');



        // Extracting data
        $batch_data = collect($responseBatches)->filter(fn($batch) => $batch->status === 'OPEN');

        $uniqueDepartments = [];
        $uniqueRoles = [];

        // dd(collect($responseRoles)->pluck('name')->unique()->toArray());

        $user = $this->fetchApiData($accessToken, 'http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');
        $userDepartmentId = $user->department->id;

        if ($responseRoles) {
            $roles = collect($responseRoles);
            // dd($roles);
            // Extract and deduplicate  roles
            //TODO: get all roles related to user department
            $uniqueRoles = collect($roles)->filter(fn($role) => $role->departmentId === $userDepartmentId)->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            })->unique('id')->values()->toArray();
        }


        return view('kpi-setup.create', compact( 'batch_data', 'uniqueRoles',));
    }

    /**
     * Helper function to fetch data from API and return as object.
     *
     * @param string $accessToken
     * @param string $url
     * @return object|null
     */
    private function fetchApiData(string $accessToken, string $url)
    {
        $response = Http::withToken($accessToken)->get($url);

        return $response->successful() ? $response->object() : null;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            // 'score' => 'required|integer',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            // 'departmentId' => 'required|integer',
            'empRoleId' => 'required|integer',
        ]);

        // Prepare the data for KPI creation
        $kpiData = $request->only([
            'name',
            'description',
            'score',
            'type',
            'batchId',
            'departmentId',
            'empRoleId'
        ]);
        $kpiData['active'] = (bool) $request->input('active'); // Cast to boolean

        // Send the request to the API
        $response = $this->sendApiRequest('http://192.168.1.200:5123/Appraisal/Kpi', $kpiData, 'POST');

        // Check the response and redirect
        if ($response->success) {
            return redirect()->route('kpi.index')->with('toast_success', 'KPI created successfully');
        }

        // Log errors (if any)
        Log::error('Failed to create KPI', [
            'status' => $response->status ?? 'N/A',
            'response' => $response->data ?? 'No response received',
        ]);

        return redirect()->back()->with('toast_error', 'Sorry, failed to create KPI' . $response->body());
    }

    /**
     * Helper method to send an API request.
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @return object {success: bool, status: int|null, data: mixed|null}
     */
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



    /**
     * Display the specified resource.
     */


    public function show(string $id)
    {
        try {
            // Fetch data using helper method
            $responseRoles = $this->fetchShowApiData('http://192.168.1.200:5124/HRMS/emprole');
            $responseBatches = $this->fetchShowApiData('http://192.168.1.200:5123/Appraisal/batch');
            $responseKpi = $this->fetchShowApiData('http://192.168.1.200:5123/Appraisal/Kpi/' . $id);

            // Extract and process data
            $batch_data = $responseBatches ?? [];
            $kpi_data = $responseKpi ?? null;

            $uniqueRoles = [];


            $user = $this->fetchShowApiData( 'http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');
            $userDepartmentId = $user->department->id;

            if ($responseRoles) {
                $roles = collect($responseRoles);

                $uniqueRoles = collect($roles)->filter(fn($role) => $role->departmentId === $userDepartmentId)->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                })->unique('id')->values()->toArray();
            }

            if ($kpi_data) {
                return view('kpi-setup.edit', compact('kpi_data',  'uniqueRoles', 'batch_data'));
            }

            Log::error('Failed to fetch KPI', [
                'id' => $id,
                'response' => $responseKpi,
            ]);
            return redirect()->back()->with('toast_error', 'KPI does not exist');
        } catch (\Exception $e) {
            Log::error('Exception occurred while fetching KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }

    /**
     * Helper method to fetch data from an API.
     *
     * @param string $url
     * @return object|null
     */
    private function fetchShowApiData(string $url)
    {
        $accessToken = session('api_token');

        try {
            $response = Http::withToken($accessToken)->get($url);

            return $response->successful() ? $response->object() : null;
        } catch (\Exception $e) {
            Log::error('API Request Error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }


    public function update_state(Request $request, string $id)
    {
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
                return redirect()->route('kpi.index')->with('toast_success', 'Kpi state updated successfully');
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
        // Validate the request data
        $request->validate([
            'status' => 'required|string',
        ]);

        // dd($request);

        // Get the access token from the request or environment
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the batch update
        $batchData = [
            'id' => $id,
            'status' => $request->input('status'),

        ];

        // dd($batchData);

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/kpi/update-status', $batchData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('kpi.index')->with('toast_success', 'Kpi status updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update batch', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update batch status');
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            // 'score' => 'required|integer',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            // 'departmentId' => 'required|integer',
            'empRoleId' => 'required|integer',
        ]);

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the KPI update
        $kpiData = [
            'id' => $id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'type' => $request->input('type'),
            'active' => $request->input('active') == 1 ? true : false,
            'batchId' => $request->input('batchId'),
            'departmentId' => $request->input('departmentId'),
            'empRoleId' => $request->input('empRoleId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Kpi/", $kpiData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('kpi.index')->with('toast_success', 'KPI updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update KPI', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update KPI' . $response->body());
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }





    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request, string $id)
    {
        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        try {
            // Make the DELETE request to the external API
            $response = Http::withToken($accessToken)
                ->delete("http://192.168.1.200:5123/Appraisal/Kpi/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('kpi.index')->with('toast_success', 'KPI deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete KPI', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete KPI');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
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
