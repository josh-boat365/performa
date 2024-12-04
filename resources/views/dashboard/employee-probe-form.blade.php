<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="#">Probe</a> > Select Sections or Metrics to Probe
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
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                <form action="{{ route('submit.employee.probe') }}" method="POST">
                                    @csrf
                                    @if (isset($appraisal) && !empty($appraisal))
                                        @foreach ($appraisal as $kpi)
                                            <div class="kpi">
                                                <h3>KPI: {{ $kpi->kpiName }}</h3>
                                                <p>{{ $kpi->kpiDescription }}</p>

                                                @if (isset($kpi->sections) && count($kpi->sections) > 0)
                                                    @foreach ($kpi->sections as $sectionId => $section)
                                                        <div class="section-card" @style(['margin-top: 2rem'])>
                                                            <h4>Section: {{ $section->sectionName }}
                                                                (<span
                                                                    style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                            </h4>
                                                            <p>{{ $section->sectionDescription }}</p>

                                                            @if (empty($section->metrics))
                                                                {{--  <form action="{{ route('self.rating') }}" method="POST"
                                                                    class="section-form">
                                                                    @csrf  --}}
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3"
                                                                                type="number" name="sectionEmpScore"
                                                                                readonly placeholder="Enter Score"
                                                                                value="{{ $section->sectionEmpScore->sectionEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <input class="form-control mb-3"
                                                                                type="text" name="employeeComment"
                                                                                placeholder="Enter your comments"
                                                                                readonly
                                                                                value="{{ $section->sectionEmpScore->employeeComment ?? '' }}">
                                                                        </div>
                                                                        <input type="hidden" name="sectionEmpScoreId"
                                                                            value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                        <input type="hidden" name="sectionId"
                                                                            value="{{ $section->sectionId }}">
                                                                        <input type="hidden" name="kpiId"
                                                                            value="{{ $kpi->kpiId }}">

                                                                    </div>
                                                                {{--  </form>  --}}

                                                                <span class="mb-2 badge rounded-pill bg-dark"><strong>Supervisor
                                                                        Score and
                                                                        Comment</strong></span>

                                                                {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3" type="number"
                                                                            readonly name="metricSupScore"
                                                                            placeholder="Enter Score" readonly
                                                                            value="{{ $section->sectionEmpScore->sectionSupScore == 0 ? '' : $section->sectionEmpScore->sectionSupScore }}">
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <input class="form-control mb-3" type="text"
                                                                            readonly name="supervisorComment"
                                                                            placeholder="Enter your comments" readonly
                                                                            value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                    </div>
                                                                    <div class="form-check form-check-dark mb-3">
                                                                        <input @style(['width:1.4rem; height:1.4rem'])
                                                                            class="form-check-input" type="checkbox"
                                                                            name="scoreId" readonly
                                                                            value="{{ $section->sectionEmpScore->id }}"
                                                                            id="formCheckcolor4">
                                                                    </div>


                                                            @endif

                                                            @if (isset($section->metrics) && count($section->metrics) > 0)
                                                                <ul>
                                                                    @foreach ($section->metrics as $metricId => $metric)
                                                                        <li>
                                                                            <strong>{{ $metric->metricName }}</strong>:
                                                                            (<span
                                                                                style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                            <p>{{ $metric->metricDescription }}</p>
                                                                            {{--  ==== EMPLOYEE SCORING WITH COMMENT INPUT ====  --}}

                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input class="form-control mb-3"
                                                                                            type="number"
                                                                                            name="metricEmpScore"
                                                                                            placeholder="Enter Score"
                                                                                            readonly
                                                                                            value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <input class="form-control mb-3"
                                                                                            type="text"
                                                                                            name="employeeComment"
                                                                                            placeholder="Enter your comments"
                                                                                            readonly
                                                                                            value="{{ $metric->metricEmpScore->employeeComment ?? '' }}">
                                                                                    </div>


                                                                                </div>



                                                                            <span
                                                                                class="mb-2 badge rounded-pill bg-dark"><strong>Supervisor
                                                                                    Score and
                                                                                    Comment</strong></span>

                                                                            {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}

                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input class="form-control mb-3"
                                                                                            type="number" readonly
                                                                                            name="metricSupScore"
                                                                                            placeholder="Enter Score"
                                                                                            readonly
                                                                                            value="{{ $metric->metricEmpScore->metricSupScore == 0 ? '' : $metric->metricEmpScore->metricSupScore }}">
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <input class="form-control mb-3"
                                                                                            type="text" readonly
                                                                                            name="supervisorComment"
                                                                                            placeholder="Enter your comments"
                                                                                            readonly
                                                                                            value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                                    </div>


                                                                                    <div
                                                                                        class="form-check form-check-dark mb-3">
                                                                                        <input @style(['width:1.4rem; height:1.4rem'])
                                                                                            class="form-check-input"
                                                                                            type="checkbox"
                                                                                            name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id }}"
                                                                                            id="formCheckcolor4">
                                                                                    </div>
                                                                                </div>




                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p>No metrics available for this section.</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p>No sections available for this KPI.</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="float-end">
                                        <div class="mt-5 d-flex gap-3">
                                            <button type="submit" class="btn btn-dark">Submit Probe</button>
                                        </div>
                                    </div>
                                </form>
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
