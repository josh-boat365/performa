<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMetricRequest;
use App\Http\Requests\UpdateMetricRequest;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MetricController extends Controller
{
    private AppraisalApiService $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }
    public function index(Request $request, $kpiId, $sectionScore, $id)
    {
        try {
            $metricsResponse = $this->appraisalService->getAllMetrics();

            // Handle both wrapped and unwrapped response
            if (is_array($metricsResponse) && isset($metricsResponse['data']) && is_array($metricsResponse['data'])) {
                $metricsData = $metricsResponse['data'];
            } elseif (is_array($metricsResponse)) {
                $metricsData = $metricsResponse;
            } else {
                $metricsData = [];
            }

            $metrics = collect($metricsData);

            Log::info('Metric Index - All Metrics count', ['count' => count($metricsData)]);
            Log::info('Metric Index - Looking for section ID', ['sectionId' => $id, 'sectionId_type' => gettype($id)]);

            // Log first metric structure to understand the format
            if ($metrics->count() > 0) {
                $firstMetric = $metrics->first();
                Log::info('First metric structure', [
                    'metric_keys' => array_keys($firstMetric),
                    'metric_id_value' => $firstMetric['metricId'] ?? $firstMetric['id'] ?? 'N/A',
                    'has_section' => isset($firstMetric['section']),
                    'section_keys' => isset($firstMetric['section']) ? array_keys($firstMetric['section']) : [],
                    'has_sectionId_field' => isset($firstMetric['sectionId']),
                ]);
            }

            // Filter metrics by section ID - mirroring old project pattern
            $activeMetrics = $metrics->filter(function ($metric) use ($id) {
                // Try different paths to find section ID
                $sectionId = null;

                if (isset($metric['section']) && is_array($metric['section'])) {
                    // If section is an array/object
                    $sectionId = $metric['section']['sectionId'] ?? $metric['section']['id'] ?? null;
                } elseif (isset($metric['sectionId'])) {
                    // If sectionId is a direct field
                    $sectionId = $metric['sectionId'];
                }

                $matches = (int)$sectionId === (int)$id;

                Log::info('Filtering metric', [
                    'metric_id' => $metric['metricId'] ?? $metric['id'] ?? 'unknown',
                    'section_id_found' => $sectionId,
                    'target_id' => $id,
                    'target_id_type' => gettype($id),
                    'section_id_type' => gettype($sectionId),
                    'matches' => $matches
                ]);

                return $matches;
            });

            Log::info('Metric Index - Filtered Metrics count', ['count' => $activeMetrics->count()]);

            $sectionId = $id;
            $totalMetricScore = $activeMetrics->sum(function ($metric) {
                return $metric['metricScore'] ?? $metric['score'] ?? 0;
            });
            session(['totalMetricScore' => $totalMetricScore]);

            $sortedMetrics = $activeMetrics->sortByDesc(function ($metric) {
                return $metric['createdAt'] ?? $metric['metricCreatedAt'] ?? '';
            });
            $metrics = $this->paginate($sortedMetrics->toArray(), 25, $request);

            return view('metric-setup.index', compact('metrics', 'sectionId', 'totalMetricScore', 'sectionScore', 'kpiId'));
        } catch (ApiException $e) {
            Log::error('Failed to load metrics', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    public function create(string $kpiScore, $sectionMetricScore, $id)
    {
        $sectionId = $id;
        $totalMetricScore = session('totalMetricScore');

        return view('metric-setup.create', compact('sectionId', 'kpiScore', 'sectionMetricScore', 'totalMetricScore'));
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetricRequest $request)
    {
        try {
            $sectionId = $request->input('sectionId');
            $kpiId = $request->input('kpiId');
            $sectionMetricScore = $request->input('sectionMetricScore');

            Log::info('Metric Store - Request data', [
                'sectionId' => $sectionId,
                'kpiId' => $kpiId,
                'name' => $request->input('name'),
                'score' => $request->input('score'),
            ]);

            // Prepare the data exactly like the old project
            $metricData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => $request->input('score'),
                'active' => $request->input('active') == 1 ? true : false,
                'sectionId' => $sectionId,
            ];

            Log::info('Metric Store - Sending data to API', $metricData);

            $this->appraisalService->createMetric($metricData);

            $id = $sectionId;
            session(['kpiId' => $kpiId, 'sectionMetricScore' => $sectionMetricScore]);

            return redirect()->route('metric.index', compact('kpiId', 'sectionMetricScore', 'id'))->with('toast_success', 'Metric created successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to create Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show($kpiId, $sectionMetricScore, $sectionId, $metricId)
    {
        try {
            $metricResponse = $this->appraisalService->getMetric($metricId);
            $metricData = $metricResponse['data'] ?? $metricResponse ?? null;

            if (!$metricData) {
                return redirect()->back()->with('toast_error', 'Metric does not exist.');
            }

            return view('metric-setup.edit', compact('metricData', 'sectionId', 'kpiId', 'sectionMetricScore'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($metricId, $sectionId, $kpiId, $sectionMetricScore)
    {
        try {
            $metricResponse = $this->appraisalService->getMetric($metricId);
            $metricData = $metricResponse['data'] ?? $metricResponse ?? null;

            if (!$metricData) {
                return redirect()->back()->with('toast_error', 'Metric does not exist.');
            }

            return view('metric-setup.edit', compact('metricData', 'sectionId', 'kpiId', 'sectionMetricScore'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Metric', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', 'Failed to load metric. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMetricRequest $request, $id)
    {
        try {
            $sectionId = $request->input('sectionId');
            $kpiId = $request->input('kpiId');
            $sectionMetricScore = $request->input('sectionMetricScore');

            $this->appraisalService->updateMetric($id, [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => $request->input('score'),
                'active' => (bool)$request->input('active'),
                'sectionId' => $sectionId,
            ]);

            $routeId = $sectionId;
            return redirect()->route('metric.index', ['kpiId' => $kpiId, 'sectionMetricScore' => $sectionMetricScore, 'id' => $routeId])->with('toast_success', 'Metric updated successfully.');
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
