<x-base-layout>

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

        <div class="progress fixed-top" style="height: 10px;">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>

        <!-- end page title -->
        <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Appraisal Grade</h4>
                    </div>
                </div>
                @php
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
                    $badgeDetails = getBadgeDetails($gradeDetails['status'] ?? null);
                @endphp


                <div class="mt-3">
                    <div class="d-flex gap-5">
                        <h5>Grade: <b>{{ $gradeDetails['grade'] ?? '___' }}</b></h5>
                        <h5>Score: <b>{{ $gradeDetails['kpiScore'] ?? '___' }}</b></h5>
                        <h5>Remark: <b>{{ $gradeDetails['remark'] ?? '___' }}</b></h5>
                        <h5>Status: <b><span class="badge rounded-pill {{ $badgeDetails['class'] }}">
                                    {{ $badgeDetails['text'] }}
                                </span></b>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">


                    <div class="card-body">
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        @if (in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                            <div></div>
                        @else
                            <div id="pagination-count" class=" text-center mb-3">
                                <span><b>Current Page</b></span>
                                <span class="badge rounded-pill bg-primary" id="current-page">1</span>/ <span><b>Last
                                        Page</b></span><span class="badge rounded-pill bg-dark"
                                    id="total-pages">1</span>
                            </div>
                        @endif


                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $index => $kpi)
                                        <div class="kpi">
                                            {{--  {{ dd($appraisal)}}  --}}
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
                                                                                min="0" 
                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                max="{{ $section->sectionScore }}"
                                                                                title="The Score cannot be more than the section score {{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) &&
                                                                                        in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                                                value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <textarea class="form-control mb-3 comment-input" type="text" name="employeeComment" required
                                                                                placeholder="Enter your comments" @disabled(isset($section->sectionEmpScore) &&
                                                                                        in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])) rows="3">{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</textarea>
                                                                        </div>
                                                                        @if (
                                                                            !isset($section->sectionEmpScore) ||
                                                                                !in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
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
                                                                            $section->sectionEmpScore->prob == false)
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
                                                                                    required rows="3">{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    @elseif(
                                                                        ($section->sectionEmpScore->status === 'CONFIRMATION' || $section->sectionEmpScore->status === 'COMPLETED') &&
                                                                            $section->sectionEmpScore->prob == true)
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
                                                                                    placeholder="Enter your comments" required rows="3">{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
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
                                                                                    placeholder="Enter your comments" required rows="3">{{ $section->sectionEmpScore->probComment ?? '' }}</textarea>
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

                                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                                max="{{ $metric->metricScore }}"
                                                                                                title="The Score cannot be more than the section score {{ $metric->metricScore }}"
                                                                                                @disabled(
                                                                                                    (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION'])) ||
                                                                                                        (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM'])))
                                                                                                value="{{ optional($metric->metricEmpScore)->metricEmpScore ?? '' }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control mb-3" type="text" name="employeeComment" required placeholder="Enter your comments"
                                                                                                rows="3" @disabled(
                                                                                                    (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION'])) ||
                                                                                                        (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM'])))>{{ optional($metric->metricEmpScore)->employeeComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                        @if (
                                                                                            !isset($metric->metricEmpScore) ||
                                                                                                (!in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION']) &&
                                                                                                    !in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM'])))
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
                                                                                            $metric->metricEmpScore->prob == false)
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
                                                                                                    placeholder="Enter your comments" required rows="3">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                        {{--  @elseif((isset($metric->metricEmpScore) || $metric->metricEmpScore->status === 'COMPLETED') && $metric->metricEmpScore->prob == true)  --}}
                                                                                    @elseif(
                                                                                        ($metric->metricEmpScore->status === 'CONFIRMATION' || $metric->metricEmpScore->status === 'COMPLETED') &&
                                                                                            $metric->metricEmpScore->prob == true)
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
                                                                                                    placeholder="Enter your comments" required rows="3">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
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
                                                                                                    placeholder="Enter your comments" required rows="3">{{ $metric->metricEmpScore->probComment ?? '' }}</textarea>
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

                            @if (isset($section->sectionEmpScore) &&
                                    ($section->sectionEmpScore->status === 'REVIEW' ||
                                        $section->sectionEmpScore->status === 'CONFIRMATION' ||
                                        $section->sectionEmpScore->status === 'COMPLETED' ||
                                        $section->sectionEmpScore->status === 'PROBLEM'))
                                <div></div>
                            @else
                                <div class="float-end">
                                    <div class="d-flex gap-3 pagination-controls">
                                        <button id="prev-btn" class="btn btn-dark" disabled>Previous</button>
                                        <button id="next-btn" class="btn btn-primary">Next</button>

                                        <button id="submit-btn" type="button" data-bs-toggle="modal"
                                            class="btn btn-success" data-bs-target=".bs-delete-modal-lg"
                                            id="submitAppraisalButton" disabled>Submit Appraisal</button>
                                    </div>
                                </div>

                                <div class="modal fade bs-delete-modal-lg" tabindex="-1" role="dialog"
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
                                            @style(['width: 8rem; height: fit-content']) data-bs-target=".bs-delete-modal-lg">Accept</button>

                                        <a href="{{ route('show.employee.probe', $kpi->kpi->kpiId) }}"
                                            class="btn btn-warning" @style(['width: 8rem; height: fit-content'])>Probe</a>
                                    </div>
                                </div>

                                <!-- Modal for Delete Confirmation -->
                                <div class="modal fade bs-delete-modal-lg" tabindex="-1" role="dialog"
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
                                                        <button type="submit" class="btn btn-success">Yes,
                                                            Accept </button>
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
                                        let currentPage = parseInt(sessionStorage.getItem('currentPage') || 0);
                                        const sectionsPerPage = 3;
                                        const totalPages = Math.ceil(sections.length / sectionsPerPage);

                                        // Save button states and form data - stored in sessionStorage to persist across page refreshes
                                        let saveButtonStates = JSON.parse(sessionStorage.getItem('saveButtonStates') || '{}');
                                        let formData = JSON.parse(sessionStorage.getItem('formData') || '{}');

                                        totalPagesSpan.textContent = totalPages;

                                        // Initialize save buttons with unique IDs and states
                                        function initializeSaveButtons() {
                                            sections.forEach((section, index) => {
                                                const saveBtn = section.querySelector('button[type="submit"]');
                                                if (saveBtn) {
                                                    const btnId = `save-btn-${index}`;
                                                    saveBtn.setAttribute('data-save-id', btnId);
                                                    saveBtn.setAttribute('data-section-index', index);

                                                    // Initialize state if not exists
                                                    if (!(btnId in saveButtonStates)) {
                                                        saveButtonStates[btnId] = 0;
                                                    }

                                                    updateSaveButtonAppearance(saveBtn, saveButtonStates[btnId]);
                                                }
                                            });
                                            sessionStorage.setItem('saveButtonStates', JSON.stringify(saveButtonStates));
                                        }

                                        // Restore form data from sessionStorage
                                        function restoreFormData() {
                                            sections.forEach((section, sectionIndex) => {
                                                const scoreInputs = section.querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const commentInputs = section.querySelectorAll('textarea[name="employeeComment"]');

                                                scoreInputs.forEach(input => {
                                                    const key = `${sectionIndex}_${input.name}`;
                                                    if (formData[key]) {
                                                        input.value = formData[key];
                                                        validateField(input, false); // Validate without triggering state change
                                                    }
                                                });

                                                commentInputs.forEach(input => {
                                                    const key = `${sectionIndex}_${input.name}`;
                                                    if (formData[key]) {
                                                        input.value = formData[key];
                                                        validateField(input, false); // Validate without triggering state change
                                                    }
                                                });
                                            });
                                        }

                                        // Save form data to sessionStorage
                                        function saveFormData(input, sectionIndex) {
                                            const key = `${sectionIndex}_${input.name}`;
                                            formData[key] = input.value;
                                            sessionStorage.setItem('formData', JSON.stringify(formData));
                                        }

                                        function updateSaveButtonAppearance(button, state) {
                                            if (state === 1) {
                                                // Saved state - green appearance
                                                button.classList.remove('btn-primary', 'btn-warning');
                                                button.classList.add('btn-success');
                                                button.innerHTML = 'Saved';
                                            } else {
                                                // Check if section has any data to determine if it's "not saved" or "save changes"
                                                const sectionIndex = parseInt(button.getAttribute('data-section-index'));
                                                const section = sections[sectionIndex];
                                                const hasData = checkSectionHasData(section, sectionIndex);

                                                if (hasData) {
                                                    // Has data but not saved - yellow warning
                                                    button.classList.remove('btn-success', 'btn-primary');
                                                    button.classList.add('btn-warning');
                                                    button.innerHTML = 'Save Changes';
                                                } else {
                                                    // No data - blue primary
                                                    button.classList.remove('btn-success', 'btn-warning');
                                                    button.classList.add('btn-primary');
                                                    button.innerHTML = 'Not Saved';
                                                }
                                            }
                                        }

                                        function checkSectionHasData(section, sectionIndex) {
                                            const scoreInputs = section.querySelectorAll('input[type="number"][name*="EmpScore"]');
                                            const commentInputs = section.querySelectorAll('textarea[name="employeeComment"]');

                                            const hasScores = Array.from(scoreInputs).some(input => input.value.trim() !== '');
                                            const hasComments = Array.from(commentInputs).some(input => input.value.trim() !== '');

                                            return hasScores || hasComments;
                                        }

                                        function validateField(field, triggerStateChange = true) {
                                            const value = field.value.trim();
                                            const section = field.closest('.section-tab');
                                            const sectionIndex = Array.from(sections).indexOf(section);
                                            const saveBtn = section?.querySelector('button[type="submit"]');
                                            const btnId = saveBtn?.getAttribute('data-save-id');

                                            // Save field data
                                            if (triggerStateChange) {
                                                saveFormData(field, sectionIndex);
                                            }

                                            if (value === '') {
                                                field.classList.add('is-invalid');
                                                field.classList.remove('is-valid', 'field-modified');
                                                section?.classList.add('border-danger');
                                            } else {
                                                field.classList.remove('is-invalid');
                                                field.classList.add('is-valid');
                                                section?.classList.remove('border-danger');
                                            }

                                            // Update save button state only for the specific section
                                            if (btnId && triggerStateChange) {
                                                const currentSavedState = saveButtonStates[btnId] || 0;
                                                const hasData = checkSectionHasData(section, sectionIndex);

                                                if (currentSavedState === 1 && hasData) {
                                                    // Field was previously saved, now it's modified - mark as needing save
                                                    field.classList.add('field-modified');
                                                    saveButtonStates[btnId] = 0; // Mark as needing save
                                                    updateSaveButtonAppearance(saveBtn, 0);
                                                    sessionStorage.setItem('saveButtonStates', JSON.stringify(saveButtonStates));
                                                } else if (!hasData) {
                                                    // No data in section
                                                    field.classList.remove('field-modified');
                                                    saveButtonStates[btnId] = 0;
                                                    updateSaveButtonAppearance(saveBtn, 0);
                                                    sessionStorage.setItem('saveButtonStates', JSON.stringify(saveButtonStates));
                                                } else if (hasData && currentSavedState === 0) {
                                                    // Has data but not saved
                                                    updateSaveButtonAppearance(saveBtn, 0);
                                                }
                                            }

                                            return value !== '';
                                        }

                                        function checkInputs(page) {
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            let allFilled = true;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const commentInputs = sections[i].querySelectorAll('textarea[name="employeeComment"]');

                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !== '');

                                                if (!scoresFilled || !commentsFilled) {
                                                    allFilled = false;
                                                    sections[i].classList.add('border-danger');
                                                } else {
                                                    sections[i].classList.remove('border-danger');
                                                }
                                            }

                                            return allFilled;
                                        }

                                        function checkAllSaveButtonsOnPage(page) {
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const saveBtn = sections[i].querySelector('button[type="submit"]');
                                                const btnId = saveBtn?.getAttribute('data-save-id');

                                                if (btnId && saveButtonStates[btnId] !== 1) {
                                                    return false;
                                                }
                                            }
                                            return true;
                                        }

                                        function checkAllSaveButtonsGlobally() {
                                            for (let page = 0; page < totalPages; page++) {
                                                if (!checkAllSaveButtonsOnPage(page)) {
                                                    return false;
                                                }
                                            }
                                            return true;
                                        }

                                        function updateProgressBar() {
                                            let totalSaved = 0;
                                            let totalSections = sections.length;

                                            sections.forEach((section, index) => {
                                                const saveBtn = section.querySelector('button[type="submit"]');
                                                const btnId = saveBtn?.getAttribute('data-save-id');

                                                if (btnId && saveButtonStates[btnId] === 1) {
                                                    totalSaved++;
                                                }
                                            });

                                            const percent = Math.round((totalSaved / totalSections) * 100);
                                            progressBar.style.width = percent + '%';
                                            progressBar.setAttribute('aria-valuenow', percent);
                                            progressBar.textContent = percent + '%';
                                        }

                                        function updateButtons() {
                                            prevBtn.disabled = currentPage === 0;

                                            // Enable next button only if all save buttons on current page have been clicked
                                            const currentPageSaved = checkAllSaveButtonsOnPage(currentPage);
                                            const currentPageFilled = checkInputs(currentPage);
                                            nextBtn.disabled = currentPage === totalPages - 1 || !currentPageFilled || !currentPageSaved;

                                            // Enable submit button only if all save buttons globally have been clicked
                                            const allPagesSaved = checkAllSaveButtonsGlobally();
                                            const allPagesFilled = Array.from({
                                                length: totalPages
                                            }).every((_, i) => checkInputs(i));
                                            submitBtn.disabled = !allPagesFilled || !allPagesSaved;

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
                                            sessionStorage.setItem('currentPage', page);
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
                                            if (currentPage < totalPages - 1 && checkInputs(currentPage) && checkAllSaveButtonsOnPage(
                                                    currentPage)) {
                                                currentPage++;
                                                showPage(currentPage);
                                            }
                                        });

                                        // Enhanced input event listeners
                                        document.querySelectorAll('input[type="number"][name*="EmpScore"], textarea[name="employeeComment"]')
                                            .forEach(input => {
                                                input.addEventListener('input', function() {
                                                    validateField(this, true);
                                                    updateButtons();
                                                });

                                                // Also trigger validation on blur to catch paste operations
                                                input.addEventListener('blur', function() {
                                                    validateField(this, true);
                                                    updateButtons();
                                                });
                                            });

                                        // Enhanced AJAX form handler with save button state management
                                        document.querySelectorAll('form.ajax-eval-form').forEach(form => {
                                            form.addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                const scrollPos = window.scrollY;
                                                const formData = new FormData(form);
                                                const saveBtn = form.querySelector('button[type="submit"]');
                                                const btnId = saveBtn?.getAttribute('data-save-id');
                                                const sectionIndex = parseInt(saveBtn?.getAttribute('data-section-index'));
                                                const originalText = saveBtn.innerHTML;

                                                // Store scroll position and current page state before submission
                                                sessionStorage.setItem('preserveScrollPosition', scrollPos.toString());
                                                sessionStorage.setItem('currentPage', currentPage.toString());

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
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            // Update save button state to saved (1) for this specific button only
                                                            if (btnId) {
                                                                saveButtonStates[btnId] = 1;
                                                                sessionStorage.setItem('saveButtonStates', JSON.stringify(
                                                                    saveButtonStates));
                                                            }

                                                            sessionStorage.setItem('showSuccessToast', JSON.stringify({
                                                                message: data.message || 'Saved successfully'
                                                            }));

                                                            // Mark fields as saved (remove modified state) for this section only
                                                            const section = form.closest('.section-tab');
                                                            const fields = section.querySelectorAll(
                                                                'input[type="number"][name*="EmpScore"], textarea[name="employeeComment"]'
                                                                );
                                                            fields.forEach(field => {
                                                                field.classList.remove('field-modified');
                                                            });

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

                                        // Initialize save buttons
                                        initializeSaveButtons();

                                        // Restore form data
                                        restoreFormData();

                                        // Show the initial page
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

                                    // Add CSS for modified field styling
                                    const style = document.createElement('style');
                                    style.textContent = `
                                            .field-modified {
                                                border-color: #0d6efd !important;
                                                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
                                            }

                                            .btn-warning {
                                                background-color: #ffc107 !important;
                                                border-color: #ffc107 !important;
                                                color: #000 !important;
                                            }
                                        `;
                                    document.head.appendChild(style);
                                </script>
                            @endpush


                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
