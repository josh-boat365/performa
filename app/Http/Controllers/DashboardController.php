<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;


class DashboardController extends Controller
{
    public function index()
    {

        $userName = session('user_name');
        $userEmail = session('user_email');


        return view("dashboard.index", compact('userEmail', 'userName'));
    }
    public function show()
    {
        $accessToken = session('api_token');

        // Step 1: Fetch data from the API
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi');

        if ($response->successful()) {
            $kpis = $response->json();

            // Step 2: Initialize an array to hold active batches
            $activeBatches = [];

            // Step 3: Loop through the KPIs to find active batches
            foreach ($kpis as $kpi_data) {
                if (isset($kpi_data['batch']['active']) && $kpi_data['batch']['active'] === true) {

                    $activeBatches[] = [
                        'id' => $kpi_data['id'],
                        'name' => $kpi_data['batch']['name'],
                        'count' => count($kpis),
                        'status' => $kpi_data['batch']['status'],
                    ];
                }
            }

            // Step 4: Pass the active batches to the view
            return view("dashboard.show-kpis", compact('activeBatches'));
        }
    }


    public function edit($id){

        // Fetch data from the API endpoints
        $kpis = $this->fetchKpis();
        $sections = $this->fetchSections();
        $metrics = $this->fetchMetrics();



        // Get the employee role ID from the session
        $currentEmpRoleId = 4; // Assuming you store the empRole ID in the session

        // Organize the data
        $organizedData = $this->organizeDataByRole($kpis, $sections, $metrics, $currentEmpRoleId);

        $data = json_encode($organizedData);

        $appraisal = json_decode($data);
        // dd($appraisal);

        return view("dashboard.kpi-form", compact('appraisal'));
    }

    private function fetchKpis()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Kpi');

            // dd($response->json());

        return $response->json();
    }

    private function fetchSections()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5123/Appraisal/Section');

            // dd($response->json());

        return $response->json();
    }

    private function fetchMetrics()
    {
        $accessToken = session('api_token');
        $response = Http::withToken($accessToken)
        ->get('http://192.168.1.200:5123/Appraisal/Metric');

        // dd($response->json());

        return $response->json();
    }

    private function organizeDataByRole($kpis, $sections, $metrics, $currentEmpRoleId)
    {
        // Initialize an array to hold organized data by role
        $organized = [];

        foreach ($kpis as $kpi) {
            // Check if the empRole ID matches the current user's role
            if ($kpi['empRole']['id'] === $currentEmpRoleId) {
                $roleId = $kpi['empRole']['id'];
                if (!isset($organized[$roleId])) {
                    $organized[$roleId] = [
                        'roleName' => $kpi['empRole']['name'], // Store role name
                        'kpis' => []
                    ];
                }

                // Add the KPI to the organized data
                $organized[$roleId]['kpis'][$kpi['id']] = [
                    'name' => $kpi['name'],
                    'description' => $kpi['description'],
                    'sections' => []
                ];

                // Find sections related to the current KPI
                foreach ($sections as $section) {
                    // dd($sections);
                    if ($section['kpi']['id'] === $kpi['id']) {
                        $organized[$roleId]['kpis'][$kpi['id']]['sections'][$section['id']] = [
                            'name' => $section['name'],
                            'description' => $section['description'],
                            'metrics' => []
                        ];

                        // Find metrics related to the current section
                        foreach ($metrics as $metric) {
                            if ($metric['section']['id'] === $section['id']) {
                                $organized[$roleId]['kpis'][$kpi['id']]['sections'][$section['id']]['metrics'][] = [
                                    'id' => $metric['id'],
                                    'name' => $metric['name'],
                                    'description' => $metric['description'],
                                    'score' => $metric['score'],
                                    'active' => $metric['active']
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $organized;
    }





    public function my_kpis()
    {
        return view("dashboard.my-kpis");
    }


    public function kpi_setup()
    {
        return view("kpi-setup.kpi-setup");
    }
    public function score_setup()
    {
        return view("kpi-setup.score-setup");
    }
}
