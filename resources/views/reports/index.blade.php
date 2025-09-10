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
                <form id="filterForm" method="POST" action="{{ route('reports.filter') }}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center g-2">
                                <!-- Batch Filter -->
                                <div class="col-md-4">
                                    <label for="batchFilter" class="form-label">Batch</label>
                                    <select id="batchFilter" class="select2 form-control" name="batchId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select batch....</option>
                                        @foreach ($batches as $batch)
                                            <option value="{{ $batch['batchId'] }}">{{ $batch['batchName'] }} -
                                                {{ $batch['batchStatus'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Department Filter -->
                                <div class="col-md-4">
                                    <label for="departmentFilter" class="form-label">Department</label>
                                    <select id="departmentFilter" class="select2 form-control" name="departmentId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select department....</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department['departmentId'] }}">
                                                {{ $department['departmentName'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Branch Filter -->
                                <div class="col-md-4">
                                    <label for="branchFilter" class="form-label">Branch</label>
                                    <select id="branchFilter" class="select2 form-control" name="branchId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select branch....</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch['branchId'] }}">{{ $branch['branchName'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <!-- KPI Filter -->
                                    <div class="col-md-2">
                                        <label for="kpiFilter" class="form-label">KPI</label>
                                        <select id="kpiFilter" class="select2 form-control" name="kpiId"
                                            data-placeholder="Choose ...">
                                            <option value="">Select KPI....</option>
                                            @foreach ($kpis as $kpi)
                                                <option value="{{ $kpi['kpiId'] }}">{{ $kpi['kpiName'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Employee Filter -->
                                    <div class="col-md-2">
                                        <label for="employeeFilter" class="form-label">Employee</label>
                                        <select id="employeeFilter" class="select2 form-control" name="employeeId"
                                            data-placeholder="Choose ...">
                                            <option value="">Select employees....</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee['employeeId'] }}">
                                                    {{ $employee['employeeName'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Buttons -->
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="d-flex gap-2">
                                            <button id="filterButton" type="button" class="btn btn-success">
                                                <i class="bx bx-filter-alt"></i> Filter
                                            </button>
                                            <a href="{{ route('report.index') }}" class="btn btn-primary">
                                                <i class="bx bx-rotate-left"></i> Refresh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                                    <th>Branch</th>
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
                                                <td>{{ $employee->branchName ?? 'N/A' }}</td>
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
                                            <td colspan="12" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="12" class="text-center">No data available</td>
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
                            const branchId = $('#branchFilter').val();
                            const kpiId = $('#kpiFilter').val();
                            const employeeId = $('#employeeFilter').val();

                            // Make AJAX call to fetch filtered data
                            $.ajax({
                                url: "{{ route('report.index') }}", // Replace with your route
                                type: "GET",
                                data: {
                                    batchId: batchId,
                                    departmentId: departmentId,
                                    branchId: branchId,
                                    kpiId: kpiId,
                                    employeeId: employeeId
                                },
                                success: function(response) {
                                    // Update the table body with new data
                                    const tableBody = $('#datatable-buttons tbody');
                                    tableBody.empty();

                                    if (response.reports.length === 0) {
                                        tableBody.append(
                                            '<tr><td colspan="12" class="text-center">No data available</td></tr>');
                                    } else {
                                        response.reports.forEach(report => {
                                            report.employees.forEach(employee => {
                                                // Collect supervisor names
                                                const supervisorNames = employee.scores ? [...new Set(
                                                        employee.scores
                                                        .map(score => score.supervisorName)
                                                        .filter(name => name))]
                                                    .join(' ') :
                                                    'N/A';

                                                // Collect probe names
                                                const probeNames = employee.scores ? [...new Set(
                                                        employee.scores
                                                        .map(score => score.probName)
                                                        .filter(name => name))]
                                                    .join(' ') :
                                                    'N/A';

                                                // Collect status
                                                const statuses = employee.scores ? [...new Set(employee
                                                        .scores
                                                        .map(score => score.status)
                                                        .filter(status => status))]
                                                    .join(' ') :
                                                    'N/A';

                                                const row = `
                                                <tr>
                                                    <td>${report.batchName ?? 'N/A'}</td>
                                                    <td>${employee.employeeName ?? 'N/A'}</td>
                                                    <td>${employee.totalScore?.grade ?? 'N/A'}</td>
                                                    <td>${employee.totalScore?.totalKpiScore ?? 'N/A'}</td>
                                                    <td>${employee.totalScore?.remark ?? 'N/A'}</td>
                                                    <td>${statuses}</td>
                                                    <td>${employee.branchName ?? 'N/A'}</td>
                                                    <td>${employee.departmentName ?? 'N/A'}</td>
                                                    <td>${employee.roleName ?? 'N/A'}</td>
                                                    <td>${supervisorNames}</td>
                                                    <td>${probeNames}</td>
                                                    <td><a href="/reports/employee/${employee.employeeId}/summary"><span class="badge rounded-pill bg-primary">View</span></a></td>
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
