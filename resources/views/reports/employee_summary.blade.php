<x-base-layout>
    <div class="container-fluid px-2">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="{{ route('report.index') }}">Appraisal Reports</a> >
                        Employee Appraisal
                    </h4>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <form action="{{ route('employee.printPdf', $employeeId) }}" method="GET">
                @csrf
                <div class="float-end">
                    <button type="submit" class="btn btn-success" id="print-pdf">
                        <i class="bx bx-file"></i> Print-PDF
                    </button>
                </div>
            </form>
        </div>



        <div class="col-xl-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    @foreach ($employee as $employeeData)
                        @foreach ($employeeData->employees as $employee)
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <img @style(['width:10rem']) src="{{ asset('bpsl_imgs/purple-logo-bpsl.png') }}"
                                    alt="{{ $employee->employeeName }} . ' s Image'">
                                <h6 class="fw-bold">{{ Carbon\Carbon::now()->format('D M d Y g:i A') }}
                                </h6>
                            </div>
                            <div class="d-flex justify-content-center">
                                <h5 class="mb-4 text-uppercase fw-bold py-2 px-4" @style(['font-weight:700; width:fit-content; background-color: rgb(229 232 236); '])>Employee Appraisal
                                    Report -
                                    {{ $employeeData->batchName }}</h5>
                            </div>

                            {{--  Employee Information  --}}

                            <div class="d-flex gap-4 justify-content-center">
                                <div>
                                    <img @style(['width:8rem']) src="{{ asset('bpsl_imgs/user-1.png') }}"
                                        alt="Employee Image">
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">

                                        <thead class="table-light">

                                            <th>EMPLOYEE INFORMATION</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>

                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td> Name: <b>{{ $employee->employeeName }}</b></td>
                                                <td>Employee ID: <b>{{ $employee->staffNumber ?? '444' }}</b></td>
                                                <td>Role: <b>{{ $employee->roleName }}</b></td>
                                                <td>Branch: <b>Head Office</b></td>
                                            </tr>
                                            <tr>
                                                <td>Department: <b>{{ $employee->departmentName }}</b></td>
                                                <td>Grade: <b>{{ $employee->totalScore->grade ?? '___' }}</b></td>
                                                <td>Score: <b>{{ $employee->totalScore->totalKpiScore ?? '___' }}</b>
                                                </td>
                                                <td>Remark: <b>{{ $employee->totalScore->remark ?? '___' }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        <div class=""
                            style="background-color: rgb(183, 183, 183); height: 1px; margin-top:4rem; margin-bottom:3rem">
                        </div>

                        {{--  Appraisal Questions, Score and Comments  --}}
                        @foreach ($employee->scores as $index => $score)
                            <div style="background-color: rgba(243, 249, 255, 0.904);">
                                <h5 class="p-2" style="background-color: rgb(229 232 236);"> <b>Question
                                        {{ $index + 1 }}. </b>{{ $score->questionName ?? '___' }} <br>
                                    <small>{{ $score->questionDescription ?? '___' }}</small>
                                </h5>
                                <div class="p-3">
                                    <div>
                                        <span><b>Employee Section Score</b>:
                                            {{ $score->metricEmpScore ?? '___' }}</span> &nbsp;&nbsp;
                                        <span><b>Employee Metric Score</b>:
                                            {{ $score->sectionEmpScore ?? '___' }}</span> <br>
                                        <p><b>Employee Comment</b>: {{ $score->employeeComment ?? '___' }}</p>
                                    </div>

                                    <div>
                                        <span><b>Supervisor Section Score</b>:
                                            {{ $score->sectionSupScore ?? '___' }}</span> &nbsp;&nbsp;
                                        <span><b>Supervisor Metric Score</b>:
                                            {{ $score->metricSupScore ?? '___' }}</span> <br>
                                        <p><b>Supervisor Comment</b>: {{ $score->supervisorComment ?? '___' }}</p>
                                    </div>

                                    <span><b>Probe Supervisor Section Score</b>:
                                        {{ $score->sectionProbScore ?? '___' }}</span> &nbsp;&nbsp;
                                    <span><b>Probe Supervisor Metric Score</b>:
                                        {{ $score->metricProbScore ?? '___' }}</span> <br>
                                    <p><b>Probe Supervisor Comment</b>: {{ $score->probComment ?? '___' }}</p>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

    </div>


</x-base-layout>
