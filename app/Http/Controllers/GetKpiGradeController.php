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

            $submittedEmployeeGradeResponse = $this->appraisalService->getEmployeeTotalGrade($submittedEmployeeGradeData);
            $supervisorGradeForEmployeeResponse = $this->appraisalService->getEmployeeTotalGrade($supervisorGradeForEmployeeData);

            // Handle response that might be wrapped in 'data' key
            $submittedEmployeeGrade = $submittedEmployeeGradeResponse['data'] ?? $submittedEmployeeGradeResponse;
            $supervisorGradeForEmployee = $supervisorGradeForEmployeeResponse['data'] ?? $supervisorGradeForEmployeeResponse;

            // Convert arrays to objects for blade template compatibility
            return (object)[
                'submittedEmployeeGrade' => !empty($submittedEmployeeGrade) ? (object) $submittedEmployeeGrade : null,
                'supervisorGradeForEmployee' => !empty($supervisorGradeForEmployee) ? (object) $supervisorGradeForEmployee : null
            ];
        } catch (ApiException $e) {
            Log::error('Failed to retrieve employee grades', [
                'kpiId' => $kpiId,
                'batchId' => $batchId,
                'employeeId' => $employeeId,
                'message' => $e->getMessage()
            ]);
            return (object)[
                'submittedEmployeeGrade' => null,
                'supervisorGradeForEmployee' => null
            ];
        }
    }
}
