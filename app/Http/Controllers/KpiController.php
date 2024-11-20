<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class KpiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {

    //     $accessToken = session('api_token');

    //     $responseRoles = Http::withToken($accessToken)
    //         ->get('http://192.168.1.200:5124/HRMS/emprole');

    //     $responseBatches = Http::withToken($accessToken)
    //         ->get('http://192.168.1.200:5123/Appraisal/batch');

    //     if ($responseBatches->successful()) {
    //         $batch_data = $responseBatches->json();
    //     }

    //     if ($responseRoles->successful()) {
    //         $rolesWithDepartments = $responseRoles->json();

    //         // Extract departments and roles
    //         $departments = array_map(function ($role) {
    //             return $role['department'];
    //         }, $rolesWithDepartments);

    //         $roles = array_map(function ($role) {
    //             return [
    //                 'id' => $role['id'],
    //                 'name' => $role['name']
    //             ];
    //         }, $rolesWithDepartments);

    //         // Get unique departments and roles
    //         $uniqueDepartments = array_unique($departments, SORT_REGULAR);
    //         $uniqueRoles = array_unique($roles, SORT_REGULAR);
    //     }

    //     return view('kpi-setup.create-department-kpi', compact('uniqueDepartments', 'batch_data', 'uniqueRoles'));
    // }


    public function index()
    {
        $accessToken = session('api_token');

        // Fetch roles and departments
        $responseRoles = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5124/HRMS/emprole');

        // Fetch batches
        $responseBatches = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/batch');

        // Fetch KPIs
        $responseKpis = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi');



        // Initialize variables
        $uniqueDepartments = [];
        $uniqueRoles = [];
        $batch_data = [];
        $kpi_data = [];

        // Process batches response
        if ($responseBatches->successful()) {
            $batch_data = $responseBatches->json();
        }

        // Process roles response
        if ($responseRoles->successful()) {
            $rolesWithDepartments = $responseRoles->json();

            // Extract departments and roles
            $departments = array_map(function ($role) {
                return $role['department'];
            }, $rolesWithDepartments);

            // dd($departments);

            $roles = array_map(function ($role) {
                return [
                    'id' => $role['id'],
                    'name' => $role['name']
                ];
            }, $rolesWithDepartments);

            // Get unique departments and roles
            $uniqueDepartments = array_unique($departments, SORT_REGULAR);
            $uniqueRoles = array_unique($roles, SORT_REGULAR);
        }

        // Process KPIs response
        if ($responseKpis->successful()) {
            $kpi_data = $responseKpis->json();

            // dd($kpi_data);

            // Sort the kpi_data array by creation date in descending order
            usort($kpi_data, function ($a, $b) {
                return strtotime($b['createdAt']) - strtotime($a['createdAt']);
            });

            // Get current page from URL e.g. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Define how many items we want to be visible in each page
            $perPage = 10;

            // Slice the collection to get the items to display in current page
            $currentItems = array_slice($kpi_data, ($currentPage - 1) * $perPage, $perPage);

            // Create our paginator and pass it to the view
            $kpis = new LengthAwarePaginator($currentItems, count($kpi_data), $perPage);

            // dd($kpi_data);

            // Append query parameters to the pagination links
            $kpis->setPath(request()->url());
        } else {
            $kpis = new LengthAwarePaginator([], 0, 10);
        }

        return view('kpi-setup.index', compact('uniqueDepartments', 'batch_data', 'uniqueRoles', 'kpis'));
    }

    // public function index()
    // {
    //     $accessToken = session('api_token');

    //     // Fetch roles and departments
    //     $responseRoles = Http::withToken($accessToken)
    //         ->get('http://192.168.1.200:5124/HRMS/emprole');

    //     // Fetch batches
    //     $responseBatches = Http::withToken($accessToken)
    //         ->get('http://192.168.1.200:5123/Appraisal/batch');

    //     // Fetch KPIs
    //     $responseKpis = Http::withToken($accessToken)
    //         ->get('http://192.168.1.200:5123/Appraisal/Kpi');

    //     // Initialize variables
    //     $uniqueDepartments = [];
    //     $uniqueRoles = [];
    //     $batch_data = [];
    //     $departmentsWithBatches = [];

    //     // Process batches response
    //     if ($responseBatches->successful()) {
    //         $batch_data = $responseBatches->json();
    //     }

    //     // Process roles response
    //     if ($responseRoles->successful()) {
    //         $rolesWithDepartments = $responseRoles->json();

    //         // Extract departments and roles
    //         $departments = array_map(function ($role) {
    //             return $role['department'];
    //         }, $rolesWithDepartments);

    //         $roles = array_map(function ($role) {
    //             return [
    //                 'id' => $role['id'],
    //                 'name' => $role['name']
    //             ];
    //         }, $rolesWithDepartments);

    //         // Get unique departments and roles
    //         $uniqueDepartments = array_unique($departments, SORT_REGULAR);
    //         $uniqueRoles = array_unique($roles, SORT_REGULAR);
    //     }

    //     // Process KPIs response
    //     if ($responseKpis->successful()) {
    //         $kpi_data = $responseKpis->json();

    //         // Extract departments with their batches
    //         foreach ($kpi_data as $kpi) {
    //             $departmentId = $kpi['department']['id'];
    //             $batchId = $kpi['batch']['id'];

    //             if (!isset($departmentsWithBatches[$departmentId])) {
    //                 $departmentsWithBatches[$departmentId] = [
    //                     'department' => $kpi['department'],
    //                     'batches' => []
    //                 ];

    //             }

    //             if (!in_array($kpi['batch'], $departmentsWithBatches[$departmentId]['batches'])) {
    //                 $departmentsWithBatches[$departmentId]['batches'][] = $kpi['batch'];
    //             }
    //         }

    //         // Flatten the array for pagination
    //         $departmentsWithBatches = array_values($departmentsWithBatches);


    //         // Sort the departmentsWithBatches array by department name
    //         usort($departmentsWithBatches, function ($a, $b) {
    //             return strcmp($a['department']['name'], $b['department']['name']);
    //         });

    //         // Get current page from URL e.g. &page=1
    //         $currentPage = LengthAwarePaginator::resolveCurrentPage();

    //         // Define how many items we want to be visible in each page
    //         $perPage = 10;

    //         // Slice the collection to get the items to display in current page
    //         $currentItems = array_slice($departmentsWithBatches, ($currentPage - 1) * $perPage, $perPage);

    //         // Create our paginator and pass it to the view
    //         $departmentsBatchData = new LengthAwarePaginator($currentItems, count($departmentsWithBatches), $perPage);

    //         // Append query parameters to the pagination links
    //         $departmentsBatchData->setPath(request()->url());
    //     } else {
    //         $departmentsBatchData = new LengthAwarePaginator([], 0, 10);
    //     }


    //     // dd($departmentsBatchData);

    //     return view('kpi-setup.create-department-kpi', compact('uniqueDepartments', 'batch_data', 'uniqueRoles', 'departmentsBatchData'));
    // }





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
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|integer',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            'departmentId' => 'required|integer',
            'empRoleId' => 'required|integer',
        ]);

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the KPI creation
        $kpiData = [
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
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)
                ->post('http://192.168.1.200:5123/Appraisal/Kpi', $kpiData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'KPI created successfully');
            } else {
                // Log the error response
                Log::error('Failed to create KPI', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to create KPI');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while creating KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
        }
    }





    private function getManagerById($managerId)
    {
        try {
            // Get the access token from the session
            $accessToken = session('api_token'); // Ensure this is securely managed

            // Make the GET request to the external API to fetch manager details
            $response = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5124/HRMS/Employee' . $managerId);


            // Check the response status and return appropriate response
            if ($response->successful()) {
                $manager = $response->json();
                return [
                    'id' => $manager['id'],
                    'name' => $manager['name'],
                    'email' => $manager['email']
                ];
            } else {
                // Handle the case where the manager details could not be fetched
                return [
                    'id' => $managerId,
                    'name' => 'Unknown Manager',
                    'email' => 'unknown@example.com'
                ];
            }
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching manager details', ['error' => $e->getMessage()]);

            // Return a default response in case of an error
            return [
                'id' => $managerId,
                'name' => 'Unknown Manager',
                'email' => 'unknown@example.com'
            ];
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $accessToken = session('api_token');

        try {

            // Fetch roles and departments
            $responseRoles = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5124/HRMS/emprole');

            // Fetch batches
            $responseBatches = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/batch');

            // Fetch KPI
            $responseKpi = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/Kpi/' . $id);



            // Initialize variables
            $uniqueDepartments = [];
            $uniqueRoles = [];
            $batch_data = [];
            $kpi_data = [];

            // Process batches response
            if ($responseBatches->successful()) {
                $batch_data = $responseBatches->json();
            }

            // Process roles response
            if ($responseRoles->successful()) {
                $rolesWithDepartments = $responseRoles->json();

                // Extract departments and roles
                $departments = array_map(function ($role) {
                    return $role['department'];
                }, $rolesWithDepartments);

                // dd($departments);

                $roles = array_map(function ($role) {
                    return [
                        'id' => $role['id'],
                        'name' => $role['name']
                    ];
                }, $rolesWithDepartments);

                // Get unique departments and roles
                $uniqueDepartments = array_unique($departments, SORT_REGULAR);
                $uniqueRoles = array_unique($roles, SORT_REGULAR);
            }

            // Process KPIs response
            if ($responseKpi->successful()) {
                $kpi_data = $responseKpi->json();

                return view('kpi-setup.edit', compact('kpi_data', 'uniqueDepartments', 'uniqueRoles', 'batch_data'));
            } else {
                // Log the error response
                Log::error('Failed to fetch KPI', [
                    'status' => $responseKpi->status(),
                    'response' => $responseKpi->body()
                ]);
                return redirect()->back()->with('toast_error', 'KPI does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|integer',
            'type' => 'required|string',
            'active' => 'required|integer',
            'batchId' => 'required|integer',
            'departmentId' => 'required|integer',
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
                return redirect()->back()->with('toast_error', 'Sorry, failed to update KPI');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating KPI', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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
                return redirect()->back()->with('toast_success', 'KPI deleted successfully');
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
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
        }
    }
}
