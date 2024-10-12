<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return view("dashboard.index");
    }
    public function batch_setup(){
        return view("batch-setup.index");
    }

    public function dep_kpi_setup(){
        return view("kpi-setup.create-department-kpi");
    }

    public function unit_kpi_setup(){
        return view("kpi-setup.create-unit-kpi");
    }
    public function kpi_setup(){
        return view("kpi-setup.kpi-setup");
    }
    public function score_setup(){
        return view("kpi-setup.score-setup");
    }

    public function section_setup(){
        return view("kpi-setup.section-setup");
    }
}
