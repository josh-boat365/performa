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
        {{--  {{ dd($reports) }}  --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="filterForm" method="POST" action="{{ route('reports.filter') }}">
                            @csrf
                            <div class="d-flex justify-content-between">
                                <div class="filters d-flex gap-3">
                                    <div class="col-3">
                                        <p class="m-0">Batch</p>
                                        <select id="batchFilter" class="select2 form-control" name="batchId"
                                            data-placeholder="Choose ...">
                                            <option value="">Select batch....</option>
                                            @foreach ($batches as $batch)
                                                <option value="{{ $batch['batchId'] }}">{{ $batch['batchName'] }} -
                                                    {{ $batch['batchStatus'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <p class="m-0">Department</p>
                                        <select id="departmentFilter" class="select2 form-control" name="departmentId"
                                            data-placeholder="Choose ...">
                                            <option value="">Select department....</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department['departmentId'] }}">
                                                    {{ $department['departmentName'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{--  {{ dd($kpis) }}  --}}
                                    <div class="col-3">
                                        <p class="m-0">KPI</p>
                                        <select id="kpiFilter" name="kpiId" class="select2 form-control"
                                            data-placeholder="Choose ...">
                                            <option value="">Select KPI....</option>
                                            @foreach ($kpis as $kpi)
                                                <option value="{{ $kpi['kpiId'] }}">{{ $kpi['kpiName'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <p class="m-0">Employee</p>
                                        <select id="employeeFilter" name="employeeId" class="select2 form-control"
                                            data-placeholder="Choose ...">
                                            <option value="">Select employees....</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee['employeeId'] }}">
                                                    {{ $employee['employeeName'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <button id="filterButton" type="submit" class="btn btn-success"><i
                                                class="bx bx-filter-alt"></i> Filter</button>
                                    </div>

                                </div>
                        </form>

                        <div class="">
                            <a href="{{ route('report.index') }}" class="btn btn-primary"><i style="font-size: x-large"
                                    class="bx bx-rotate-left"></i></a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

    @php
        $reports = $reports ?? collect(); // Default to an empty collection if undefined
    @endphp


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Appraisal Reports Table</h4>

                    <div class="table-responsive">
                        <table id="datatable-buttons"
                            class="reportsTable table table-bordered table-striped table-hover dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Batch</th>
                                    <th>Employee Full Name</th>
                                    <th>Grade</th>
                                    <th>Score</th>
                                    <th>Remark</th>
                                    <th>Status</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Supervisor Name</th>
                                    <th>Probe Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTableBody">
                                @if ($reports && $reports->isNotEmpty())
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

                                                $status = collect($employee->scores)
                                                    ->pluck('status')
                                                    ->unique()
                                                    ->filter()
                                                    ->implode(' ');
                                            @endphp
                                            <tr>
                                                <td>{{ $report->batchName ?? 'N/A' }}</td>
                                                <td>{{ $employee->employeeName ?? 'N/A' }}</td>
                                                <td>{{ $employee->totalScore->grade ?? 'N/A' }}</td>
                                                <td>{{ $employee->totalScore->totalKpiScore ?? 'N/A' }}</td>
                                                <td>{{ $employee->totalScore->remark ?? 'N/A' }}</td>
                                                <td>{{ $status ?? 'N/A' }}</td>
                                                <td>{{ $employee->departmentName ?? 'N/A' }}</td>
                                                <td>{{ $employee->roleName ?? 'N/A' }}</td>
                                                <td>{{ $supervisorName ?? 'N/A' }}</td>
                                                <td>{{ $probeName ?? 'N/A' }}</td>
                                                <td><a
                                                        href="{{ route('reports.employee.summary', $employee->employeeId) }}"><span
                                                            class="badge rounded-pill bg-primary">View</span></a></td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center">No data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <script>
                        document.getElementById('filterButton').addEventListener('click', function() {
                            // Gather filter criteria
                            const batchId = $('#batchFilter').val();
                            const departmentId = $('#departmentFilter').val();
                            const kpiId = $('#kpiFilter').val();
                            const employeeId = $('#employeeFilter').val();

                            // Make AJAX call to fetch filtered data
                            $.ajax({
                                url: "{{ route('report.index') }}", // Replace with your route
                                type: "GET",
                                data: {
                                    batchId: batchId,
                                    departmentId: departmentId,
                                    kpiId: kpiId,
                                    employeeId: employeeId
                                },
                                success: function(response) {
                                    // Update the table body with new data
                                    const tableBody = $('#datatable-buttons tbody');
                                    tableBody.empty();

                                    if (response.reports.length === 0) {
                                        tableBody.append(
                                            '<tr><td colspan="10" class="text-center">No data available</td></tr>');
                                    } else {
                                        response.reports.forEach(report => {
                                            report.employees.forEach(employee => {
                                                const row = `
                                <tr>
                                    <td>${report.batchName ?? 'N/A'}</td>
                                    <td>${employee.employeeName ?? 'N/A'}</td>
                                    <td>${employee.totalScore?.grade ?? 'N/A'}</td>
                                    <td>${employee.totalScore?.totalKpiScore ?? 'N/A'}</td>
                                    <td>${employee.totalScore?.remark ?? 'N/A'}</td>
                                    <td>${employee.departmentName ?? 'N/A'}</td>
                                    <td>${employee.roleName ?? 'N/A'}</td>
                                    <td>${employee.supervisorName ?? 'N/A'}</td>
                                    <td>${employee.probeName ?? 'N/A'}</td>
                                    <td><a href="#"><span class="badge rounded-pill bg-primary">View</span></a></td>
                                </tr>
                            `;
                                                tableBody.append(row);
                                            });
                                        });
                                    }
                                },
                                error: function() {
                                    alert('Failed to fetch filtered data. Please try again.');
                                }
                            });
                        });
                    </script>


                </div>
            </div>
        </div> <!-- end col -->
    </div>
    </div>
</x-base-layout>
