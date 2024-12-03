<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('show.batch.kpi', $batchId) }}">My Kpis</a> Available KPIs For You
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
                                                                <input type="hidden" name="sectionEmpScoreId"
                                                                    value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                <input type="hidden" name="sectionId"
                                                                    value="{{ $section->sectionId }}">
                                                                <input type="hidden" name="kpiId"
                                                                    value="{{ $kpi->kpiId }}">

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
                                                                            <input type="hidden"
                                                                                name="metricEmpScoreId"
                                                                                value="{{ $metric->metricEmpScore->id ?? '' }}">
                                                                            <input type="hidden" name="metricId"
                                                                                value="{{ $metric->metricId }}">
                                                                            <input type="hidden" name="sectionId"
                                                                                value="{{ $section->sectionId }}">
                                                                            <input type="hidden" name="kpiId"
                                                                                value="{{ $kpi->kpiId }}">

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
                                                                                    value="{{ old('metricSupScore.' . $metricId . '.' . '.' . $sectionId) }}">
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <input class="form-control mb-3"
                                                                                    type="text" readonly
                                                                                    name="supervisorComment"
                                                                                    placeholder="Enter your comments"
                                                                                    required
                                                                                    value="{{ old('supervisorComment.' . $metricId . '.' . '.' . $sectionId) }}">
                                                                            </div>
                                                                            <input type="hidden" name="metricId"
                                                                                value="{{ $metric->metricId }}">
                                                                            <input type="hidden" name="sectionId"
                                                                                value="{{ $sectionId }}">

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
                                                <form action="{{ route('accept.rating') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->batchId }}">
                                                    <input type="hidden" name="status" value="COMPLETED">
                                                    <button type="submit" class="btn btn-primary">Accept</button>
                                                </form>

                                                <a href="{{ route('show.employee.probe', $kpi->kpiId) }}"
                                                    class="btn btn-warning">Probe</a>
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
