<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalMetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        try {
            // Fetch sections data using helper method

            $metricsResponse = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Metric");
            $sectionsResponse = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Section");


            $sections_data = collect($sectionsResponse);
            $metrics_data = collect($metricsResponse);

            $validSectionIds = $sections_data->filter(function ($section) {
                return $section->kpi && ($section->kpi->type === 'GLOBAL' || $section->kpi->type === 'PROBATION');
            })->pluck('id');

            // Step 4: Filter metrics that belong to the valid sections
            $filteredMetrics = $metrics_data->filter(function ($metric) use ($validSectionIds) {
                return $validSectionIds->contains($metric->section->id);
            });

            // Optional: If you want to ensure the metrics are active, you can further filter
            $activeMetrics = $filteredMetrics->filter(function ($metric) {
                return $metric->active === true;
            });



            $sortedMetrics = $activeMetrics->sortByDesc('createdAt');

            $metrics = $this->paginate($sortedMetrics, 25, $request);


            return view('global-kpi.index-metric', compact('metrics'));
        } catch (\Exception $e) {
            Log::error('Exception occurred in index method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load metrics. Please try again.');
        }
    }


    public function create()
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }


        $sections = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Section");

        // Filter the KMetric to include only those with active state of true
        $activeSections = collect($sections)->filter(function ($section) {
            return $section->active === true && $section->kpi->type == 'GLOBAL' || $section->kpi->type == 'PROBATION';
        });


        return view('global-kpi.create-metric', compact('activeSections'));
    }








    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|numeric',
            'active' => 'required|integer',
            'sectionId' => 'required|integer',
        ]);

        $accessToken = session('api_token');
        $apiUrl = 'http://192.168.1.200:5123/Appraisal/Metric';

        // Prepare the data for Metric creation
        $metricData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => (float) $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'sectionId' => $request->input('sectionId'),
        ];

        try {
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)->post($apiUrl, $metricData);

            if ($response->successful()) {
                return redirect()->route('global.metric.index')->with('toast_success', 'Metric created successfully.');
            }

            // Log unsuccessful response
            Log::error('Failed to create Metric', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Sorry, failed to create Metric.' . $response->body());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while creating Metric', [
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
     * Display the specified resource.
     */


    public function show(string $id)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Metric/{$id}";




        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)->get($apiUrl);

            $sections = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Section");

            // Filter the Metric to include only those with active state of true
            $activeSections = collect($sections)->filter(function ($section) {
                return $section->active === true && $section->kpi->type === 'GLOBAL' ||
                    $section->kpi->type === 'PROBATION';
            });

            if ($response->successful()) {
                // Convert the response to an object for better handling
                $metricData = $response->object();



                return view('global-kpi.edit-metric', compact('metricData', 'activeSections'));
            }

            // Log unsuccessful response
            Log::error('Failed to fetch Metric', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Metric does not exist.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Metric', [
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
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {

        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|numeric',
            'active' => 'required|boolean',
            'sectionId' => 'required|integer',
        ]);

        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Metric";

        // Prepare the data for Metric update
        $metricData = [
            'id' => $id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => (float) $request->input('score'),
            'active' => (bool)$request->input('active'),
            'sectionId' => $request->input('sectionId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)->put($apiUrl, $metricData);

            if ($response->successful()) {
                return redirect()->route('global.metric.index')->with('toast_success', 'Metric updated successfully.');
            }

            // Log unsuccessful response
            Log::error('Failed to update Metric', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Update Metric Error:' . $response->body());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Metric', [
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
                ->delete("http://192.168.1.200:5123/Appraisal/Metric/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Metric deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Metric, there are Section <br> dependent on this Metric and can not be deleted, <b>DEACTIVATE INSTEAD</b>');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Metric', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }



    private function makeApiRequest(string $method, string $url, array $data = null)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

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
