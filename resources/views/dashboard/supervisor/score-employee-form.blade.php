<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="#">Employee KPIs</a> > Score Employee
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
                        <h4 class="card-title mb-4">Supervisor Evaluation Form</h4>

                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && !empty($appraisal))

                                    @foreach ($appraisal as $kpi)
                                        <div class="kpi">
                                            @if (isset($kpi->sections) && count($kpi->sections) > 0)
                                                @foreach ($kpi->sections as $section)
                                                    <div class="section-card" style="margin-top: 2rem;">
                                                        <h4>{{ $section->sectionName }}
                                                            (<span
                                                                style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                        </h4>
                                                        <p>{{ $section->sectionDescription }}</p>

                                                        @if (isset($section->metrics) && count($section->metrics) == 0)
                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3 score-input"
                                                                        type="number" name="sectionEmpScore" required
                                                                        placeholder="Enter Score"
                                                                        max="{{ $section->sectionScore }}" readonly
                                                                        title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                        @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'REVIEW')
                                                                        value="{{ $section->sectionEmpScore->sectionEmpScore ?? '' }}">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class="form-control mb-3 comment-input"
                                                                        type="text" name="employeeComment"
                                                                        placeholder="Enter your comments" readonly
                                                                        @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'REVIEW')
                                                                        value="{{ $section->sectionEmpScore->employeeComment ?? '' }}">
                                                                </div>

                                                            </div>


                                                            <span class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                    Score and
                                                                    Comment</strong></span>

                                                            {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}
                                                            <form action="{{ route('supervisor.rating') }}"
                                                                method="POST" class="section-form">
                                                                @csrf
                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3 score-input"
                                                                            type="number" name="sectionSupScore"
                                                                            required placeholder="Enter Score"
                                                                            max="{{ $section->sectionScore }}"
                                                                            @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                            title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                            value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <input class="form-control mb-3 comment-input"
                                                                            type="text" name="supervisorComment"
                                                                            placeholder="Enter your comments"
                                                                            @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                            value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                    </div>
                                                                    @if (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                        <div></div>
                                                                    @else
                                                                        <input type="hidden" name="scoreId"
                                                                            value="{{ $section->sectionEmpScore->id ?? '' }}">

                                                                        <button type="submit"
                                                                            style="height: fit-content"
                                                                            class="btn btn-primary">Save</button>
                                                                    @endif
                                                                </div>
                                                            </form>
                                                        @else
                                                            <div></div>
                                                        @endif

                                                        @if (isset($section->metrics) && count($section->metrics) > 0)
                                                            <ul>
                                                                @foreach ($section->metrics as $metric)
                                                                    <li>
                                                                        <strong>{{ $metric->metricName }}</strong>:
                                                                        (<span
                                                                            style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                        <p>{{ $metric->metricDescription }}</p>

                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input
                                                                                    class="form-control mb-3 score-input"
                                                                                    type="number" name="metricEmpScore"
                                                                                    placeholder="Enter Score" required
                                                                                    max="{{ $metric->metricScore }}"
                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION', 'PROBLEM']))
                                                                                    title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                    value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <input
                                                                                    class="form-control mb-3 comment-input"
                                                                                    type="text"
                                                                                    name="employeeComment"
                                                                                    placeholder="Enter your comments"
                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION', 'PROBLEM']))
                                                                                    value="{{ $metric->metricEmpScore->employeeComment ?? '' }}">
                                                                            </div>

                                                                        </div>

                                                                        <span
                                                                            class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                                Score and
                                                                                Comment</strong></span>

                                                                        {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}
                                                                        <form action="{{ route('supervisor.rating') }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            <div class="d-flex gap-3">
                                                                                <div class="col-md-2">
                                                                                    <input class="form-control mb-3"
                                                                                        type="number"
                                                                                        name="metricSupScore"
                                                                                        max="{{ $metric->metricScore }}"
                                                                                        @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                        title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                        placeholder="Enter Score"
                                                                                        required
                                                                                        value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                                </div>
                                                                                <div class="col-md-9">
                                                                                    <input class="form-control mb-3"
                                                                                        type="text"
                                                                                        name="supervisorComment"
                                                                                        @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                        placeholder="Enter your comments"
                                                                                        value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                                </div>

                                                                                <input type="hidden" name="scoreId"
                                                                                    value="{{ $metric->metricEmpScore->id ?? '' }}">


                                                                                <button type="submit"
                                                                                    style="height: fit-content"
                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                    class="btn btn-primary">Save</button>

                                                                            </div>
                                                                        </form>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p></p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <p></p>
                                            @endif
                                        </div>
                                    @endforeach
                                    <hr class="mt-10">

                                    @if (isset($metric->metricEmpScore) &&
                                            in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM', 'COMPLETED']))
                                        <div></div>
                                    @else
                                        <div class="float-end">
                                            <button type="button" data-bs-toggle="modal" class="btn btn-primary"
                                                data-bs-target=".bs-delete-modal-lg" id="submitAppraisalButton"
                                                disabled>Submit Appraisal</button>
                                        </div>

                                        <div class="modal fade bs-delete-modal-lg" tabindex="-1" role="dialog"
                                            aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-md modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                            Appraisal
                                                            Submit</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="text-center mb-4">Are you sure you want to
                                                            <b>Submit</b> employee <b>Appraisal</b> for
                                                            <b>Confirmation</b>?

                                                        </h4>
                                                        <form action="{{ route('submit.appraisal') }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="kpiId"
                                                                value="{{ $kpi->kpiId }}">
                                                            <input type="hidden" name="batchId"
                                                                value="{{ $kpi->batchId }}">
                                                            <input type="hidden" name="status"
                                                                value="CONFIRMATION">
                                                            <div class="d-grid">
                                                                <div class="mt-5 ">
                                                                    <button type="submit" id="submitReviewButton"
                                                                        class="btn btn-success">Submit
                                                                        Employee Appraisal
                                                                        For Confirmation</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                            </div>


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
