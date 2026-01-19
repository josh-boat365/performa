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
    </style>

    <div class="container-fluid px-2">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        <a href="#" class="text-primary">
                            <i class="bx bx-arrow-back me-1"></i>Probe
                        </a> / Select Sections or Metrics to Probe
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
                        <div id="progress-bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="progress-text">0%</span>
                        </div>
                    </div>
                    <span class="text-muted small">Probing Selection</span>
                </div>
            </div>
        </div>

        <!-- Employee Probing Form Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-target-lock me-2"></i>Employee Probing Form
                        </h5>
                        <span class="badge rounded-pill bg-warning fs-6">PROBE</span>
                    </div>
                    <div class="card-body">
                        <div id="kpi-form">
                            @if (isset($appraisal) && $appraisal->isNotEmpty())
                                @foreach ($appraisal as $kpi)
                                    <div class="kpi">
                                        @foreach ($kpi->activeSections as $sectionIndex => $section)
                                            <div class="card border section-tab mb-3" style="border-radius: 10px;"
                                                data-section-page="{{ floor($sectionIndex / 3) }}">
                                                <div class="card-body {{ $section->metrics->isEmpty() ? 'bg-light' : '' }}">
                                                    <div class="section-card" style="margin-top: 1rem;">
                                                        <h5 class="card-title mb-2">
                                                            {{ $section->sectionName }}
                                                            <span class="badge bg-danger ms-2">{{ $section->sectionScore }}</span>
                                                        </h5>
                                                        <p class="text-muted small">{{ $section->sectionDescription }}</p>

                                                        @if ($section->metrics->isEmpty())
                                                            <form action="{{ route('submit.employee.probe') }}"
                                                                method="POST"
                                                                class="section-form ajax-emp-prob-eval-form">
                                                                @csrf
                                                                <!-- Employee Score Display -->
                                                                <div class="mb-3">
                                                                    <span class="badge rounded-pill bg-secondary mb-2">
                                                                        <strong>Employee Score and Comment</strong>
                                                                    </span>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control score-input"
                                                                                type="number" name="sectionEmpScore"
                                                                                required placeholder="Score"
                                                                                min="0" step="0.01"
                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                max="{{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                                                title="Max score: {{ $section->sectionScore }}"
                                                                                value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <textarea class="form-control comment-input" name="employeeComment" required
                                                                                placeholder="Enter your comments" rows="2"
                                                                                @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'COMPLETED')>{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Supervisor Score Display (readonly) -->
                                                                <x-appraisal.score-display
                                                                    label="Supervisor Score and Comment"
                                                                    badgeClass="bg-primary"
                                                                    :score="optional($section->sectionEmpScore)->sectionSupScore ?? ''"
                                                                    :comment="$section->sectionEmpScore->supervisorComment ?? ''" />

                                                                <!-- Probe Checkbox -->
                                                                <div class="d-flex gap-3 align-items-center mt-3">
                                                                    <div class="form-check form-check-dark">
                                                                        <input style="width:1.8rem; height:2rem"
                                                                            class="form-check-input" type="checkbox"
                                                                            name="prob" id="checkProb_section_{{ $section->sectionId }}"
                                                                            value="true"
                                                                            @checked(isset($section->sectionEmpScore) && $section->sectionEmpScore->prob === true)>
                                                                        <label class="form-check-label ms-2" for="checkProb_section_{{ $section->sectionId }}">
                                                                            Mark for Probe
                                                                        </label>
                                                                    </div>
                                                                    <input type="hidden" name="scoreId"
                                                                        value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                    <input type="hidden" name="sectionId"
                                                                        value="{{ $section->sectionId }}">
                                                                    <input type="hidden" name="kpiId"
                                                                        value="{{ $kpi->kpi->kpiId }}">
                                                                    <input type="hidden" name="kpiType"
                                                                        value="{{ $kpi->kpi->kpiType }}">
                                                                    <button type="submit" class="btn btn-primary btn-save ms-auto">
                                                                        <i class="bx bx-save me-1"></i>Save
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        @endif

                                                        @if (isset($section->metrics) && count($section->metrics) > 0)
                                                            @foreach ($section->metrics as $metric)
                                                                <div class="card border border-success mb-3" style="border-radius: 10px;">
                                                                    <div class="card-body" style="background-color: rgba(30, 255, 0, 0.05);">
                                                                        <div class="metric-card">
                                                                            <h6 class="card-title">
                                                                                {{ $metric->metricName }}
                                                                                <span class="badge bg-danger ms-2">{{ $metric->metricScore }}</span>
                                                                            </h6>
                                                                            <p class="text-muted small">{{ $metric->metricDescription }}</p>

                                                                            <form action="{{ route('submit.employee.probe') }}"
                                                                                method="POST"
                                                                                class="metric-form ajax-emp-prob-eval-form">
                                                                                @csrf
                                                                                <!-- Employee Score -->
                                                                                <div class="mb-3">
                                                                                    <span class="badge rounded-pill bg-secondary mb-2">
                                                                                        <strong>Employee Score and Comment</strong>
                                                                                    </span>
                                                                                    <div class="d-flex gap-3">
                                                                                        <div class="col-md-2">
                                                                                            <input class="form-control"
                                                                                                type="number"
                                                                                                placeholder="Score"
                                                                                                readonly
                                                                                                value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                        </div>
                                                                                        <div class="col-md-10">
                                                                                            <textarea class="form-control" name="employeeComment" required
                                                                                                placeholder="Enter your comments" rows="2"
                                                                                                @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'COMPLETED')>{{ $metric->metricEmpScore->employeeComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Supervisor Score Display (readonly) -->
                                                                                <x-appraisal.score-display
                                                                                    label="Supervisor Score and Comment"
                                                                                    badgeClass="bg-primary"
                                                                                    :score="optional($metric->metricEmpScore)->metricSupScore ?? ''"
                                                                                    :comment="$metric->metricEmpScore->supervisorComment ?? ''" />

                                                                                <!-- Probe Checkbox -->
                                                                                <div class="d-flex gap-3 align-items-center mt-3">
                                                                                    <div class="form-check form-check-dark">
                                                                                        <input style="width:1.8rem; height:2rem"
                                                                                            class="form-check-input"
                                                                                            type="checkbox"
                                                                                            name="prob"
                                                                                            id="checkProb_metric_{{ $metric->metricId }}"
                                                                                            value="true"
                                                                                            @checked(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === true)>
                                                                                        <label class="form-check-label ms-2" for="checkProb_metric_{{ $metric->metricId }}">
                                                                                            Mark for Probe
                                                                                        </label>
                                                                                    </div>
                                                                                    <input type="hidden" name="scoreId"
                                                                                        value="{{ $metric->metricEmpScore->id ?? '' }}">
                                                                                    <input type="hidden" name="metricId"
                                                                                        value="{{ $metric->metricId }}">
                                                                                    <input type="hidden" name="sectionId"
                                                                                        value="{{ $section->sectionId }}">
                                                                                    <input type="hidden" name="kpiId"
                                                                                        value="{{ $kpi->kpi->kpiId }}">
                                                                                    <input type="hidden" name="kpiType"
                                                                                        value="{{ $kpi->kpi->kpiType }}">
                                                                                    <button type="submit" class="btn btn-primary btn-save ms-auto">
                                                                                        <i class="bx bx-save me-1"></i>Save
                                                                                    </button>
                                                                                </div>
                                                                            </form>
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
                                    <p class="text-muted mt-2">No KPIs available for probing.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer with Submit Button -->
                    @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-end">
                            <button type="button" data-bs-toggle="modal" class="btn btn-dark"
                                data-bs-target=".bs-delete-modal-lg">
                                <i class="bx bx-send me-1"></i>Submit Appraisal For Probe
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
        <x-appraisal.confirmation-modal
            id="bs-delete-modal-lg"
            title="Push to Probe Supervisor"
            icon="bx-transfer"
            iconColor="text-warning"
            headerClass="bg-dark text-white"
            message="Push Your Scores To Probe?"
            description="This will send your selections to a higher supervisor for review."
            :action="route('submit.appraisal')"
            buttonText="Yes, Send To Supervisor"
            buttonClass="btn-success"
            buttonIcon="bx-check"
            :hiddenFields="[
                'employeeId' => $employeeId,
                'kpiId' => $kpi->kpi->kpiId,
                'batchId' => $kpi->kpi->batchId,
                'status' => 'PROBLEM'
            ]" />
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const saveForms = document.querySelectorAll('form.ajax-emp-prob-eval-form');
                const sections = document.querySelectorAll('.section-tab');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');

                const showToast = (type, message) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: type,
                        title: message
                    });
                };

                function updateProgressBar() {
                    const checkboxes = document.querySelectorAll('input[name="prob"]');
                    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                    const percent = checkboxes.length > 0 ? Math.round((checkedCount / checkboxes.length) * 100) : 0;

                    if (progressBar) {
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);
                    }
                    if (progressText) {
                        progressText.textContent = percent + '%';
                    }
                }

                // Attach checkbox listeners
                document.querySelectorAll('input[name="prob"]').forEach(checkbox => {
                    checkbox.addEventListener('change', updateProgressBar);
                });

                saveForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const scrollPos = window.scrollY;
                        const formData = new FormData(form);
                        const saveBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                        const originalHTML = saveBtn.innerHTML;

                        // Add btn-saving class
                        saveBtn.classList.add('btn-saving');
                        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...';

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
                                setTimeout(() => window.scrollTo({
                                    top: scrollPos,
                                    behavior: 'smooth'
                                }), 150);

                                // Show saved state
                                saveBtn.classList.remove('btn-saving');
                                saveBtn.classList.add('btn-saved');
                                saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Saved';

                                showToast('success', 'Selection saved successfully.');

                                // Reset button after delay
                                setTimeout(() => {
                                    saveBtn.classList.remove('btn-saved');
                                    saveBtn.innerHTML = originalHTML;
                                }, 2000);

                                updateProgressBar();
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.scrollTo({
                                    top: scrollPos,
                                    behavior: 'smooth'
                                });

                                saveBtn.classList.remove('btn-saving');
                                saveBtn.innerHTML = originalHTML;

                                showToast('error', 'Something went wrong while saving.');
                            });
                    });
                });

                // Initial progress bar update
                updateProgressBar();
            });
        </script>
    @endpush

</x-base-layout>
