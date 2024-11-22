<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        try {
            // Fetch sections data using helper method
            $sections = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Section");
            $metrics = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Metric");

            // Filter the KMetric to include only those with active state of true
            $activeMetrics = collect($metrics)->filter(function ($metric) {
                return $metric->active === true;
            });


            return view('metric-setup.index', compact('sections', 'activeMetrics'));
        } catch (\Exception $e) {
            Log::error('Exception occurred in index method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load metrics. Please try again.');
        }
    }


    private function makeApiRequest(string $method, string $url, array $data = null)
    {
        $accessToken = session('api_token');

        try {
            $response = Http::withToken($accessToken)->$method($url, $data);

            if ($response->successful()) {
                return $response->object();
            }

            Log::error('API Request Failed', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('API Request Exception', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
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
            'active' => 'required|integer',
            'sectionId' => 'required|integer',
        ]);

        $accessToken = session('api_token');
        $apiUrl = 'http://192.168.1.200:5123/Appraisal/Metric';

        // Prepare the data for Metric creation
        $metricData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'sectionId' => $request->input('sectionId'),
        ];

        try {
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)->post($apiUrl, $metricData);

            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Metric created successfully.');
            }

            // Log unsuccessful response
            Log::error('Failed to create Metric', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Sorry, failed to create Metric.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while creating Metric', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with(
                'toast_error',
                'There is no internet connection. Please check your internet and try again.'
            );
        }
    }



    /**
     * Display the specified resource.
     */


    public function show(string $id)
    {
        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Metric/{$id}";

        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)->get($apiUrl);

            if ($response->successful()) {
                // Convert the response to an object for better handling
                $metricData = $response->object();

                return view('metric-setup.edit', compact('metricData'));
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
                'There is no internet connection. Please check your internet and try again.'
            );
        }
    }




    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)
    // {
    //     // Validate the request data
    //     $request->validate([
    //         'name' => 'required|string',
    //         'description' => 'required|string',
    //         'score' => 'required|integer',
    //         'active' => 'required|integer',
    //         'sectionId' => 'required|integer',
    //     ]);

    //     // Get the access token from the session
    //     $accessToken = session('api_token'); // Replace with your actual access token

    //     // Prepare the data for the Section update
    //     $metricData = [
    //         'id' => $id,
    //         'name' => $request->input('name'),
    //         'description' => $request->input('description'),
    //         'score' => $request->input('score'),
    //         'active' => $request->input('active') == 1 ? true : false,
    //         'sectionId' => $request->input('sectionId'),
    //     ];

    //     try {
    //         // Make the PUT request to the external API
    //         $response = Http::withToken($accessToken)
    //             ->put("http://192.168.1.200:5123/Appraisal/Metric/", $metricData);

    //         // Check the response status and return appropriate response
    //         if ($response->successful()) {
    //             return redirect()->route('kpi.index')->with('toast_success', 'Metric updated successfully');
    //         } else {
    //             // Log the error response
    //             Log::error('Failed to update Metric', [
    //                 'status' => $response->status(),
    //                 'response' => $response->body()
    //             ]);
    //             return redirect()->back()->with('toast_error', 'Sorry, failed to update Metric');
    //         }
    //     } catch (\Exception $e) {
    //         // Log the exception
    //         Log::error('Exception occurred while updating Metric', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
    //     }
    // }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|integer',
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
            'score' => $request->input('score'),
            'active' => (bool)$request->input('active'),
            'sectionId' => $request->input('sectionId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)->put($apiUrl, $metricData);

            if ($response->successful()) {
                return redirect()->route('metric.index')->with('toast_success', 'Metric updated successfully.');
            }

            // Log unsuccessful response
            Log::error('Failed to update Metric', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Sorry, failed to update Metric.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Metric', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with(
                'toast_error',
                'There is no internet connection. Please check your internet and try again.'
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
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Metric');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Metric', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
        }
    }
}
