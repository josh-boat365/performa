<x-base-layout>

    <style>
        /* Hide number input spinners/arrows */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Save button styling */
        .btn-save {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-save:hover:not(.btn-saved):not(.btn-saving) {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Saved state - gray button but still clickable */
        .btn-save.btn-saved {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
            cursor: pointer !important;
            opacity: 0.95;
        }

        .btn-save.btn-saved:hover {
            background-color: #5a6268 !important;
            border-color: #545b62 !important;
            color: white !important;
        }

        .btn-save.btn-saved:focus,
        .btn-save.btn-saved:active {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }

        .btn-save:disabled {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
            cursor: not-allowed !important;
            opacity: 0.65;
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
                        <a href="{{ route('show.batch.kpi', $batchId) }}" class="text-primary">
                            <i class="bx bx-arrow-back me-1"></i>My KPIs
                        </a> / Your Appraisal
                    </h4>
                </div>
            </div>
        </div>

        <!-- Progress Bar - Sticky -->
        @if (!in_array($kpiStatus ?? '', ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
        <div class="progress-container">
            <div class="container-fluid">
                <div class="progress-wrapper">
                    <div class="progress-info">
                        <span class="badge bg-primary" id="current-page">1</span>
                        <span class="text-muted">of</span>
                        <span class="badge bg-dark" id="total-pages">1</span>
                    </div>
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="progress-text">0%</span>
                        </div>
                    </div>
                    <span class="text-muted small">Completion</span>
                </div>
            </div>
        </div>
        @endif

        <!-- end page title -->
        @php
if (!function_exists('getBadgeDetails')) {
    function getBadgeDetails($status)
    {
        return match ($status) {
            'PENDING' => ['class' => 'bg-dark', 'text' => 'PENDING'],
            'REVIEW' => ['class' => 'bg-warning', 'text' => 'REVIEW'],
            'CONFIRMATION' => ['class' => 'bg-primary', 'text' => 'CONFIRMATION'],
            'COMPLETED' => ['class' => 'bg-success', 'text' => 'COMPLETED'],
            'PROBLEM' => ['class' => 'bg-danger', 'text' => 'PROBE'],
            default => ['class' => 'bg-secondary', 'text' => 'PENDING'],
        };
    }
}
$badgeDetails = getBadgeDetails($gradeDetails['status'] ?? null);
        @endphp


        <!-- end page title -->

        <!-- Appraisal Grades Summary -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-bar-chart-alt-2 me-2"></i>Appraisal Grades Summary
                        </h5>
                        <span class="badge rounded-pill {{ $badgeDetails['class'] }} fs-6">
                            {{ $badgeDetails['text'] }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="grade-summary-grid">
                            <!-- Final Employee Grade -->
                            <x-appraisal.grade-card
                                title="Final Employee Grade"
                                badgeClass="bg-success"
                                :items="[
                                    'Grade' => $gradeDetails['grade'] ?? '___',
                                    'Score' => $gradeDetails['kpiScore'] ?? '___',
                                    'Remark' => $gradeDetails['remark'] ?? '___'
                                ]">
                                <a href="#" class="btn btn-sm btn-outline-success mt-2" data-bs-toggle="modal" data-bs-target=".bs-recommendation-modal-lg">
                                    <i class="bx bx-message-detail me-1"></i>View Recommendation
                                </a>
                            </x-appraisal.grade-card>

                            <!-- Employee Submitted Grade -->
                            <x-appraisal.grade-card
                                title="Your Submitted Grade"
                                badgeClass="bg-secondary"
                                :employeeName="$gradeDetails['employeeName'] ?? ($submittedEmployeeGrade->employeeName ?? '----')"
                                :badges="[
                                    $submittedEmployeeGrade->totalKpiScore ?? '----',
                                    $submittedEmployeeGrade->grade ?? '----',
                                    $submittedEmployeeGrade->remark ?? '----'
                                ]" />

                            <!-- Supervisor Grade -->
                            <x-appraisal.grade-card
                                title="Supervisor's Grade"
                                badgeClass="bg-primary"
                                :employeeName="$gradeDetails['supervisorName'] ?? ($supervisorGradeForEmployee->employeeName ?? '----')"
                                :badges="[
                                    $supervisorGradeForEmployee->totalKpiScore ?? '----',
                                    $supervisorGradeForEmployee->grade ?? '----',
                                    $supervisorGradeForEmployee->remark ?? '----'
                                ]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Scroll Retention ---
                const scrollKey = 'employeeKpiFormScroll_' + '{{ $employeeId }}';
                // Restore scroll position
                if (sessionStorage.getItem(scrollKey)) {
                    window.scrollTo({
                        top: parseInt(sessionStorage.getItem(scrollKey)),
                        behavior: 'auto'
                    });
                }
                // Save scroll position on scroll
                window.addEventListener('scroll', function () {
                    sessionStorage.setItem(scrollKey, window.scrollY);
                });

                // --- Enforce Numeric Validation and Max Value ---
                document.querySelectorAll('input[type="number"][name*="EmpScore"]').forEach(input => {
                    input.setAttribute('required', 'required');
                    input.setAttribute('min', '0');
                    if (!input.hasAttribute('max')) {
                        input.setAttribute('max', '100'); // Set a reasonable max if not present
                    }
                    input.addEventListener('input', function () {
                        let val = this.value;
                        if (val !== '' && (isNaN(val) || val < 0 || val > parseInt(this.getAttribute('max')))) {
                            this.classList.add('is-invalid');
                            this.classList.remove('is-valid');
                        } else if (val !== '') {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                        } else {
                            this.classList.remove('is-valid');
                            this.classList.add('is-invalid');
                        }
                    });
                });

                // --- AJAX Save with Save State Management ---
                document.querySelectorAll('form.ajax-eval-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const formData = new FormData(form);
                        const saveBtn = form.querySelector('button.btn-save');
                        if (!saveBtn) return;

                        saveBtn.classList.add('btn-saving');
                        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';
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
                                saveBtn.classList.remove('btn-saving');
                                saveBtn.disabled = false;
                                if (data.success) {
                                    // Mark button as saved - change to gray
                                    saveBtn.classList.add('btn-saved');
                                    saveBtn.classList.remove('btn-success');
                                    saveBtn.classList.add('btn-secondary');
                                    saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Saved';

                                    // Store save state in data attribute
                                    saveBtn.setAttribute('data-saved', 'true');

                                    // Update Next and Submit button states
                                    updatePaginationButtons();

                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            icon: 'success',
                                            title: data.message || 'Saved!',
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }
                                } else {
                                    saveBtn.innerHTML = '<i class="bx bx-save me-1"></i>Save';
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            icon: 'error',
                                            title: data.message || 'Save failed',
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                saveBtn.classList.remove('btn-saving');
                                saveBtn.disabled = false;
                                saveBtn.innerHTML = '<i class="bx bx-save me-1"></i>Save';
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        toast: true,
                                        icon: 'error',
                                        title: 'An error occurred!',
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    });
                                }
                            });
                    });
                });
            });
        </script>

        <!-- Recommendation Modal -->
        <div class="modal fade bs-recommendation-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="myLargeModalLabel">
                            <i class="bx bx-message-detail me-2"></i>Supervisor's Recommendation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                        <div class="p-3 bg-light text-dark border rounded" style="word-break: break-word; white-space: pre-line;">
                            @if (isset($gradeDetails['recommendation']) && !empty($gradeDetails['recommendation']) && $gradeDetails['recommendation'] !== 'No Recommendation')
                                <p class="mb-0" style="word-break: break-word; white-space: pre-line;">{{ $gradeDetails['recommendation'] }}</p>
                            @else
                                <p class="text-center text-muted mb-0">
                                    <i class="bx bx-info-circle me-1"></i>No recommendation available yet
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluation Form Section -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bx bx-edit me-2"></i>Employee Evaluation Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $index => $kpi)
                                        <div class="kpi">

                                            @foreach ($kpi->activeSections as $sectionIndex => $section)
                                                @php
                                                    // Show all sections when COMPLETED, otherwise hide for pagination
                                                    $isCompleted = isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'COMPLETED';
                                                    $displayStyle = $isCompleted ? 'display: block;' : 'display: none;';
                                                @endphp
                                                <div class="card border border-primary section-tab mb-3" style="border-radius: 10px; {{ $displayStyle }}"
                                                    data-section-page="{{ floor($sectionIndex / 3) }}">
                                                    <div class="card-body"
                                                        style="{{ $section->metrics->isEmpty() ? 'background-color: #0000ff0d;' : '' }}">
                                                        <div class="section-card" style="margin-top: 1rem;">
                                                            <h4 class="card-title">{{ $section->sectionName }} (<span
                                                                    style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                            </h4>
                                                            <p>{{ $section->sectionDescription }}</p>

                                                            @if ($section->metrics->isEmpty())
                                                                <form action="{{ route('self.rating') }}"
                                                                    method="POST" class="ajax-eval-form section-form">
                                                                    @csrf
                                                                    <div class="d-flex gap-3 p-4">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3 score-input"
                                                                                type="number" name="sectionEmpScore"
                                                                                required placeholder="Enter Score"
                                                                                min="0" step="0.01"
                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                max="{{ $section->sectionScore }}"
                                                                                title="The Score cannot be more than the section score {{ $section->sectionScore }}"
                                                                                @disabled(
                    isset($section->sectionEmpScore) &&
                    in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])
                )
                                                                                value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <textarea class="form-control mb-3 comment-input" type="text" name="employeeComment"
                                                                                placeholder="Enter your comments" @disabled(
                    isset($section->sectionEmpScore) &&
                    in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])
                ) rows="3">{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</textarea>
                                                                        </div>
                                                                        @if (
                    !isset($section->sectionEmpScore) ||
                    !in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])
                )
                                                                            <input type="hidden" name="kpiType"
                                                                                value="{{ $kpi->kpi->kpiType }}">
                                                                            <input type="hidden"
                                                                                name="sectionEmpScoreId"
                                                                                value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                            <input type="hidden" name="sectionId"
                                                                                value="{{ $section->sectionId ?? '' }}">
                                                                            <input type="hidden" name="kpiId"
                                                                                value="{{ $kpi->kpi->kpiId ?? '' }}">
                                                                            <button type="submit"
                                                                                class="btn btn-success btn-save {{ optional($section->sectionEmpScore)->sectionEmpScore ? 'btn-saved' : '' }}"
                                                                                style="height: fit-content; min-width: 85px;"
                                                                                data-saved="{{ optional($section->sectionEmpScore)->sectionEmpScore ? 'true' : 'false' }}">
                                                                                @if(optional($section->sectionEmpScore)->sectionEmpScore)
                                                                                    <i class="bx bx-check me-1"></i>Saved
                                                                                @else
                                                                                    Save
                                                                                @endif
                                                                            </button>
                                                                        @else
                                                                            {{-- Show saved score as read-only badge when completed --}}
                                                                            @if(optional($section->sectionEmpScore)->sectionEmpScore)
                                                                                <span class="badge bg-secondary" style="height: fit-content;">Score: {{ $section->sectionEmpScore->sectionEmpScore }}</span>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </form>


                                                                {{-- Supervisor/Probing Score Display --}}
                                                                @if (isset($section->sectionEmpScore))
                                                                    @if (in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'COMPLETED']))
                                                                        <x-appraisal.score-display
                                                                            label="Supervisor Score and Comment"
                                                                            badgeClass="bg-success"
                                                                            :score="optional($section->sectionEmpScore)->sectionSupScore ?? ''"
                                                                            :comment="$section->sectionEmpScore->supervisorComment ?? ''" />

                                                                        @if($section->sectionEmpScore->prob == true)
                                                                            <x-appraisal.score-display
                                                                                label="Probing Score and Comment"
                                                                                badgeClass="bg-dark"
                                                                                :score="optional($section->sectionEmpScore)->sectionProbScore ?? ''"
                                                                                :comment="$section->sectionEmpScore->probComment ?? ''" />
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            @else
                                                                @foreach ($section->metrics as $metric)
                                                                    <div class="card border border-success"
                                                                        @style(['border-radius: 10px;'])>
                                                                        <div class="card-body" @style(['background-color: #1eff000d'])>
                                                                            <div class="metric-card">
                                                                                <h5>{{ $metric->metricName }} (<span
                                                                                        style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                                </h5>
                                                                                <p>{{ $metric->metricDescription }}</p>

                                                                                <form
                                                                                    action="{{ route('self.rating') }}"
                                                                                    method="POST"
                                                                                    class="ajax-eval-form metric-form">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3 p-4">
                                                                                        <input type="hidden"
                                                                                            name="metricEmpScoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">
                                                                                        <input type="hidden"
                                                                                            name="metricId"
                                                                                            value="{{ $metric->metricId }}">
                                                                                        <input type="hidden"
                                                                                            name="kpiType"
                                                                                            value="{{ $kpi->kpi->kpiType }}">
                                                                                        <input type="hidden"
                                                                                            name="kpiId"
                                                                                            value="{{ $kpi->kpi->kpiId }}">
                                                                                        <input type="hidden"
                                                                                            name="sectionId"
                                                                                            value="{{ $section->sectionId }}">
                                                                                        <div class="col-md-2">
                                                                                            <input
                                                                                                class="form-control mb-3"
                                                                                                type="number"
                                                                                                name="metricEmpScore"
                                                                                                required
                                                                                                placeholder="Enter Score"
                                                                                                min="0"
                                                                                                step="0.01"
                                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                                max="{{ $metric->metricScore }}"
                                                                                                title="The Score cannot be more than the section score {{ $metric->metricScore }}"
                                                                                                @disabled(
                        (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION'])) ||
                        (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM']))
                    )
                                                                                                value="{{ optional($metric->metricEmpScore)->metricEmpScore ?? '' }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control mb-3" type="text" name="employeeComment"  placeholder="Enter your comments"
                                                                                                rows="3" @disabled(
                        (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION'])) ||
                        (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM']))
                    )>{{ optional($metric->metricEmpScore)->employeeComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                        @if (
                        !isset($metric->metricEmpScore) ||
                        (!in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION']) &&
                            !in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM']))
                    )
                                                                                            <button type="submit"
                                                                                                class="btn btn-success btn-save {{ optional($metric->metricEmpScore)->metricEmpScore ? 'btn-saved' : '' }}"
                                                                                                style="height: fit-content; min-width: 85px;"
                                                                                                data-saved="{{ optional($metric->metricEmpScore)->metricEmpScore ? 'true' : 'false' }}">
                                                                                                @if(optional($metric->metricEmpScore)->metricEmpScore)
                                                                                                    <i class="bx bx-check me-1"></i>Saved
                                                                                                @else
                                                                                                    Save
                                                                                                @endif
                                                                                            </button>
                                                                                        @else
                                                                                            {{-- Show saved score as read-only badge when completed --}}
                                                                                            @if(optional($metric->metricEmpScore)->metricEmpScore)
                                                                                                <span class="badge bg-secondary" style="height: fit-content;">Score: {{ $metric->metricEmpScore->metricEmpScore }}</span>
                                                                                            @endif
                                                                                        @endif
                                                                                    </div>
                                                                                </form>

                                                                                {{-- Supervisor/Probing Score Display for Metrics --}}
                                                                                @if (isset($metric->metricEmpScore))
                                                                                    @if (in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'COMPLETED']))
                                                                                        <x-appraisal.score-display
                                                                                            label="Supervisor Score and Comment"
                                                                                            badgeClass="bg-success"
                                                                                            :score="optional($metric->metricEmpScore)->metricSupScore ?? ''"
                                                                                            :comment="$metric->metricEmpScore->supervisorComment ?? ''" />

                                                                                        @if($metric->metricEmpScore->prob == true)
                                                                                            <x-appraisal.score-display
                                                                                                label="Probing Score and Comment"
                                                                                                badgeClass="bg-dark"
                                                                                                :score="optional($metric->metricEmpScore)->metricProbScore ?? ''"
                                                                                                :comment="$metric->metricEmpScore->probComment ?? ''" />
                                                                                        @endif
                                                                                    @endif
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
                                    <p>No KPIs available for this employee.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer with Pagination Controls --}}
                    @if (
    isset($section->sectionEmpScore) &&
    in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])
)
                        {{-- No pagination controls when in review/confirmation/completed/problem states --}}
                    @else
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <button id="prev-btn" class="btn btn-outline-dark" disabled>
                                        <i class="bx bx-chevron-left me-1"></i>Previous
                                    </button>
                                    <button id="next-btn" class="btn btn-primary">
                                        Next<i class="bx bx-chevron-right ms-1"></i>
                                    </button>
                                </div>

                                <button id="submit-btn" type="button" data-bs-toggle="modal"
                                    class="btn btn-success btn-lg" data-bs-target=".bs-submit-modal-lg"
                                    disabled>
                                    <i class="bx bx-send me-1"></i>Submit Appraisal
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- WHEN SUPERVISOR HAS SUBMITTED THEIR REVIEW (not COMPLETED) --}}
                    @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                                <button type="button" data-bs-toggle="modal" class="btn btn-success btn-lg"
                                    data-bs-target=".bs-accept-modal-lg">
                                    <i class="bx bx-check-circle me-1"></i>Accept Scores
                                </button>

                                <button type="button" data-bs-toggle="modal" class="btn btn-outline-dark"
                                    data-bs-target=".bs-push-review-modal-lg">
                                    <i class="bx bx-revision me-1"></i>Push for Review
                                </button>

                                <a href="{{ route('show.employee.probe', [$kpi->kpi->kpiId, $kpi->kpi->batchId]) }}"
                                    class="btn btn-warning">
                                    <i class="bx bx-search-alt me-1"></i>Probe
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- WHEN APPRAISAL IS COMPLETED - Show completion message --}}
                    @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'COMPLETED')
                        <div class="card-footer bg-success bg-opacity-10">
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <i class="bx bx-check-circle text-success fs-4"></i>
                                <span class="text-success fw-semibold">Appraisal Completed</span>
                            </div>
                        </div>
                    @endif

                    <!-- Modals - Only show when not COMPLETED -->
                    @if(isset($kpi) && (!isset($section->sectionEmpScore) || $section->sectionEmpScore->status !== 'COMPLETED'))
                        <x-appraisal.confirmation-modal
                            id="bs-submit-modal-lg"
                            title="Confirm Submission"
                            icon="bx-send"
                            iconColor="text-warning"
                            headerClass="bg-success text-white"
                            message="Submit your appraisal?"
                            description="This will send your self-evaluation to your supervisor for review."
                            :action="route('submit.appraisal')"
                            buttonText="Yes, Submit for Review"
                            buttonClass="btn-success"
                            buttonIcon="bx-check"
                            :hiddenFields="[
                                'employeeId' => $employeeId,
                                'kpiId' => $kpi->kpi->kpiId,
                                'batchId' => $kpi->kpi->batchId,
                                'status' => 'REVIEW'
                            ]" />

                        <x-appraisal.confirmation-modal
                            id="bs-accept-modal-lg"
                            title="Accept Supervisor's Scores"
                            icon="bx-check-circle"
                            iconColor="text-success"
                            headerClass="bg-success text-white"
                            message="Accept supervisor's scores?"
                            description="By accepting, you confirm that you agree with the scores given by your supervisor."
                            :action="route('submit.appraisal')"
                            buttonText="Yes, Accept"
                            buttonClass="btn-success"
                            buttonIcon="bx-check"
                            :hiddenFields="[
                                'employeeId' => $employeeId,
                                'kpiId' => $kpi->kpi->kpiId,
                                'batchId' => $kpi->kpi->batchId,
                                'status' => 'COMPLETED'
                            ]" />

                        <x-appraisal.confirmation-modal
                            id="bs-push-review-modal-lg"
                            title="Push Back for Review"
                            icon="bx-revision"
                            iconColor="text-warning"
                            headerClass="bg-warning"
                            message="Push back to supervisor?"
                            description="This will send the appraisal back to your supervisor for another review."
                            :action="route('submit.appraisal')"
                            buttonText="Yes, Push for Review"
                            buttonClass="btn-warning"
                            buttonIcon="bx-revision"
                            :hiddenFields="[
                                'employeeId' => $employeeId,
                                'kpiId' => $kpi->kpi->kpiId,
                                'batchId' => $kpi->kpi->batchId,
                                'status' => 'REVIEW'
                            ]" />
                    @endif

                    @push('scripts')
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const sections = document.querySelectorAll('.section-tab');
                                        const prevBtn = document.getElementById('prev-btn');
                                        const nextBtn = document.getElementById('next-btn');
                                        const submitBtn = document.getElementById('submit-btn');
                                        const currentPageSpan = document.getElementById('current-page');
                                        const totalPagesSpan = document.getElementById('total-pages');
                                        const progressBar = document.getElementById('progress-bar');
                                        const progressText = document.getElementById('progress-text');

                                        // Check if appraisal is completed - if so, show all sections
                                        const kpiStatus = '{{ $kpiStatus ?? '' }}';
                                        const isCompleted = kpiStatus === 'COMPLETED';

                                        if (isCompleted) {
                                            // Show all sections when completed
                                            sections.forEach(section => {
                                                section.style.display = 'block';
                                            });
                                            return; // Exit early - no need for pagination logic
                                        }

                                        // Use unique key per employee to avoid page persistence across different forms
                                        const currentEmployeeId = '{{ $employeeId }}';
                                        const pageStorageKey = `currentPage_employee_${currentEmployeeId}`;
                                        const lastEmployeeKey = 'lastViewedEmployeeId';
                                        const savedFormsKey = `savedForms_${currentEmployeeId}`;

                                        // Check if we're viewing a different employee - if so, reset to page 0
                                        const lastViewedEmployee = sessionStorage.getItem(lastEmployeeKey);
                                        let currentPage = 0;

                                        if (lastViewedEmployee === currentEmployeeId) {
                                            currentPage = parseInt(sessionStorage.getItem(pageStorageKey) || 0);
                                        } else {
                                            sessionStorage.setItem(lastEmployeeKey, currentEmployeeId);
                                            sessionStorage.setItem(pageStorageKey, '0');
                                            sessionStorage.setItem(savedFormsKey, JSON.stringify({}));
                                        }

                                        const sectionsPerPage = 3;
                                        const totalPages = Math.ceil(sections.length / sectionsPerPage);

                                        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;

                                        // Get saved forms from sessionStorage or initialize
                                        let savedForms = {};
                                        try {
                                            const stored = sessionStorage.getItem(savedFormsKey);
                                            savedForms = stored ? JSON.parse(stored) : {};
                                        } catch (e) {
                                            savedForms = {};
                                        }

                                        // Initialize save button states from sessionStorage
                                        document.querySelectorAll('button.btn-save').forEach((btn, index) => {
                                            // Get form identifier (use form's closest section index as ID)
                                            const form = btn.closest('form.ajax-eval-form');
                                            const section = btn.closest('.section-tab');
                                            let formId = null;

                                            // Try to get a unique identifier from the form
                                            if (form && form.dataset.formId) {
                                                formId = form.dataset.formId;
                                            } else if (section && section.dataset.sectionId) {
                                                formId = 'section_' + section.dataset.sectionId;
                                            } else {
                                                // Use index as fallback
                                                formId = 'form_' + index;
                                            }

                                            btn.dataset.formId = formId;

                                            // Check if this form was previously saved
                                            const isSaved = savedForms[formId] === true;

                                            if (isSaved || btn.classList.contains('btn-saved')) {
                                                btn.setAttribute('data-saved', 'true');
                                                btn.classList.add('btn-saved');
                                                btn.classList.remove('btn-success');
                                                btn.textContent = '✓ Saved';
                                            } else {
                                                btn.setAttribute('data-saved', 'false');
                                                btn.classList.remove('btn-saved');
                                                btn.classList.add('btn-success');
                                                btn.textContent = 'Save';
                                                    allFilled = false;
                                                    sections[i].classList.add('border-danger');
                                                } else {
                                                    sections[i].classList.remove('border-danger');
                                                }
                                            }

                                            return allFilled;
                                        }

                                        function checkAllSavedOnPage(page) {
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            let allSaved = true;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const section = sections[i];
                                                const saveButtons = section.querySelectorAll('button.btn-save');

                                                saveButtons.forEach(btn => {
                                                    const formId = btn.dataset.formId || 'form_unknown';
                                                    // Check both attribute and sessionStorage to ensure persistence
                                                    const isSaved = btn.getAttribute('data-saved') === 'true' && savedForms[formId] === true;

                                                    if (!isSaved) {
                                                        allSaved = false;
                                                    }
                                                });
                                            }

                                            return allSaved;
                                        }

                                        function checkAllSavedAcrossAllPages() {
                                            const allSaveButtons = document.querySelectorAll('button.btn-save');
                                            let allSaved = true;

                                            allSaveButtons.forEach(btn => {
                                                const formId = btn.dataset.formId || 'form_unknown';
                                                // Check both attribute and sessionStorage to ensure persistence
                                                const isSaved = btn.getAttribute('data-saved') === 'true' && savedForms[formId] === true;

                                                if (!isSaved) {
                                                    allSaved = false;
                                                }
                                            });

                                            return allSaved;
                                        }

                                        function updatePaginationButtons() {
                                            updateButtons();
                                        }

                                        function updateProgressBar() {
                                            let totalValid = 0;
                                            sections.forEach(section => {
                                                const scoreInputs = section.querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                if (scoresFilled) totalValid++;
                                            });
                                            const percent = Math.round((totalValid / sections.length) * 100);

                                            if (progressBar) {
                                                progressBar.style.width = percent + '%';
                                                progressBar.setAttribute('aria-valuenow', percent);

                                                // Update color based on progress
                                                progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
                                                if (percent < 33) {
                                                    progressBar.classList.add('bg-danger');
                                                } else if (percent < 66) {
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
                                            const allCurrentPageSaved = checkAllSavedOnPage(currentPage);
                                            if (nextBtn) nextBtn.disabled = currentPage === totalPages - 1 || !allCurrentPageSaved;

                                            // Check if all save buttons across all pages are saved
                                            const allPagesSaved = checkAllSavedAcrossAllPages();
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

                                            // Smooth scroll to top of form
                                            const formContainer = document.getElementById('kpi-form');
                                            if (formContainer) {
                                                formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                            }
                                        }

                                        if (prevBtn) {
                                            prevBtn.addEventListener('click', function() {
                                                if (currentPage > 0) {
                                                    currentPage--;
                                                    showPage(currentPage);
                                                }
                                            });
                                        }

                                        if (nextBtn) {
                                            nextBtn.addEventListener('click', function() {
                                                // Check both: all inputs filled AND all forms saved on current page
                                                const inputsFilled = checkInputs(currentPage);
                                                const allSaved = checkAllSavedOnPage(currentPage);

                                                if (currentPage < totalPages - 1 && inputsFilled && allSaved) {
                                                    currentPage++;
                                                    showPage(currentPage);
                                                } else if (!inputsFilled) {
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            toast: true,
                                                            icon: 'warning',
                                                            title: 'Please fill all score fields on this page',
                                                            position: 'top-end',
                                                            showConfirmButton: false,
                                                            timer: 2000,
                                                            timerProgressBar: true
                                                        });
                                                    }
                                                } else if (!allSaved) {
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            toast: true,
                                                            icon: 'warning',
                                                            title: 'Please save all forms on this page before proceeding',
                                                            position: 'top-end',
                                                            showConfirmButton: false,
                                                            timer: 2000,
                                                            timerProgressBar: true
                                                        });
                                                    }
                                                }
                                            });
                                        }

                                        // Input validation listeners
                                        document.querySelectorAll('input[type="number"][name*="EmpScore"], textarea[name="employeeComment"]')
                                            .forEach(input => {
                                                input.addEventListener('input', function() {
                                                    validateField(this);
                                                    updateButtons();

                                                    // Reset save button when input changes
                                                    const form = this.closest('form.ajax-eval-form');
                                                    if (form) {
                                                        const saveBtn = form.querySelector('button.btn-save');
                                                        if (saveBtn && saveBtn.classList.contains('btn-saved')) {
                                                            const formId = saveBtn.dataset.formId || 'form_unknown';

                                                            // Remove from saved forms in sessionStorage
                                                            delete savedForms[formId];
                                                            sessionStorage.setItem(savedFormsKey, JSON.stringify(savedForms));

                                                            // Reset to unsaved state
                                                            saveBtn.classList.remove('btn-saved');
                                                            saveBtn.classList.add('btn-success');
                                                            saveBtn.innerHTML = 'Save';
                                                            saveBtn.setAttribute('data-saved', 'false');
                                                        }
                                                    }
                                                });
                                            });

                                        // AJAX form handler - NO page reload, instant feedback
                                        document.querySelectorAll('form.ajax-eval-form').forEach(form => {
                                            form.addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                const saveBtn = form.querySelector('button.btn-save');

                                                if (!saveBtn) return;

                                                const formData = new FormData(form);

                                                // Show saving state
                                                saveBtn.classList.add('btn-saving');
                                                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';
                                                saveBtn.disabled = true;

                                                fetch(form.action, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                        },
                                                        body: formData
                                                    })
                                                    .then(response => {
                                                        // Try to parse the response as JSON
                                                        return response.json().then(data => {
                                                            // Check for session expiration
                                                            if (response.status === 401 && data.session_expired) {
                                                                Swal.fire({
                                                                    icon: 'warning',
                                                                    title: 'Session Expired',
                                                                    text: 'Please log in again.',
                                                                    confirmButtonText: 'OK'
                                                                }).then(() => {
                                                                    window.location.href = data.redirect || '{{ route("login") }}';
                                                                });
                                                                return null;
                                                            }
                                                            // Return the data regardless of status code
                                                            return data;
                                                        }).catch(err => {
                                                            // If response is not JSON, return a structured error
                                                            console.error('Response parsing error:', err);
                                                            return {
                                                                success: false,
                                                                message: 'Server returned an invalid response'
                                                            };
                                                        });
                                                    })
                                                    .then(data => {
                                                        if (!data) return;

                                                        saveBtn.classList.remove('btn-saving');
                                                        saveBtn.disabled = false;

                                                        if (data.success) {
                                                            // Get form identifier for tracking
                                                            const formId = saveBtn.dataset.formId || 'form_unknown';

                                                            // Mark as saved in sessionStorage
                                                            savedForms[formId] = true;
                                                            sessionStorage.setItem(savedFormsKey, JSON.stringify(savedForms));

                                                            // Show saved state
                                                            saveBtn.classList.remove('btn-saving');
                                                            saveBtn.classList.add('btn-saved');
                                                            saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Saved';
                                                            saveBtn.setAttribute('data-saved', 'true');
                                                            saveBtn.disabled = false; // Keep clickable for re-editing

                                                            // Update hidden ID field if returned
                                                            if (data.id) {
                                                                const idField = form.querySelector('input[name="sectionEmpScoreId"], input[name="metricEmpScoreId"]');
                                                                if (idField) idField.value = data.id;
                                                            }

                                                            // Update progress bar
                                                            updateProgressBar();
                                                            updateButtons();

                                                            // Toast notification
                                                            if (typeof Swal !== 'undefined') {
                                                                Swal.fire({
                                                                    toast: true,
                                                                    icon: 'success',
                                                                    title: data.message || 'Saved successfully!',
                                                                    position: 'top-end',
                                                                    showConfirmButton: false,
                                                                    timer: 2000,
                                                                    timerProgressBar: true
                                                                });
                                                            }
                                                        } else {
                                                            // Show error state
                                                            saveBtn.innerHTML = 'Save';

                                                            if (typeof Swal !== 'undefined') {
                                                                Swal.fire({
                                                                    toast: true,
                                                                    icon: 'error',
                                                                    title: data.message || 'Failed to save',
                                                                    position: 'top-end',
                                                                    showConfirmButton: false,
                                                                    timer: 3000,
                                                                    timerProgressBar: true
                                                                });
                                                            }
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        saveBtn.classList.remove('btn-saving');
                                                        saveBtn.disabled = false;
                                                        saveBtn.innerHTML = 'Save';

                                                        if (typeof Swal !== 'undefined') {
                                                            Swal.fire({
                                                                toast: true,
                                                                icon: 'error',
                                                                title: 'An error occurred. Please try again.',
                                                                position: 'top-end',
                                                                showConfirmButton: false,
                                                                timer: 3000,
                                                                timerProgressBar: true
                                                            });
                                                        }
                                                    });
                                            });
                                        });

                                        // Show the initial page
                                        showPage(currentPage);

                                        // Check for any stored toast messages (from page navigation)
                                        setTimeout(() => {
                                            const successToast = sessionStorage.getItem('showSuccessToast');
                                            if (successToast) {
                                                const toastData = JSON.parse(successToast);
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        toast: true,
                                                        icon: 'success',
                                                        title: toastData.message,
                                                        position: 'top-end',
                                                        showConfirmButton: false,
                                                        timer: 3000,
                                                        timerProgressBar: true
                                                    });
                                                }
                                                sessionStorage.removeItem('showSuccessToast');
                                            }

                                            const errorToast = sessionStorage.getItem('showErrorToast');
                                            if (errorToast) {
                                                const toastData = JSON.parse(errorToast);
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        toast: true,
                                                        icon: 'error',
                                                        title: toastData.message,
                                                        position: 'top-end',
                                                        showConfirmButton: false,
                                                        timer: 3000,
                                                        timerProgressBar: true
                                                    });
                                                }
                                                sessionStorage.removeItem('showErrorToast');
                                            }
                                        }, 100);
                                    });
                                </script>
                            @endpush

                </div>
            </div>
        </div>
    </div>

</x-base-layout>
