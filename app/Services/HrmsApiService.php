<?php

namespace App\Services;

use App\Exceptions\ApiException;

/**
 * HrmsApiService
 *
 * Service for communicating with the HRMS (Human Resource Management System) API.
 * Handles all operations related to employees, departments, and roles.
 */
class HrmsApiService extends BaseApiService
{
    public function __construct()
    {
        $this->initialize();
    }

    protected function getServiceName(): string
    {
        return 'HRMS Service';
    }

    protected function getConfigKey(): string
    {
        return 'hrms';
    }

    // ======================== Employee Operations ========================

    /**
     * Get all employees
     *
     * @return array List of employees
     * @throws ApiException
     */
    public function getAllEmployees(): array
    {
        return $this->get($this->getEndpoint('employee'));
    }

    /**
     * Get employee by ID
     *
     * @param int|string $employeeId The employee ID
     * @return array Employee details
     * @throws ApiException
     */
    public function getEmployee($employeeId): array
    {
        return $this->get($this->getEndpoint('employee') . "/{$employeeId}");
    }

    /**
     * Get information about the currently logged-in employee
     *
     * @return array Current employee information
     * @throws ApiException
     */
    public function getCurrentEmployeeInformation(): array
    {
        return $this->get($this->getEndpoint('employee_info'));
    }

    /**
     * Create a new employee
     *
     * @param array $data Employee data
     * @return array Created employee
     * @throws ApiException
     */
    public function createEmployee(array $data): array
    {
        return $this->post($this->getEndpoint('employee'), $data);
    }

    /**
     * Update an employee
     *
     * @param int|string $employeeId The employee ID
     * @param array $data Updated employee data
     * @return array Updated employee
     * @throws ApiException
     */
    public function updateEmployee($employeeId, array $data): array
    {
        return $this->put($this->getEndpoint('employee') . "/{$employeeId}", $data);
    }

    /**
     * Delete an employee
     *
     * @param int|string $employeeId The employee ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteEmployee($employeeId): array
    {
        return $this->delete($this->getEndpoint('employee') . "/{$employeeId}");
    }

    // ======================== Department Operations ========================

    /**
     * Get all departments
     *
     * @return array List of departments
     * @throws ApiException
     */
    public function getAllDepartments(): array
    {
        return $this->get($this->getEndpoint('department'));
    }

    /**
     * Get department by ID
     *
     * @param int|string $departmentId The department ID
     * @return array Department details
     * @throws ApiException
     */
    public function getDepartment($departmentId): array
    {
        return $this->get($this->getEndpoint('department') . "/{$departmentId}");
    }

    /**
     * Create a new department
     *
     * @param array $data Department data
     * @return array Created department
     * @throws ApiException
     */
    public function createDepartment(array $data): array
    {
        return $this->post($this->getEndpoint('department'), $data);
    }

    /**
     * Update a department
     *
     * @param int|string $departmentId The department ID
     * @param array $data Updated department data
     * @return array Updated department
     * @throws ApiException
     */
    public function updateDepartment($departmentId, array $data): array
    {
        return $this->put($this->getEndpoint('department') . "/{$departmentId}", $data);
    }

    /**
     * Delete a department
     *
     * @param int|string $departmentId The department ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteDepartment($departmentId): array
    {
        return $this->delete($this->getEndpoint('department') . "/{$departmentId}");
    }

    // ======================== Role Operations ========================

    /**
     * Get all employee roles
     *
     * @return array List of roles
     * @throws ApiException
     */
    public function getAllRoles(): array
    {
        return $this->get($this->getEndpoint('emp_role'));
    }

    /**
     * Get role by ID
     *
     * @param int|string $roleId The role ID
     * @return array Role details
     * @throws ApiException
     */
    public function getRole($roleId): array
    {
        return $this->get($this->getEndpoint('emp_role') . "/{$roleId}");
    }

    /**
     * Create a new employee role
     *
     * @param array $data Role data
     * @return array Created role
     * @throws ApiException
     */
    public function createRole(array $data): array
    {
        return $this->post($this->getEndpoint('emp_role'), $data);
    }

    /**
     * Update an employee role
     *
     * @param int|string $roleId The role ID
     * @param array $data Updated role data
     * @return array Updated role
     * @throws ApiException
     */
    public function updateRole($roleId, array $data): array
    {
        return $this->put($this->getEndpoint('emp_role') . "/{$roleId}", $data);
    }

    /**
     * Delete an employee role
     *
     * @param int|string $roleId The role ID
     * @return array Delete response
     * @throws ApiException
     */
    public function deleteRole($roleId): array
    {
        return $this->delete($this->getEndpoint('emp_role') . "/{$roleId}");
    }

    // ======================== Batch Operations ========================

    /**
     * Get all appraisal batches
     *
     * @return array List of batches
     * @throws ApiException
     */
    public function getAllBatches(): array
    {
        return $this->get($this->getEndpoint('batch'));
    }

    // ======================== Branch Operations ========================

    /**
     * Get all branches
     *
     * @return array List of branches
     * @throws ApiException
     */
    public function getAllBranches(): array
    {
        return $this->get($this->getEndpoint('branch'));
    }
}
