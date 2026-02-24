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

class SectionController extends Controller
{
    private AppraisalApiService $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $kpiScore, $id)
    {
        try {
            $sectionsResponse = $this->appraisalService->getAllSections();
            // dd($sectionsResponse);
            $sections = collect($sectionsResponse['data'] ?? $sectionsResponse ?? []);

            // Filter sections by KPI ID and REGULAR type
            $activeSections = $sections->filter(function ($section) use ($id) {
                $kpiId = $section['kpiId'] ?? $section['kpi']['id'] ?? null;
                $kpiType = $section['kpi']['type'] ?? null;
                return $kpiId == $id && $kpiType === 'REGULAR';
            });

            $sortedSections = $activeSections->sortByDesc('createdAt');
            $totalSectionScore = $sortedSections->sum('score');
            session(['totalSectionScore' => $totalSectionScore]);

            $sections = $this->paginate($sortedSections->toArray(), 25, $request);
            $kpiId = $id;

            return view('section-setup.index', compact('sections', 'kpiId', 'kpiScore', 'totalSectionScore'));
        } catch (ApiException $e) {
            Log::error('Failed to load sections', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }

    public function create($id)
    {
        $kpiId = $id;
        $kpiScore = 100;
        $totalSectionScore = session('totalSectionScore');

        return view('section-setup.create', compact('kpiId', 'kpiScore', 'totalSectionScore'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request)
    {
        try {
            $kpiId = $request->input('kpiId');

            Log::info('Section Store - Request data', [
                'kpiId' => $kpiId,
                'name' => $request->input('name'),
                'score' => $request->input('score'),
            ]);

            // Prepare the data exactly like the old project
            $sectionData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => $request->input('score'),
                'active' => $request->input('active') == 1 ? true : false,
                'kpiId' => $kpiId,
            ];

            Log::info('Section Store - Sending data to API', $sectionData);

            $this->appraisalService->createSection($sectionData);

            $kpiScore = 100;
            $id = $kpiId;

            return redirect()->route('section.index', compact('kpiScore', 'id'))->with('toast_success', 'Section created successfully');
        } catch (ApiException $e) {
            Log::error('Failed to create Section', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }




    public function show(string $kpiId, $sectionId)
    {
        try {
            $sectionResponse = $this->appraisalService->getSection($sectionId);
            $sectionData = $sectionResponse['data']  ?? $sectionResponse ?? null;

            if (!$sectionData) {
                return redirect()->back()->with('toast_error', 'Section does not exist.');
            }

            $totalSectionScore = session('totalSectionScore');

            return view('section-setup.edit', compact('sectionData', 'kpiId', 'totalSectionScore'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Section', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, $kpiId, $id)
    {
        try {
            $this->appraisalService->updateSection($id, [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'score' => $request->input('score'),
                'active' => (bool)$request->input('active'),
                'kpiId' => $request->input('kpiId'),
            ]);

            $kpiScore = 100;
            $routeId = $kpiId;

            return redirect()->route('section.index', ['kpiScore' => $kpiScore, 'id' => $routeId])->with('toast_success', 'Section updated successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to update Section', ['message' => $e->getMessage()]);
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
            return redirect()->back()->with('toast_success', 'Section deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete Section', ['message' => $e->getMessage()]);
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
