<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $kpiScore, $id)
    {
        try {
            // Fetch sections data using helper method
            $sectionsResponse = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Section");
            $kpis = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Kpi");

            // Filter the KPIs to include only those with active state of true
            $activeKpis = collect($kpis)->filter(function ($kpi) {
                return $kpi->active === true && $kpi->type == 'GLOBAL';
            });

            // Convert the response to a collection
            $sectionsCollect = collect($sectionsResponse);
            // $sections = $sectionsResponse;
            // dd($sectionsCollect);


            // Filter sections where active is true and the KPI type is 'GLOBAL' or 'PROBATION'
            $filteredSections =
                $sectionsCollect->filter(function ($section) {
                    return ($section->active === true || $section->active === false) &&
                        ($section->kpi->type === 'GLOBAL' || $section->kpi->type === 'PROBATION');
                });

            $sections = $filteredSections->sortByDesc('createdAt');
            // dd($sections);


            $sections = $this->paginate($sections, 25, $request);



            return view('global-kpi.index-section', compact('sections', 'activeKpis'));
        } catch (\Exception $e) {
            Log::error('Exception occurred in index method', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('toast_error', 'Failed to load sections. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $kpis = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Kpi");

        // Filter the KPIs to include only those with active state of true
        $activeKpis = collect($kpis)->filter(function ($kpi) {
            return $kpi->active === true &&  $kpi->type == 'GLOBAL' || $kpi->type == 'PROBATION';
        });

        // dd($activeKpis);

        return view('global-kpi.create-section', compact('activeKpis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|numeric',
            'active' => 'required|integer',
            'kpiId' => 'required|integer',
        ]);

        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the Section creation
        $sectionData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'score' => (float) $request->input('score'),
            'active' => $request->input('active') == 1 ? true : false,
            'kpiId' => $request->input('kpiId'),
        ];

        try {
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)
                ->post('http://192.168.1.200:5123/Appraisal/Section', $sectionData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('global.section.index')->with('toast_success', 'Global Section created successfully');
            } else {
                // Log the error response
                Log::error('Failed to create Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to Global create Section'. $response->body());
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while creating Global Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
        }
    }



    public function show(string $id)
    {
        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Section/{$id}";

        $kpis = $this->makeApiRequest('GET', "http://192.168.1.200:5123/Appraisal/Kpi");

        // Filter the KPIs to include only those with active state of true
        $activeKpis = collect($kpis)->filter(function ($kpi) {
            return $kpi->active === true &&  $kpi->type == 'GLOBAL' || $kpi->type == 'PROBATION';
        });

        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)->get($apiUrl);

            if ($response->successful()) {
                // Convert the response to an object
                $sectionData = $response->object();

                return view('global-kpi.edit-section', compact('sectionData', 'activeKpis'));
            }

            // Log the error response
            Log::error('Failed to fetch Global Section', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return redirect()->back()->with('toast_error', 'Global Section does not exist.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Global Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with(
                'toast_error',
                'Something went wrong, check your internet and try again, <b>Or Contact IT</b>'
            );
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'score' => 'required|numeric',
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
            'score' => (float) $request->input('score'),
            'active' => (bool)$request->input('active'),
            'kpiId' => $request->input('kpiId'),
        ];

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)->put($apiUrl, $sectionData);

            if ($response->successful()) {

                return redirect()->route('global.section.index')->with('toast_success', 'Global Section updated successfully.');
            }

            // Log the error response
            Log::error('Failed to update Global Section', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Update Global Section Error:' . $response->body());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Global Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with(
                'toast_error',
                'Something went wrong, check your internet and try again, <b>Or Contact IT</b>'
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
                return redirect()->back()->with('toast_success', 'Global Section deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete Global Section', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Global Section, there are Metrics <br> dependent on this Section and can not be deleted, <b>DEACTIVATE INSTEAD</b>');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Global Section', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact IT</b>');
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
