<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMetricRequest;
use App\Http\Requests\UpdateMetricRequest;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalMetricController extends Controller
{
    private AppraisalApiService $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $metricsResponse = $this->appraisalService->getAllMetrics();
            $sectionsResponse = $this->appraisalService->getAllSections();

            // Handle both wrapped and unwrapped metrics response
            if (is_array($metricsResponse) && isset($metricsResponse['data']) && is_array($metricsResponse['data'])) {
                $metricsData = $metricsResponse['data'];
            } elseif (is_array($metricsResponse)) {
                $metricsData = $metricsResponse;
            } else {
                $metricsData = [];
            }

            // Handle both wrapped and unwrapped sections response
            if (is_array($sectionsResponse) && isset($sectionsResponse['data']) && is_array($sectionsResponse['data'])) {
                $sectionsData = $sectionsResponse['data'];
            } elseif (is_array($sectionsResponse)) {
                $sectionsData = $sectionsResponse;
            } else {
                $sectionsData = [];
            }

            $sections = collect($sectionsData);
            $metrics = collect($metricsData);

            Log::info('Global Metric Index - All Metrics count', ['count' => count($metricsData)]);

            $validSectionIds = $sections->filter(function ($section) {
                return isset($section['kpi']) &&
                    ($section['kpi']['type'] === 'GLOBAL' || $section['kpi']['type'] === 'PROBATION');
            })->pluck('id');

            $filteredMetrics = $metrics->filter(function ($metric) use ($validSectionIds) {
                return isset($metric['section']) && $validSectionIds->contains($metric['section']['id'] ?? null);
            });

            Log::info('Global Metric Index - Filtered Metrics count', ['count' => $filteredMetrics->count()]);

            // Filter for only active metrics
            $activeMetrics = $filteredMetrics->filter(function ($metric) {
                return $metric['active'] === true;
            });

            Log::info('Global Metric Index - Active Metrics count', ['count' => $activeMetrics->count()]);

            $sortedMetrics = $activeMetrics->sortByDesc('createdAt');
            $metrics = $this->paginate($sortedMetrics->toArray(), 25, $request);

            return view('global-kpi.index-metric', compact('metrics'));
        } catch (ApiException $e) {
            Log::error('Failed to load metrics', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    public function create()
    {
        try {
            $sectionsResponse = $this->appraisalService->getAllSections();

            // Handle both wrapped and unwrapped sections response
            if (is_array($sectionsResponse) && isset($sectionsResponse['data']) && is_array($sectionsResponse['data'])) {
                $sectionsData = $sectionsResponse['data'];
            } elseif (is_array($sectionsResponse)) {
                $sectionsData = $sectionsResponse;
            } else {
                $sectionsData = [];
            }

            $sections = collect($sectionsData);

            $activeSections = $sections->filter(function ($section) {
                return $section['active'] === true &&
                    isset($section['kpi']) &&
                    ($section['kpi']['type'] === 'GLOBAL' || $section['kpi']['type'] === 'PROBATION');
            });

            return view('global-kpi.create-metric', compact('activeSections'));
        } catch (ApiException $e) {
            Log::error('Failed to load sections', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }








    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetricRequest $request)
    {
        try {
            $this->appraisalService->createMetric([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => (float) $request->input('score'),
                'active' => $request->input('active') == 1 ? true : false,
                'sectionId' => $request->input('sectionId'),
            ]);

            return redirect()->route('global.metric.index')->with('toast_success', 'Metric created successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to create Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $metricResponse = $this->appraisalService->getMetric($id);
            $sectionsResponse = $this->appraisalService->getAllSections();

            // Handle wrapped and unwrapped metric response
            if (isset($metricResponse['data'])) {
                $metricData = is_array($metricResponse['data']) ? $metricResponse['data'] : $metricResponse['data'];
            } else {
                $metricData = is_array($metricResponse) && !isset($metricResponse['data']) ? $metricResponse : null;
            }

            // Handle both wrapped and unwrapped sections response
            if (is_array($sectionsResponse) && isset($sectionsResponse['data']) && is_array($sectionsResponse['data'])) {
                $sectionsData = $sectionsResponse['data'];
            } elseif (is_array($sectionsResponse)) {
                $sectionsData = $sectionsResponse;
            } else {
                $sectionsData = [];
            }

            $sections = collect($sectionsData);

            if (!$metricData) {
                return redirect()->back()->with('toast_error', 'Metric does not exist.');
            }

            $activeSections = $sections->filter(function ($section) {
                return $section['active'] === true &&
                    isset($section['kpi']) &&
                    ($section['kpi']['type'] === 'GLOBAL' || $section['kpi']['type'] === 'PROBATION');
            });

            return view('global-kpi.edit-metric', compact('metricData', 'activeSections'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMetricRequest $request, $id)
    {
        try {
            $this->appraisalService->updateMetric($id, [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => (float) $request->input('score'),
                'active' => (bool)$request->input('active'),
                'sectionId' => $request->input('sectionId'),
            ]);

            return redirect()->route('global.metric.index')->with('toast_success', 'Metric updated successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to update Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->appraisalService->deleteMetric($id);
            return redirect()->back()->with('toast_success', 'Metric deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
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
