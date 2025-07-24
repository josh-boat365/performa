<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        try {
            // Get the access token from the request or environment
            $accessToken = session('api_token'); // Replace with your actual access token

            // Make the GET request to the external API
            $response = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/batch');

            // Check the response status and return appropriate response
            if ($response->successful()) {
                $batch_data = $response->json();

                // Sort the batch_data array by creation date in descending order
                usort($batch_data, function ($a, $b) {
                    return strtotime($b['createdAt']) - strtotime($a['createdAt']);
                });

                // Get current page form url e.g. &page=1
                $currentPage = LengthAwarePaginator::resolveCurrentPage();

                // Define how many items we want to be visible in each page
                $perPage = 10;

                // Slice the collection to get the items to display in current page
                $currentItems = array_slice($batch_data, ($currentPage - 1) * $perPage, $perPage);

                // Create our paginator and pass it to the view
                $batches = new LengthAwarePaginator($currentItems, count($batch_data), $perPage);

                // Append query parameters to the pagination links
                $batches->setPath(request()->url());

                return view("batch-setup.index", compact('batches'));
            } else {
                return redirect()->back()->with('toast_error', 'Failed to fetch batches');
            }
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error during batch fetch', ['error' => $e->getMessage()]);

            // Return with a toast error message to the user
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
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

        $accessToken = session('api_token');

        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/batch');

        $batch_data = collect($response->json());

        // Pluck batch by status where pending or open using filter method
        $batch_data = $batch_data->filter(function ($value, $key) {
            return $value['status'] == 'PENDING' || $value['status'] == 'OPEN';
        });

        // Check if there is an existing PENDING or OPEN batch
        if ($batch_data->count() > 0) {
            return redirect()->back()->with(
                'toast_error',
                'Sorry, you cannot create a new batch while there is an existing PENDING or OPEN batch. <br> Batches: ' .
                    $batch_data->implode('name', ', ')
            );
        }

        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
        ]);

        // Prepare the data for the batch creation
        $batchData = [
            'name' => $request->input('name'),
            'year' => $request->input('year'),
        ];

        try {
            // Make the POST request to the external API
            $response = Http::withToken($accessToken)
                ->post('http://192.168.1.200:5123/Appraisal/batch', $batchData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->back()->with('toast_success', 'Batch created successfully');
            } else {
                // Log the error response
                Log::error('Failed to create batch', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to create batch');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while creating batch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
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

        try {
            // Make the GET request to the external API
            $response = Http::withToken($accessToken)
                ->get('http://192.168.1.200:5123/Appraisal/batch/' . $id);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                $batch_data = $response->json();

                return view('batch-setup.edit', compact('batch_data'));
            } else {
                // Log the error response
                Log::error('Failed to fetch batch', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Batch does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while fetching batch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

        // Validate the request data
        $request->validate([
            'name' => 'required|string',
            'year' => 'required|integer',
            // 'status' => 'required|string',
            'active' => 'required|integer',
        ]);

        // dd($request);

        // Get the access token from the request or environment
        $accessToken = session('api_token'); // Replace with your actual access token

        // Prepare the data for the batch update
        $batchData = [
            'id' => $id,
            'name' => $request->input('name'),
            'year' => $request->input('year'),
            // 'status' => $request->input('status'),
            'active' => $request->input('active') == 1 ? true : false, // Convert to boolean
        ];

        // dd($batchData);

        try {
            // Make the PUT request to the external API
            $response = Http::withToken($accessToken)
                ->put('http://192.168.1.200:5123/Appraisal/batch', $batchData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('batch.setup.index')->with('toast_success', 'Batch updated successfully');
            } else {
                // Log the error response
                Log::error('Failed to update batch', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to update batch');
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

    public function update_state(Request $request, string $id)
    {
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

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
                ->put('http://192.168.1.200:5123/Appraisal/batch/update-activation', $batchData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('batch.setup.index')->with('toast_success', 'Batch state updated successfully');
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
        // Validate session
        // $sessionValidation = ValidateSessionController::validateSession();
        // if ($sessionValidation) {
        //     return $sessionValidation;
        // }

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
                ->put('http://192.168.1.200:5123/Appraisal/batch/update-status', $batchData);

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('batch.setup.index')->with('toast_success', 'Batch status updated successfully');
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Get the access token from the session
        $accessToken = session('api_token'); // Replace with your actual access token

        try {
            // Make the DELETE request to the external API
            $response = Http::withToken($accessToken)
                ->delete("http://192.168.1.200:5123/Appraisal/Batch/{$id}");

            // Check the response status and return appropriate response
            if ($response->successful()) {
                return redirect()->route('batch.setup.index')->with('toast_success', 'Batch deleted successfully');
            } else {
                // Log the error response
                Log::error('Failed to delete batch', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return redirect()->back()->with('toast_error', 'Sorry, failed to delete batch');
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while deleting batch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }
}
