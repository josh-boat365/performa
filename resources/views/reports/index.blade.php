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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="filterForm" method="GET" action="{{ route('report.index') }}">
                            <!-- Filter Row: Batch, Branch, Department, Employee -->
                            <div class="row align-items-center g-3 mb-3">
                                <!-- Batch Filter -->
                                <div class="col-md-3">
                                    <label for="batchFilter" class="form-label">Batch</label>
                                    <select id="batchFilter" class="select2 form-control filter-dropdown" name="batchId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select batch....</option>
                                        @foreach ($batches as $batch)
                                            <option value="{{ $batch['batchId'] }}"
                                                {{ request('batchId') == $batch['batchId'] ? 'selected' : '' }}>
                                                {{ $batch['batchName'] }} - {{ $batch['batchStatus'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Branch Filter -->
                                <div class="col-md-3">
                                    <label for="branchFilter" class="form-label">Branch</label>
                                    <select id="branchFilter" class="select2 form-control filter-dropdown" name="branchId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select branch....</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch['branchId'] }}"
                                                {{ request('branchId') == $branch['branchId'] ? 'selected' : '' }}>
                                                {{ $branch['branchName'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Department Filter -->
                                <div class="col-md-3">
                                    <label for="departmentFilter" class="form-label">Department</label>
                                    <select id="departmentFilter" class="select2 form-control filter-dropdown" name="departmentId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select department....</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department['departmentId'] }}"
                                                {{ request('departmentId') == $department['departmentId'] ? 'selected' : '' }}>
                                                {{ $department['departmentName'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Employee Filter -->
                                <div class="col-md-3">
                                    <label for="employeeFilter" class="form-label">Employee</label>
                                    <select id="employeeFilter" class="select2 form-control filter-dropdown" name="employeeId"
                                        data-placeholder="Choose ...">
                                        <option value="">Select employees....</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee['employeeId'] }}"
                                                {{ request('employeeId') == $employee['employeeId'] ? 'selected' : '' }}>
                                                {{ $employee['employeeName'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons Row -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button id="applyFilters" type="submit" class="btn btn-success">
                                            <i class="bx bx-filter"></i> Apply Filters
                                        </button>
                                        <a href="{{ route('report.index') }}" class="btn btn-warning">
                                            <i class="bx bx-x"></i> Clear All Filters
                                        </a>
                                        <button type="button" id="refreshData" class="btn btn-primary">
                                            <i class="bx bx-rotate-left"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        @php
            $reports = $reports ?? collect();
            $totalEmployees = $reports->sum(function ($report) {
                return count($report->employees ?? []);
            });
        @endphp

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Appraisal Reports Table</h4>
                            <!-- Results counter -->
                            <span id="resultsCounter" class="badge bg-info text-white fs-6">
                                Total Results: <span id="totalCount">{{ $totalEmployees }}</span>
                            </span>
                        </div>

                        @if(request()->anyFilled(['batchId', 'branchId', 'departmentId', 'employeeId']))
                        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                            <i class="bx bx-info-circle me-2"></i>
                            Showing filtered results.
                            <a href="{{ route('report.index') }}" class="alert-link">Clear all filters</a> to see all data.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Loading indicator -->
                        <div id="loadingIndicator" class="text-center" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading data, please wait...</p>
                        </div>

                        <div class="table-responsive" id="tableContainer">
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
                                                    $supervisorName = collect($employee->scores)
                                                        ->pluck('supervisorName')
                                                        ->unique()
                                                        ->filter()
                                                        ->implode(', ');

                                                    $probeName = collect($employee->scores)
                                                        ->pluck('probName')
                                                        ->unique()
                                                        ->filter()
                                                        ->implode(', ');

                                                    $status = collect($employee->scores)
                                                        ->pluck('status')
                                                        ->unique()
                                                        ->filter()
                                                        ->implode(', ');
                                                @endphp
                                                <tr>
                                                    <td>{{ $report->batchName ?? 'N/A' }}</td>
                                                    <td>{{ $employee->employeeName ?? 'N/A' }}</td>
                                                    <td>{{ $employee->totalScore->grade ?? 'N/A' }}</td>
                                                    <td>{{ $employee->totalScore->totalKpiScore ?? 'N/A' }}</td>
                                                    <td>{{ $employee->totalScore->remark ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge {{ $status == 'Completed' ? 'bg-success' : ($status == 'Pending' ? 'bg-warning' : 'bg-secondary') }}">
                                                            {{ $status ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $employee->branchName ?? 'N/A' }}</td>
                                                    <td>{{ $employee->departmentName ?? 'N/A' }}</td>
                                                    <td>{{ $employee->roleName ?? 'N/A' }}</td>
                                                    <td>{{ $supervisorName ?? 'N/A' }}</td>
                                                    <td>{{ $probeName ?? 'N/A' }}</td>
                                                    <td>
                                                        <a
                                                            href="{{ route('reports.employee.summary', $employee->employeeId) }}">
                                                            <span class="badge rounded-pill bg-primary">View</span>
                                                        </a>
                                                    </td>
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
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for all filter dropdowns
            $('.select2').select2({
                placeholder: function() {
                    return $(this).data('placeholder');
                },
                allowClear: true,
                width: '100%'
            });

            // Show loading state when form is submitted
            $('#filterForm').on('submit', function() {
                $('#loadingIndicator').show();
                $('#tableContainer').hide();
            });

            // Refresh button functionality
            $('#refreshData').on('click', function() {
                $('#loadingIndicator').show();
                $('#tableContainer').hide();

                // Add a cache-busting parameter
                const url = new URL(window.location.href);
                url.searchParams.set('refresh', Date.now());
                window.location.href = url.toString();
            });

            // Enhance user experience with filter hints
            $('.filter-dropdown').on('change', function() {
                updateFilterStatus();
            });

            function updateFilterStatus() {
                const activeFilters = [];

                if ($('#batchFilter').val()) {
                    activeFilters.push('Batch: ' + $('#batchFilter option:selected').text());
                }
                if ($('#branchFilter').val()) {
                    activeFilters.push('Branch: ' + $('#branchFilter option:selected').text());
                }
                if ($('#departmentFilter').val()) {
                    activeFilters.push('Department: ' + $('#departmentFilter option:selected').text());
                }
                if ($('#employeeFilter').val()) {
                    activeFilters.push('Employee: ' + $('#employeeFilter option:selected').text());
                }

                // Remove any existing filter status
                $('#filterStatus').remove();

                if (activeFilters.length > 0) {
                    const statusHtml = `
                        <div id="filterStatus" class="alert alert-info mt-3">
                            <strong>Active Filters:</strong> ${activeFilters.join(', ')}
                        </div>
                    `;
                    $('#filterForm').after(statusHtml);
                }
            }

            // Initialize filter status on page load
            updateFilterStatus();
        });
    </script>

    <style>
        .loading-overlay {
            border-radius: 0.5rem;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            line-height: 36px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }
        .badge {
            font-size: 0.85em;
        }
        #loadingIndicator {
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
        }
    </style>
</x-base-layout>
