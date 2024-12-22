<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    //
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        // $accessToken = session('api_token');

        // // Fetch departments
        // $responseDepartments = Http::withToken($accessToken)
        //     ->get('http://192.168.1.200:5124/HRMS/Department');

        // // Fetch roles
        // $responseRoles = Http::withToken($accessToken)
        //     ->get('http://192.168.1.200:5124/HRMS/EmpRole');

        // // Fetch user information
        // $responseUser  = Http::withToken($accessToken)
        //     ->get('http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');

        // // Handle responses
        // $departments = $responseDepartments->successful() ? $responseDepartments->object() : [];
        // $roles = $responseRoles->successful() ? $responseRoles->object() : [];
        // $user = $responseUser->successful() ? $responseUser->object() : null;


        // // Initialize arrays
        // $departments_ids = [];
        // $roles_ids = [];
        // $manager_ids = [];
        // $role_manager_ids = [];

        // // Populate departments array
        // if (!empty($departments)) {
        //     foreach ($departments as $department) {
        //         $departments_ids[] = $department->id;
        //         $manager_ids[] = $department->manager;
        //     }
        // }

        // // Populate roles array
        // if (!empty($roles)) {
        //     foreach ($roles as $role) {
        //         $roles_ids[] = $role->id;
        //         $role_manager_ids[] = $role->manager;
        //     }
        // }

        // $role_manager_ids = array_unique($role_manager_ids);
        // $manager_ids = array_unique($manager_ids);

        // $hr_department = 1;

        // // Debugging output (remove in production)
        // // dd($departments_ids, $roles_ids);

        // // Define gates if user data is available
        // if ($user) {

        //     Gate::define('access-setup', function () use ($user, $manager_ids,  $role_manager_ids) {
        //         return (in_array($user->empRole->id, $manager_ids) || in_array($user->empRole->id, $role_manager_ids));
        //     });

        //     Gate::define('view-as-supervisor-or-department-head', function () use ($user, $manager_ids,  $role_manager_ids) {
        //         return (in_array($user->empRole->id, $manager_ids) || in_array($user->empRole->id, $role_manager_ids));
        //     });

        //     Gate::define('view-hr-setup', function () use ($user,$hr_department) {
        //         return $user->department->id ===  $hr_department;
        //     });


        // }
    }
}
