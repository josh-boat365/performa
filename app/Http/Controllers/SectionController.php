<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $kpiId = $id;
        $accessToken = session('api_token');


        // Fetch sections data
        $responseSection = Http::withToken($accessToken)
            ->get("http://192.168.1.200:5123/Appraisal/Section");

        // if ($response->successful()) {
        //     $kpi = $response->json();
        //     $kpi_data = [
        //         'id' => $kpi['id'],
        //         'name' => $kpi['name']
        //     ];
        // }

        if ($responseSection->successful()) {
            $sections = $responseSection->json();

            // Filter sections where kpi->id matches $kpiId
            $filteredSections = array_filter($sections, function ($section) use ($kpiId) {
                return $section['kpi']['id'] == $kpiId;
            });

            // dd($filteredSections);

            foreach ($filteredSections as $kpi_section) {
                if ($kpi_section['kpi']['id'] == $kpiId) {
                    session(
                        [
                            'kpi_section_id' => $kpi_section['kpi']['id'],
                            'kpi_section_name' => $kpi_section['kpi']['name'],
                        ]
                    );
                }
            }
        }

        return view("section-setup.index", compact('filteredSections'));
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
            'kpiId' => 'required|integer',
        ]);

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the Section creation
        $sectionData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'kpiId' => $request->input('kpiId'),
        ];

        try {
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)
                ->post('http://192.168.1.200:5123/Appraisal/Section', $sectionData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Section created successfully');
            } else {
                // Log the error response
                Log::error('Failed to create Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to create Section');
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
                ->get('http://192.168.1.200:5123/Appraisal/Section/' . $id);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                $section_data = $response->json();

                return view('section-setup.edit', compact('section_data'));
            } else {
                // Log the error response
                Log::error('Failed to fetch Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Section does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Section', [
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
            'kpiId' => 'required|integer',
        ]);

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the Section update
        $sectionData = [
            'id' => $id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'kpiId' => $request->input('kpiId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put("http://192.168.1.200:5123/Appraisal/Section/", $sectionData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('kpi.index')->with('toast_success', 'Section updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update Section');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Section', [
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
                ->delete("http://192.168.1.200:5123/Appraisal/Section/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Section deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Section');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
        }
    }
}
