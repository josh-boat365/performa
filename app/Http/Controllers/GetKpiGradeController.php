<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\AppraisalApiService;
use App\Exceptions\ApiException;

class GetKpiGradeController extends Controller
{
    private AppraisalApiService $appraisalService;

    public function __construct(AppraisalApiService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }

    public function getGrade($kpiId, $batchId, $employeeId)
    {
        try {
            $submittedEmployeeGradeData = [
                'employeeId' => (int) $employeeId,
                'kpiId' => (int) $kpiId,
                'batchId' => (int) $batchId,
                'status' => 'SCORING'
            ];

            $supervisorGradeForEmployeeData = [
                'employeeId' => (int) $employeeId,
                'kpiId' => (int) $kpiId,
                'batchId' => (int) $batchId,
                'status' => 'REVIEW'
            ];

            $submittedEmployeeGrade = $this->appraisalService->getEmployeeTotalGrade($submittedEmployeeGradeData);
            $supervisorGradeForEmployee = $this->appraisalService->getEmployeeTotalGrade($supervisorGradeForEmployeeData);

            return (object)[
                'submittedEmployeeGrade' => $submittedEmployeeGrade,
                'supervisorGradeForEmployee' => $supervisorGradeForEmployee
            ];
        } catch (ApiException $e) {
            Log::error('Failed to retrieve employee grades', [
                'kpiId' => $kpiId,
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'message' => $e->getMessage()
            ]);
            return (object)[
                'submittedEmployeeGrade' => [],
                'supervisorGradeForEmployee' => []
            ];
        }
    }
}
