<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalKpiController;
use App\Http\Controllers\UpdateKpiScoringState;
use App\Http\Controllers\GlobalMetricController;
use App\Http\Controllers\GlobalWeightController;
use App\Http\Controllers\GlobalSectionController;
use App\Http\Controllers\AppraisalScoreController;
use App\Http\Controllers\SupervisorScoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.auth-login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


Route::group(
    ['middleware' => ['session.notfound']],
    function () {

        //Employee KPI Setup
        Route::get("dashboard/index", [DashboardController::class, "index"])->name("dashboard.index");
        Route::get("dashboard/view-kpi", [DashboardController::class, "show"])->name("view.kpi");
        Route::get("dashboard/edit-kpi", [DashboardController::class, "view_kpi"])->name("edit.kpi");
        Route::get("dashboard/active/batch", [DashboardController::class, "show"])->name("show-batch");
        Route::get("dashboard/employee-batch-kpi/{id}", [DashboardController::class, "showEmployeeKpi"])->name("show.batch.kpi");
        Route::get("dashboard/employee-kpi/{id}", [DashboardController::class, "editEmployeeKpi"])->name("show.employee.kpi");

        //Supervisor Score for Employee
        Route::get("dashboard/employee-supervisor-kpi-score/{id}", [DashboardController::class, "showEmployeeSupervisorKpiScore"])->name("show.employee.supervisor.kpi.score");
        Route::get("dashboard/employee-probe/{id}", [DashboardController::class, "showEmployeeProbe"])->name("show.employee.probe");
        Route::post("dashboard/employee-probe/submit", [AppraisalScoreController::class, "submitProbing"])->name("submit.employee.probe");

        Route::get("dashboard/supervisor/show-employee-kpis", [SupervisorScoreController::class, "index"])->name("supervisor.index");
        Route::get("dashboard/supervisor/show-employee-kpi-form/kpi/{kpiId}/batch/{batchId}", [SupervisorScoreController::class, "edit"])->name("supervisor.edit");
        //Supervisor Score
        Route::post("dashboard/supervisor/rating", [SupervisorScoreController::class, "store"])->name("supervisor.rating");
        Route::post("dashboard/submit-rating-score-for-employee-confirmation", [UpdateKpiScoringState::class, "store"])->name("submit.supervisor.rating");

        //Appraisal Score
        Route::post("dashboard/self-rating", [AppraisalScoreController::class, "store"])->name("self.rating");
        Route::post("dashboard/submit-self-rating-score-for-supervisor-review", [UpdateKpiScoringState::class, "store"])->name("submit.appraisal");





        //hr/batch Setup
        Route::get("dashboard/appraisal/hr/batch-setup", [BatchController::class, "index"])->name("batch.setup.index");
        Route::post('dashboard/appraisal/create-batch', [BatchController::class, 'store'])->name('create.batch');
        Route::get("dashboard/appraisal/hr/batch/{id}/show", [BatchController::class, "show"])->name("show.batch");
        Route::post("dashboard/appraisal/hr/batch/update/{id}", [BatchController::class, "update"])->name("update.batch");
        Route::post("dashboard/appraisal/hr/batch/update-state/{id}", [BatchController::class, "update_state"])->name("update.batch.state");
        Route::post("dashboard/appraisal/hr/batch/update-status/{id}", [BatchController::class, "update_status"])->name("update.batch.status");
        Route::post("dashboard/appraisal/hr/batch/delete-batch/{id}", [BatchController::class, "destroy"])->name("delete.batch");


        //Admin KPI - Setup
        //hr/global
        Route::get("dashboard/appraisal/hr/hr/global-kpi-setup", [GlobalKpiController::class, "index"])->name("global.index");
        Route::get("dashboard/appraisal/hr/global-kpi-setup/create", [GlobalKpiController::class, "create"])->name("create.global.kpi");
        Route::post("dashboard/appraisal/hr/global-kpi-setup/store", [GlobalKpiController::class, "store"])->name("store.global.kpi");
        Route::get("dashboard/appraisal/hr/global-kpi/{id}/show", [GlobalKpiController::class, "show"])->name("show.global.kpi");
        Route::post("dashboard/appraisal/hr/global-kpi/{id}/update", [GlobalKpiController::class, "update"])->name("update.global.kpi");
        Route::delete("dashboard/appraisal/hr/global-kpi/{id}/delete", [GlobalKpiController::class, "destroy"])->name("delete.global.kpi");

        // Global - section
        Route::get("dashboard/appraisal/hr/global-section-setup", [GlobalSectionController::class, "index"])->name("global.section.index");
        Route::get("dashboard/appraisal/hr/global-section-setup/create", [GlobalSectionController::class, "create"])->name("create.global.section");
        Route::post("dashboard/appraisal/hr/global-section-setup/store", [GlobalSectionController::class, "store"])->name("store.global.section");
        Route::get("dashboard/appraisal/hr/global-section/{id}/show", [GlobalSectionController::class, "show"])->name("show.global.section");
        Route::post("dashboard/appraisal/hr/global-section/{id}/update", [GlobalSectionController::class, "update"])->name("update.global.section");
        Route::delete("dashboard/appraisal/hr/global-section/{id}/delete", [GlobalSectionController::class, "destroy"])->name("delete.global.section");


        // Global - metric
        Route::get("dashboard/appraisal/hr/global-metric-setup", [GlobalMetricController::class, "index"])->name("global.metric.index");
        Route::get("dashboard/appraisal/hr/global-metric-setup/create", [GlobalMetricController::class, "create"])->name("create.global.metric");
        Route::post("dashboard/appraisal/hr/global-metric-setup/store", [GlobalMetricController::class, "store"])->name("store.global.metric");
        Route::get("dashboard/appraisal/hr/global-metric/{id}/show", [GlobalMetricController::class, "show"])->name("show.global.metric");
        Route::post("dashboard/appraisal/hr/global-metric/{id}/update", [GlobalMetricController::class, "update"])->name("update.global.metric");
        Route::delete("dashboard/appraisal/hr/global-metric/{id}/delete", [GlobalMetricController::class, "destroy"])->name("delete.global.metric");

        //hr/global - Weight/Score for Department
        Route::get("dashboard/appraisal/hr/global-weight-setup", [GlobalWeightController::class, "index"])->name("global.weight.index");
        Route::get("dashboard/appraisal/hr/global-weight-setup/create", [GlobalWeightController::class, "create"])->name("create.global.weight");
        Route::post("dashboard/appraisal/hr/global-weight-setup/store", [GlobalWeightController::class, "store"])->name("store.global.weight");
        Route::get("dashboard/appraisal/hr/global-weight-setup/{id}/show", [GlobalWeightController::class, "show"])->name("show.global.weight");
        Route::post("dashboard/appraisal/hr/global-weight-setup/{id}update", [GlobalWeightController::class, "update"])->name("update.global.weight");
        Route::post("dashboard/appraisal/hr/global-weight-setup{id}/delete", [GlobalWeightController::class, "destroy"])->name("delete.global.weight");

        //Grade Setup
        Route::get("dashboard/appraisal/hr/grade-setup", [GradeController::class, "index"])->name("grade.index");
        Route::post("dashboard/appraisal/hr/grade-setup/store", [GradeController::class, "store"])->name("store.grade");
        Route::get("dashboard/appraisal/hr/grade-setup/{id}/show", [GradeController::class, "show"])->name("show.grade");
        Route::post("dashboard/appraisal/hr/grade-setup/{id}update", [GradeController::class, "update"])->name("update.grade");
        Route::post("dashboard/appraisal/hr/grade-setup/{id}delete", [GradeController::class, "delete"])->name("delete.grade");


        //Department
        Route::get("dashboard/appraisal/department/kpi-setup", [KpiController::class, "index"])->name("kpi.index");
        Route::get("dashboard/appraisal/department/kpi-setup/create", [KpiController::class, "create"])->name("create.kpi");
        Route::post("dashboard/appraisal/department/kpi-setup/store", [KpiController::class, "store"])->name("store.kpi");
        Route::get("dashboard/appraisal/department/kpi/{id}/show", [KpiController::class, "show"])->name("show.kpi");
        Route::post("dashboard/appraisal/department/kpi/{id}/update", [KpiController::class, "update"])->name("update.kpi");
        Route::delete("dashboard/appraisal/department/kpi/{id}/delete", [KpiController::class, "destroy"])->name("delete.kpi");
        Route::post("dashboard/appraisal/department/kpi/update-state/{id}", [KpiController::class, "update_state"])->name("update.kpi.state");
        Route::post("dashboard/appraisal/department/kpi/update-status/{id}", [KpiController::class, "update_status"])->name("update.kpi.status");


        //Setion Setup
        Route::get("dashboard/department/kpi/section-setup/", [SectionController::class, "index"])->name("section.index");
        Route::get("dashboard/department/kpi/section-setup/create", [SectionController::class, "create"])->name("create.section");
        Route::post("dashboard/department/kpi/section-setup/store", [SectionController::class, "store"])->name("store.section");
        Route::get("dashboard/department/kpi/{id}/section-update", [SectionController::class, "show"])->name("show.section");
        Route::post("dashboard/department/kpi/{id}/section-update", [SectionController::class, "update"])->name("update.section");
        Route::post("dashboard/department/kpi/{id}/section-delete", [SectionController::class, "destroy"])->name("delete.section");

        //Metric Setup
        Route::get("dashboard/section/metric-setup", [MetricController::class, "index"])->name("metric.index");
        Route::get("dashboard/section/metric-setup/create", [MetricController::class, "create"])->name("create.metric");
        Route::post("dashboard/section/metric-setup/store", [MetricController::class, "store"])->name("store.metric");
        Route::get("dashboard/section/{id}/metric-update", [MetricController::class, "show"])->name("show.metric");
        Route::post("dashboard/section/{id}/metric-update", [MetricController::class, "update"])->name("update.metric");
        Route::post("dashboard/section/{id}/metric-delete", [MetricController::class, "destroy"])->name("delete.metric");



        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    }
);
