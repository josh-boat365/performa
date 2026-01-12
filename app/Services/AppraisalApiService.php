<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Exceptions\ApiException;

/**
 * AppraisalApiService
 *
 * Service for communicating with the Appraisal API. Handles all operations
 * related to KPIs, batches, scores, sections, metrics, and grades.
 */
class AppraisalApiService extends BaseApiService
{
    public function __construct()
    {
        $this->initialize();
    }

    protected function getServiceName(): string
    {
        return 'Appraisal Service';
    }

    protected function getConfigKey(): string
    {
        return 'appraisal';
    }

    /**
     * Authenticate user with the Appraisal API
     *
     * @param string $appName The application name
     * @param string $username The username
     * @param string $password The password
     * @param bool $validateAppAccess Whether to validate app access
     * @return array The authentication response containing access_token and profile
     * @throws ApiException
     */
    public function login(
        string $appName,
        string $username,
        string $password,
        bool $validateAppAccess = true
    ): array {
        return $this->post($this->getEndpoint('login'), [
            'appName' => $appName,
            'user' => $username,
            'password' => $password,
            'validateAppAcess' => $validateAppAccess, // Note: API has typo in 'Acess'
        ]);
    }

    /**
     * Get all KPIs
     *
     * @return array List of KPIs
     * @throws ApiException
     */
    public function getAllKpis(): array
    {
        return $this->get($this->getEndpoint('kpi'));
    }

    /**
     * Get KPI by ID
     *
     * @param int|string $kpiId The KPI ID
     * @return array KPI details
     * @throws ApiException
     */
    public function getKpi($kpiId): array
    {
        return $this->get($this->getEndpoint('kpi') . "/{$kpiId}");
    }

    /**
     * Get all KPIs for an employee
     *
     * @return array List of employee KPIs
     * @throws ApiException
     */
    public function getAllKpisForEmployee(): array
    {
        return $this->get($this->getEndpoint('kpi') . '/GetAllKpiForEmployee');
    }

    /**
     * Get KPI for specific employee
     *
     * @param int|string $kpiId The KPI ID
     * @return array KPI details for employee
     * @throws ApiException
     */
    public function getKpiForEmployee($kpiId): array
    {
        return $this->get($this->getEndpoint('kpi') . "/GetKpiForEmployee/{$kpiId}");
    }

    /**
     * Create a new KPI
     *
     * @param array $data KPI data
     * @return array Created KPI details
     * @throws ApiException
     */
    public function createKpi(array $data): array
    {
        return $this->post($this->getEndpoint('kpi'), $data);
    }

    /**
     * Update a KPI
     *
     * @param int|string $kpiId The KPI ID
     * @param array $data Updated KPI data
     * @return array Updated KPI details
     * @throws ApiException
     */
    public function updateKpi($kpiId, array $data): array
    {
        return $this->put($this->getEndpoint('kpi') . "/{$kpiId}", $data);
    }

    /**
     * Update KPI activation state (active/inactive)
     *
     * @param int|string $kpiId The KPI ID
     * @param array $data Activation data (should include 'id' and 'active' keys)
     * @return array Updated state
     * @throws ApiException
     */
    public function updateKpiActivation($kpiId, array $data): array
    {
        // Note: Backend uses lowercase 'kpi' for this endpoint
        return $this->put('/Appraisal/kpi/update-activation', $data);
    }

    /**
     * Update KPI type
     *
     * @param int|string $kpiId The KPI ID
     * @param array $data Type update data (should include 'id' and 'type' keys)
     * @return array Updated type
     * @throws ApiException
     */
    public function updateKpiType($kpiId, array $data): array
    {
        return $this->put($this->getEndpoint('kpi') . '/update-type', $data);
    }

    /**
     * Delete a KPI
     *
     * @param int|string $kpiId The KPI ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteKpi($kpiId): array
    {
        return $this->delete($this->getEndpoint('kpi') . "/{$kpiId}");
    }

    // ======================== Batch Operations ========================

    /**
     * Get all batches
     *
     * @return array List of batches
     * @throws ApiException
     */
    public function getAllBatches(): array
    {
        return $this->get($this->getEndpoint('batch'));
    }

    /**
     * Get batch by ID
     *
     * @param int|string $batchId The batch ID
     * @return array Batch details
     * @throws ApiException
     */
    public function getBatch($batchId): array
    {
        return $this->get($this->getEndpoint('batch') . "/{$batchId}");
    }

