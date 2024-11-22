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
    // public function index($kpiId )
    // {
    //     // $kpiId = $id;
    //     $accessToken = session('api_token');


    //     // Fetch sections data
    //     $responseSection = Http::withToken($accessToken)
    //         ->get("http://192.168.1.200:5123/Appraisal/Section");

    //         // dd($responseSection->json());

    //     // if ($response->successful()) {
    //     //     $kpi = $response->json();
    //     //     $kpi_data = [
    //     //         'id' => $kpi['id'],
    //     //         'name' => $kpi['name']
    //     //     ];
    //     // }

    //     if ($responseSection->successful()) {
    //         $sections = $responseSection->json();

    //         // Filter sections where kpi->id matches $kpiId
    //         $filteredSections = array_filter($sections, function ($section) use ($kpiId) {
    //             return $section['kpi']['id'] == $kpiId;
    //         });

    //         // dd($filteredSections);

    //         foreach ($filteredSections as $kpi_section) {
    //             if ($kpi_section['kpi']['id'] == $kpiId) {
    //                 session(
    //                     [
    //                         'kpi_section_id' => $kpi_section['kpi']['id'],
    //                         'kpi_section_name' => $kpi_section['kpi']['name'],
    //                     ]
    //                 );
    //             }
    //         }
    //     }

    //     return view("section-setup.index", compact('filteredSections'));
    // }

    public function index()
    {
        try {
            // Fetch sections data using helper method
            $sections = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Section");
            $kpis = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Kpi");

            // Filter the KPIs to include only those with active state of true
            $activeKpis = collect($kpis)->filter(function ($kpi) {
                return $kpi->active === true;
            });


            return view('section-setup.index', compact('sections', 'activeKpis'));
        } catch (\Exception $e) {
            Log::error('Exception occurred in index method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load sections. Please try again.');
        }
    }

    /**
     * Helper method to make API requests.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $url API URL
     * @param array|null $data Request payload
     * @return object|null
     */
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

    


    public function show(string $id)
    {
        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Section/{$id}";

        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)->get($apiUrl);

            if ($response->successful()) {
                // Convert the response to an object
                $sectionData = $response->object();

                return view('section-setup.edit', compact('sectionData'));
            }

            // Log the error response
            Log::error('Failed to fetch Section', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return redirect()->back()->with('toast_error', 'Section does not exist.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
    //         'kpiId' => 'required|integer',
    //     ]);

    //     // Get the access token from the session
    //     $accessToken = session('api_token'); // Replace with your actual access token

    //     // Prepare the data for the Section update
    //     $sectionData = [
    //         'id' => $id,
    //         'name' => $request->input('name'),
    //         'description' => $request->input('description'),
    //         'score' => $request->input('score'),
    //         'active' => $request->input('active') == 1 ? true : false,
    //         'kpiId' => $request->input('kpiId'),
    //     ];

    //     try {
    //         // Make the PUT request to the external API
    //         $response = Http::withToken($accessToken)
    //             ->put("http://192.168.1.200:5123/Appraisal/Section/", $sectionData);

    //         // Check the response status and return appropriate response
    //         if ($response->successful()) {
    //             return redirect()->route('kpi.index')->with('toast_success', 'Section updated successfully');
    //         } else {
    //             // Log the error response
    //             Log::error('Failed to update Section', [
    //                 'status' => $response->status(),
    //                 'response' => $response->body()
    //             ]);
    //             return redirect()->back()->with('toast_error', 'Sorry, failed to update Section');
    //         }
    //     } catch (\Exception $e) {
    //         // Log the exception
    //         Log::error('Exception occurred while updating Section', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again.');
    //     }
    // }


    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|integer',
            'active' => 'required|boolean',
            'kpiId' => 'required|integer',
        ]);

        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Section/";

        // Prepare the data for the Section update
        $sectionData = [
            'id' => $id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => $request->input('score'),
            'active' => (bool)$request->input('active'),
            'kpiId' => $request->input('kpiId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)->put($apiUrl, $sectionData);

            if ($response->successful()) {
                return redirect()
                    ->route('section.index')
                    ->with('toast_success', 'Section updated successfully.');
            }

            // Log the error response
            Log::error('Failed to update Section', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Sorry, failed to update Section.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Section', [
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
