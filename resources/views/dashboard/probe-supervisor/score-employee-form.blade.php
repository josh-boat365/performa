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


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Probe Supervisor Evaluation Form</h4>

                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $kpi)
                                        <div class="kpi">

                                            @foreach ($kpi->activeSections as $section)
                                                <div class="card border border-primary" style="border-radius: 10px; ">
                                                    <div class="card-body"
                                                        style=" {{ $section->metrics->isEmpty() ? 'background-color: #0000ff0d;' : '' }}">
                                                        <div class="section-card" style="margin-top: 2rem;">
                                                            <h4 class="card-title">{{ $section->sectionName }} (<span
                                                                    style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                            </h4>
                                                            <p>{{ $section->sectionDescription }}</p>

                                                            @if ($section->metrics->isEmpty())
                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3 score-input"
                                                                            type="number" name="sectionEmpScore"
                                                                            required placeholder="Enter Score"
                                                                            min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                            max="{{ $section->sectionScore }}" readonly
                                                                            title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                            @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'PROBLEM')
                                                                            value="{{ $section->sectionEmpScore->sectionEmpScore ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <input class="form-control mb-3 comment-input"
                                                                            type="text" name="employeeComment"
                                                                            placeholder="Enter your comments" readonly
                                                                            @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'PROBLEM')
                                                                            value="{{ $section->sectionEmpScore->employeeComment ?? '' }}">
                                                                    </div>

                                                                </div>


                                                                <span
                                                                    class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                        Score and
                                                                        Comment</strong></span>

                                                                {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3 score-input"
                                                                            type="number" name="sectionSupScore"
                                                                            required placeholder="Enter Score"
                                                                            min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
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
                                                                    <div class="form-check form-check-dark mb-3">
                                                                        <input @style(['width:1.8rem; height:2rem'])
                                                                            class="form-check-input" type="checkbox"
                                                                            disabled name="scoreId"
                                                                            @checked(isset($section->sectionEmpScore) && $section->sectionEmpScore->prob === true)
                                                                            id="formCheckcolor4">
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


                                                                {{--  ==== PROBING  SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                                <form action="{{ route('prob.store') }}" method="POST"
                                                                    class="section-form">
                                                                    @csrf
                                                                    @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->prob === false)
                                                                        <div></div>
                                                                    @else
                                                                        <span
                                                                            class="mb-2 badge rounded-pill bg-dark"><strong>Probing
                                                                                Supervisor
                                                                                Score and
                                                                                Comment</strong></span>
                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input
                                                                                    class="form-control mb-3 score-input"
                                                                                    type="number"
                                                                                    name="sectionProbScore" required
                                                                                    placeholder="Enter Score"
                                                                                    min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                                    max="{{ $section->sectionScore }}"
                                                                                    @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED']))
                                                                                    title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                                    value="{{ optional($section->sectionEmpScore)->sectionProbScore == 0 ? '' : optional($section->sectionEmpScore)->sectionProbScore }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <input
                                                                                    class="form-control mb-3 comment-input"
                                                                                    type="text" name="probComment"
                                                                                    placeholder="Enter your comments"
                                                                                    @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED']))
                                                                                    value="{{ $section->sectionEmpScore->probComment ?? '' }}">
                                                                            </div>
                                                                            @if (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['COMPLETED']))
                                                                                <div></div>
                                                                            @else
                                                                                <input type="hidden" name="scoreId"
                                                                                    value="{{ $section->sectionEmpScore->id ?? '' }}">

                                                                                <button type="submit"
                                                                                    style="height: fit-content"
                                                                                    class="btn btn-primary">Save</button>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </form>
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

                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input
                                                                                            class="form-control mb-3 score-input"
                                                                                            type="number"
                                                                                            name="metricEmpScore"
                                                                                            placeholder="Enter Score"
                                                                                            required
                                                                                            min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                                            max="{{ $metric->metricScore }}"
                                                                                            readonly
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                            title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                            value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <input
                                                                                            class="form-control mb-3 comment-input"
                                                                                            type="text"
                                                                                            name="employeeComment"
                                                                                            placeholder="Enter your comments"
                                                                                            readonly
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                            value="{{ $metric->metricEmpScore->employeeComment ?? '' }}">
                                                                                    </div>

                                                                                </div>

                                                                                <span
                                                                                    class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                                        Score and
                                                                                        Comment</strong></span>

                                                                                {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input
                                                                                            class="form-control mb-3"
                                                                                            type="number"
                                                                                            name="metricSupScore"
                                                                                            min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                                            max="{{ $metric->metricScore }}"
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                            title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                            placeholder="Enter Score"
                                                                                            required
                                                                                            value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <input
                                                                                            class="form-control mb-3"
                                                                                            type="text"
                                                                                            name="supervisorComment"
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                            placeholder="Enter your comments"
                                                                                            value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                                    </div>
                                                                                    <div
                                                                                        class="form-check form-check-dark mb-3">
                                                                                        <input @style(['width:1.8rem; height:2rem'])
                                                                                            class="form-check-input"
                                                                                            type="checkbox"
                                                                                            name="scoreId" disabled
                                                                                            @checked(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === true)
                                                                                            id="formCheckcolor4">
                                                                                    </div>

                                                                                    @if (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                        <div></div>
                                                                                    @else
                                                                                        <input type="hidden"
                                                                                            name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">

                                                                                        <button type="submit"
                                                                                            style="height: fit-content"
                                                                                            class="btn btn-primary">Save</button>
                                                                                    @endif

                                                                                </div>


                                                                                {{--  ==== PROBING  SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                                                <form
                                                                                    action="{{ route('prob.store') }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    @if (isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === false)
                                                                                        <div></div>
                                                                                    @else
                                                                                        <span
                                                                                            class="mb-2 badge rounded-pill bg-dark"><strong>Probing
                                                                                                Supervisor
                                                                                                Score and
                                                                                                Comment</strong></span>
                                                                                        <div class="d-flex gap-3">
                                                                                            <div class="col-md-2">
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="number"
                                                                                                    name="metricProbScore"
                                                                                                    min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                                                                                                    max="{{ $metric->metricScore }}"
                                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['COMPLETED']))
                                                                                                    title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                                    placeholder="Enter Score"
                                                                                                    required
                                                                                                    {{--  value"{{ optional($metric->metricEmpScore)->metricProbScore === 0 ? '' : optional($metric->metricEmpScore)->metricProbScore }}">  --}}
                                                                                                    value="{{ optional($metric->metricEmpScore)->metricProbScore ?: '' }}">
                                                                                            </div>
                                                                                            <div class="col-md-9">
                                                                                                <input
                                                                                                    class="form-control mb-3"
                                                                                                    type="text"
                                                                                                    name="probComment"
                                                                                                    @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['COMPLETED']))
                                                                                                    placeholder="Enter your comments"
                                                                                                    value="{{ $metric->metricEmpScore->probComment ?? '' }}">
                                                                                            </div>

                                                                                            @if (isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['COMPLETED']))
                                                                                                <div></div>
                                                                                            @else
                                                                                                <input type="hidden"
                                                                                                    name="scoreId"
                                                                                                    value="{{ $metric->metricEmpScore->id ?? '' }}">

                                                                                                <button type="submit"
                                                                                                    style="height: fit-content"
                                                                                                    class="btn btn-primary">Save</button>
                                                                                            @endif

                                                                                        </div>
                                                                                    @endif
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
                                    <p>No KPIs available for this employee.</p>
                                @endif
                            </div>

                            <hr class="mt-10">
                            @if (isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'COMPLETED')
                                <div></div>
                            @else
                                <div class="float-end">
                                    <button type="button" data-bs-toggle="modal" class="btn btn-dark"
                                        data-bs-target=".bs-delete-modal-lg"id="submitAppraisalButton" disabled>Resolve
                                        Employee
                                        Probe</button>
                                </div>

                                <div class="modal fade bs-delete-modal-lg" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                    Appraisal
                                                    Submit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4">Are you sure you want to
                                                    <b>Submit to resolve</b> employee <b>Appraisal</b>
                                                    <b>Probe</b>?

                                                </h4>
                                                <form action="{{ route('submit.appraisal') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="status" value="COMPLETED">
                                                    <div class="d-grid">

                                                        <button type="submit" class="btn btn-success">Submit
                                                            Employee Appraisal
                                                            For Confirmation</button>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <script>
                                // Function to check if all score inputs are filled
                                function checkInputs() {
                                    const scoreInputs = document.querySelectorAll('input[type="number"][name*="ProbScore"]');
                                    const allFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');

                                    // Enable or disable the submit button based on input values
                                    document.getElementById('submitAppraisalButton').disabled = !allFilled;
                                }

                                // Attach event listeners to all score inputs
                                document.querySelectorAll('input[type="number"][name*="ProbScore"]').forEach(input => {
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
