<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('show-batch') }}">Active Batch</a> > Available
                        KPIs For You</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="col-md-12">

            {{--  ===== KPI FOR EMPLOYEE - CARD =====  --}}
            @if (in_array($employeeKpi, [null]))
                <div class="card card-body mb-10">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('show.employee.kpi', $employeeKpi['id']) }}">
                            <h4>{{ $employeeKpi['kpi_name'] }}</h4>
                        </a>
                        <div>
                            {{--  <span class="badge rounded-pill bg-dark font-size-13">Number of KPIS</span>  --}}
                            {{--  <span
                            class="badge rounded-pill bg-primary font-size-13">{{ $employeeKpi['section_count'] }}</span>  --}}
                            <!-- Display section count -->
                        </div>
                        <div>
                            <a href="{{ route('show.employee.kpi', $employeeKpi['id']) }}">
                                <span class="badge rounded-pill bg-dark font-size-13"><i class="bx bx-show-alt"></i>Open
                                    kpi</span>
                            </a>
                        </div>
                    </div>
                    {{--  <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: {{ session('progress') }}%; font-weight: 900;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="{{ session('progress') }}">{{ session('progress') }}%</div>
                    </div>
                </div>  --}}
                </div>
            @else
                <div class="card card-body mb-10">
                    <h5>You Have No Active Appraisal </h5>
                </div>
            @endif







        </div>



    </div>




</x-base-layout>
