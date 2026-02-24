<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    private AppraisalApiService $appraisalService;

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
            $grades = $this->appraisalService->getAllGrades();

            return view('grade.index', compact('grades'));
        } catch (ApiException $e) {
            Log::error('Failed to load grades', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
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
    public function store(StoreGradeRequest $request)
    {
        try {
            $this->appraisalService->createGrade([
                'grade' => Str::upper($request->input('grade')),
                'minScore' => (float) $request->input('minScore'),
                'maxScore' => (float) $request->input('maxScore'),
                'remark' => $request->input('remark'),
            ]);

            return redirect()->route('grade.index')->with('toast_success', 'Grade created successfully');
        } catch (ApiException $e) {
            Log::error('Failed to create Grade', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $grade = $this->appraisalService->getGrade($id);

            if (!$grade) {
                return redirect()->back()->with('toast_error', 'Grade does not exist.');
            }

            return view('grade.edit', compact('grade'));
        } catch (ApiException $e) {
            Log::error('Failed to fetch Grade', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeRequest $request, string $id)
    {
        try {
            $this->appraisalService->updateGrade($id, [
                'id' => $id,
                'grade' => Str::upper($request->input('grade')),
                'minScore' => (float) $request->input('minScore'),
                'maxScore' => (float) $request->input('maxScore'),
                'remark' => $request->input('remark'),
            ]);

            return redirect()->route('grade.index')->with('toast_success', 'Grade updated successfully.');
        } catch (ApiException $e) {
            Log::error('Failed to update Grade', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->appraisalService->deleteGrade($id);
            return redirect()->back()->with('toast_success', 'Grade deleted successfully');
        } catch (ApiException $e) {
            Log::error('Failed to delete Grade', ['message' => $e->getMessage()]);
            return redirect()->back()->with('toast_error', $e->getMessage());
        }
    }
}
