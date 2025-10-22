<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GetKpiGradeController extends Controller
{
    public static function getGrade($kpiId, $batchId, $employeeId){
        // Get the access token from the session
        $accessToken = session('api_token');

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

        $responseEmployeeGrade = Http::withToken($accessToken)->put('http://192.168.1.200:5123/Appraisal/Score/get-employee-total-grade', $submittedEmployeeGradeData);
        $responseSupervisorGradeForEmployee =  Http::withToken($accessToken)->put('http://192.168.1.200:5123/Appraisal/Score/get-employee-total-grade', $supervisorGradeForEmployeeData);

        if ($responseEmployeeGrade->successful()) {
            $submittedEmployeeGrade = $responseEmployeeGrade->object();
        } else {
            Log::error('Failed to retrieve Submitted Employee Grade', [
                'status' => $responseEmployeeGrade->status(),
                'response' => $responseEmployeeGrade->body()
            ]);
            $submittedEmployeeGrade = [];
        }

        if ($responseSupervisorGradeForEmployee->successful()) {
            $supervisorGradeForEmployee = $responseSupervisorGradeForEmployee->object();
        } else {
            Log::error('Failed to retrieve Supervisor Grade For Employee', [
                'status' => $responseSupervisorGradeForEmployee->status(),
                'response' => $responseSupervisorGradeForEmployee->body()
            ]);
            $supervisorGradeForEmployee = [];
        }

        return (object)[
            'submittedEmployeeGrade' => $submittedEmployeeGrade,
            'supervisorGradeForEmployee' => $supervisorGradeForEmployee
        ];
    }
}
