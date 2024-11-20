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
    public function index($sectionId)
    {
        $accessToken = session('api_token');

        // Fetch sections data
        $responseMetric = Http::withToken($accessToken)
            ->get("http://192.168.1.200:5123/Appraisal/Metric");


        if ($responseMetric->successful()) {
            $metrics = $responseMetric->json();

            // dd($metrics);

            // Filter sections where kpi->id matches $sectionId
            $filteredMetrics = array_filter($metrics, function ($metric) use ($sectionId) {
                return  $metric['section']['id'] == $sectionId;
            });

            // dd($filteredMetrics);

            foreach ($filteredMetrics as $section_metric) {
                if ($section_metric['section']['id'] == $sectionId) {
                    session(
                        [
                            'section_metric_id' => $section_metric['section']['id'],
                            'section_metric_name' => $section_metric['section']['name'],
                        ]
                    );
                }
            }
        }

        return view("metric-setup.index", compact('filteredMetrics'));
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

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the Section creation
        $metricData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'sectionId' => $request->input('sectionId'),
        ];

        try {
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)
                ->post('http://192.168.1.200:5123/Appraisal/Metric', $metricData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Metric created successfully');
            } else {
                // Log the error response
                Log::error('Failed to create Metric', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to create Metric');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while creating Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $accessToken = session('api_token');

        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/Metric/' . $id);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                $metric_data = $response->json();

                return view('metric-setup.edit', compact('metric_data'));
            } else {
                // Log the error response
                Log::error('Failed to fetch Metric', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Section does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Metric', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
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
            'description' => 'required|string',
            'score' => 'required|integer',
            'active' => 'required|integer',
            'sectionId' => 'required|integer',
        ]);

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the Section update
        $metricData = [
            'id' => $id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'sectionId' => $request->input('sectionId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Metric/", $metricData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('kpi.index')->with('toast_success', 'Metric updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update Metric', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update Metric');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Metric', [
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
