<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accessToken = session('api_token');

        if (!$accessToken) {
            return redirect()->route('login')->with('toast_error', 'We can not find session, please login again'); // Redirect to login if token is missing
        }

        $gradesResponse = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Grade');

        if ($gradesResponse->successful()) {
            $grades = $gradesResponse->object();
        }






        return view('grade.index', compact('grades'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('grade.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'grade' => 'required|string|min:1|max:3',
            'minScore' => 'required|numeric',
            'maxScore' => 'required|numeric',
            'remark' => 'required|string',
        ]);

        // dd($request);

        // Prepare the data for KPI creation
        $kpiData = [
            'grade' => (Str::upper($request->input('grade'))),
            'minScore' => (float) $request->input('minScore'),
            'maxScore' => (float) $request->input('maxScore'),
            'remark' =>  $request->input('remark'),
        ];

        $accessToken = session('api_token');

        // Send the request to the API
        $response = Http::withToken($accessToken)
            ->post('http://192.168.1.200:5123/Appraisal/Grade', $kpiData,);

        // Check the response and redirect
        if ($response->successful()) {
            return redirect()->route('grade.index')->with('toast_success', 'Grade created successfully');
        }

        // Log errors (if any)
        Log::error('Failed to create Grade', [
            'status' => $response->status ?? 'N/A',
            'response' => $response->data ?? 'No response received',
        ]);

        return redirect()->back()->with('toast_error', 'Sorry, failed to create Grade');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Grade/{$id}";


        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)->get($apiUrl);

            if ($response->successful()) {
                // Convert the response to an object
                $grade = $response->object();

                return view('grade.edit', compact('grade'));
            }

            // Log the error response
            Log::error('Failed to fetch Grade', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return redirect()->back()->with('toast_error', 'Grade does not exist.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching Grade', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with(
                'toast_error',
                'There is no internet connection. Please check your internet and try again, <b>Or Contact IT</b>'
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request);
        // Validate the request data
        $request->validate([
            'grade' => 'string|min:1|max:3',
            'minScore' => 'integer',
            'maxScore' => 'integer',
            'remark' => 'string',
        ]);

        $accessToken = session('api_token');
        $apiUrl = "http://192.168.1.200:5123/Appraisal/Grade";

        $grade = Str::upper($request->input('grade'));

        // Prepare the data for KPI creation
        $globalWeightData = [
            'id' => $id,
            'grade' => $grade,
            'minScore' => (float) $request->input('minScore'),
            'maxScore' => (float) $request->input('maxScore'),
            'remark' => $request->input('remark'),
        ];

        // dd($globalWeightData);

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)->put($apiUrl, $globalWeightData);

            // dd($response);

            if ($response->successful()) {
                // $json_message = response()->json(['message' => 'Section updated successfully.']);
                return redirect()
                    ->route('grade.index')
                    ->with('toast_success', 'Grade updated successfully.');
            }

            // Log the error response
            Log::error('Failed to update Grade', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return redirect()->back()->with('toast_error', 'Update Grade Error:' . $response->body());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating Grade', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with(
                'toast_error',
                'There is no internet connection. Please check your internet and try again, <b>Or Contact IT</b>'
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
                ->delete("http://192.168.1.200:5123/Appraisal/Grade/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Grade deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete Grade', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete Grade, there are Section <br> dependent on this Metric and can not be deleted, <b>DEACTIVATE INSTEAD</b>');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting Grade', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'There is no internet connection. Please check your internet and try again, <b>Or Contact IT</b>');
        }
    }
}