    /**
     * Create a new batch
     *
     * @param array $data Batch data
     * @return array Created batch
     * @throws ApiException
     */
    public function createBatch(array $data): array
    {
        return $this->post($this->getEndpoint('batch'), $data);
    }

    /**
     * Update a batch
     *
     * @param int|string $batchId The batch ID
     * @param array $data Updated batch data (should include 'id' key)
     * @return array Updated batch
     * @throws ApiException
     */
    public function updateBatch($batchId, array $data): array
    {
        return $this->put($this->getEndpoint('batch'), $data);
    }

    /**
     * Update batch state (activation)
     *
     * @param int|string $batchId The batch ID
     * @param array $data State update data (should include 'id' and 'active' keys)
     * @return array Updated state
     * @throws ApiException
     */
    public function updateBatchState($batchId, array $data): array
    {
        return $this->put($this->getEndpoint('batch') . '/update-activation', $data);
    }

    /**
     * Update batch status
     *
     * @param int|string $batchId The batch ID
     * @param array $data Status update data (should include 'id' and 'status' keys)
     * @return array Updated status
     * @throws ApiException
     */
    public function updateBatchStatus($batchId, array $data): array
    {
        return $this->put($this->getEndpoint('batch') . '/update-status', $data);
    }

    /**
     * Delete a batch
     *
     * @param int|string $batchId The batch ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteBatch($batchId): array
    {
        return $this->delete($this->getEndpoint('batch') . "/{$batchId}");
    }

    // ======================== Score Operations ========================

    /**
     * Submit employee score
     *
     * @param array $scoreData The score data
     * @return array Score submission response
     * @throws ApiException
     */
    public function submitEmployeeScore(array $scoreData): array
    {
        return $this->post($this->getEndpoint('score') . '/employee-score', $scoreData);
    }

    /**
     * Submit supervisor score
     *
     * @param array $scoreData The score data
     * @return array Score submission response
     * @throws ApiException
     */
    public function submitSupervisorScore(array $scoreData): array
    {
        return $this->post($this->getEndpoint('score') . '/supervisor-score', $scoreData);
    }

    /**
     * Get employee appraisal
     *
     * @param int|string $employeeId The employee ID
     * @param int|string $batchId The batch ID
     * @return array Employee appraisal data
     * @throws ApiException
     */
    public function getEmployeeAppraisal($employeeId, $batchId): array
    {
        return $this->get($this->getEndpoint('score') . "/employee/{$employeeId}/batch/{$batchId}");
    }

    // ======================== Grade Operations ========================

    /**
     * Get all grades
     *
     * @return array List of grades
     * @throws ApiException
     */
    public function getAllGrades(): array
    {
        return $this->get($this->getEndpoint('grade'));
    }

    /**
     * Get grade by ID
     *
     * @param int|string $gradeId The grade ID
     * @return array Grade details
     * @throws ApiException
     */
    public function getGrade($gradeId): array
    {
        return $this->get($this->getEndpoint('grade') . "/{$gradeId}");
    }

    /**
     * Get employee grade for a batch
     *
     * @param int|string $batchId The batch ID
     * @param int|string $employeeId The employee ID
     * @return array Employee grade
     * @throws ApiException
     */
    public function getEmployeeGrade($batchId, $employeeId): array
    {
        $endpoint = $this->getEndpoint('grade') . "/batch/{$batchId}/employee/{$employeeId}";
        $response = $this->get($endpoint);
        // API returns data directly, not wrapped in ['data']
        return $response['data'] ?? $response ?? [];
    }

    /**
     * Get employee total KPI score for a batch
     *
     * @param array $data The request data (batchId, employeeId)
     * @return array Employee total KPI score and grade details
     * @throws ApiException
     */
    public function getEmployeeTotalKpiScore(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/employee-total-kpiscore', $data);
    }

    /**
     * Create a new grade
     *
     * @param array $data Grade data
     * @return array Created grade
     * @throws ApiException
     */
    public function createGrade(array $data): array
    {
        return $this->post($this->getEndpoint('grade'), $data);
    }

    /**
     * Update a grade
     *
     * @param int|string $gradeId The grade ID
     * @param array $data Updated grade data
     * @return array Updated grade
     * @throws ApiException
     */
    public function updateGrade($gradeId, array $data): array
    {
        return $this->put($this->getEndpoint('grade') . "/{$gradeId}", $data);
    }

