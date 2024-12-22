<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Policies\PermissionPolicy;

class PermissionMiddleware
{
    public function handle($request, Closure $next)
    {
        $accessToken = session('api_token');

        if (!$accessToken) {
            Log::error('Access token not found in session.');
            return redirect()->route('login');
        }

        // Fetch departments, roles, and user information from APIs
        $responseDepartments = Http::withToken($accessToken)->get('http://192.168.1.200:5124/HRMS/Department');
        $responseRoles = Http::withToken($accessToken)->get('http://192.168.1.200:5124/HRMS/EmpRole');
        $responseUser = Http::withToken($accessToken)->get('http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');

        if (!$responseDepartments->successful() || !$responseRoles->successful() || !$responseUser->successful()) {
            Log::error('Failed to fetch data from APIs.');
            return redirect()->route('error.page');
        }

        // Prepare data for policies
        $departments = collect($responseDepartments->object())->pluck('id')->toArray();
        $managers = collect($responseDepartments->object())->pluck('manager')->unique()->toArray();
        $roleManagers = collect($responseRoles->object())->pluck('manager')->unique()->toArray();
        $user = $responseUser->object();

        session([
            'departments' => $departments,
            'managers' => $managers,
            'roleManagers' => $roleManagers
        ]);


        // Create an instance of the policy
        $policy = new PermissionPolicy();

        // Check permissions using the policy
        if (!$policy->accessSetup($user, $departments)) {
            Log::info('Access denied for setup permissions.');
            // return redirect()->back()->with('toast_warning','You do not have access');
        }

        if (!$policy->viewAsSupervisorOrDepartmentHead($user, $managers, $roleManagers)) {
            Log::info('Access denied for supervisor/department head view permissions.');
            // return redirect()->back()->with('toast_warning','You do not have access');
        }

        if (!$policy->viewHrSetup($user, $departments)) {
            Log::info('Access denied for HR setup permissions.');
            // return redirect()->back()->with('toast_warning','You do not have access');
        }

        return $next($request);
    }
}
