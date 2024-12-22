<?php

namespace App\Policies;

class PermissionPolicy
{
    /**
     * Determine if the user can access setup features.
     */
    public function accessSetup($user, array $departments)
    {
        // Check if user's department ID is in the allowed department IDs
        return in_array($user->department->id, $departments);
    }

    /**
     * Determine if the user can view as supervisor or department head.
     */
    public function viewAsSupervisorOrDepartmentHead($user, array $managers, array $roleManagers)
    {
        // Check if the user's role ID matches manager or role manager IDs
        return in_array($user->empRole->department->manager, $managers) || in_array($user->empRole->manager, $roleManagers);
    }

    /**
     * Determine if the user can view HR setup.
     */
    public function viewHrSetup($user, array $departments)
    {
        // Check if the user's department ID is in the HR setup department IDs
        return in_array($user->department->id, $departments);
    }
}
