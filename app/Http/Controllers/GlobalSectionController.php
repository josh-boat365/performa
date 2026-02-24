<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GlobalSectionController extends Controller
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

            Log::info('Global Section Index - All Sections count', ['count' => count($sectionsData)]);

            // Log first section to see structure if there are any
            if (!empty($sectionsData)) {
                Log::info('Global Section Index - First Section structure', [
                    'first_section_keys' => array_slice(array_keys($sectionsData[0]), 0, 10),
                    'has_kpi_nested' => isset($sectionsData[0]['kpi']),
                    'kpi_type' => $sectionsData[0]['kpi']['type'] ?? 'NOT_FOUND'
                ]);
            }

            // Filter sections where the KPI type is 'GLOBAL' or 'PROBATION'
            $filteredSections = $sections->filter(function ($section) {
                // Check if kpi is directly nested in section
                if (isset($section['kpi']) && is_array($section['kpi'])) {
                    return ($section['kpi']['type'] === 'GLOBAL' || $section['kpi']['type'] === 'PROBATION');
                }
                return false;
            });

            Log::info('Global Section Index - Filtered Sections count', ['count' => $filteredSections->count()]);

            $sortedSections = $filteredSections->sortByDesc('createdAt');
            $sections = $this->paginate($sortedSections->toArray(), 25, $request);

            // Get all KPIs for reference
            $kpisResponse = $this->appraisalService->getAllKpis();
            if (is_array($kpisResponse) && isset($kpisResponse['data']) && is_array($kpisResponse['data'])) {
                $kpisData = $kpisResponse['data'];
            } elseif (is_array($kpisResponse)) {
                $kpisData = $kpisResponse;
            } else {
                $kpisData = [];
            }

            $kpis = collect($kpisData);
            $activeKpis = $kpis->filter(function ($kpi) {
                return $kpi['active'] === true && ($kpi['type'] === 'GLOBAL' || $kpi['type'] === 'PROBATION');
            });

            return view('global-kpi.index-section', compact('sections', 'activeKpis'));
        } catch (ApiException $e) {
            Log::error('Failed to load sections', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $kpisResponse = $this->appraisalService->getAllKpis();

            // Handle both wrapped and unwrapped responses
            if (is_array($kpisResponse) && isset($kpisResponse['data']) && is_array($kpisResponse['data'])) {
                $kpisData = $kpisResponse['data'];
            } elseif (is_array($kpisResponse)) {
                $kpisData = $kpisResponse;
            } else {
                $kpisData = [];
            }

            $kpis = collect($kpisData);

            // Filter for active GLOBAL/PROBATION KPIs
            $activeKpis = $kpis->filter(function ($kpi) {
                return $kpi['active'] === true && ($kpi['type'] === 'GLOBAL' || $kpi['type'] === 'PROBATION');
            });

            return view('global-kpi.create-section', compact('activeKpis'));
        } catch (ApiException $e) {
            Log::error('Failed to load KPIs for section creation', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request)
    {
        try {
            $this->appraisalService->createSection([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => (float) $request->input('score'),
                'active' => $request->input('active') == 1 ? true : false,
                'kpiId' => $request->input('kpiId'),
            ]);

            return redirect()->route('global.section.index')->with('toast_success', 'Global Section created successfully');
        } catch (ApiException $e) {
            Log::error('Failed to create Global Section', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }



    public function show(string $id)
    {
        try {
            $kpisResponse = $this->appraisalService->getAllKpis();
            $sectionResponse = $this->appraisalService->getSection($id);

            // Handle wrapped and unwrapped section response
            if (isset($sectionResponse['data'])) {
                $sectionData = is_array($sectionResponse['data']) ? $sectionResponse['data'] : $sectionResponse['data'];
            } else {
                $sectionData = is_array($sectionResponse) && !isset($sectionResponse['data']) ? $sectionResponse : null;
            }

            // Handle both wrapped and unwrapped KPI responses
            if (is_array($kpisResponse) && isset($kpisResponse['data']) && is_array($kpisResponse['data'])) {
                $kpisData = $kpisResponse['data'];
            } elseif (is_array($kpisResponse)) {
                $kpisData = $kpisResponse;
            } else {
                $kpisData = [];
            }

            $kpis = collect($kpisData);

            if (!$sectionData) {
                return redirect()->back()->with('toast_error', 'Global Section does not exist.');
            }

            // Filter for active GLOBAL/PROBATION KPIs
            $activeKpis = $kpis->filter(function ($kpi) {
                return $kpi['active'] === true && ($kpi['type'] === 'GLOBAL' || $kpi['type'] === 'PROBATION');
            });

            return view('global-kpi.edit-section', compact('sectionData', 'activeKpis'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Global Section', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, string $id)
    {
        try {
            $this->appraisalService->updateSection($id, [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => (float) $request->input('score'),
                'active' => (bool)$request->input('active'),
                'kpiId' => $request->input('kpiId'),
            ]);

            return redirect()->route('global.section.index')->with('toast_success', 'Global Section updated successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to update Global Section', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->appraisalService->deleteSection($id);
            return redirect()->back()->with('toast_success', 'Global Section deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete Global Section', ['message' => $e->getMessage()]);
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
