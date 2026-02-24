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

        /* Unsaved form warning border */
        .border-warning {
            border-color: #ffc107 !important;
            border-width: 2px !important;
        }

        /* Saved button styling */
        .btn-saved {
            pointer-events: none;
        }
    </style>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('show.batch.kpi', $batchId) }}">My KPIs</a> > Your
                        Appraisal
                    </h4>
                </div>
            </div>
        </div>

        <!-- Progress Bar - Sticky -->
        @if (!in_array($kpiStatus ?? '', ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
            <div class="progress-container">
                <div class="container-fluid card">
                    <div class="progress-wrapper p-3 d-flex align-items-center justify-content-between ">
                        <div class="progress-info">
                            <span class="">Page</span>
                            <span class="badge bg-primary" id="current-page">1</span>
                            <span class="text-muted">of</span>
                            <span class="">Page</span>
                            <span class="badge bg-dark" id="total-pages">1</span>
                        </div>
                        <div class="progress flex-fill mx-3" style="height: 12px;">
                            <div id="progress-bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <span id="progress-text">0%</span>
                            </div>
                        </div>
                        <span class=" small">Completion</span>
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

        <!-- Appraisal Completed Message - Top -->
        @if (($gradeDetails['status'] ?? null) === 'COMPLETED')
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bx bx-check-circle me-3" style="font-size: 2rem;"></i>
                        <div>
                            <h5 class="alert-heading mb-0">Appraisal Completed</h5>
                            <p class="mb-0">Your appraisal has been successfully completed.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                        <div class="d-flex gap-5">
                            <!-- Final Employee Grade Card -->
                            <div class="card flex-fill border border-success border-2">
                                <div class="card-body">
                                    <span class="badge bg-success mb-3 fs-6">Final Employee Grade</span>
                                    <div>
                                        <div class="d-flex gap-2 mb-2 justify-content-between">
                                        <span class="text-muted fs-6">Grade</span>
                                        <span class=""><h5 class=" fs-5"><strong>{{ $gradeDetails['grade'] ?? '___' }}</strong></h5></span>
                                        </div>

                                        <div class="d-flex gap-2 mb-2 justify-content-between">
                                        <span class="text-muted  fs-6">Score</span>
                                        <span class=""><h5 class=" fs-5"><strong>{{ $gradeDetails['kpiScore'] ?? '___' }}</strong></h5></span>
                                        </div>

                                        <div class="d-flex gap-2 mb-2 justify-content-between">
                                        <span class="text-muted  fs-6">Remark</span>
                                        <span class=""><h5 class=" fs-5"><strong>{{ $gradeDetails['remark'] ?? '___' }}</strong></h5></span>
                                        </div>

                                        <a href="#" class="btn btn-sm btn-outline-success fs-6" data-bs-toggle="modal" data-bs-target=".bs-recommendation-modal-lg">
                                            <i class="bx bx-message-detail me-1"></i>View Recommendation
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Submitted Grade Card -->
                            <div class="card flex-fill border border-secondary border-2">
                                <div class="card-body">
                                    <span class="badge bg-secondary mb-3 fs-6">Your Submitted Grade</span>
                                    <h6 class="text-muted mb-3 fs-6">{{ $submittedEmployeeGrade->employeeName ?? '----' }}</h6>

                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-secondary fs-6">{{ $submittedEmployeeGrade->totalKpiScore ?? '----' }}</span>
                                        <span class="badge bg-secondary fs-6">{{ $submittedEmployeeGrade->grade ?? '----' }}</span>
                                        <span class="badge bg-secondary fs-6">{{ $submittedEmployeeGrade->remark ?? '----' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor Grade Card -->
                            <div class="card flex-fill border border-primary border-2">
                                <div class="card-body">
                                    <span class="badge bg-primary mb-3 fs-6">Supervisor's Grade</span>
                                    <h6 class="text-muted mb-3 fs-6">{{ $supervisorGradeForEmployee->employeeName ?? '----' }}</h6>

                                    <div class="d-flex gap-2 mb-3">
                                        <span class="badge bg-primary fs-6">{{ $supervisorGradeForEmployee->totalKpiScore ?? '----' }}</span>
                                        <span class="badge bg-primary fs-6">{{ $supervisorGradeForEmployee->grade ?? '----' }}</span>
                                        <span class="badge bg-primary fs-6">{{ $supervisorGradeForEmployee->remark ?? '----' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--  Recommendation Modal   --}}
        <div class="modal fade bs-recommendation-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
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
                                                <div class="card border border-primary section-tab" @style(['border-radius: 10px;'])
                                                    @style(['border-radius: 10px; display: none;'])
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
                                                                    <div class="d-flex gap-3">
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
                                                                            <button type="submit" @style(['height: fit-content'])
                                                                                class="btn btn-success">Save</button>
                                                                            <div id="ajax-loader" style="display:none;">
                                                                                <div class="spinner-border text-primary"
                                                                                    role="status">
                                                                                    <span
                                                                                        class="visually-hidden">Loading...</span>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </form>


                                                                {{-- Supervisor Comment and Score when Supervisor has submitted their scores --}}
                                                                @if (isset($section->sectionEmpScore))
                                                                    @if (
                        ($section->sectionEmpScore->status === 'CONFIRMATION' || $section->sectionEmpScore->status === 'COMPLETED') &&
                        $section->sectionEmpScore->prob == false
                    )
                                                                        <span
                                                                            class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                                Score and Comment</strong></span>
                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input class="form-control mb-3"
                                                                                    type="number" readonly
                                                                                    name="metricSupScore"
                                                                                    placeholder="Enter Score" required
                                                                                    value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <textarea class="form-control mb-3" type="text" readonly name="supervisorComment" placeholder="Enter your comments"
                                                                                     rows="3">{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    @elseif(
                        ($section->sectionEmpScore->status === 'CONFIRMATION' || $section->sectionEmpScore->status === 'COMPLETED') &&
                        $section->sectionEmpScore->prob == true
                    )
                                                                        <span
                                                                            class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                                Score and Comment</strong></span>
                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input class="form-control mb-3"
                                                                                    type="number" readonly
                                                                                    name="metricSupScore"
                                                                                    placeholder="Enter Score" required
                                                                                    value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <textarea class="form-control mb-3" type="text" readonly name="supervisorComment"
                                                                                    placeholder="Enter your comments" rows="3">{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                            </div>
                                                                        </div>

                                                                        <span
                                                                            class="mb-2 badge rounded-pill bg-dark"><strong>Probing
                                                                                Score and Comment</strong></span>
                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input class="form-control mb-3"
                                                                                    type="number" readonly
                                                                                    name="metricSupScore"
                                                                                    placeholder="Enter Score" required
                                                                                    value="{{ optional($section->sectionEmpScore)->sectionProbScore ?? '' }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <textarea class="form-control mb-3" type="text" readonly name="supervisorComment"
                                                                                    placeholder="Enter your comments"  rows="3">{{ $section->sectionEmpScore->probComment ?? '' }}</textarea>
                                                                            </div>
                                                                        </div>
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
                                                                                    <div class="d-flex gap-3">
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
                                                                                                @style(['height: fit-content'])
                                                                                                class="btn btn-success">Save</button>
                                                                                            <div id="ajax-loader"
                                                                                                style="display:none;">
                                                                                                <div class="spinner-border text-primary"
                                                                                                    role="status">
                                                                                                    <span
                                                                                                        class="visually-hidden">Loading...</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </form>

                                                                                {{-- Supervisor Comment and Score when Supervisor has submitted their scores --}}
                                                                                @if (isset($metric->metricEmpScore))
                                                                                    @if (
                            ($metric->metricEmpScore->status === 'CONFIRMATION' || $metric->metricEmpScore->status === 'COMPLETED') &&
                            $metric->metricEmpScore->prob == false
                        )
                                                                                        <span
                                                                                            class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                                                Score and
                                                                                                Comment</strong></span>
                                                                                        <div class="d-flex gap-3">
                                                                                            <div class="col-md-2">
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="number"
                                                                                                    readonly
                                                                                                    name="metricSupScore"
                                                                                                    placeholder="Enter Score"
                                                                                                    required
                                                                                                    value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                                            </div>
                                                                                            <div class="col-md-9">
                                                                                                <textarea class="form-control mb-3" type="text" readonly name="supervisorComment"
                                                                                                    placeholder="Enter your comments"  rows="3">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                        {{--  @elseif((isset($metric->metricEmpScore) || $metric->metricEmpScore->status === 'COMPLETED') && $metric->metricEmpScore->prob == true)  --}}
                                                                                    @elseif(
                            ($metric->metricEmpScore->status === 'CONFIRMATION' || $metric->metricEmpScore->status === 'COMPLETED') &&
                            $metric->metricEmpScore->prob == true
                        )
                                                                                        <span
                                                                                            class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                                                Score and
                                                                                                Comment</strong></span>
                                                                                        <div class="d-flex gap-3">
                                                                                            <div class="col-md-2">
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="number"
                                                                                                    readonly
                                                                                                    name="metricSupScore"
                                                                                                    placeholder="Enter Score"
                                                                                                    required
                                                                                                    value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                                            </div>
                                                                                            <div class="col-md-9">
                                                                                                <textarea class="form-control mb-3" type="text" readonly name="supervisorComment"
                                                                                                    placeholder="Enter your comments"  rows="3">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span
                                                                                            class="mb-2 badge rounded-pill bg-dark"><strong>Probing
                                                                                                Score and
                                                                                                Comment</strong></span>
                                                                                        <div class="d-flex gap-3">
                                                                                            <div class="col-md-2">
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="number"
                                                                                                    readonly
                                                                                                    name="metricSupScore"
                                                                                                    placeholder="Enter Score"
                                                                                                    required
                                                                                                    value="{{ optional($metric->metricEmpScore)->metricProbScore ?? '' }}">
                                                                                            </div>
                                                                                            <div class="col-md-9">
                                                                                                <textarea class="form-control mb-3" type="text" readonly name="supervisorComment"
                                                                                                    placeholder="Enter your comments"  rows="3">{{ $metric->metricEmpScore->probComment ?? '' }}</textarea>
                                                                                            </div>
                                                                                        </div>
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

                            <hr class="mt-10">

                            @if (($gradeDetails['status'] ?? null) === 'COMPLETED')
                                <div class="mt-4">
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <i class="bx bx-check-circle me-3" style="font-size: 2rem;"></i>
                                        <div>
                                            <h5 class="alert-heading mb-0">Appraisal Completed</h5>
                                            <p class="mb-0">Your appraisal has been successfully completed.</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif (
    isset($section->sectionEmpScore) &&
    ($section->sectionEmpScore->status === 'REVIEW' ||
        $section->sectionEmpScore->status === 'CONFIRMATION' ||
        $section->sectionEmpScore->status === 'PROBLEM')
)
                                <div></div>
                            @else
                                <div class="float-end">
                                    <div class="d-flex gap-3 pagination-controls">
                                        <button id="prev-btn" class="btn btn-dark" disabled>Previous</button>
                                        <button id="next-btn" class="btn btn-primary">Next</button>

                                        <button id="submit-btn" type="button" data-bs-toggle="modal"
                                            class="btn btn-success" data-bs-target=".submit-appraisal-modal"
                                            id="submitAppraisalButton" disabled>Submit Appraisal</button>
                                    </div>
                                </div>

                                <div class="modal fade submit-appraisal-modal" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm Appraisal
                                                    Submit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4">Are you sure you want to
                                                    <b>Submit</b> your <b>Appraisal</b> to your
                                                    <b>Supervisor</b> for <b>Review?</b>
                                                </h4>
                                                <form action="{{ route('submit.appraisal') }}" method="POST"
                                                    id="appraisalForm">
                                                    @csrf
                                                    <input type="hidden" name="employeeId"
                                                        value="{{ $employeeId }}">
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="status" value="REVIEW">
                                                    <div class="d-grid">

                                                        <button type="submit" id="submitReviewButton"
                                                            class="btn btn-success">Submit Appraisal For
                                                            Review</button>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{--  WHEN SUPERVISOR HAS SUBMITTED THEIR REVIEW  --}}

                            @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                <div class="float-end">
                                    <div class="d-flex gap-3">
                                        <button type="button" data-bs-toggle="modal" class="btn btn-primary"
                                            @style(['width: 8rem; height: fit-content']) data-bs-target=".accept-appraisal-modal">Accept</button>

                                        <button type="button" data-bs-toggle="modal" class="btn btn-dark"
                                            @style(['width: 8rem; height: fit-content']) data-bs-target=".bs-push-review-modal-lg">Push for Review</button>

                                        <a href="{{ route('show.employee.probe', [$kpi->kpi->kpiId, $kpi->kpi->batchId]) }}"
                                            class="btn btn-warning" @style(['width: 8rem; height: fit-content'])>Probe</a>
                                    </div>
                                </div>

                                <!-- Modal for Confirmation -->
                                <div class="modal fade accept-appraisal-modal" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                    Supervisor Score</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4">Are you sure you want to
                                                    <b>Accept</b> this scores from your
                                                    <b>Supervisor?</b>
                                                </h4>
                                                <form action="{{ route('submit.appraisal') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="employeeId"
                                                        value="{{ $employeeId }}">
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="status" value="COMPLETED">
                                                    <div class="d-grid">
                                                        <button type="submit" id="acceptAppraisalButton" class="btn btn-success">Yes,
                                                            Accept </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{--  MODAL FOR PUSHING BACK TO SUPERVISOR FOR REVIEW  --}}
                                 <div class="modal fade bs-push-review-modal-lg" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Push Appraisal Back to Supervisor for Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4">Are you sure you want to
                                                    <b>Submit</b> your <b>Appraisal</b> back to your
                                                    <b>Supervisor</b> for <b>Review?</b>
                                                </h4>
                                                <form action="{{ route('submit.appraisal') }}" method="POST"
                                                    id="appraisalForm">
                                                    @csrf
                                                    <input type="hidden" name="employeeId"
                                                        value="{{ $employeeId }}">
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="status" value="REVIEW">
                                                    <div class="d-grid">

                                                        <button type="submit" id="submitReviewButton"
                                                            class="btn btn-warning">Push Appraisal Back For
                                                            Review</button>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div></div>
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

                                        // Use unique key per employee to avoid page persistence across different forms
                                        const currentEmployeeId = '{{ $employeeId }}';
                                        const pageStorageKey = `currentPage_employee_${currentEmployeeId}`;
                                        const lastEmployeeKey = 'lastViewedEmployeeId';

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

                                        totalPagesSpan.textContent = totalPages;

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

                                        // Track saved state for each form
                                        function initializeSavedState() {
                                            document.querySelectorAll('form.ajax-eval-form').forEach(form => {
                                                const scoreInput = form.querySelector('input[type="number"][name*="EmpScore"]');
                                                const saveBtn = form.querySelector('button[type="submit"]');

                                                if (scoreInput && scoreInput.value.trim() !== '') {
                                                    // Form has pre-filled value (already saved)
                                                    form.dataset.saved = 'true';
                                                    if (saveBtn) {
                                                        saveBtn.textContent = 'Saved';
                                                        saveBtn.classList.remove('btn-success');
                                                        saveBtn.classList.add('btn-secondary');
                                                    }
                                                } else {
                                                    form.dataset.saved = 'false';
                                                }
                                            });
                                        }

                                        // Mark form as unsaved when input changes
                                        function markFormUnsaved(form) {
                                            form.dataset.saved = 'false';
                                            const saveBtn = form.querySelector('button[type="submit"]');
                                            if (saveBtn) {
                                                saveBtn.textContent = 'Save';
                                                saveBtn.classList.remove('btn-secondary');
                                                saveBtn.classList.add('btn-success');
                                            }
                                        }

                                        function checkInputs(page) {
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            let allFilled = true;
                                            let allSaved = true;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const form = sections[i].querySelector('form.ajax-eval-form');
                                                {{-- Comments are no longer required, only scores --}}

                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');

                                                // Check if form is saved (must be both filled AND saved)
                                                const isSaved = form ? form.dataset.saved === 'true' : true;

                                                if (!scoresFilled) {
                                                    allFilled = false;
                                                    sections[i].classList.add('border-danger');
                                                    sections[i].classList.remove('border-warning');
                                                } else if (!isSaved) {
                                                    allSaved = false;
                                                    sections[i].classList.remove('border-danger');
                                                    sections[i].classList.add('border-warning');
                                                } else {
                                                    sections[i].classList.remove('border-danger');
                                                    sections[i].classList.remove('border-warning');
                                                }
                                            }

                                            return allFilled && allSaved;
                                        }

                                        function updateProgressBar() {
                                            let totalValid = 0;
                                            sections.forEach(section => {
                                                const scoreInputs = section.querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const form = section.querySelector('form.ajax-eval-form');
                                                {{--  const commentInputs = section.querySelectorAll('textarea[name="employeeComment"]');  --}}
                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                const isSaved = form ? form.dataset.saved === 'true' : true;
                                                {{--  const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !=='');  --}}
                                                {{--  if (scoresFilled && commentsFilled) totalValid++;  --}}
                                                if (scoresFilled && isSaved) totalValid++;
                                            });
                                            const percent = Math.round((totalValid / sections.length) * 100);
                                            progressBar.style.width = percent + '%';
                                            progressBar.setAttribute('aria-valuenow', percent);
                                            progressBar.textContent = percent + '%';
                                        }

                                        function updateButtons() {
                                            prevBtn.disabled = currentPage === 0;
                                            nextBtn.disabled = currentPage === totalPages - 1 || !checkInputs(currentPage);
                                            submitBtn.disabled = !Array.from({
                                                length: totalPages
                                            }).every((_, i) => checkInputs(i));
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

                                            currentPageSpan.textContent = page + 1;
                                            sessionStorage.setItem(pageStorageKey, page);
                                            updateButtons();
                                            window.scrollTo({
                                                top: sections[start].offsetTop,
                                                behavior: 'smooth'
                                            });
                                        }

                                        prevBtn.addEventListener('click', function() {
                                            if (currentPage > 0) {
                                                currentPage--;
                                                showPage(currentPage);
                                            }
                                        });

                                        nextBtn.addEventListener('click', function() {
                                            if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
                                                currentPage++;
                                                showPage(currentPage);
                                            }
                                        });

                                        document.querySelectorAll('input[type="number"][name*="EmpScore"], textarea[name="employeeComment"]')
                                            .forEach(input => {
                                                input.addEventListener('input', function() {
                                                    validateField(this);
                                                    // Mark the form as unsaved when input changes
                                                    const form = this.closest('form.ajax-eval-form');
                                                    if (form) {
                                                        markFormUnsaved(form);
                                                    }
                                                    updateButtons();
                                                });
                                            });

                                        // Modified AJAX form handler with page refresh and scroll preservation
                                        document.querySelectorAll('form.ajax-eval-form').forEach(form => {
                                            form.addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                const scrollPos = window.scrollY;
                                                const formData = new FormData(form);
                                                const saveBtn = form.querySelector('button[type="submit"]');
                                                const originalText = saveBtn.innerHTML;

                                                // Store scroll position and current page state before submission
                                                sessionStorage.setItem('preserveScrollPosition', scrollPos.toString());
                                                sessionStorage.setItem(pageStorageKey, currentPage.toString());

                                                saveBtn.innerHTML =
                                                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                                                saveBtn.disabled = true;

                                                fetch(form.action, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'X-CSRF-TOKEN': document.querySelector(
                                                                'meta[name="csrf-token"]').getAttribute('content')
                                                        },
                                                        body: formData
                                                    })
                                                    .then(response => {
                                                        // Check for 401 status (session expired)
                                                        if (response.status === 401) {
                                                            return response.json().then(data => {
                                                                if (data.session_expired) {
                                                                    alert('Your session has expired. Please log in again.');
                                                                    window.location.href = data.redirect || '{{ route("login") }}';
                                                                    return null;
                                                                }
                                                                return data;
                                                            });
                                                        }
                                                        return response.json();
                                                    })
                                                    .then(data => {
                                                        if (!data) return; // Session expired, already redirecting

                                                        // Store the response data for after refresh
                                                        if (data.success) {
                                                            sessionStorage.setItem('showSuccessToast', JSON.stringify({
                                                                message: data.message || 'Saved successfully'
                                                            }));
                                                        } else {
                                                            sessionStorage.setItem('showErrorToast', JSON.stringify({
                                                                message: data.message || 'An error occurred'
                                                            }));
                                                        }

                                                        // Force page refresh to get updated data
                                                        window.location.reload();
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        sessionStorage.setItem('showErrorToast', JSON.stringify({
                                                            message: 'An unexpected error occurred'
                                                        }));

                                                        // Force page refresh even on error
                                                        window.location.reload();
                                                    });
                                            });
                                        });

                                        function smoothScroll(targetForm) {
                                            $('html, body').animate({
                                                scrollTop: $(targetForm).offset().top
                                            }, 500);
                                        }

                                        // Show the initial page
                                        initializeSavedState();
                                        showPage(currentPage);

                                        // Check for toast messages after page refresh and restore scroll position
                                        setTimeout(() => {
                                            // First, restore scroll position
                                            const savedScrollPos = sessionStorage.getItem('preserveScrollPosition');
                                            if (savedScrollPos) {
                                                const scrollPos = parseInt(savedScrollPos);
                                                if (!isNaN(scrollPos)) {
                                                    window.scrollTo({
                                                        top: scrollPos,
                                                        behavior: 'instant'
                                                    });
                                                    console.log(`Scroll position restored to: ${scrollPos}`);
                                                }
                                                sessionStorage.removeItem('preserveScrollPosition');
                                            }

                                            // Then show toast messages
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

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const nextBtn = document.getElementById('next-btn');

                                        function validatePage(currentPage) {
                                            const sections = document.querySelectorAll('.section-tab');
                                            const sectionsPerPage = 3;
                                            const start = currentPage * sectionsPerPage;
                                            const end = start + sectionsPerPage;

                                            let allSaved = true;
                                            let emptyField = null;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const saveButtons = sections[i].querySelectorAll('button[type="submit"]');

                                                // Check if all score inputs are filled
                                                scoreInputs.forEach(input => {
                                                    if (!input.value.trim()) {
                                                        emptyField = input;
                                                        input.classList.add('is-invalid');
                                                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                                    } else {
                                                        input.classList.remove('is-invalid');
                                                    }
                                                });

                                                // Check if all save buttons are marked as "Saved"
                                                saveButtons.forEach(button => {
                                                    if (!button.classList.contains('btn-secondary')) {
                                                        allSaved = false;
                                                    }
                                                });
                                            }

                                            if (emptyField) {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Incomplete Form',
                                                    text: 'Please fill in all the required score fields before proceeding.',
                                                });
                                                return false;
                                            }

                                            if (!allSaved) {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: 'Unsaved Changes',
                                                    text: 'Please save all changes before proceeding to the next page.',
                                                });
                                                return false;
                                            }

                                            return true;
                                        }

                                        nextBtn.addEventListener('click', function(event) {
                                            const currentPage = parseInt(document.getElementById('current-page').textContent) - 1;
                                            if (!validatePage(currentPage)) {
                                                event.preventDefault();
                                            }
                                        });

                                        function updateNextButtonState() {
                                            const currentPage = parseInt(document.getElementById('current-page').textContent) - 1;
                                            const totalPages = parseInt(document.getElementById('total-pages').textContent);
                                            const sections = document.querySelectorAll('.section-tab');
                                            const sectionsPerPage = 3;
                                            const start = currentPage * sectionsPerPage;
                                            const end = start + sectionsPerPage;

                                            let allSaved = true;
                                            let allFilled = true;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const saveButtons = sections[i].querySelectorAll('button[type="submit"]');

                                                // Check if all score inputs are filled
                                                scoreInputs.forEach(input => {
                                                    if (!input.value.trim()) {
                                                        allFilled = false;
                                                    }
                                                });

                                                // Check if all save buttons are marked as "Saved"
                                                saveButtons.forEach(button => {
                                                    if (!button.classList.contains('btn-secondary')) {
                                                        allSaved = false;
                                                    }
                                                });
                                            }

                                            // Disable next button if on last page or if not all fields are filled and saved
                                            nextBtn.disabled = currentPage === totalPages - 1 || !(allFilled && allSaved);
                                        }

                                        document.querySelectorAll('input[type="number"][name*="EmpScore"], button[type="submit"]').forEach(element => {
                                            element.addEventListener('input', updateNextButtonState);
                                            element.addEventListener('click', updateNextButtonState);
                                        });

                                        updateNextButtonState();
                                    });
                                </script>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Handle loading indicator for submit review button (Submit Appraisal For Review)
                                        const submitReviewButtons = document.querySelectorAll('#submitReviewButton');
                                        submitReviewButtons.forEach(button => {
                                            const form = button.closest('form');
                                            if (form) {
                                                form.addEventListener('submit', function(e) {
                                                    const submitBtn = form.querySelector('#submitReviewButton');
                                                    if (submitBtn && !submitBtn.disabled) {
                                                        const originalHTML = submitBtn.innerHTML;
                                                        const modal = form.closest('.modal');
                                                        const closeBtn = modal ? modal.querySelector('.btn-close') : null;

                                                        submitBtn.disabled = true;
                                                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                                                        if (closeBtn) closeBtn.disabled = true;
                                                    }
                                                });
                                            }
                                        });

                                        // Handle loading indicator for accept appraisal button (Yes, Accept)
                                        const acceptButton = document.getElementById('acceptAppraisalButton');
                                        if (acceptButton) {
                                            const form = acceptButton.closest('form');
                                            if (form) {
                                                form.addEventListener('submit', function(e) {
                                                    if (acceptButton && !acceptButton.disabled) {
                                                        const originalHTML = acceptButton.innerHTML;
                                                        const modal = form.closest('.modal');
                                                        const closeBtn = modal ? modal.querySelector('.btn-close') : null;

                                                        acceptButton.disabled = true;
                                                        acceptButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                                                        if (closeBtn) closeBtn.disabled = true;
                                                    }
                                                });
                                            }
                                        }
                                    });
                                </script>
                            @endpush


                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
