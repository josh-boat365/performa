<x-base-layout>

    <div class="container-fluid px-1">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Overview of Appraisal Reports</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        {{--  {{ dd($reports, $batches, $employees, $kpis, $departments) }}  --}}

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">Batch<i
                                            class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu p-3">
                                        @foreach ($batches as $batch )
                                        <div class="form-check form-check-primary mb-3">
                                            <input class="form-check-input" type="checkbox" id="formCheckcolor1" value="{{ $batch->batchId }}">
                                            <label class="form-check-label" for="formCheckcolor1">
                                                {{ $batch['batchName'] }}
                                            </label>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">Department<i
                                            class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu p-3">
                                        @foreach ($departments as $department )
                                        <div class="form-check form-check-primary mb-3">
                                            <input class="form-check-input" type="checkbox" id="formCheckcolor1" value="{{ $department->departmentId }}">
                                            <label class="form-check-label" for="formCheckcolor1">
                                                {{ $department['departmentName'] }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">KPI<i
                                            class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu p-3">
                                        @foreach ($kpis as $kpi )
                                        <div class="form-check form-check-primary mb-3">
                                            <input class="form-check-input" type="checkbox" id="formCheckcolor1" value="{{ $kpi->kpiId }}">
                                            <label class="form-check-label" for="formCheckcolor1">
                                                {{ $kpi['kpiName'] }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">Employee<i
                                            class="mdi mdi-chevron-down"></i></button>
                                    <div class="dropdown-menu p-3">
                                        @foreach ($employees as $employee)
                                        <div class="form-check form-check-primary mb-3">
                                            <input class="form-check-input" type="checkbox" id="formCheckcolor1" value="{{ $employee->employeeId }}">
                                            <label class="form-check-label" for="formCheckcolor1">
                                                {{ $employee['employeeName'] }}
                                            </label>
                                        </div>

                                        @endforeach


                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success"><i class="bx bx-filter-alt"></i>
                                    Filter</button>

                            </div>
                            <div class="d-flex gap-3">
                                <button class="btn btn-primary"><i class="bx bx-spreadsheet"></i>Print-Excel</button>
                                <button class="btn btn-success"><i class="bx bx-file"></i>Print-PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">



                        <h4 class="card-title mb-4">Appraisal Reports Table</h4>


                        <div class="table-responsive">
                            <table id="datatable-buttons"
                                class="table table-bordered table-striped table-hover dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Batch</th>
                                        <th>Employee Full Name</th>
                                        <th>Grade</th>
                                        <th>Score</th>
                                        <th>Remark</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th>Supervisor Name</th>
                                        <th>Probe Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reports as $report)
                                        @foreach ($report->employees as $employee)
                                            @php
                                                // Collect all supervisorName and probeName, then get unique values
                                                $supervisorName = collect($employee->scores)
                                                    ->pluck('supervisorName')
                                                    ->unique()
                                                    ->filter()
                                                    ->implode(' ');

                                                $probeName = collect($employee->scores)
                                                    ->pluck('probName')
                                                    ->unique()
                                                    ->filter()
                                                    ->implode(' ');
                                            @endphp
                                            <tr>
                                                <td>{{ $report->batchName ?? 'N/A' }}</td>
                                                <td>{{ $employee->employeeName  ?? 'N/A' }}</td>
                                                <td>{{ $employee->totalScore->grade  ?? 'N/A' }}</td>
                                                <td>{{ $employee->totalScore->totalKpiScore  ?? 'N/A' }}</td>
                                                <td>{{ $employee->totalScore->remark  ?? 'N/A' }}</td>
                                                <td>{{ $employee->departmentName  ?? 'N/A' }}</td>
                                                <td>{{ $employee->roleName  ?? 'N/A' }}</td>
                                                <td>{{ $supervisorName ?? 'N/A' }}</td>
                                                <td>{{ $probeName ?? 'N/A' }}</td>
                                                <td><a href="#"><span
                                                            class="badge rounded-pill bg-primary">View</span></a></td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>



                    </div>
                </div>
            </div> <!-- end col -->
        </div>




    </div>



</x-base-layout>
