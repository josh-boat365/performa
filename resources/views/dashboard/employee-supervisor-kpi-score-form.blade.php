<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('show-batch') }}">Batch</a> Available KPIs For You
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
                                <form action="" method="POST">
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
                                                                <form action="{{ route('self.rating') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="d-flex gap-3">
                                                                        <input class="form-control mb-3" type="number"
                                                                            name="sectionEmpScore" required
                                                                            placeholder="Enter Score"
                                                                            value="{{ old('sectionEmpScore.' . $sectionId) }}">
                                                                        <input type="hidden" name="sectionId"
                                                                            value="{{ $sectionId }}">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Save</button>
                                                                    </div>
                                                                </form>
                                                                <form action="{{ route('self.rating') }}"
                                                                    method="POST">
                                                                    <div class="d-flex gap-3">
                                                                        <input class="form-control mb-3" type="text"
                                                                            name="employeeComment"
                                                                            placeholder="Enter your comments" required
                                                                            value="{{ old('employeeComment.' . $sectionId) }}">
                                                                        <input type="hidden" name="sectionId"
                                                                            value="{{ $sectionId }}">
                                                                        <button @style(['height: fit-content']) type="submit"
                                                                            class="btn btn-secondary">Submit
                                                                            Comment</button>
                                                                    </div>
                                                                </form>
                                                            @endif

                                                            @if (isset($section->metrics) && count($section->metrics) > 0)
                                                                <ul>
                                                                    @foreach ($section->metrics as $metricId => $metric)
                                                                        <li>
                                                                            <strong>{{ $metric->metricName }}</strong>:
                                                                            {{ $metric->metricScore }}
                                                                            <p>{{ $metric->metricDescription }}</p>
                                                                            {{--  ==== EMPLOYEE SCORING WITH COMMENT INPUT ====  --}}
                                                                            <form action="{{ route('self.rating') }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input class="form-control mb-3"
                                                                                            type="number"
                                                                                            name="metricEmpScore"
                                                                                            placeholder="Enter Score"
                                                                                            required
                                                                                            value="{{ old('metricEmpScore.' . $metricId . '.' . '.' . $sectionId) }}">
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <input class="form-control mb-3"
                                                                                            type="text"
                                                                                            name="employeeComment"
                                                                                            placeholder="Enter your comments"
                                                                                            required
                                                                                            value="{{ old('employeeComment.' . $metricId . '.' . '.' . $sectionId) }}">
                                                                                    </div>
                                                                                    <input type="hidden"
                                                                                        name="metricId"
                                                                                        value="{{ $metric->metricId }}">
                                                                                    <input type="hidden"
                                                                                        name="sectionId"
                                                                                        value="{{ $sectionId }}">
                                                                                    <button @style(['height: fit-content'])
                                                                                        type="submit"
                                                                                        class="btn btn-primary">Save</button>
                                                                                </div>

                                                                            </form>

                                                                            <span
                                                                                class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                                    Score and
                                                                                    Comment</strong></span>

                                                                            {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}
                                                                            <form action="{{ route('self.rating') }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input class="form-control mb-3"
                                                                                            type="number" readonly
                                                                                            name="metricSupScore"
                                                                                            placeholder="Enter Score"
                                                                                            required
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
                                                                                    <input type="hidden"
                                                                                        name="metricId"
                                                                                        value="{{ $metric->metricId }}">
                                                                                    <input type="hidden"
                                                                                        name="sectionId"
                                                                                        value="{{ $sectionId }}">

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

                                            <div class="float-end">
                                                <div class="mt-5 d-flex gap-3">
                                                    <button type="submit" class="btn btn-primary">Accept</button>
                                                    <a href="{{ route('show.employee.probe',$kpi->kpiId) }}" class="btn btn-warning">Probe</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

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