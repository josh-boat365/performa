<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return view("dashboard.index");
    }

    public function kpi_setup(){
        return view("dashboard.kpi-setup.kpi-setup");
    }
    public function score_setup(){
        return view("dashboard.kpi-setup.score-setup");
    }

    public function section_setup(){
        return view("dashboard.kpi-setup.section-setup");
    }
}
