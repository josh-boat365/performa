<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalWeightController extends Controller
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

        $responseWeight = $this->fetchApiData($accessToken, 'http://192.168.1.200:5123/Appraisal/KpiWeight');

        // dd($responseWeight);


        // Filter the KPIs to include only those with active state of true or false
        $active = collect($responseWeight)->filter(function ($kpi) {
            return ($kpi->kpi?->type == 'GLOBAL' || $kpi->kpi?->type == 'PROBATION')
                && $kpi->kpi?->active == true;
        });


        // dd($active);

        // Sort the KPIs to place the newly created one first
        $activeKpis = $active->sortByDesc('createdAt');

        // Paginate the KPIs to display 25 per page
        $activeKpis = $this->paginate($activeKpis, 25, $request);



        return view('global-kpi.weight.index-weight', compact('activeKpis'));
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

        $kpis = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Kpi");

        $responseDepartment = $this->makeApiRequest('GET', 'http://192.168.1.200:5124/HRMS/Department');

        $departments = collect($responseDepartment);




        // Filter the KPIs to include only those with active state of true
        $activeKpis = collect($kpis)->filter(function ($kpi) {
            return $kpi->active === true &&  $kpi->type == 'GLOBAL' || $kpi->type == 'PROBATION';
        });

        // dd($activeKpis);

        return view('global-kpi.weight.create-weight', compact('activeKpis', 'departments'));
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
            'kpiId' => 'required|integer',
            'departmentId' => 'required|integer',
            'weight' => 'required|numeric',
        ]);

        // dd($request);

        // Prepare the data for KPI creation
        $kpiData = [
            'kpiId' => $request->input('kpiId'),
            'departmentId' => $request->input('departmentId'),
            'weight' => (float) $request->input('weight'),
        ];

        // Send the request to the API
        $response = $this->sendApiRequest('http://192.168.1.200:5123/Appraisal/KpiWeight', $kpiData, 'POST');

        // Check the response and redirect
        if ($response->success) {
            return redirect()->route('global.weight.index')->with('toast_success', 'Weight For Global KPI created successfully');
        }

        // Log errors (if any)
        Log::error('Failed to create Global Weight KPI', [
            'status' => $response->status ?? 'N/A',
            'response' => $response->data ?? 'No response received',
        ]);

        return redirect()->back()->with('toast_error', 'Sorry, failed to create Global Weight KPI');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }

        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/KpiWeight/{$id}";

        $kpis = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Kpi");

        // Filter the KPIs to include only those with active state of true
        $activeKpis = collect($kpis)->filter(function ($kpi) {
            return $kpi->active === true &&  $kpi->type == 'GLOBAL' || $kpi->type == 'PROBATION';
        });

        $responseDepartment = $this->makeApiRequest('GET', 'http://192.168.1.200:5124/HRMS/Department');

        $departments = collect($responseDepartment);


        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)->get($apiUrl);

            if ($response->successful()) {
                // Convert the response to an object
                $globalWeight = $response->object();

                return view('global-kpi.weight.edit-weight', compact('globalWeight', 'activeKpis', 'departments'));
            }

            // Log the error response
            Log::error('Failed to fetch Global Weight', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return redirect()->back()->with('toast_error', 'Global Weight does not exist.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Global Weight', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with(
                'toast_error',
                'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>'
            );
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
            'kpiId' => 'required|integer',
            'departmentId' => 'required|integer',
            'weight' => 'required|numeric',
        ]);

        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/KpiWeight";




        // Prepare the data for KPI creation
        $globalWeightData = [
            'id' => $id,
            'kpiId' => $request->input('kpiId'),
            'departmentId' => $request->input('departmentId'),
            'weight' => (float) $request->input('weight'),
        ];

        // dd($globalWeightData);

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)->put($apiUrl, $globalWeightData);

            if ($response->successful()) {
                // $json_message = response()->json(['message' => 'Section updated successfully.']);
                return redirect()
                    ->route('global.weight.index')
                    ->with('toast_success', 'Global Weight updated successfully.');
            }

            // Log the error response
            Log::error('Failed to update Global Weight', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Update Global Weight Error:' . $response->body());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Global Weight', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with(
                'toast_error',
                'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>'
            );
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
                ->delete("http://192.168.1.200:5123/Appraisal/KpiWeight/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Global Weight deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete Global Weight', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Global Weight, there are Section <br> dependent on this Metric and can not be deleted, <b>DEACTIVATE INSTEAD</b>');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Global Weight', [
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





    private function makeApiRequest(string $method, string $url, array $data = null)
    {
        // Validate session
        $sessionValidation = ValidateSessionController::validateSession();
        if ($sessionValidation) {
            return $sessionValidation;
        }
        
        $accessToken = session('api_token');

        try {
            $response = Http::withToken($accessToken)->$method($url, $data);

            if ($response->successful()) {
                return $response->object();
            }

            Log::error('API Request Failed: Global', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('API Request Exception: Global', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
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
