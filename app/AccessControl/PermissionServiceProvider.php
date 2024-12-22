<?php

namespace App\AccessControl;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register policies if needed
        // $this->registerPolicies();

        // Register permissions lazily
        // $this->registerPermissions();
    }

    protected function registerPolicies()
    {
        // You can register any additional policies here if needed
    }

    protected function registerPermissions()
    {
        // This method will be called when needed
        // $this->defineGates();
    }

    // protected function defineGates()
    // {
    //     // Check if the session has the access token
    //     $accessToken = session('api_token');

    //     if ($accessToken) {
    //         // Fetch departments
    //         $responseDepartments = Http::withToken($accessToken)
    //             ->get('http://192.168.1.200:5124/HRMS/Department');

    //         // Fetch roles
    //         $responseRoles = Http::withToken($accessToken)
    //             ->get('http://192.168.1.200:5124/HRMS/EmpRole');

    //         // Fetch user information
    //         $responseUser  = Http::withToken($accessToken)
    //             ->get('http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');

    //         // Handle responses
    //         $departments = $responseDepartments->successful() ? $responseDepartments->object() : [];
    //         $roles = $responseRoles->successful() ? $responseRoles->object() : [];
    //         $user = $responseUser->successful() ? $responseUser->object() : null;

    //         // Initialize arrays
    //         $departments_ids = [];
    //         $roles_ids = [];
    //         $manager_ids = [];
    //         $role_manager_ids = [];

    //         // Populate departments array
    //         if (!empty($departments)) {
    //             foreach ($departments as $department) {
    //                 $departments_ids[] = $department->id;
    //                 $manager_ids[] = $department->manager;
    //             }
    //         }

    //         // Populate roles array
    //         if (!empty($roles)) {
    //             foreach ($roles as $role) {
    //                 $roles_ids[] = $role->id;
    //                 $role_manager_ids[] = $role->manager;
    //             }
    //         }

    //         $role_manager_ids = array_unique($role_manager_ids);
    //         $manager_ids = array_unique($manager_ids);

    //         $hr_department = 1;

    //         // Define gates if user data is available
    //         if ($user) {
    //             Gate::define('access-setup', function () use ($user, $manager_ids, $role_manager_ids) {
    //                 return (in_array($user->empRole->id, $manager_ids) || in_array($user->empRole->id, $role_manager_ids));
    //             });

    //             Gate::define('view-as-supervisor-or-department-head', function () use ($user, $manager_ids, $role_manager_ids) {
    //                 return (in_array($user->empRole->id, $manager_ids) || in_array($user->empRole->id, $role_manager_ids));
    //             });

    //             Gate::define('view-hr-setup', function () use ($user, $hr_department) {
    //                 return $user->department->id === $hr_department;
    //             });
    //         }
    //     }
    // }
}