    /**
     * Delete a grade
     *
     * @param int|string $gradeId The grade ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteGrade($gradeId): array
    {
        return $this->delete($this->getEndpoint('grade') . "/{$gradeId}");
    }

    // ======================== Section Operations ========================

    /**
     * Get all sections
     *
     * @return array List of sections
     * @throws ApiException
     */
    public function getAllSections(): array
    {
        return $this->get($this->getEndpoint('section'));
    }

    /**
     * Get section by ID
     *
     * @param int|string $sectionId The section ID
     * @return array Section details
     * @throws ApiException
     */
    public function getSection($sectionId): array
    {
        return $this->get($this->getEndpoint('section') . "/{$sectionId}");
    }

    /**
     * Create a new section
     *
     * @param array $data Section data
     * @return array Created section
     * @throws ApiException
     */
    public function createSection(array $data): array
    {
        return $this->post($this->getEndpoint('section'), $data);
    }

    /**
     * Update a section
     *
     * @param int|string $sectionId The section ID
     * @param array $data Updated section data (should include 'id' key)
     * @return array Updated section
     * @throws ApiException
     */
    public function updateSection($sectionId, array $data): array
    {
        return $this->put($this->getEndpoint('section'), $data);
    }

    /**
     * Delete a section
     *
     * @param int|string $sectionId The section ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteSection($sectionId): array
    {
        return $this->delete($this->getEndpoint('section') . "/{$sectionId}");
    }

    // ======================== Metric Operations ========================

    /**
     * Get all metrics
     *
     * @return array List of metrics
     * @throws ApiException
     */
    public function getAllMetrics(): array
    {
        return $this->get($this->getEndpoint('metric'));
    }

    /**
     * Get metric by ID
     *
     * @param int|string $metricId The metric ID
     * @return array Metric details
     * @throws ApiException
     */
    public function getMetric($metricId): array
    {
        return $this->get($this->getEndpoint('metric') . "/{$metricId}");
    }

    /**
     * Create a new metric
     *
     * @param array $data Metric data
     * @return array Created metric
     * @throws ApiException
     */
    public function createMetric(array $data): array
    {
        return $this->post($this->getEndpoint('metric'), $data);
    }

    /**
     * Update a metric
     *
     * @param int|string $metricId The metric ID
     * @param array $data Updated metric data (should include 'id' key)
     * @return array Updated metric
     * @throws ApiException
     */
    public function updateMetric($metricId, array $data): array
    {
        return $this->put($this->getEndpoint('metric'), $data);
    }

    /**
     * Delete a metric
     *
     * @param int|string $metricId The metric ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteMetric($metricId): array
    {
        return $this->delete($this->getEndpoint('metric') . "/{$metricId}");
    }

    // ======================== Weight Operations ========================

    /**
     * Get all weights
     *
     * @return array List of weights
     * @throws ApiException
     */
    public function getAllWeights(): array
    {
        return $this->get($this->getEndpoint('weight'));
    }

    /**
     * Get weight by ID
     *
     * @param int|string $weightId The weight ID
     * @return array Weight details
     * @throws ApiException
     */
    public function getWeight($weightId): array
    {
        return $this->get($this->getEndpoint('weight') . "/{$weightId}");
    }

    /**
     * Create a new weight
     *
     * @param array $data Weight data
     * @return array Created weight
     * @throws ApiException
     */
    public function createWeight(array $data): array
    {
        return $this->post($this->getEndpoint('weight'), $data);
    }

    /**
     * Update a weight
     *
     * @param int|string $weightId The weight ID
     * @param array $data Updated weight data
     * @return array Updated weight
     * @throws ApiException
     */
    public function updateWeight($weightId, array $data): array
    {
        return $this->put($this->getEndpoint('weight') . "/{$weightId}", $data);
    }

    /**
     * Delete a weight
     *
     * @param int|string $weightId The weight ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteWeight($weightId): array
    {
        return $this->delete($this->getEndpoint('weight') . "/{$weightId}");
    }

    // ======================== Score Status Operations ========================

    /**
     * Update score status for appraisal review
     *
     * @param array $data The status update data (kpiId, batchId, status)
     * @return array Status update response
     * @throws ApiException
     */
    public function updateScoreStatus(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/update-score-status', $data);
    }

    /**
     * Update employee score to probing state
     *
     * @param array $data The probing data (scoreId, prob)
     * @return array Update response
     * @throws ApiException
     */
    public function updateEmployeeScoreToProbing(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/UpdateEmployeeScoreToProb', $data);
    }

    // ======================== Supervisor Score Operations ========================

