<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Employees Awaiting Confirmation</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="col-md-12">


            @if (!empty($employeeSupDetails))

                @forelse ($employeeSupDetails as $employee)

                    <a
                        href="{{ url('dashboard/supervisor/show-employee-kpi-form/kpi/' . $employee->kpis[0]->kpiId . '/batch' . '/' . $employee->kpis[0]->batchId .'/employee-id'. '/'. $employee->employee->employeeId) }}">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between items-center">


                                <div>
                                    <span> {{ $employee->employee->employeeFullName }}</span> : <span
                                        class="badge rounded-pill bg-dark font-size-13">{{ $employee->kpis[0]->kpiType }}</span>
                                </div>


                                <div>
                                    <span
                                        class="badge rounded-pill bg-warning font-size-13">{{ $employee->kpis[0]->kpiName }}</span>
                                </div>
                                <div>
                                    <span class="badge rounded-pill bg-primary font-size-13">Submitted For Review</span>
                                </div>
                            </div>
                        </div>
                    </a>

                @empty
                    <div class="card card-body">
                        <div class="justify-content-between">
                            <h5><b>No Employee Has Submitted Their Kpi For Review Yet..............</b></h5>
                        </div>
                    </div>
                @endforelse
            @else
                @forelse ($employeeProbDetails as $employee)
                    <a
                        href="{{ url('dashboard/supervisor/show-employee-kpi-prob-form/kpi/' . $employee->kpis[0]->kpiId . '/batch' . '/' . $employee->kpis[0]->batchId .'/employee-id'. '/'. $employee->employee->employeeId) }}">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between items-center">


                                <div>
                                    <span> {{ $employee->employee->employeeFullName }}</span> : <span
                                        class="badge rounded-pill bg-dark font-size-13">{{ $employee->kpis[0]->kpiType }}</span>
                                </div>


                                <div>
                                    <span
                                        class="badge rounded-pill bg-warning font-size-13">{{ $employee->kpis[0]->kpiName }}</span>
                                </div>
                                <div>
                                    <span class="badge rounded-pill bg-dark font-size-13">Submitted For Probing</span>
                                </div>
                            </div>
                        </div>
                    </a>

                @empty
                    <div class="card card-body">
                        <div class="justify-content-between">
                            <h5><b>No Employee Has Submitted Their Appraisal Yet..............</b></h5>
                        </div>
                    </div>
                @endforelse
            @endif




        </div>



    </div>




</x-base-layout>
