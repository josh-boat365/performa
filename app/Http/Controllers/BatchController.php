<?php

namespace App\Http\Controllers;

use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class BatchController extends Controller
{
    protected $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get all batches using the service
            $batch_data = $this->appraisalService->getAllBatches();

            // Sort the batch_data array by creation date in descending order
            usort($batch_data, function ($a, $b) {
                return strtotime($b['createdAt'] ?? 0) - strtotime($a['createdAt'] ?? 0);
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
        } catch (ApiException $e) {
            Log::error('API Error during batch fetch', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
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
    public function store(StoreBatchRequest $request)
    {
        try {
            // Get all batches to check if there's an existing PENDING or OPEN batch
            $batch_data = collect($this->appraisalService->getAllBatches());

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

            // Prepare the data for the batch creation
            $batchData = [
                'name' => $request->input('name'),
                'year' => $request->input('year'),
            ];

            // Create the batch using the service
            $response = $this->appraisalService->createBatch($batchData);

            return redirect()->back()->with('toast_success', 'Batch created successfully');
        } catch (ApiException $e) {
            Log::error('API Error during batch creation', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
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
        try {
            // Get batch by ID using the service
            $batch_data = $this->appraisalService->getBatch($id);

            return view('batch-setup.edit', compact('batch_data'));
        } catch (ApiException $e) {
            Log::error('API Error during batch fetch', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
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
    public function update(UpdateBatchRequest $request, string $id)
    {
        try {
            // Prepare the data for the batch update
            $batchData = [
                'id' => $id,
                'name' => $request->input('name'),
                'year' => $request->input('year'),
                'active' => $request->input('active') == 1 ? true : false, // Convert to boolean
            ];

            // Update the batch using the service
            $response = $this->appraisalService->updateBatch($id, $batchData);

            return redirect()->route('batch.setup.index')->with('toast_success', 'Batch updated successfully');
        } catch (ApiException $e) {
            Log::error('API Error during batch update', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
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
        // Validate the request data
        $request->validate([
            'active' => 'required|integer',
        ]);

        try {
            // Prepare the data for the batch state update
            $batchData = [
                'id' => $id,
                'active' => $request->input('active') == 1 ? true : false, // Convert to boolean
            ];

            // Update batch activation using the service
            $response = $this->appraisalService->updateBatchState($id, $batchData);

            return redirect()->route('batch.setup.index')->with('toast_success', 'Batch state updated successfully');
        } catch (ApiException $e) {
            Log::error('API Error during batch state update', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating batch state', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('toast_error', 'Something went wrong, check your internet and try again, <b>Or Contact Application Support</b>');
        }
    }

    public function update_status(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'status' => 'required|string|in:PENDING,OPEN,CLOSED,COMPLETED',
        ]);

        try {
            // Prepare the data for the batch status update
            $batchData = [
                'id' => $id,
                'status' => $request->input('status'),
            ];

            // Update batch status using the service
            $response = $this->appraisalService->updateBatchStatus($id, $batchData);

            return redirect()->route('batch.setup.index')->with('toast_success', 'Batch status updated successfully');
        } catch (ApiException $e) {
            Log::error('API Error during batch status update', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', 'Failed to update batch status: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Exception occurred while updating batch status', [
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
        try {
            // Delete batch using the service
            $response = $this->appraisalService->deleteBatch($id);

            return redirect()->route('batch.setup.index')->with('toast_success', 'Batch deleted successfully');
        } catch (ApiException $e) {
            Log::error('API Error during batch deletion', ['error' => $e->getMessage(), 'status' => $e->getStatusCode()]);
            return redirect()->back()->with('toast_error', 'Failed to delete batch: ' . $e->getMessage());
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
