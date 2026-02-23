<x-base-layout>

    @php
        $accessToken = session('api_token');
        // Fetch user information
        $responseUser = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');

        // Handle responses
        $user = $responseUser->successful() ? $responseUser->object() : null;
        $supervisorId = $user->id;
    @endphp

    <style>
        /* Hide number input spinners/arrows */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        input[type="number"] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }

        /* Progress bar container - sticky at top */
        .progress-container {
            position: sticky;
            top: 60px;
            z-index: 1020;
            background: #fff;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-wrapper .progress {
            flex: 1;
            height: 20px;
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-wrapper .progress-bar {
            border-radius: 10px;
            font-weight: 800;
            font-size: 12px;
            transition: width 0.4s ease;
        }

        .progress-info {
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        /* Save button states */
        .btn-save {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            min-width: 80px;
            transition: all 0.3s ease;
        }

        .btn-save:hover:not(.btn-saved):not(.btn-saving) {
            background-color: #0c63e4;
            border-color: #0b5ed7;
        }

        /* Saved state - gray button */
        .btn-save.btn-saved {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white;
            cursor: default;
        }

        .btn-save.btn-saved:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }

        .btn-save.btn-saving {
            pointer-events: none;
            opacity: 0.8;
        }

        /* Sticky pagination controls */
        .pagination-sticky {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 15px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            border-radius: 10px 10px 0 0;
            z-index: 100;
            margin-top: 20px;
        }

        /* Section cards animation */
        .section-tab {
            transition: all 0.3s ease;
        }

        .section-tab.border-danger {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
        }

        /* Form input focus states */
        .score-input:focus, .comment-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Grade summary cards - responsive */
        .grade-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .grade-card {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-size: 1rem;
        }

        .grade-card .badge {
            font-size: 0.85rem;
            padding: 0.5em 0.8em;
        }

        .grade-card .text-muted {
            font-size: 0.95rem;
        }

        .grade-card strong {
            font-size: 1.1rem;
        }

        .grade-card .d-flex.gap-2 .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.7em;
        }
    </style>

    <div class="container-fluid px-2">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        <a href="{{ route('supervisor.index') }}" class="text-primary">
                            <i class="bx bx-arrow-back me-1"></i>Employee KPIs
                        </a> / Score Employee
                    </h4>
                </div>
            </div>
        </div>

        <!-- Progress Bar - Sticky -->
        <div class="progress-container">
            <div class="container-fluid">
                <div class="progress-wrapper">
                    <div class="progress-info">
                        <span class="badge bg-primary" id="current-page">1</span>
                        <span class="text-muted">of</span>
                        <span class="badge bg-dark" id="total-pages">1</span>
                    </div>
                    <div class="progress">
                        <div id="progress-bar"
                            class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">
                            <span id="progress-text">0%</span>
                        </div>
                    </div>
                    <span class="text-muted small">Completion</span>
                </div>
            </div>
        </div>

        <!-- Grades Summary Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-bar-chart-alt-2 me-2"></i>Grades Summary
                        </h5>
                        <span class="badge rounded-pill bg-primary fs-6">REVIEW</span>
                    </div>
                    <div class="card-body">
                        <div class="grade-summary-grid">
                            <!-- Submitted Employee Grade -->
                            <x-appraisal.grade-card title="Submitted Employee Grade" badgeClass="bg-secondary"
                                :employeeName="$submittedEmployeeGrade->employeeName ?? '----'" :items="[
        'Score' => $submittedEmployeeGrade->totalKpiScore ?? '----',
        'Grade' => $submittedEmployeeGrade->grade ?? '----',
        'Remark' => $submittedEmployeeGrade->remark ?? '----'
    ]" />

                            <!-- Supervisor Grade For Employee -->
                            <x-appraisal.grade-card title="Supervisor Grade For Employee" badgeClass="bg-primary"
                                :employeeName="$supervisorGradeForEmployee->employeeName ?? '----'" :items="[
        'Score' => $supervisorGradeForEmployee->totalKpiScore ?? '----',
        'Grade' => $supervisorGradeForEmployee->grade ?? '----',
        'Remark' => $supervisorGradeForEmployee->remark ?? '----'
    ]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor Evaluation Form Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bx bx-edit-alt me-2"></i>Supervisor Evaluation Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="kpi-form">
                            @if (isset($appraisal) && $appraisal->isNotEmpty())
                                @foreach ($appraisal as $index => $kpi)
                                    <div class="kpi">
                                        @foreach ($kpi->activeSections as $sectionIndex => $section)
                                            <div class="card border section-tab mb-3" style="border-radius: 10px; display: none;"
                                                data-section-page="{{ floor($sectionIndex / 3) }}">
                                                <div class="card-body {{ $section->metrics->isEmpty() ? 'bg-light' : '' }}">
                                                    <div class="section-card" style="margin-top: 1rem;">
                                                        <h5 class="card-title mb-2">
                                                            {{ $section->sectionName }}
                                                            <span class="badge bg-danger ms-2">{{ $section->sectionScore }}</span>
                                                        </h5>
                                                        <p class="text-muted small">{{ $section->sectionDescription }}</p>

                                                        @if ($section->metrics->isEmpty())
                                                            <!-- Employee Score Display (readonly) -->
                                                            <x-appraisal.score-display label="Employee Score and Comment"
                                                                badgeClass="bg-secondary"
                                                                :score="optional($section->sectionEmpScore)->sectionEmpScore ?? ''"
                                                                :comment="optional($section->sectionEmpScore)->employeeComment ?? ''" />

                                                            <!-- Supervisor Score Form -->
                                                            <div class="mt-3">
                                                                <span class="mb-2 badge rounded-pill bg-primary">
                                                                    <strong>Supervisor Score and Comment</strong>
                                                                </span>
                                                                <form action="{{ route('supervisor.rating') }}" method="POST"
                                                                    class="section-form ajax-sup-eval-form mt-2">
                                                                    @csrf
                                                                    <div class="d-flex gap-3 p-4">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control score-input" type="number"
                                                                                name="sectionSupScore" required placeholder="Score"
                                                                                min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                                max="{{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                title="Max score: {{ $section->sectionScore }}"
                                                                                value="{{ optional($section->sectionEmpScore)->sectionSupScore == 0 ? '' : optional($section->sectionEmpScore)->sectionSupScore }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <textarea class="form-control comment-input"
                                                                                name="supervisorComment"
                                                                                placeholder="Enter your comments" rows="2"
                                                                                @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))>{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                        </div>
                                                                        @if (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                            <div></div>
                                                                        @else
                                                                            <input type="hidden" name="scoreId"
                                                                                value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                            <button type="submit" class="btn btn-primary btn-save"
                                                                                style="height: fit-content">
                                                                                <i class="bx bx-save me-1"></i>Save
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @else
                                                            @foreach ($section->metrics as $metric)
                                                                <div class="card border border-success mb-3" style="border-radius: 10px;">
                                                                    <div class="card-body"
                                                                        style="background-color: rgba(30, 255, 0, 0.05);">
                                                                        <div class="metric-card">
                                                                            <h6 class="card-title">
                                                                                {{ $metric->metricName }}
                                                                                <span
                                                                                    class="badge bg-danger ms-2">{{ $metric->metricScore }}</span>
                                                                            </h6>
                                                                            <p class="text-muted small">{{ $metric->metricDescription }}</p>

                                                                            <!-- Employee Score Display (readonly) -->
                                                                            <x-appraisal.score-display label="Employee Score and Comment"
                                                                                badgeClass="bg-secondary"
                                                                                :score="$metric->metricEmpScore->metricEmpScore ?? ''"
                                                                                :comment="$metric->metricEmpScore->employeeComment ?? ''" />

                                                                            <!-- Supervisor Score Form -->
                                                                            <div class="mt-3">
                                                                                <span class="mb-2 badge rounded-pill bg-primary">
                                                                                    <strong>Supervisor Score and Comment</strong>
                                                                                </span>
                                                                                <form action="{{ route('supervisor.rating') }}"
                                                                                    method="POST" class="ajax-sup-eval-form mt-2">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3 p-4">
                                                                                        <div class="col-md-2">
                                                                                            <input class="form-control score-input"
                                                                                                type="number" name="metricSupScore" min="0"
                                                                                                step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                                                max="{{ $metric->metricScore }}"
                                                                                                @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                title="Max score: {{ $metric->metricScore }}"
                                                                                                placeholder="Score" required
                                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore == 0 ? '' : optional($metric->metricEmpScore)->metricSupScore }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control comment-input"
                                                                                                name="supervisorComment"
                                                                                                @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                placeholder="Enter your comments"
                                                                                                rows="2">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                        <input type="hidden" name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">
                                                                                        <button type="submit"
                                                                                            class="btn btn-primary btn-save"
                                                                                            style="height: fit-content"
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))>
                                                                                            <i class="bx bx-save me-1"></i>Save
                                                                                        </button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-info-circle text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">No KPIs available for this employee.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer with Pagination and Submit -->
                    @if (!isset($metric->metricEmpScore) || !in_array($metric->metricEmpScore->status ?? '', ['CONFIRMATION', 'PROBLEM', 'COMPLETED']))
                        <div class="card-footer bg-white pagination-sticky">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <button id="prev-btn" class="btn btn-dark" disabled>
                                        <i class="bx bx-chevron-left me-1"></i>Previous
                                    </button>
                                    <button id="next-btn" class="btn btn-primary">
                                        Next<i class="bx bx-chevron-right ms-1"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target=".bs-push-employee-modal-lg">
                                        <i class="bx bx-upload me-1"></i>Push to Employee
                                    </button>
                                    <button id="submit-btn" type="button" data-bs-toggle="modal" class="btn btn-success"
                                        data-bs-target=".bs-submit-appraisal-modal-lg" disabled>
                                        <i class="bx bx-check-circle me-1"></i>Submit Appraisal
                                    </button>

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if (!isset($metric->metricEmpScore) || !in_array($metric->metricEmpScore->status ?? '', ['CONFIRMATION', 'PROBLEM', 'COMPLETED']))
        <div class="modal fade bs-submit-appraisal-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="myLargeModalLabel">
                            <i class="bx bx-check-circle me-2"></i>Confirm Appraisal Submit
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bx bx-question-mark text-warning" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Submit employee Appraisal for Confirmation?</h5>
                        <p class="text-muted">This action will send the appraisal to the employee for confirmation.</p>

                        <form action="{{ route('submit.appraisal') }}" method="POST">
                            @csrf
                            <input type="hidden" name="employeeId" value="{{ $employeeId }}">
                            <input type="hidden" name="kpiId" value="{{ $kpiId }}">
                            <input type="hidden" name="batchId" value="{{ $batchId }}">
                            <input type="hidden" name="supervisorId" value="{{ $supervisorId }}">
                            <input type="hidden" name="status" value="CONFIRMATION">

                            <div class="mb-3 text-start">
                                <label for="supervisorRecommendation" class="form-label">
                                    <i class="bx bx-message-detail me-1"></i>Supervisor Recommendation (Optional)
                                </label>
                                <textarea class="form-control" id="supervisorRecommendation" name="supervisorRecommendation"
                                    rows="4" placeholder="Enter your recommendation here..."></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="submitReviewButton" class="btn btn-success">
                                    <i class="bx bx-send me-1"></i>Submit Employee Appraisal For Confirmation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Push to Employee Modal -->
    <div class="modal fade bs-push-employee-modal-lg" tabindex="-1" role="dialog" aria-labelledby="pushEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="pushEmployeeModalLabel">
                        <i class="bx bx-upload me-2"></i>Push Appraisal to Employee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bx bx-question-mark text-warning" style="font-size: 48px;"></i>
                    <h5 class="mt-3">Push this appraisal to the employee for review?</h5>
                    <p class="text-muted">This will send the appraisal to the employee for their review and confirmation.</p>
                    <form action="{{ route('submit.appraisal') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employeeId" value="{{ $employeeId }}">
                        <input type="hidden" name="kpiId" value="{{ $kpiId }}">
                        <input type="hidden" name="batchId" value="{{ $batchId }}">
                        <input type="hidden" name="supervisorId" value="{{ $supervisorId }}">
                        <input type="hidden" name="status" value="SCORING">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-upload me-1"></i>Yes, Push to Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sections = document.querySelectorAll('.section-tab');
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                const submitBtn = document.getElementById('submit-btn');
                const currentPageSpan = document.getElementById('current-page');
                const totalPagesSpan = document.getElementById('total-pages');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');

                // Use unique key per employee to avoid page persistence across different forms
                const currentEmployeeId = '{{ $employeeId }}';
                const pageStorageKey = `currentPage_supervisor_${currentEmployeeId}`;
                const lastEmployeeKey = 'lastViewedEmployeeId_supervisor';

                // Check if we're viewing a different employee - if so, reset to page 0
                const lastViewedEmployee = sessionStorage.getItem(lastEmployeeKey);
                let currentPage = 0;

                if (lastViewedEmployee === currentEmployeeId) {
                    // Same employee, restore the page
                    currentPage = parseInt(sessionStorage.getItem(pageStorageKey) || 0);
                } else {
                    // Different employee, start from page 0
                    sessionStorage.setItem(lastEmployeeKey, currentEmployeeId);
                    sessionStorage.setItem(pageStorageKey, '0');
                }

                const sectionsPerPage = 3;
                const totalPages = Math.ceil(sections.length / sectionsPerPage);

                if (totalPagesSpan) totalPagesSpan.textContent = totalPages;

                // Initialize save button states
                document.querySelectorAll('button.btn-save').forEach(btn => {
                    // If button already has btn-saved class (from server), mark as saved
                    if (btn.classList.contains('btn-saved')) {
                        btn.setAttribute('data-saved', 'true');
                    } else {
                        btn.setAttribute('data-saved', 'false');
                    }
                });

                // Helper function to show toast messages
                function showToast(type, message) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            icon: type,
                            title: message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        console.warn('SweetAlert2 not loaded. Message:', message);
                        alert(message);
                    }
                }

                function validateField(field) {
                    const value = field.value.trim();
                    if (value === '') {
                        field.classList.add('is-invalid');
                        field.classList.remove('is-valid');
                        field.closest('.section-tab')?.classList.add('border-danger');
                        return false;
                    } else {
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                        field.closest('.section-tab')?.classList.remove('border-danger');
                        return true;
                    }
                }

                function checkInputs(page) {
                    const start = page * sectionsPerPage;
                    const end = start + sectionsPerPage;
                    let allFilled = true;

                    for (let i = start; i < end && i < sections.length; i++) {
                        const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="SupScore"]');

                        const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');

                        if (!scoresFilled) {
                            allFilled = false;
                            sections[i].classList.add('border-danger');
                        } else {
                            sections[i].classList.remove('border-danger');
                        }
                    }

                    return allFilled;
                }

                function checkAllSupervisorSavedOnPage(page) {
                    const start = page * sectionsPerPage;
                    const end = start + sectionsPerPage;
                    let allSaved = true;

                    for (let i = start; i < end && i < sections.length; i++) {
                        const saveButtons = sections[i].querySelectorAll('button.btn-save');
                        saveButtons.forEach(btn => {
                            if (btn.getAttribute('data-saved') !== 'true') {
                                allSaved = false;
                            }
                        });
                    }

                    return allSaved;
                }

                function checkAllSupervisorSavedAcrossAllPages() {
                    const allSaveButtons = document.querySelectorAll('button.btn-save');
                    let allSaved = true;

                    allSaveButtons.forEach(btn => {
                        if (btn.getAttribute('data-saved') !== 'true') {
                            allSaved = false;
                        }
                    });

                    return allSaved;
                }

                function updateSupervisorPaginationButtons() {
                    updateButtons();
                }

                function updateProgressBar() {
                    let totalValid = 0;
                    sections.forEach(section => {
                        const scoreInputs = section.querySelectorAll('input[type="number"][name*="SupScore"]');
                        const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                        if (scoresFilled) totalValid++;
                    });
                    const percent = sections.length > 0 ? Math.round((totalValid / sections.length) * 100) : 0;

                    if (progressBar) {
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);

                        // Color coding based on progress
                        progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
                        if (percent < 50) {
                            progressBar.classList.add('bg-danger');
                        } else if (percent < 100) {
                            progressBar.classList.add('bg-warning');
                        } else {
                            progressBar.classList.add('bg-success');
                        }
                    }
                    if (progressText) {
                        progressText.textContent = percent + '%';
                    }
                }

                function updateButtons() {
                    if (prevBtn) prevBtn.disabled = currentPage === 0;

                    // Check if all save buttons on current page are saved
                    const allCurrentPageSaved = checkAllSupervisorSavedOnPage(currentPage);
                    if (nextBtn) nextBtn.disabled = currentPage === totalPages - 1 || !allCurrentPageSaved;

                    // Check if all save buttons across all pages are saved
                    const allPagesSaved = checkAllSupervisorSavedAcrossAllPages();
                    if (submitBtn) submitBtn.disabled = !allPagesSaved;
                    updateProgressBar();
                }

                function showPage(page) {
                    sections.forEach(section => {
                        section.style.display = 'none';
                    });
                    const start = page * sectionsPerPage;
                    const end = start + sectionsPerPage;
                    for (let i = start; i < end && i < sections.length; i++) {
                        sections[i].style.display = 'block';
                    }

                    if (currentPageSpan) currentPageSpan.textContent = page + 1;
                    sessionStorage.setItem(pageStorageKey, page);
                    updateButtons();
                    window.scrollTo({
                        top: sections[start]?.offsetTop - 150 || 0,
                        behavior: 'smooth'
                    });
                }

                if (prevBtn) {
                    prevBtn.addEventListener('click', function () {
                        if (currentPage > 0) {
                            currentPage--;
                            showPage(currentPage);
                        }
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function () {
                        if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
                            currentPage++;
                            showPage(currentPage);
                        }
                    });
                }

                document.querySelectorAll('input[type="number"][name*="SupScore"], textarea[name="supervisorComment"]')
                    .forEach(input => {
                        input.addEventListener('input', function () {
                            validateField(this);
                            updateButtons();
                        });
                    });

                // Modified AJAX form handler with dynamic UI update
                document.querySelectorAll('form.ajax-sup-eval-form, form.section-form').forEach(form => {
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const scrollPos = window.scrollY;
                        const formData = new FormData(form);
                        const saveBtn = form.querySelector('button[type="submit"]');
                        const originalHTML = saveBtn.innerHTML;

                        // Store scroll position
                        sessionStorage.setItem('preserveScrollPosition', scrollPos.toString());

                        saveBtn.classList.add('btn-saving');
                        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...';
                        saveBtn.disabled = true;

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                // Mark button as saved - change to gray
                                saveBtn.classList.add('btn-saved');
                                saveBtn.classList.remove('btn-primary');
                                saveBtn.classList.add('btn-secondary');
                                saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Saved';

                                // Store save state in data attribute
                                saveBtn.setAttribute('data-saved', 'true');

                                // Update Next and Submit button states
                                updateSupervisorPaginationButtons();

                                showToast('success', data.message || 'Saved successfully');
                                // Dynamically update the DOM instead of reloading
                                updateFormUI(form, data);
                            } else {
                                showToast('error', data.message || 'An error occurred');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showToast('error', error.message || 'An unexpected error occurred. Please try again.');
                        } finally {
                            saveBtn.classList.remove('btn-saving');
                            if (!saveBtn.classList.contains('btn-saved')) {
                                saveBtn.innerHTML = originalHTML;
                            }
                            saveBtn.disabled = false;
                        }
                    });
                });

                function updateFormUI(form, data) {
                    // Example: Update score and comment fields dynamically
                    const scoreInput = form.querySelector('input[name*="SupScore"]');
                    const commentInput = form.querySelector('textarea[name="supervisorComment"]');

                    if (scoreInput && data.updatedScore) {
                        scoreInput.value = data.updatedScore;
                    }

                    if (commentInput && data.updatedComment) {
                        commentInput.value = data.updatedComment;
                    }

                    // Additional UI updates can be added here
                }

                // Show the initial page
                showPage(currentPage);

                // Check for toast messages after page refresh and restore scroll position
                setTimeout(() => {
                    const savedScrollPos = sessionStorage.getItem('preserveScrollPosition');
                    if (savedScrollPos) {
                        const scrollPos = parseInt(savedScrollPos);
                        if (!isNaN(scrollPos)) {
                            window.scrollTo({
                                top: scrollPos,
                                behavior: 'instant'
                            });
                        }
                        sessionStorage.removeItem('preserveScrollPosition');
                    }

                    const successToast = sessionStorage.getItem('showSuccessToast');
                    if (successToast) {
                        const toastData = JSON.parse(successToast);
                        showToast('success', toastData.message);
                        sessionStorage.removeItem('showSuccessToast');
                    }

                    const errorToast = sessionStorage.getItem('showErrorToast');
                    if (errorToast) {
                        const toastData = JSON.parse(errorToast);
                        showToast('error', toastData.message);
                        sessionStorage.removeItem('showErrorToast');
                    }
                }, 100);
            });
        </script>
    @endpush

</x-base-layout>
