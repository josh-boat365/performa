<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class DashboardController extends Controller
{
    public function index(){
        return view("dashboard.index");
    }
    public function view_kpi(){
        return view("dashboard.view-kpi");
    }

    public function kpi_form(){

        $dummyCollection = collect([
            ['id' => 1, 'name' => 'John Doe', 'position' => 'Developer', 'rating' => 5],
            ['id' => 2, 'name' => 'Jane Smith', 'position' => 'Designer', 'rating' => 4],
            ['id' => 3, 'name' => 'Samuel Green', 'position' => 'Manager', 'rating' => 3],
            ['id' => 4, 'name' => 'Emily Brown', 'position' => 'Marketer', 'rating' => 4],
            ['id' => 5, 'name' => 'Michael Johnson', 'position' => 'Accountant', 'rating' => 5],
            ['id' => 6, 'name' => 'Sarah Davis', 'position' => 'HR Specialist', 'rating' => 3],
            ['id' => 7, 'name' => 'Paul Walker', 'position' => 'Developer', 'rating' => 5],
            ['id' => 8, 'name' => 'Diana Blake', 'position' => 'Designer', 'rating' => 4],
            ['id' => 9, 'name' => 'Chris Turner', 'position' => 'Manager', 'rating' => 4],
            ['id' => 10, 'name' => 'Olivia White', 'position' => 'Marketer', 'rating' => 3],
            ['id' => 11, 'name' => 'Robert Harris', 'position' => 'Developer', 'rating' => 4],
            ['id' => 12, 'name' => 'Mia Thomas', 'position' => 'HR Specialist', 'rating' => 3],
            ['id' => 13, 'name' => 'Jake Evans', 'position' => 'Developer', 'rating' => 5],
            ['id' => 14, 'name' => 'Sophia Scott', 'position' => 'Designer', 'rating' => 4],
            ['id' => 15, 'name' => 'William Clark', 'position' => 'Manager', 'rating' => 4],
            ['id' => 16, 'name' => 'Isabella Wilson', 'position' => 'Marketer', 'rating' => 5],
            ['id' => 17, 'name' => 'James Lewis', 'position' => 'Developer', 'rating' => 5],
            ['id' => 18, 'name' => 'Ava Hall', 'position' => 'HR Specialist', 'rating' => 3],
            ['id' => 19, 'name' => 'Lucas Adams', 'position' => 'Designer', 'rating' => 4],
            ['id' => 20, 'name' => 'Ethan Campbell', 'position' => 'Accountant', 'rating' => 5],
        ]);

        // Convert the collection to a paginator
        $perPage = 5; // Number of records per page
        $currentPage = request()->get('page', 1); // Get the current page from the request
        $items = $dummyCollection->slice(($currentPage - 1) * $perPage, $perPage)->all(); // Slice the collection
        $kpis = new LengthAwarePaginator($items, $dummyCollection->count(), $perPage, $currentPage, [
            'path' => request()->url(), // Set the pagination path to the current URL
        ]);

        return view("dashboard.kpi-form", compact('kpis'));
    }

    public function my_kpis(){
        return view("dashboard.my-kpis");
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
