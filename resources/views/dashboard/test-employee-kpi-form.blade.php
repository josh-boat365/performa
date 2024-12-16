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

                       @if(in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                        <div></div>
                       @else

                        <div id="pagination-count" class=" text-center mb-3">
                            <span><b>Current Page</b></span>
                            <span class="badge rounded-pill bg-primary" id="current-page">1</span>/ <span><b>Last Page</b></span><span
                                class="badge rounded-pill bg-dark" id="total-pages">1</span>
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
                                                                    method="POST" class="section-form">
                                                                    @csrf
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3 score-input"
                                                                                type="number" name="sectionEmpScore"
                                                                                required placeholder="Enter Score"
                                                                                max="{{ $section->sectionScore }}"
                                                                                title="The Score cannot be more than the section score {{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) &&
                                                                                        in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                                                value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input
                                                                                class="form-control mb-3 comment-input"
                                                                                type="text" name="employeeComment"
                                                                                placeholder="Enter your comments"
                                                                                @disabled(isset($section->sectionEmpScore) &&
                                                                                        in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                                                value="{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}">
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
                                                                                value="{{ $section->sectionId }}">
                                                                            <input type="hidden" name="kpiId"
                                                                                value="{{ $kpi->kpi->kpiId }}">
                                                                            <button type="submit" @style(['height: fit-content'])
                                                                                class="btn btn-success">Save</button>
                                                                        @endif
                                                                    </div>
                                                                </form>


                                                                {{-- Supervisor Comment and Score when Supervisor has submitted their scores --}}
                                                                @if (isset($section->sectionEmpScore))
                                                                    @if ($section->sectionEmpScore->status === 'CONFIRMATION' || $section->sectionEmpScore->status === 'COMPLETED')
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
                                                                                <input class="form-control mb-3"
                                                                                    type="text" readonly
                                                                                    name="supervisorComment"
                                                                                    placeholder="Enter your comments"
                                                                                    required
                                                                                    value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                    @elseif(isset($section->sectionEmpScore) && $section->sectionEmpScore->prob == true)
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
                                                                                <input class="form-control mb-3"
                                                                                    type="text" readonly
                                                                                    name="supervisorComment"
                                                                                    placeholder="Enter your comments"
                                                                                    required
                                                                                    value="{{ $section->sectionEmpScore->probComment ?? '' }}">
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
                                                                                    class="metric-form">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3">
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
                                                                                                max="{{ $metric->metricScore }}"
                                                                                                title="The Score cannot be more than the section score {{ $metric->metricScore }}"
                                                                                                @disabled(
                                                                                                    (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION'])) ||
                                                                                                        (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM'])))
                                                                                                value="{{ optional($metric->metricEmpScore)->metricEmpScore ?? '' }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <input
                                                                                                class="form-control mb-3"
                                                                                                type="text"
                                                                                                name="employeeComment"
                                                                                                placeholder="Enter your comments"
                                                                                                @disabled(
                                                                                                    (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION'])) ||
                                                                                                        (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM'])))
                                                                                                value="{{ optional($metric->metricEmpScore)->employeeComment ?? '' }}">
                                                                                        </div>
                                                                                        @if (
                                                                                            !isset($metric->metricEmpScore) ||
                                                                                                (!in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION']) &&
                                                                                                    !in_array($section->sectionEmpScore->status, ['COMPLETED', 'PROBLEM'])))
                                                                                            <button type="submit"
                                                                                                @style(['height: fit-content'])
                                                                                                class="btn btn-success">Save</button>
                                                                                        @endif
                                                                                    </div>
                                                                                </form>

                                                                                {{-- Supervisor Comment and Score when Supervisor has submitted their scores --}}
                                                                                @if (isset($metric->metricEmpScore))
                                                                                    @if ($metric->metricEmpScore->status === 'CONFIRMATION' || $metric->metricEmpScore->status === 'COMPLETED')
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
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="text"
                                                                                                    readonly
                                                                                                    name="supervisorComment"
                                                                                                    placeholder="Enter your comments"
                                                                                                    required
                                                                                                    value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    @elseif(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob == true)
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
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="text"
                                                                                                    readonly
                                                                                                    name="supervisorComment"
                                                                                                    placeholder="Enter your comments"
                                                                                                    required
                                                                                                    value="{{ $metric->metricEmpScore->probComment ?? '' }}">
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
                                    // Function to check if all score inputs are filled
                                    function checkInputs() {
                                        const scoreInputs = document.querySelectorAll('input[type="number"][name*="EmpScore"]');
                                        const allFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');

                                        // Enable or disable the submit button based on input values
                                        document.getElementById('submitAppraisalButton').disabled = !allFilled;
                                    }

                                    // Attach event listeners to all score inputs
                                    document.querySelectorAll('input[type="number"][name*="EmpScore"]').forEach(input => {
                                        input.addEventListener('input', checkInputs);
                                    });

                                    // Initial check in case inputs are pre-filled
                                    checkInputs();
                                </script>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const sections = document.querySelectorAll('.section-tab');
                                        const prevBtn = document.getElementById('prev-btn');
                                        const nextBtn = document.getElementById('next-btn');
                                        const submitBtn = document.getElementById('submit-btn');
                                        const currentPageSpan = document.getElementById('current-page');
                                        const totalPagesSpan = document.getElementById('total-pages');
                                        let currentPage = parseInt(localStorage.getItem('currentPage') || 0);
                                        const sectionsPerPage = 3;
                                        const totalPages = Math.ceil(sections.length / sectionsPerPage);

                                        // Initialize Pagination Count
                                        totalPagesSpan.textContent = totalPages;

                                        function showPage(page) {
                                            sections.forEach(section => {
                                                section.style.display = 'none';
                                            });
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            for (let i = start; i < end && i < sections.length; i++) {
                                                sections[i].style.display = 'block';
                                            }
                                            prevBtn.disabled = page === 0;
                                            nextBtn.disabled = page === totalPages - 1;
                                            submitBtn.disabled = totalPages > 1 && page !== totalPages - 1;

                                            currentPageSpan.textContent = page + 1; // Update current page display
                                            localStorage.setItem('currentPage', page); // Save the current page to localStorage
                                        }

                                        prevBtn.addEventListener('click', function() {
                                            if (currentPage > 0) {
                                                currentPage--;
                                                showPage(currentPage);
                                            }
                                        });

                                        nextBtn.addEventListener('click', function() {
                                            if (currentPage < totalPages - 1) {
                                                currentPage++;
                                                showPage(currentPage);
                                            }
                                        });

                                        // Attach event listener to forms to keep the page after submission
                                        const scoreForms = document.querySelectorAll('.score-form');
                                        scoreForms.forEach(form => {
                                            form.addEventListener('submit', function() {
                                                localStorage.setItem('currentPage',
                                                currentPage); // Save the current page before form submission
                                            });
                                        });

                                        // Show the page on load
                                        showPage(currentPage);
                                    });
                                </script>
                            @endpush



                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- end col -->
    </div>



    </div>

</x-base-layout>