    /**
     * Get pending supervisor scoring KPIs
     *
     * @return array List of pending supervisor scoring KPIs
     * @throws ApiException
     */
    public function getPendingSupervisorScoringKpi(): array
    {
        return $this->get($this->getEndpoint('kpi') . '/PendingSupervisorScoringKpi');
    }

    /**
     * Get pending probing scoring KPIs
     *
     * @return array List of pending probing scoring KPIs
     * @throws ApiException
     */
    public function getPendingProbScoringKpi(): array
    {
        return $this->get($this->getEndpoint('kpi') . '/PendingProbScoringKpi');
    }

    /**
     * Get supervisor scoring KPI details
     *
     * @param array $data The request data (employeeId, kpiId, batchId)
     * @return array KPI scoring details
     * @throws ApiException
     */
    public function getSupervisorScoringKpi(array $data): array
    {
        return $this->put($this->getEndpoint('kpi') . '/GetSupervisorScoringKpi', $data);
    }

    /**
     * Get probing scoring KPI details
     *
     * @param array $data The request data (employeeId, kpiId, batchId)
     * @return array KPI probing scoring details
     * @throws ApiException
     */
    public function getProbScoringKpi(array $data): array
    {
        return $this->put($this->getEndpoint('kpi') . '/GetProbScoringKpi', $data);
    }

    /**
     * Submit probing score
     *
     * @param array $scoreData The probing score data
     * @return array Score submission response
     * @throws ApiException
     */
    public function submitProbScore(array $scoreData): array
    {
        return $this->put($this->getEndpoint('score') . '/prob-score', $scoreData);
    }

    /**
     * Get appraisal reports
     *
     * @param array $data The filter data (batchId, departmentId, employeeId, branchId, kpiId)
     * @return array Appraisal reports
     * @throws ApiException
     */
    public function getReports(array $data): array
    {
        return $this->put($this->getEndpoint('report'), $data);
    }

    /**
     * Submit supervisor recommendation
     *
     * @param array $data The recommendation data (employeeId, kpiId, batchId, supervisorId, supervisorComment)
     * @return array Recommendation submission response
     * @throws ApiException
     */
    public function submitRecommendation(array $data): array
    {
        return $this->post($this->getEndpoint('recommendation'), $data);
    }

    /**
     * Get employee total grade
     *
     * @param array $data The grade data (employeeId, kpiId, batchId, status)
     * @return array Employee grade details
     * @throws ApiException
     */
    public function getEmployeeTotalGrade(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/get-employee-total-grade', $data);
    }

    /**
     * Update employee score to probing state
     *
     * @param array $data The probing data (scoreId, prob)
     * @return array Update response
     * @throws ApiException
     */
    public function updateEmployeeScoreToProb(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/UpdateEmployeeScoreToProb', $data);
    }

    /**
     * Update KPI score
     *
     * @param array $data The score update data
     * @return array Updated score
     * @throws ApiException
     */
    public function updateKpiScore(array $data): array
    {
        return $this->put($this->getEndpoint('kpi') . '/update-score', $data);
    }

    /**
     * Get employee scoring score details
     *
     * @param array $data The request data (employeeId, kpiId, batchId)
     * @return array Employee scoring score details
     * @throws ApiException
     */
    public function getEmployeeScoringScore(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/GetEmployeeScoringScore', $data);
    }

    /**
     * Get employee scoring KPI details
     *
     * @param array $data The request data (employeeId, kpiId, batchId)
     * @return array Employee scoring KPI details
     * @throws ApiException
     */
    public function getEmployeeScoringKpi(array $data): array
    {
        return $this->put($this->getEndpoint('kpi') . '/GetEmployeeScoringKpi', $data);
    }

    /**
     * Update supervisor score
     *
     * @param array $data The supervisor score data
     * @return array Update response
     * @throws ApiException
     */
    public function updateSupervisorScore(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/supervisor-score', $data);
    }

    /**
     * Get all batches by KPI
     *
     * @param int|string $kpiId The KPI ID
     * @return array List of batches for KPI
     * @throws ApiException
     */
    public function getAllBatchesByKpi($kpiId): array
    {
        return $this->get($this->getEndpoint('kpi') . "/GetAllKpiForBatch/{$kpiId}");
    }

    /**
     * Score confirmation
     *
     * @param array $data The confirmation data
     * @return array Confirmation response
     * @throws ApiException
     */
    public function submitScoreConfirmation(array $data): array
    {
        return $this->put($this->getEndpoint('score') . '/score-confirmation', $data);
    }
}
