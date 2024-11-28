<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="#">My KPIs</a> > Your Appraisal
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
                            style="width: {{ session('progress') }}%;" aria-valuenow="{{ session('progress') }}"
                            aria-valuemin="0" aria-valuemax="{{ session('progress') }}">{{ session('progress') }}%</div>
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
                                {{--  <form id="kpiReviewForm" action="#" method="POST">  --}}
                                {{--  @csrf  --}}
                                @if (isset($appraisal) && !empty($appraisal))
                                    @foreach ($appraisal as $kpi)

                                        <div class="kpi">
                                            <h3>KPI: {{ $kpi->kpiName }}</h3>
                                            <p>{{ $kpi->kpiDescription }}</p>

                                            @if (isset($kpi->sections) && count($kpi->sections) > 0)
                                                @foreach ($kpi->sections as $sectionId => $section)
                                                    <div class="section-card" style="margin-top: 2rem;">
                                                        <h4>Section: {{ $section->sectionName }}
                                                            (<span
                                                                @style(['color: #c80f0f'])>{{ $section->sectionScore }}</span>)
                                                        </h4>
                                                        <p>{{ $section->sectionDescription }}</p>

                                                        @if (empty($section->metrics))
                                                            <form action="{{ route('self.rating') }}" method="POST"
                                                                class="section-form">
                                                                @csrf
                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3" type="number"
                                                                            name="sectionEmpScore" required
                                                                            placeholder="Enter Score"
                                                                            max="{{ $section->sectionScore }}"
                                                                            title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                            value="{{ $section->sectionEmpScore->sectionEmpScore ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <input class="form-control mb-3" type="text"
                                                                            name="employeeComment"
                                                                            placeholder="Enter your comments"
                                                                            value="{{ $section->sectionEmpScore->employeeComment ?? '' }}">
                                                                    </div>
                                                                    <input type="hidden" name="sectionEmpScoreId"
                                                                        value="{{ $section->sectionEmpScore->id ?? '' }}">
                                                                    <input type="hidden" name="sectionId"
                                                                        value="{{ $section->sectionId }}">
                                                                    <input type="hidden" name="kpiId"
                                                                        value="{{ $kpi->kpiId }}">
                                                                    <button type="submit" @style(['height: fit-content'])
                                                                        class="btn btn-primary">Save</button>
                                                                </div>
                                                            </form>
                                                        @endif

                                                        @if (isset($section->metrics) && count($section->metrics) > 0)
                                                            <ul>
                                                                @foreach ($section->metrics as $metricId => $metric)
                                                                    <li>
                                                                        <strong>{{ $metric->metricName }}</strong>:
                                                                        (<span
                                                                            @style(['color: #c80f0f'])>{{ $metric->metricScore }}</span>)
                                                                        <p>{{ $metric->metricDescription }}</p>
                                                                        <form action="{{ route('self.rating') }}"
                                                                            method="POST" class="metric-form">
                                                                            @csrf
                                                                            <div class="d-flex gap-3">
                                                                                <div class="col-md-2">
                                                                                    <input class="form-control mb-3"
                                                                                        type="number"
                                                                                        name="metricEmpScore"
                                                                                        placeholder="Enter Score"
                                                                                        required
                                                                                        max="{{ $metric->metricScore }}"
                                                                                        title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                        value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                </div>
                                                                                <div class="col-md-9">
                                                                                    <input class="form-control mb-3"
                                                                                        type="text"
                                                                                        name="employeeComment"
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
                                                                                <button type="submit" @style(['height: fit-content'])
                                                                                    class="btn btn-primary">Save</button>
                                                                            </div>
                                                                        </form>
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
                                        <hr class="mt-10">
                                        {{--  {{ dd($kpi) }}  --}}
                                        <form action="" method="POST">
                                            @csrf
                                            <div class="float-end">
                                                <div class="mt-5 d-flex gap-3">
                                                    <button type="submit" id="submitReviewButton"
                                                        class="btn btn-success">Submit
                                                        KPI For Review</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endforeach
                                @endif

                                {{--  </form>  --}}


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
