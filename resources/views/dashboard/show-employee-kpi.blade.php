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
            @if ($employeeKpi !== null)
                <div class="card card-body mb-10">
                    <div class="d-flex justify-content-between">
                        @if (isset($employeeKpi['id']))
                            <a href="{{ route('show.employee.kpi', $employeeKpi['id']) }}">
                                <h4>{{ $employeeKpi['kpi_name'] }}</h4>
                            </a>
                        @else
                            <h4>{{ $employeeKpi['kpi_name'] }}</h4>
                        @endif
                        {{--  <div>
                            <span class="badge rounded-pill bg-dark font-size-13">Number of KPIS</span>
                            <span
                                class="badge rounded-pill bg-primary font-size-13">{{ $employeeKpi['section_count'] }}</span>
                        </div>  --}}
                        <div>
                            @if (isset($employeeKpi['id']))
                                <a href="{{ route('show.employee.kpi', $employeeKpi['id']) }}">
                                    <span class="badge rounded-pill bg-dark font-size-13"><i
                                            class="bx bx-show-alt"></i>Click to Open</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @php
                        function getBadgeDetails($status)
                        {
                            return match ($status) {
                                'PENDING' => ['class' => 'bg-dark', 'text' => 'PENDING'],
                                'REVIEW' => ['class' => 'bg-warning', 'text' => 'REVIEW'],
                                'CONFIRMATION' => ['class' => 'bg-primary', 'text' => 'CONFIRMATION'],
                                'COMPLETED' => ['class' => 'bg-success', 'text' => 'COMPLETED'],
                                'PROBLEM' => ['class' => 'bg-danger', 'text' => 'PROBLEM'],
                                default => ['class' => 'bg-secondary', 'text' => 'PENDING'],
                            };
                        }
                        $badgeDetails = getBadgeDetails($gradeDetails['status'] ?? null);
                    @endphp

                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <h5>Grade: <b>{{ $gradeDetails['grade'] ?? '___' }}</b></h5>
                            <h5>Score: <b>{{ $gradeDetails['kpiScore'] ?? '___' }}</b></h5>
                            <h5>Remark: <b>{{ $gradeDetails['remark'] ?? '___' }}</b></h5>
                            <h5>Status: <b><span class="badge rounded-pill {{ $badgeDetails['class'] }}">
                                        {{ $badgeDetails['text'] }}
                                    </span></b>
                            </h5>
                        </div>
                    </div>
                </div>
            @else
                <div class="card card-body mb-10">
                    <h5>You Have No Active Appraisal </h5>
                </div>
            @endif







        </div>



    </div>




</x-base-layout>
