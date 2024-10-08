<?php

use Illuminate\Support\Facades\Route;
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
Route::get("dashboard/kpi/setup", [DashboardController::class, "kpi_setup"])->name("kpi.setup");
Route::get("dashboard/score/setup", [DashboardController::class, "score_setup"])->name("score.setup");
Route::get("dashboard/section/setup", [DashboardController::class, "section_setup"])->name("section.setup");

