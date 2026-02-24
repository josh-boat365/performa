<x-base-layout>

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
            min-width: 80px;
            transition: all 0.3s ease;
        }

        .btn-save.btn-saved {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }

        .btn-save.btn-saving {
            pointer-events: none;
            opacity: 0.8;
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

        /* Probe checkbox indicator */
        .probe-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .probe-indicator .form-check-input:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>

    <div class="container-fluid px-2">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        <a href="#" class="text-primary">
                            <i class="bx bx-arrow-back me-1"></i>Employee KPIs
                        </a> / Probe Resolution
                    </h4>
                </div>
            </div>
        </div>

        <!-- Progress Bar - Sticky -->
        <div class="progress-container">
            <div class="container-fluid">
                <div class="progress-wrapper">
                    <div class="progress-info">
                        <span class="badge bg-warning text-dark" id="probe-status">PROBE</span>
                    </div>
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar bg-warning progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="progress-text">0%</span>
                        </div>
                    </div>
                    <span class="text-muted small">Probe Resolution</span>
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
                        <span class="badge rounded-pill bg-danger fs-6">PROBE</span>
                    </div>
                    <div class="card-body">
                        <div class="grade-summary-grid">
                            <!-- Submitted Employee Grade -->
                            <x-appraisal.grade-card
                                title="Submitted Employee Grade"
                                badgeClass="bg-secondary"
                                :employeeName="$submittedEmployeeGrade->employeeName ?? '----'"
                                :items="[
                                    'Score' => $submittedEmployeeGrade->totalKpiScore ?? '----',
                                    'Grade' => $submittedEmployeeGrade->grade ?? '----',
                                    'Remark' => $submittedEmployeeGrade->remark ?? '----'
                                ]" />

                            <!-- Supervisor Grade For Employee -->
                            <x-appraisal.grade-card
                                title="Supervisor Grade For Employee"
                                badgeClass="bg-primary"
                                :employeeName="$supervisorGradeForEmployee->employeeName ?? '----'"
                                :items="[
                                    'Score' => $supervisorGradeForEmployee->totalKpiScore ?? '----',
                                    'Grade' => $supervisorGradeForEmployee->grade ?? '----',
                                    'Remark' => $supervisorGradeForEmployee->remark ?? '----'
                                ]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Probe Supervisor Evaluation Form Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bx bx-target-lock me-2"></i>Probe Supervisor Evaluation Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="kpi-form">
                            @if (isset($appraisal) && $appraisal->isNotEmpty())
                                @foreach ($appraisal as $kpi)
                                    <div class="kpi">
                                        @foreach ($kpi->activeSections as $sectionIndex => $section)
                                            @php
                                                $metrics = collect($section->metrics ?? []);
                                            @endphp
                                            <div class="card border section-tab mb-3" style="border-radius: 10px;">
                                                <div class="card-body {{ $metrics->isEmpty() ? 'bg-light' : '' }}">
                                                    <div class="section-card" style="margin-top: 1rem;">
                                                        <h5 class="card-title mb-2">
                                                            {{ $section->sectionName }}
                                                            <span class="badge bg-danger ms-2">{{ $section->sectionScore }}</span>
                                                            @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->prob === true)
                                                                <span class="badge bg-warning text-dark ms-1">
                                                                    <i class="bx bx-error-circle me-1"></i>Flagged for Probe
                                                                </span>
                                                            @endif
                                                        </h5>
                                                        <p class="text-muted small">{{ $section->sectionDescription }}</p>

                                                        @if ($metrics->isEmpty())
                                                            <!-- Employee Score Display (readonly) -->
                                                            <x-appraisal.score-display
                                                                label="Employee Score and Comment"
                                                                badgeClass="bg-secondary"
                                                                :score="$section->sectionEmpScore->sectionEmpScore ?? ''"
                                                                :comment="$section->sectionEmpScore->employeeComment ?? ''" />

                                                            <!-- Supervisor Score Display (readonly) -->
                                                            <div class="d-flex align-items-center gap-2 mt-3">
                                                                <x-appraisal.score-display
                                                                    label="Supervisor Score and Comment"
                                                                    badgeClass="bg-primary"
                                                                    :score="optional($section->sectionEmpScore)->sectionSupScore ?? ''"
                                                                    :comment="$section->sectionEmpScore->supervisorComment ?? ''" />
                                                                <div class="probe-indicator ms-2">
                                                                    <input style="width:1.5rem; height:1.5rem"
                                                                        class="form-check-input" type="checkbox" disabled
                                                                        @checked(isset($section->sectionEmpScore) && $section->sectionEmpScore->prob === true)>
                                                                    <small class="text-muted">Probe Flag</small>
                                                                </div>
                                                            </div>

                                                            <!-- Probing Supervisor Score Form -->
                                                            @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->prob !== false)
                                                                <div class="mt-3">
                                                                    <span class="mb-2 badge rounded-pill bg-dark">
                                                                        <strong><i class="bx bx-target-lock me-1"></i>Probing Supervisor Score and Comment</strong>
                                                                    </span>
                                                                    <form action="{{ route('prob.store') }}" method="POST"
                                                                        class="section-form ajax-sup-eval-form mt-2">
                                                                        @csrf
                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input class="form-control score-input" type="number"
                                                                                    name="sectionProbScore" required
                                                                                    placeholder="Score" min="0" step="0.01"
                                                                                    pattern="\d+(\.\d{1,2})?"
                                                                                    max="{{ $section->sectionScore }}"
                                                                                    @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED']))
                                                                                    title="Max score: {{ $section->sectionScore }}"
                                                                                    value="{{ optional($section->sectionEmpScore)->sectionProbScore == 0 ? '' : optional($section->sectionEmpScore)->sectionProbScore }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <textarea class="form-control comment-input"
                                                                                    name="probComment"
                                                                                    placeholder="Enter your comments" rows="2"
                                                                                    @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED']))>{{ $section->sectionEmpScore->probComment ?? '' }}</textarea>
                                                                            </div>
                                                                            @if (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED']))
                                                                                <div></div>
                                                                            @else
                                                                                <input type="hidden" name="scoreId"
                                                                                    value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                                <button type="submit" class="btn btn-primary btn-save" style="height: fit-content">
                                                                                    <i class="bx bx-save me-1"></i>Save
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        @else
                                                            @foreach ($metrics as $metric)
                                                                <div class="card border border-success mb-3" style="border-radius: 10px;">
                                                                    <div class="card-body" style="background-color: rgba(30, 255, 0, 0.05);">
                                                                        <div class="metric-card">
                                                                            <h6 class="card-title">
                                                                                {{ $metric->metricName }}
                                                                                <span class="badge bg-danger ms-2">{{ $metric->metricScore }}</span>
                                                                                @if (isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === true)
                                                                                    <span class="badge bg-warning text-dark ms-1">
                                                                                        <i class="bx bx-error-circle me-1"></i>Flagged
                                                                                    </span>
                                                                                @endif
                                                                            </h6>
                                                                            <p class="text-muted small">{{ $metric->metricDescription }}</p>

                                                                            <!-- Employee Score Display (readonly) -->
                                                                            <x-appraisal.score-display
                                                                                label="Employee Score and Comment"
                                                                                badgeClass="bg-secondary"
                                                                                :score="$metric->metricEmpScore->metricEmpScore ?? ''"
                                                                                :comment="$metric->metricEmpScore->employeeComment ?? ''" />

                                                                            <!-- Supervisor Score Display (readonly) -->
                                                                            <div class="d-flex align-items-start gap-2 mt-3">
                                                                                <div class="flex-grow-1">
                                                                                    <x-appraisal.score-display
                                                                                        label="Supervisor Score and Comment"
                                                                                        badgeClass="bg-primary"
                                                                                        :score="optional($metric->metricEmpScore)->metricSupScore ?? ''"
                                                                                        :comment="$metric->metricEmpScore->supervisorComment ?? ''" />
                                                                                </div>
                                                                                <div class="probe-indicator">
                                                                                    <input style="width:1.5rem; height:1.5rem"
                                                                                        class="form-check-input" type="checkbox" disabled
                                                                                        @checked(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === true)>
                                                                                    <small class="text-muted">Probe</small>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Probing Supervisor Score Form -->
                                                                            @if (isset($metric->metricEmpScore) && $metric->metricEmpScore->prob !== false)
                                                                                <div class="mt-3">
                                                                                    <span class="mb-2 badge rounded-pill bg-dark">
                                                                                        <strong><i class="bx bx-target-lock me-1"></i>Probing Supervisor Score and Comment</strong>
                                                                                    </span>
                                                                                    <form action="{{ route('prob.store') }}" method="POST"
                                                                                        class="ajax-sup-eval-form mt-2">
                                                                                        @csrf
                                                                                        <div class="d-flex gap-3">
                                                                                            <div class="col-md-2">
                                                                                                <input class="form-control score-input" type="number"
                                                                                                    name="metricProbScore" min="0" step="0.01"
                                                                                                    pattern="\d+(\.\d{1,2})?"
                                                                                                    max="{{ $metric->metricScore }}"
                                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['COMPLETED']))
                                                                                                    title="Max score: {{ $metric->metricScore }}"
                                                                                                    placeholder="Score" required
                                                                                                    value="{{ optional($metric->metricEmpScore)->metricProbScore ?: '' }}">
                                                                                            </div>
                                                                                            <div class="col-md-9">
                                                                                                <textarea class="form-control comment-input"
                                                                                                    name="probComment"
                                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['COMPLETED']))
                                                                                                    placeholder="Enter your comments"
                                                                                                    rows="2">{{ $metric->metricEmpScore->probComment ?? '' }}</textarea>
                                                                                            </div>
                                                                                            @if (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['COMPLETED']))
                                                                                                <div></div>
                                                                                            @else
                                                                                                <input type="hidden" name="scoreId"
                                                                                                    value="{{ $metric->metricEmpScore->id ?? '' }}">
                                                                                                <button type="submit" class="btn btn-primary btn-save" style="height: fit-content">
                                                                                                    <i class="bx bx-save me-1"></i>Save
                                                                                                </button>
                                                                                            @endif
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            @endif
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

                    <!-- Card Footer with Submit Button -->
                    @if (!isset($metric->metricEmpScore) || $metric->metricEmpScore->status !== 'COMPLETED')
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-end">
                            <button type="button" data-bs-toggle="modal" class="btn btn-success"
                                data-bs-target=".bs-delete-modal-lg" id="submitAppraisalButton" disabled>
                                <i class="bx bx-check-circle me-1"></i>Resolve Employee Probe
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if (!isset($metric->metricEmpScore) || $metric->metricEmpScore->status !== 'COMPLETED')
        <x-appraisal.confirmation-modal
            id="bs-delete-modal-lg"
            title="Resolve Employee Probe"
            icon="bx-check-circle"
            iconColor="text-success"
            headerClass="bg-success text-white"
            message="Submit to resolve employee Appraisal Probe?"
            description="This action will complete the probe resolution process."
            :action="route('submit.appraisal')"
            buttonText="Submit Employee Appraisal For Confirmation"
            buttonClass="btn-success"
            buttonIcon="bx-check"
            :hiddenFields="[
                'kpiId' => $kpi->kpi->kpiId ?? '',
                'batchId' => $kpi->kpi->batchId ?? '',
                'status' => 'COMPLETED'
            ]" />
    @endif

    <script>
        // Function to check if all score inputs are filled
        function checkInputs() {
            const scoreInputs = document.querySelectorAll('input[type="number"][name*="ProbScore"]');
            const allFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
            const submitBtn = document.getElementById('submitAppraisalButton');
            if (submitBtn) {
                submitBtn.disabled = !allFilled;
            }
        }

        // Attach event listeners to all score inputs
        document.querySelectorAll('input[type="number"][name*="ProbScore"]').forEach(input => {
            input.addEventListener('input', checkInputs);
        });

        // Initial check in case inputs are pre-filled
        checkInputs();
    </script>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sections = document.querySelectorAll('.section-tab');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');

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

                function updateProgressBar() {
                    let totalValid = 0;
                    let totalForms = 0;
                    sections.forEach(section => {
                        const scoreInput = section.querySelector('input[name*="ProbScore"]');
                        const commentInput = section.querySelector('textarea[name="probComment"]');
                        if (scoreInput) {
                            totalForms++;
                            const scoreFilled = scoreInput.value.trim() !== '';
                            const commentFilled = commentInput ? commentInput.value.trim() !== '' : true;
                            if (scoreFilled && commentFilled) totalValid++;
                        }
                    });
                    const percent = totalForms > 0 ? Math.round((totalValid / totalForms) * 100) : 0;

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

                function attachListeners() {
                    sections.forEach(section => {
                        const scoreInput = section.querySelector('input[name*="ProbScore"]');
                        const commentInput = section.querySelector('textarea[name="probComment"]');
                        [scoreInput, commentInput].forEach(input => {
                            if (input) {
                                input.addEventListener('input', () => {
                                    validateField(input);
                                    updateProgressBar();
                                });
                            }
                        });
                    });
                }

                document.querySelectorAll('form.ajax-sup-eval-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const scrollPos = window.scrollY;
                        const formData = new FormData(form);
                        const saveBtn = form.querySelector('button[type="submit"]');
                        const originalHTML = saveBtn.innerHTML;

                        saveBtn.classList.add('btn-saving');
                        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...';
                        saveBtn.disabled = true;

                        fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Show saved state
                                saveBtn.classList.remove('btn-saving');
                                saveBtn.classList.add('btn-saved');
                                saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Saved';
                                saveBtn.disabled = false;

                                showToast('success', 'Saved successfully');

                                // Reset button after delay
                                setTimeout(() => {
                                    saveBtn.classList.remove('btn-saved');
                                    saveBtn.innerHTML = originalHTML;
                                }, 2000);

                                updateProgressBar();
                                checkInputs();
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                saveBtn.classList.remove('btn-saving');
                                saveBtn.innerHTML = originalHTML;
                                saveBtn.disabled = false;
                                showToast('error', 'Failed to save');
                            })
                            .finally(() => {
                                window.scrollTo(0, scrollPos);
                            });
                    });
                });

                attachListeners();
                updateProgressBar();
            });
        </script>
    @endpush

</x-base-layout>
