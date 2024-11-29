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

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="col-md-12">
            {{--  ===== GLOBAL KPI FOR EMPLOYEE - CARD =====  --}}
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <a href="#">
                        <h4>Your Global KPI - Service Delivery</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Number of Sections</span>
                        <span class="badge rounded-pill bg-primary fonte-size-13">3</span>
                        <!-- Display section count -->
                    </div>
                    <div>
                        <a href="#">
                            <span class="badge rounded-pill bg-primary fonte-size-13"><i class="bx bxs-pencil"></i>Edit
                                Kpi</span>
                        </a>
                        <a href="#">
                            <span class="badge rounded-pill bg-dark fonte-size-13"><i class="bx bx-show-alt"></i>Open
                                kpi</span>
                        </a>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 0%; font-weight: 900;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">0%</div>
                    </div>
                </div>
            </div>

            {{--  ===== KPI FOR EMPLOYEE - CARD =====  --}}
            <div class="card card-body mb-10">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('show.employee.kpi', $employeeKpi['id']) }}">
                        <h4>{{ $employeeKpi['kpi_name'] }}</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Number of Sections</span>
                        <span
                            class="badge rounded-pill bg-primary fonte-size-13">{{ $employeeKpi['section_count'] }}</span>
                        <!-- Display section count -->
                    </div>
                    <div>
                        <a href="{{ route('show.employee.kpi', $employeeKpi['id']) }}">
                            <span class="badge rounded-pill bg-dark fonte-size-13"><i class="bx bx-show-alt"></i>Open
                                kpi</span>
                        </a>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: {{ session('progress') }}%; font-weight: 900;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="{{ session('progress') }}">{{ session('progress') }}%</div>
                    </div>
                </div>
            </div>

            {{--  ===== SUPVERVISOR SCORE FOR EMPLOYEE KPI - CARD =====  --}}
            <h4 class="mb-sm-0 font-size-18">Your Supervisor Score</h4>
            <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('show.employee.supervisor.kpi.score', $employeeKpi['id']) }}">
                        <h4>Network Support KPI</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Supervisor</span>
                        <span class="badge rounded-pill bg-primary fonte-size-13">David Kwarteng</span>
                        <!-- Display section count -->
                    </div>
                    <div>
                        {{--  <a href="#">
                            <span class="badge rounded-pill bg-primary fonte-size-13"><i class="bx bxs-pencil"></i>Edit
                                Kpi</span>
                        </a>  --}}
                        <a href="#">
                            <span class="badge rounded-pill bg-dark fonte-size-13"><i class="bx bx-show-alt"></i>Open
                                kpi</span>
                        </a>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 0%; font-weight: 900;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">0%</div>
                    </div>
                </div>
            </div>





            {{--  @forelse ($employeeKpi as $employeeKpi)  --}}
            {{--  @empty
                <div class="card card-body">
                    <div class="d-flex justify-content-between">
                        <h3>No KPI kpi Available </h3>
                    </div>
                </div>
            @endforelse  --}}

            {{--  <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('show.kpi') }}">
                        <h4>January 2025 kpi - First Quarter</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary fonte-size-13">16</span>
                    </div>
                     <div>

                        <a href="#">
                            <span class="badge rounded-pill bg-dark fonte-size-13"><i class="bx bx-show-alt"></i>View</span>
                        </a>

                    </div>
                    <div>
                        <span class="badge rounded-pill bg-success fonte-size-13">Submitted</span>
                    </div>

                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 25%; font-weight: 900;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">70%</div>
                    </div>
                </div>

            </div>  --}}
        </div>



    </div>




</x-base-layout>