<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalKpiController;
use App\Http\Controllers\AppraisalScoreController;

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


// Route::group(
//     ['middleware' => ['session.timeout']],
//     function () {

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

Route::get("dashboard/supervisor", [DashboardController::class, "supervisor"])->name("supervisor");

//Appraisal Score
Route::post("dashboard/self-rating", [AppraisalScoreController::class, "store"])->name("self.rating");



//Batch Setup
Route::get("dashboard/appraisal/batch-setup", [BatchController::class, "index"])->name("batch.setup.index");
Route::post('dashboard/appraisal/create-batch', [BatchController::class, 'store'])->name('create.batch');
Route::get("dashboard/appraisal/batch/{id}/show", [BatchController::class, "show"])->name("show.batch");
Route::post("dashboard/appraisal/batch/update/{id}", [BatchController::class, "update"])->name("update.batch");
Route::post("dashboard/appraisal/batch/update-state/{id}", [BatchController::class, "update_state"])->name("update.batch.state");
Route::post("dashboard/appraisal/batch/update-status/{id}", [BatchController::class, "update_status"])->name("update.batch.status");
Route::post("dashboard/appraisal/batch/delete-batch/{id}", [BatchController::class, "destroy"])->name("delete.batch");


//Admin KPI - Setup
//Global
Route::get("dashboard/appraisal/global-kpi-setup", [GlobalKpiController::class, "index"])->name("global.index");
Route::get("dashboard/appraisal/global-kpi-setup/create", [GlobalKpiController::class, "create"])->name("create.global.kpi");
Route::post("dashboard/appraisal/global-kpi-setup/store", [GlobalKpiController::class, "store"])->name("store.global.kpi");
Route::get("dashboard/appraisal/global-kpi/{id}/show", [GlobalKpiController::class, "show"])->name("show.global.kpi");
Route::post("dashboard/appraisal/global-kpi/{id}/update", [GlobalKpiController::class, "update"])->name("update.global.kpi");
Route::delete("dashboard/appraisal/global-kpi/{id}/delete", [GlobalKpiController::class, "destroy"])->name("delete.global.kpi");

Route::get("dashboard/appraisal/kpi-setup", [KpiController::class, "index"])->name("kpi.index");
Route::get("dashboard/appraisal/kpi-setup/create", [KpiController::class, "create"])->name("create.kpi");
Route::post("dashboard/appraisal/kpi-setup/store", [KpiController::class, "store"])->name("store.kpi");
Route::get("dashboard/appraisal/kpi/{id}/show", [KpiController::class, "show"])->name("show.kpi");
Route::post("dashboard/appraisal/kpi/{id}/update", [KpiController::class, "update"])->name("update.kpi");
Route::delete("dashboard/appraisal/kpi/{id}/delete", [KpiController::class, "destroy"])->name("delete.kpi");
Route::post("dashboard/appraisal/kpi/update-state/{id}", [KpiController::class, "update_state"])->name("update.kpi.state");
Route::post("dashboard/appraisal/kpi/update-status/{id}", [KpiController::class, "update_status"])->name("update.kpi.status");

//Score Setup
Route::get("dashboard/score-setup", [DashboardController::class, "score_setup"])->name("score.setup");

//Setion Setup
Route::get("dashboard/kpi/section-setup/", [SectionController::class, "index"])->name("section.index");
Route::get("dashboard/kpi/section-setup/create", [SectionController::class, "create"])->name("create.section");
Route::post("dashboard/kpi/section-setup/store", [SectionController::class, "store"])->name("store.section");
Route::get("dashboard/kpi/{id}/section-update", [SectionController::class, "show"])->name("show.section");
Route::post("dashboard/kpi/{id}/section-update", [SectionController::class, "update"])->name("update.section");
Route::post("dashboard/kpi/{id}/section-delete", [SectionController::class, "destroy"])->name("delete.section");

//Metric Setup
Route::get("dashboard/section/metric-setup", [MetricController::class, "index"])->name("metric.index");
Route::get("dashboard/section/metric-setup/create", [MetricController::class, "create"])->name("create.metric");
Route::post("dashboard/section/metric-setup/store", [MetricController::class, "store"])->name("store.metric");
Route::get("dashboard/section/{id}/metric-update", [MetricController::class, "show"])->name("show.metric");
Route::post("dashboard/section/{id}/metric-update", [MetricController::class, "update"])->name("update.metric");
Route::post("dashboard/section/{id}/metric-delete", [MetricController::class, "destroy"])->name("delete.metric");



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
//     }
// );
