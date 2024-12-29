<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Appraisal Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            padding: 0 0.5rem;
        }

        .col-xl-12 {
            width: 100%;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-5 {
            margin-bottom: 3rem;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .fw-bold {
            font-weight: bold;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .px-4 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .gap-4 {
            gap: 1.5rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-light {
            background-color: #f8f9fa;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-3 {
            padding: 1rem;
        }

        .hr {
            border: 0;
            height: 1px;
            background-color: #dee2e6;
        }

        .bg-light {
            background-color: rgba(243, 249, 255, 0.904);
        }

        .bg-gray {
            background-color: rgb(229, 232, 236);
        }

        .bg-dark-gray {
            background-color: rgb(183, 183, 183);
        }

        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="">
        <div class="">
            <div class="">
                <div class="">
                    @foreach ($employee as $employeeData)
                        @foreach ($employeeData->employees as $employee)
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <img style="width:10rem" src="{{ asset('bpsl_imgs/purple-logo-bpsl.png') }}"
                                    alt="Best Point Logo">
                                <h6 class="fw-bold">{{ Carbon\Carbon::now()->format('D M d Y g:i A') }}</h6>
                            </div>
                            <div class="d-flex justify-content-center">
                                <h5 class="mb-4 text-uppercase fw-bold py-2 px-4 bg-gray">Employee Appraisal Report -
                                    {{ $employeeData->batchName }}</h5>
                            </div>

                            {{--  Employee Information  --}}

                            <div class="d-flex gap-4 justify-content-center">
                                <div>
                                    <img style="width:8rem" src="{{ asset('bpsl_imgs/user-1.png') }}"
                                        alt="{{ $employee->employeeName }}'s Image">
                                </div>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>EMPLOYEE INFORMATION</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Name: <b>{{ $employee->employeeName }}</b></td>
                                                <td>Employee ID: <b>{{ $employee->staffNumber ?? ' 444' }}</b></td>
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

                        <div class="bg-dark-gray" style="height: 1px; margin-top:4rem; margin-bottom:3rem"></div>

                        {{--  Appraisal Questions, Score and Comments  --}}
                        @foreach ($employee->scores as $index => $score)
                            <div class="bg-light">
                                <h5 class="p-2 bg-gray"> <b>Question {{ $index + 1 }}.
                                    </b>{{ $score->questionName ?? '___' }} <br>
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
                            <hr class="hr">
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>

</html>
