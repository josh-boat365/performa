<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Available KPIs For You</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="badge rounded-pill bg-dark">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary">16</span>


                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"> Save</button>
                        <button class="btn btn-success"> Submit</button>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="kpi-form" role="tabpanel">


                                @foreach ($appraisal as $roleId => $role)
                                    <div class="role-section">
                                        <h2>Role: {{ $role->roleName }}</h2>

                                        @foreach ($role->kpis as $kpiId => $kpi)
                                            <div class="kpi-section">
                                                <h3>KPI: {{ $kpi->name }}</h3>
                                                <p>{{ $kpi->description }}</p>

                                                @if (!empty($kpi->sections))
                                                    @foreach ($kpi->sections as $sectionId => $section)
                                                        <div class="section">
                                                            <h4>Section: {{ $section->name }}</h4>
                                                            <p>{{ $section->description }}</p>

                                                            @if (!empty($section->metrics))
                                                                <ul>
                                                                    @foreach ($section->metrics as $metric)
                                                                        <li>
                                                                            <strong>{{ $metric->name }}</strong>:
                                                                            {{ $metric->score }}
                                                                            ({{ $metric->active ? 'Active' : 'Inactive' }})
                                                                            <p>{{ $metric->description }}</p>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p>No metrics available for this section.</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p>No sections available for this KPI.</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>



                        </div>
                    </div>

                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>


    </div>

</x-base-layout>
