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

        {{--  <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Progress</h4>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: {{ session('progress') }}%;" aria-valuenow="{{ session('progress') }}"
                            aria-valuemin="0" aria-valuemax="{{ session('progress') }}">{{ session('progress') }}%</div>
                    </div>
                </div>
            </div>
        </div>  --}}

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        <div class="p-3 text-muted">


                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $kpi)
                                        <div class="kpi">
                                            @foreach ($kpi->sections as $section)
                                                <div class="section-card" style="margin-top: 2rem;">
                                                    <h4>{{ $section->sectionName }} (<span
                                                            style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                    </h4>
                                                    <p>{{ $section->sectionDescription }}</p>

                                                    @if (isset($section->metrics) && $section->metrics->isEmpty())
                                                        <form action="{{ route('self.rating') }}" method="POST"
                                                            class="section-form">
                                                            @csrf
                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3 score-input"
                                                                        type="number" name="sectionEmpScore" required
                                                                        placeholder="Enter Score"
                                                                        max="{{ $section->sectionScore }}"
                                                                        @disabled(isset($section->sectionEmpScore) &&
                                                                                ($section->sectionEmpScore->status === 'REVIEW' ||
                                                                                    $section->sectionEmpScore->status === 'CONFIRMATION' ||
                                                                                    $section->sectionEmpScore->status === 'COMPLETED' ||
                                                                                    $section->sectionEmpScore->status === 'PROBLEM'))
                                                                        title="The Score cannot be more than the section score {{ $section->sectionScore }}"
                                                                        value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class="form-control mb-3 comment-input"
                                                                        type="text" name="employeeComment"
                                                                        placeholder="Enter your comments"
                                                                        @disabled(isset($section->sectionEmpScore) &&
                                                                                ($section->sectionEmpScore->status === 'REVIEW' ||
                                                                                    $section->sectionEmpScore->status === 'CONFIRMATION' ||
                                                                                    ($section->sectionEmpScore->status === 'COMPLETED') | ($section->sectionEmpScore->status === 'PROBLEM')))
                                                                        value="{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}">
                                                                </div>
                                                                @if (
                                                                    !isset($section->sectionEmpScore) ||
                                                                        ($section->sectionEmpScore->status !== 'REVIEW' &&
                                                                            $section->sectionEmpScore->status !== 'CONFIRMATION' &&
                                                                            $section->sectionEmpScore->status !== 'COMPLETED' &&
                                                                            $section->sectionEmpScore->status !== 'PROBLEM'))
                                                                    <input type="hidden" name="kpiType"
                                                                        value="{{ $kpi->kpiType }}">
                                                                    <input type="hidden" name="sectionEmpScoreId"
                                                                        value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                    <input type="hidden" name="sectionId"
                                                                        value="{{ $section->sectionId }}">
                                                                    <input type="hidden" name="kpiId"
                                                                        value="{{ $kpi->kpiId }}">
                                                                    <button type="submit"
                                                                        class="btn btn-success">Save</button>
                                                                @endif
                                                            </div>
                                                        </form>

                                                        {{-- SUPERVISOR COMMENT AND SCORE WHEN SUPERVISOR HAS SUBMITTED THEIR SCORES --}}
                                                        @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                            <span class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                    Score and Comment</strong></span>
                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3" type="number"
                                                                        readonly name="metricSupScore"
                                                                        placeholder="Enter Score" required
                                                                        value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class="form-control mb-3" type="text"
                                                                        readonly name="supervisorComment"
                                                                        placeholder="Enter your comments" required
                                                                        value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                </div>
                                                            </div>
                                                        @elseif (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'COMPLETED')
                                                            <span class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                    Score and Comment</strong></span>
                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3" type="number"
                                                                        readonly name="metricSupScore"
                                                                        placeholder="Enter Score" required
                                                                        value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class ="form-control mb-3" type="text"
                                                                        readonly name="supervisorComment"
                                                                        placeholder="Enter your comments" required
                                                                        value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                </div>
                                                            </div>
                                                        @elseif(isset($section->sectionEmpScore) && $section->sectionEmpScore->prob == true)
                                                            <span class="mb-2 badge rounded-pill bg-dark"><strong>Probing
                                                                    Score and Comment</strong></span>
                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3" type="number"
                                                                        readonly name="metricSupScore"
                                                                        placeholder="Enter Score" required
                                                                        value="{{ optional($section->sectionEmpScore)->sectionProbScore ?? '' }}">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class ="form-control mb-3" type="text"
                                                                        readonly name="supervisorComment"
                                                                        placeholder="Enter your comments" required
                                                                        value="{{ $section->sectionEmpScore->probComment ?? '' }}">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div></div>
                                                        @endif
                                                    @else
                                                        @foreach ($section->metrics as $metric)
                                                            <div class="metric-card">
                                                                <h5>{{ $metric->metricName }} (<span
                                                                        style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                </h5>
                                                                <p>{{ $metric->metricDescription }}</p>

                                                                <form action="{{ route('self.rating') }}"
                                                                    method="POST" class="metric-form">
                                                                    @csrf
                                                                    <div class="d-flex gap-3">
                                                                        <input type="hidden" name="metricId"
                                                                            value="{{ $metric->metricId }}">
                                                                        <input type="hidden" name="kpiId"
                                                                            value="{{ $kpi->kpiId }}">
                                                                        <input type="hidden" name="sectionId"
                                                                            value="{{ $section->sectionId }}">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3"
                                                                                type="number" name="metricEmpScore"
                                                                                required placeholder="Enter Score"
                                                                                @disabled(isset($metric->metricEmpScore) &&
                                                                                        ($metric->metricEmpScore->status === 'REVIEW' ||
                                                                                            $metric->metricEmpScore->status === 'CONFIRMATION' ||
                                                                                            $section->sectionEmpScore->status === 'COMPLETED' ||
                                                                                            $section->sectionEmpScore->status === 'PROBLEM'))
                                                                                value="{{ optional($metric->metricEmpScore)->metricEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input class="form-control mb-3"
                                                                                type="text" name="metricComment"
                                                                                placeholder="Enter your comments"
                                                                                @disabled (isset($metric->metricEmpScore) &&
                                                                                        ($metric->metricEmpScore->status === 'REVIEW' ||
                                                                                            $metric->metricEmpScore->status === 'CONFIRMATION' ||
                                                                                            $section->sectionEmpScore->status === 'COMPLETED' ||
                                                                                            $section->sectionEmpScore->status === 'PROBLEM'))
                                                                                value="{{ optional($metric->metricEmpScore)->metricComment ?? '' }}">
                                                                        </div>
                                                                        @if (
                                                                            !isset($metric->metricEmpScore) ||
                                                                                ($metric->metricEmpScore->status !== 'REVIEW' &&
                                                                                    $metric->metricEmpScore->status !== 'CONFIRMATION' &&
                                                                                    $section->sectionEmpScore->status !== 'COMPLETED' &&
                                                                                    $section->sectionEmpScore->status !== 'PROBLEM'))
                                                                            <button type="submit"
                                                                                class="btn btn-success">Save</button>
                                                                        @endif
                                                                    </div>
                                                                </form>

                                                                {{-- SUPERVISOR COMMENT AND SCORE WHEN SUPERVISOR HAS SUBMITTED THEIR SCORES --}}
                                                                @if (isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')
                                                                    <span
                                                                        class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                            Score and Comment</strong></span>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3"
                                                                                type="number" readonly
                                                                                name="metricSupScore"
                                                                                placeholder="Enter Score" required
                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input class="form-control mb-3"
                                                                                type="text" readonly
                                                                                name="supervisorComment"
                                                                                placeholder="Enter your comments"
                                                                                required
                                                                                value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                @elseif (isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'COMPLETED')
                                                                    <span
                                                                        class="mb-2 badge rounded-pill bg-success"><strong>Supervisor
                                                                            Score and Comment</strong></span>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3"
                                                                                type="number" readonly
                                                                                name="metricSupScore"
                                                                                placeholder="Enter Score" required
                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input class="form-control mb-3"
                                                                                type="text" readonly
                                                                                name="supervisorComment"
                                                                                placeholder="Enter your comments"
                                                                                required
                                                                                value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                @elseif(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob == true)
                                                                    <span
                                                                        class="mb-2 badge rounded-pill bg-dark"><strong>Probing
                                                                            Score and Comment</strong></span>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3"
                                                                                type="number" readonly
                                                                                name="metricSupScore"
                                                                                placeholder="Enter Score" required
                                                                                value="{{ optional($metric->metricEmpScore)->metricProbScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input class="form-control mb-3"
                                                                                type="text" readonly
                                                                                name="supervisorComment"
                                                                                placeholder="Enter your comments"
                                                                                required
                                                                                value="{{ $metric->metricEmpScore->probComment ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div></div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    <p></p>
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
                                    <button type="button" data-bs-toggle="modal" class="btn btn-primary"
                                        data-bs-target=".bs-delete-modal-lg">Submit Appraisal</button>
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
                                                <form action="{{ route('submit.appraisal') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->batchId }}">
                                                    <input type="hidden" name="status" value="REVIEW">
                                                    <div class="d-grid">
                                                        <div class="mt-5">
                                                            <button type="submit" id="submitReviewButton"
                                                                class="btn btn-success">Submit Appraisal
                                                                For Review</button>
                                                        </div>
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

                                        <a href="{{ route('show.employee.probe', $kpi->kpiId) }}"
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
                                                        value="{{ $kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->batchId }}">
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


                            <script>
                                const scoreInputs = document.querySelectorAll('.score-input');
                                const submitButton = document.getElementById('submitReviewButton');

                                function checkScores() {
                                    let allFilled = true;

                                    scoreInputs.forEach(input => {
                                        // Check if the input value is not empty and greater than 0
                                        if (input.value === '' || input.value <= 0) {
                                            allFilled = false;
                                        }
                                    });

                                    // Enable or disable the submit button based on the check
                                    submitButton.disabled = !allFilled;
                                }

                                // Initial check to set the button state on page load
                                checkScores();

                                // Add event listeners to score inputs to check scores on input change
                                scoreInputs.forEach(input => {
                                    input.addEventListener('input', checkScores);
                                });
                            </script>
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
