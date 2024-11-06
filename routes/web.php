<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DashboardController;

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




Route::get("dashboard/index", [DashboardController::class, "index"])->name("dashboard.index");
Route::get("dashboard/view-kpi", [DashboardController::class, "view_kpi"])->name("view.kpi");
Route::get("dashboard/edit-kpi", [DashboardController::class, "view_kpi"])->name("edit.kpi");
Route::get("dashboard/kpi-form", [DashboardController::class, "kpi_form"])->name("kpi.form");
Route::get("dashboard/my-kpis", [DashboardController::class, "my_kpis"])->name("my.kpis");

Route::get("dashboard/department-kpi-setup", [DashboardController::class, "dep_kpi_setup"])->name("create.dep.kpi");
Route::get("dashboard/role-unit-kpi-setup", [DashboardController::class, "unit_kpi_setup"])->name("create.unit.setup");
Route::get("dashboard/kpi-setup", [DashboardController::class, "kpi_setup"])->name("kpi.setup");
Route::get("dashboard/score-setup", [DashboardController::class, "score_setup"])->name("score.setup");
Route::get("dashboard/section-setup", [DashboardController::class, "section_setup"])->name("section.setup");


Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get("dashboard/appraisal/batch-setup", [BatchController::class, "index"])->name("batch.setup.index");
Route::post('dashboard/appraisal/create-batch', [BatchController::class, 'store'])->name('create.batch');
Route::get("dashboard/appraisal/batch/{id}", [BatchController::class, "show"])->name("show.batch");
Route::post("dashboard/appraisal/batch/update/{id}", [BatchController::class, "update"])->name("update.batch");


