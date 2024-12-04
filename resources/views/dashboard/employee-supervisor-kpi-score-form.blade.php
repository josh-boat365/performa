<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('show.batch.kpi', $batchId) }}">My Kpis</a>
                        Available KPIs For You
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Progress</h4>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
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

                        <div class="p-3 text-muted">
                            <div id="kpi-form">

                                @if (isset($appraisal) && !empty($appraisal))
                                    @foreach ($appraisal as $kpi)
                                        <div class="kpi">
                                            <h3>KPI: {{ $kpi->kpiName }}</h3>
                                            <p>{{ $kpi->kpiDescription }}</p>

                                            @if (isset($kpi->sections) && count($kpi->sections) > 0)
                                                @foreach ($kpi->sections as $sectionId => $section)
                                                    <div class="section-card" @style(['margin-top: 2rem'])>
                                                        <h4>Section: {{ $section->sectionName }}
                                                            ({{ $section->sectionScore }})
                                                        </h4>
                                                        <p>{{ $section->sectionDescription }}</p>

                                                        @if (empty($section->metrics))
                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3 score-input"
                                                                        type="number" name="sectionEmpScore" required
                                                                        placeholder="Enter Score" readonly
                                                                        max="{{ $section->sectionScore }}"
                                                                        title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                        value="{{ $section->sectionEmpScore->sectionEmpScore ?? '' }}">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class="form-control mb-3 comment-input"
                                                                        type="text" name="employeeComment" readonly
                                                                        placeholder="Enter your comments"
                                                                        value="{{ $section->sectionEmpScore->employeeComment ?? '' }}">
                                                                </div>


                                                            </div>
                                                            {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                            <div class="d-flex gap-3">
                                                                <div class="col-md-2">
                                                                    <input class="form-control mb-3" type="number"
                                                                        readonly name="metricSupScore"
                                                                        placeholder="Enter Score" required
                                                                        value="{{ $section->sectionEmpScore->sectionSupScore == 0 ? '' : $section->sectionEmpScore->sectionSupScore }} ">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <input class="form-control mb-3" type="text"
                                                                        readonly name="supervisorComment"
                                                                        placeholder="Enter your comments" required
                                                                        value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                </div>

                                                            </div>
                                                        @endif

                                                        @if (isset($section->metrics) && count($section->metrics) > 0)
                                                            <ul>
                                                                @foreach ($section->metrics as $metricId => $metric)
                                                                    <li>
                                                                        <strong>{{ $metric->metricName }}</strong>:
                                                                        {{ $metric->metricScore }}
                                                                        <p>{{ $metric->metricDescription }}</p>
                                                                        {{--  ==== EMPLOYEE SCORING WITH COMMENT INPUT ====  --}}

                                                                        <div class="d-flex gap-3">
                                                                            <div class="col-md-2">
                                                                                <input
                                                                                    class="form-control mb-3 score-input"
                                                                                    type="number" name="metricEmpScore"
                                                                                    placeholder="Enter Score" required
                                                                                    readonly
                                                                                    max="{{ $metric->metricScore }}"
                                                                                    title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                    value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <input
                                                                                    class="form-control mb-3 comment-input"
                                                                                    type="text"
                                                                                    name="employeeComment" readonly
                                                                                    placeholder="Enter your comments"
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
                                                                                <input class="form-control mb-3"
                                                                                    type="number" readonly
                                                                                    name="metricSupScore"
                                                                                    placeholder="Enter Score" required
                                                                                    value="{{ $metric->metricEmpScore->metricSupScore == 0 ? '' : $metric->metricEmpScore->metricSupScore }}">
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
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                            @endif
                                        </div>

                                        <hr class="mt-10">

                                        <div class="float-end">
                                            <div class="mt-5 d-flex gap-3">

                                                <button type="button" data-bs-toggle="modal" class="btn btn-primary"
                                                    data-bs-target=".bs-delete-modal-lg">Accept</button>

                                                <a href="{{ route('show.employee.probe', $kpi->kpiId) }}"
                                                    class="btn btn-warning">Probe</a>

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg"
                                                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm Supervisor Score</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h4 class="text-center mb-4">Are you sure you want to
                                                                    <b>Accept</b> this scores from your
                                                                    <b>Supervisor?</b>
                                                                </h4>
                                                                <form action="{{ route('accept.rating') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="kpiId"
                                                                        value="{{ $kpi->kpiId }}">
                                                                    <input type="hidden" name="batchId"
                                                                        value="{{ $kpi->batchId }}">
                                                                    <input type="hidden" name="status"
                                                                        value="COMPLETED">
                                                                    <div class="d-grid">
                                                                        <button type="submit"
                                                                            class="btn btn-success">Yes,
                                                                            Accept </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

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
