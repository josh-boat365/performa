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
                                                {{ $batch['batchName'] }}
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
                            <table id="datatable-buttons" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th>Batch</th>
                                        <th>Employee Name</th>
                                        <th class="text-center">Grade</th>
                                        <th>Recommendation</th>
                                        <th class="text-center">Score</th>
                                        <th>Remark</th>
                                        <th class="text-center">Status</th>
                                        <th>Branch</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th>Supervisor</th>
                                        <th>Probe</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="reportsTableBody">
                                    @php $rowNum = 0; @endphp
                                    @forelse ($reports as $report)
                                        @foreach ($report->employees ?? [] as $employee)
                                            @php
                                                $rowNum++;
                                                $scores = collect($employee->scores ?? []);
                                                $supervisorName = $scores->pluck('supervisorName')->filter()->unique()->implode(', ') ?: 'N/A';
                                                $probeName = $scores->pluck('probName')->filter()->unique()->implode(', ') ?: 'N/A';
                                                $status = $scores->pluck('status')->filter()->unique()->first() ?: 'N/A';
                                                $recommendation = $employee->totalScore->recommendation ?? null;
                                                $statusClass = match($status) {
                                                    'Completed' => 'bg-success',
                                                    'Pending' => 'bg-warning text-dark',
                                                    'In Progress' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-semibold">{{ $rowNum }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $report->batchName ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        {{--  <div class="avatar-circle me-2">
                                                            {{ strtoupper(substr($employee->employeeName ?? 'N', 0, 1)) }}
                                                        </div>  --}}
                                                        <div>
                                                            <span class="fw-medium">{{ $employee->employeeName ?? 'N/A' }}</span>
                                                            @if($employee->staffNumber ?? null)
                                                                <small class="d-block text-muted">{{ $employee->staffNumber }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $employee->totalScore->grade ?? 'N/A' }}</span>
                                                </td>
                                                <td>{{ $recommendation ?: '—' }}</td>
                                                <td class="text-center">
                                                    <strong class="text-primary">{{ $employee->totalScore->totalKpiScore ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    <span class="text-muted small">{{ $employee->totalScore->remark ?? 'N/A' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                                </td>
                                                <td>{{ $employee->branchName ?? 'N/A' }}</td>
                                                <td>{{ $employee->departmentName ?? 'N/A' }}</td>
                                                <td>{{ $employee->roleName ?? 'N/A' }}</td>
                                                <td>{{ $supervisorName }}</td>
                                                <td>{{ $probeName }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('reports.employee.summary', ['employeeId' => $employee->employeeId, 'batchId' => $report->batchId]) }}"
                                                       class="btn btn-sm btn-primary"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                                    <p class="mb-0">No reports available</p>
                                                    @if(request()->anyFilled(['batchId', 'branchId', 'departmentId', 'employeeId']))
                                                        <small>Try adjusting your filter criteria</small>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Show loading state when form is submitted
            $('#filterForm').on('submit', function() {
                showLoading();
            });

            // Refresh button functionality
            $('#refreshData').on('click', function() {
                showLoading();
                location.reload();
            });

            // Loading state helpers
            function showLoading() {
                $('#loadingIndicator').fadeIn(200);
                $('#tableContainer').fadeOut(200);
            }

            function hideLoading() {
                $('#loadingIndicator').fadeOut(200);
                $('#tableContainer').fadeIn(200);
            }

            // Enhance user experience with filter hints
            $('.filter-dropdown').on('change', function() {
                updateFilterStatus();
            });

            function updateFilterStatus() {
                const activeFilters = [];

                if ($('#batchFilter').val()) {
                    activeFilters.push('Batch: ' + $('#batchFilter option:selected').text().trim());
                }
                if ($('#branchFilter').val()) {
                    activeFilters.push('Branch: ' + $('#branchFilter option:selected').text().trim());
                }
                if ($('#departmentFilter').val()) {
                    activeFilters.push('Department: ' + $('#departmentFilter option:selected').text().trim());
                }
                if ($('#employeeFilter').val()) {
                    activeFilters.push('Employee: ' + $('#employeeFilter option:selected').text().trim());
                }

                // Remove any existing filter status
                $('#filterStatus').remove();

                if (activeFilters.length > 0) {
                    const statusHtml = `
                        <div id="filterStatus" class="alert alert-light border mt-3">
                            <i class="bx bx-filter-alt me-1"></i>
                            <strong>Selected Filters:</strong> ${activeFilters.join(' | ')}
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
        .avatar-circle {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .table > :not(caption) > * > * {
            padding: 0.6rem 0.5rem;
            vertical-align: middle;
        }
        .table thead th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .fw-medium {
            font-weight: 500;
        }
    </style>
</x-base-layout>
