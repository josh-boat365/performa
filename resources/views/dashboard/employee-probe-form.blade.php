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
                                @if (isset($appraisal) && !empty($appraisal))
                                    @foreach ($appraisal as $kpi)
                                        <div class="kpi">
                                            @if (isset($kpi->sections) && count($kpi->sections) > 0)
                                                @foreach ($kpi->sections as $sectionId => $section)
                                                    <div class="section-card" style="margin-top: 2rem;">
                                                        <h4>{{ $section->sectionName }} (<span
                                                                style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                        </h4>
                                                        <p>{{ $section->sectionDescription }}</p>

                                                        @if (empty($section->metrics))
                                                            <form action="{{ route('submit.employee.probe') }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3" type="number"
                                                                            readonly placeholder="Enter Score"
                                                                            @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                                            value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <input class="form-control mb-3" type="text"
                                                                            name="employeeComment"
                                                                            placeholder="Enter your comments"
                                                                            @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'COMPLETED')
                                                                            value="{{ $section->sectionEmpScore->employeeComment ?? '' }}">
                                                                    </div>
                                                                </div>

                                                                <span class="mb-2 badge rounded-pill bg-dark"><strong>Supervisor
                                                                        Score and Comment</strong></span>

                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3" type="number"
                                                                            readonly placeholder="Enter Score"
                                                                            @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                                            value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input class="form-control" type="text"
                                                                            readonly placeholder="Enter your comments"
                                                                            @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                                            value="{{ $section->sectionEmpScore->supervisorComment ?? '' }}">
                                                                    </div>
                                                                    <div class="form-check form-check-dark mb-3">
                                                                        <input @style(['width:1.8rem; height:2rem'])
                                                                            class="form-check-input" type="checkbox"
                                                                            name="scoreId" @checked($section->sectionEmpScore->prob === true)
                                                                            value="{{ $section->sectionEmpScore->id }}"
                                                                            id="formCheckcolor4">
                                                                    </div>
                                                                    <input type="hidden" name="sectionId"
                                                                        value="{{ $section->sectionId }}">
                                                                    <input type="hidden" name="kpiId"
                                                                        value="{{ $kpi->kpiId }}">
                                                                    <input type="hidden" name="kpiType"
                                                                        value="{{ $kpi->kpiType }}">
                                                                    <input type="submit" class="btn btn-primary"
                                                                        value="Save" @style(['height: fit-content'])>
                                                                </div>

                                                            </form>
                                                        @endif

                                                        @if (isset($section->metrics) && count($section->metrics) > 0)
                                                            <ul>
                                                                @foreach ($section->metrics as $metricId => $metric)
                                                                    <li>
                                                                        <strong>{{ $metric->metricName }}</strong>:
                                                                        (<span
                                                                            style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                        <p>{{ $metric->metricDescription }}</p>
                                                                        <form
                                                                            action="{{ route('submit.employee.probe') }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            <div class="d-flex gap-3">
                                                                                <div class="col-md-2">
                                                                                    <input class="form-control mb-3"
                                                                                        type="number"
                                                                                        placeholder="Enter Score"
                                                                                        readonly
                                                                                        @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')
                                                                                        value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                </div>
                                                                                <div class="col-md-9">
                                                                                    <input class="form-control mb-3"
                                                                                        type="text"
                                                                                        name="employeeComment"
                                                                                        placeholder="Enter your comments"
                                                                                        @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'COMPLETED')
                                                                                        value="{{ $metric->metricEmpScore->employeeComment ?? '' }}">
                                                                                </div>
                                                                            </div>

                                                                            <span
                                                                                class="mb-2 badge rounded-pill bg-dark"><strong>Supervisor
                                                                                    Score and Comment</strong></span>

                                                                            <div class="d-flex gap-3">
                                                                                <div class="col-md-2">
                                                                                    <input class="form-control mb-3"
                                                                                        type="number" readonly
                                                                                        placeholder="Enter Score"
                                                                                        @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')
                                                                                        value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input class="form-control mb-3"
                                                                                        type="text" readonly
                                                                                        placeholder="Enter your comments"
                                                                                        @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')
                                                                                        value="{{ $metric->metricEmpScore->supervisorComment ?? '' }}">
                                                                                </div>
                                                                                <div
                                                                                    class="form-check form-check-dark mb-3">
                                                                                    <input @style(['width:1.8rem; height:2rem'])
                                                                                        class="form-check-input"
                                                                                        @checked(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === true)
                                                                                        type="checkbox" name="scoreId"
                                                                                        value="{{ $metric->metricEmpScore->id ?? '' }}"
                                                                                        id="formCheckcolor4">
                                                                                </div>
                                                                                <input type="hidden" name="metricId"
                                                                                    value="{{ $metric->metricId }}">
                                                                                <input type="hidden" name="sectionId"
                                                                                    value="{{ $section->sectionId }}">
                                                                                <input type="hidden" name="kpiId"
                                                                                    value="{{ $kpi->kpiId }}">
                                                                                <input type="hidden" name="kpiType"
                                                                                    value="{{ $kpi->kpiType }}">
                                                                                <input type="submit"
                                                                                    class="btn btn-primary"
                                                                                    value="Save" @style(['height: fit-content'])>
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
                                @endif

                                <hr class="mt-10">

                                @if (isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                    <div class="float-end">
                                        {{--  <div class="d-flex gap-3">  --}}
                                        <button type="button" data-bs-toggle="modal" class="btn btn-dark"
                                            @style(['width: 100%; height: fit-content']) data-bs-target=".bs-delete-modal-lg">Submit
                                            Appraisal For Probe</button>


                                        {{--  </div>  --}}
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
                                                        <b>Push Your Scores To Probe</b> To a Higher
                                                        <b>Supervisor?</b>
                                                    </h4>
                                                    <form action="{{ route('submit.appraisal') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="kpiId"
                                                            value="{{ $kpi->kpiId }}">
                                                        <input type="hidden" name="batchId"
                                                            value="{{ $kpi->batchId }}">
                                                        <input type="hidden" name="status" value="PROBLEM">
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




                                {{--  <form action="" method="POST">
                                    @csrf
                                    <div class="float-end">
                                        <div class="mt-5 d-flex gap-3">
                                            <button type="submit" class="btn btn-dark">Submit Probe</button>
                                        </div>
                                    </div>
                                </form>  --}}
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
