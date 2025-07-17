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
                        <h4 class="card-title mb-4">Employee Probing Form</h4>



                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $kpi)
                                        <div class="kpi">


                                            @foreach ($kpi->activeSections as $section)
                                                <div class="card border border-primary" @style(['border-radius: 10px;'])>
                                                    <div class="card-body"
                                                        style="{{ $section->metrics->isEmpty() ? 'background-color: #0000ff0d;' : '' }}">
                                                        <div class="section-card" style="margin-top: 2rem;">
                                                            <h4>{{ $section->sectionName }} (<span
                                                                    style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                            </h4>
                                                            <p>{{ $section->sectionDescription }}</p>

                                                            @if ($section->metrics->isEmpty())
                                                                <form action="{{ route('submit.employee.probe') }}"
                                                                    method="POST"
                                                                    class="section-form ajax-emp-prob-eval-form">
                                                                    @csrf
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3 score-input"
                                                                                type="number" name="sectionEmpScore"
                                                                                required placeholder="Enter Score"
                                                                                min="0" step="0.01"
                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                max="{{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                                                title="The Score cannot be more than the section score {{ $section->sectionScore }}"
                                                                                value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <textarea class="form-control mb-3 comment-input" type="text" name="employeeComment" required
                                                                                placeholder="Enter your comments" @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'COMPLETED') rows="3">{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</textarea>
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
                                                                                @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')
                                                                                value="{{ optional($section->sectionEmpScore)->sectionSupScore ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <textarea class="form-control" type="text" readonly placeholder="Enter your comments" rows="3"
                                                                                @disabled(isset($section->sectionEmpScore) && $section->sectionEmpScore->status === 'CONFIRMATION')>{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                        </div>
                                                                        <div class="form-check form-check-dark mb-3">
                                                                            <input @style(['width:1.8rem; height:2rem'])
                                                                                class="form-check-input" type="checkbox"
                                                                                name="prob" id="checkProb"
                                                                                value="true"
                                                                                @checked(isset($section->sectionEmpScore) && $section->sectionEmpScore->prob === true)>
                                                                        </div>
                                                                        <input type="hidden" name="scoreId"
                                                                            value="{{ $section->sectionEmpScore->id ?? '' }}">

                                                                        {{--  <input type="hidden" name="metricId"
                                                                            value="{{ $section->metricId }}">  --}}
                                                                        <input type="hidden" name="sectionId"
                                                                            value="{{ $section->sectionId }}">
                                                                        <input type="hidden" name="kpiId"
                                                                            value="{{ $kpi->kpi->kpiId }}">
                                                                        <input type="hidden" name="kpiType"
                                                                            value="{{ $kpi->kpi->kpiType }}">
                                                                        <input type="submit" class="btn btn-primary"
                                                                            value="Save" @style(['height: fit-content'])>
                                                                    </div>
                                                                </form>
                                                            @endif


                                                            @if (isset($section->metrics) && count($section->metrics) > 0)
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
                                                                                    action="{{ route('submit.employee.probe') }}"
                                                                                    method="POST"
                                                                                    class="metric-form ajax-emp-prob-eval-form">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3">
                                                                                        <div class="col-md-2">
                                                                                            <input
                                                                                                class="form-control mb-3"
                                                                                                type="number"
                                                                                                placeholder="Enter Score"
                                                                                                readonly
                                                                                                @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')
                                                                                                value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control mb-3" type="text" required name="employeeComment" placeholder="Enter your comments"
                                                                                                rows="3" @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'COMPLETED')>{{ $metric->metricEmpScore->employeeComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                    </div>

                                                                                    <span
                                                                                        class="mb-2 badge rounded-pill bg-dark"><strong>Supervisor
                                                                                            Score and
                                                                                            Comment</strong></span>

                                                                                    <div class="d-flex gap-3">
                                                                                        <div class="col-md-2">
                                                                                            <input
                                                                                                class="form-control mb-3"
                                                                                                type="number" readonly
                                                                                                placeholder="Enter Score"
                                                                                                @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')
                                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore ?? '' }}">
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <textarea class="form-control mb-3" type="text" readonly placeholder="Enter your comments" rows="3"
                                                                                                @disabled(isset($metric->metricEmpScore) && $metric->metricEmpScore->status === 'CONFIRMATION')>{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                        <div
                                                                                            class="form-check form-check-dark mb-3">
                                                                                            <input @style(['width:1.8rem; height:2rem'])
                                                                                                class="form-check-input"
                                                                                                type="checkbox"
                                                                                                name="prob"
                                                                                                id="checkProb"
                                                                                                value="true"
                                                                                                @checked(isset($metric->metricEmpScore) && $metric->metricEmpScore->prob === true)
                                                                                                onchange="updateProbValue(this)">
                                                                                        </div>
                                                                                        <input type="hidden"
                                                                                            name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">

                                                                                        <input type="hidden"
                                                                                            name="metricId"
                                                                                            value="{{ $metric->metricId }}">
                                                                                        <input type="hidden"
                                                                                            name="sectionId"
                                                                                            value="{{ $section->sectionId }}">
                                                                                        <input type="hidden"
                                                                                            name="kpiId"
                                                                                            value="{{ $kpi->kpi->kpiId }}">
                                                                                        <input type="hidden"
                                                                                            name="kpiType"
                                                                                            value="{{ $kpi->kpi->kpiType }}">
                                                                                        <input type="submit"
                                                                                            class="btn btn-primary"
                                                                                            value="Save"
                                                                                            @style(['height: fit-content'])>
                                                                                    </div>
                                                                                </form>


                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p></p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </div>

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
                                                    <input type="hidden" name="employeeId"
                                                        value="{{ $employeeId }}">
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="status" value="PROBLEM">
                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-success">Yes,
                                                            Send To Supervisor </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end col -->
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const saveForms = document.querySelectorAll('form.ajax-emp-prob-eval-form');

                const showToast = (type, message) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: type,
                        title: message
                    });
                };

                saveForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const scrollPos = window.scrollY;
                        const formData = new FormData(form);
                        const saveBtn = form.querySelector(
                            'button[type="submit"], input[type="submit"]');
                        const originalText = saveBtn.innerHTML || saveBtn.value;

                        const restoreButton = () => {
                            if (saveBtn.tagName === 'BUTTON') {
                                saveBtn.innerHTML = originalText;
                            } else {
                                saveBtn.value = originalText;
                                saveBtn.disabled = false;
                            }
                        };

                        if (saveBtn.tagName === 'BUTTON') {
                            saveBtn.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                        } else {
                            saveBtn.value = 'Saving...';
                            saveBtn.disabled = true;
                        }

                        fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                setTimeout(() => window.scrollTo({
                                    top: scrollPos,
                                    behavior: 'smooth'
                                }), 150);
                                showToast('success', 'Selection saved successfully.');
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.scrollTo({
                                    top: scrollPos,
                                    behavior: 'smooth'
                                });
                                showToast('error', 'Something went wrong while saving.');
                            })
                            .finally(() => {
                                restoreButton();
                            });
                    });
                });
            });
        </script>
    @endpush


    </div>

</x-base-layout>
