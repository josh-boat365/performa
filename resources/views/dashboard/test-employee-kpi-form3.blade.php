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
                                                                                                step="0.01"
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

                                        <a href="{{ route('show.employee.probe', ['id' => $kpi->kpi->kpiId, 'batchId' => $kpi->kpi->batchId]) }}"
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

                                        function updateProgressBar() {
                                            let totalValid = 0;
                                            sections.forEach(section => {
                                                const scoreInputs = section.querySelectorAll('input[type="number"][name*="EmpScore"]');
                                                const commentInputs = section.querySelectorAll('textarea[name="employeeComment"]');
                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !==
                                                    '');
                                                if (scoresFilled && commentsFilled) totalValid++;
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
                                            if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
                                                currentPage++;
                                                showPage(currentPage);
                                            }
                                        });

                                        document.querySelectorAll('input[type="number"][name*="EmpScore"], textarea[name="employeeComment"]')
                                            .forEach(input => {
                                                input.addEventListener('input', function() {
                                                    validateField(this);
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
                            @endpush


                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
