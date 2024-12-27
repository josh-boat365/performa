<x-base-layout>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"><a href="{{ route('report.index') }}">Appraisal Reports</a> >
                    Employee Appraisal
                    {{--  > <a href="#">{{ session('kpi_section_name') }}</a>  --}}
                </h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="">
            <div class="d-flex gap-3">
                {{--  <button type="button" class="btn btn-primary"><i class="bx bx-spreadsheet"></i>
                Print-Excel</button>  --}}

                <button type="button" class="btn btn-success">
                    <a href="{{ route('employee.printPdf', ['id' => $employeeId]) }}" target="_blank"
                        style="color: white; text-decoration: none;">
                        <i class="bx bx-file"></i> Print-PDF
                    </a>
                </button>


            </div>
        </div>
    </div>

    <div class="">
        {{--  <h1>Employee Performance Summary</h1>  --}}
        @foreach ($employee as $employeeData)
            @foreach ($employeeData->employees as $employee)
                <!-- Employee Header Information -->

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Employee Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-4">
                            <div>
                                <img style="width: 8rem; height: 8rem" class="justify-items-center"
                                    src="{{ asset('bpsl_imgs/user-2.png') }}"
                                    alt="{{ $employee->employeeName }} . ' Image'">
                            </div>
                            <div>
                                <p><strong>Name:</strong> {{ $employee->employeeName }}</p>
                                <p><strong>Department:</strong> {{ $employee->departmentName }}</p>
                                <p><strong>Role:</strong> {{ $employee->roleName }}</p>
                                <p><strong>Grade Score:</strong> {{ $employee->totalScore->totalKpiScore ?? 'N/A' }}</p>
                                <p><strong>Grade:</strong> {{ $employee->totalScore->grade ?? 'N/A' }}</p>
                                <p><strong>Grade Remark:</strong> {{ $employee->totalScore->remark ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p><strong>Batch:</strong> {{ $employeeData->batchName }}</p>
                                <p><strong>Status:</strong> {{ $employeeData->status }}</p>
                                <p><strong>Created On:</strong>
                                    {{ \Carbon\Carbon::parse($employeeData->createdAt)->format('d M, Y') }}</p>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Appraisal Batches -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Appraisal Summary Table -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Scores</th>
                                <th>Supervisor Name</th>
                                <th>Probation Name</th>
                                <th>Comments</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employee->scores as $index => $score)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>Question:</strong> {{ $score->questionName ?? 'N/A' }} <br>
                                        <strong>Description:</strong> {{ $score->questionDescription ?? 'N/A' }} <br>
                                    </td>
                                    <td>
                                        <p><strong>Section Score:</strong> {{ $score->sectionSupScore ?? 'N/A' }} </p>
                                        <p><strong>Section Probe Score:</strong>
                                            {{ $score->sectionProbScore ?? 'N/A' }}</p>
                                        <p><strong>Metric Score:</strong> {{ $score->metricSupScore ?? 'N/A' }}</p>
                                        <p><strong>Metric Probe Score:</strong> {{ $score->metricProbScore ?? 'N/A' }}
                                        </p>
                                    </td>
                                    <td>{{ $score->supervisorName }}</td>
                                    <td>{{ $score->probName }}</td>
                                    <td>
                                        <strong>Employee:</strong> {{ $score->employeeComment ?? 'N/A' }}<br>
                                        <strong>Supervisor:</strong> {{ $score->supervisorComment ?? 'N/A' }}<br>
                                        <strong>Probation:</strong> {{ $score->probComment ?? 'N/A' }}
                                    </td>
                                    <td>{{ $score->status }}</td>
                                    <td>{{ \Carbon\Carbon::parse($score->createdAt)->format('d M, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        @endforeach
    </div>


    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const printPdfButton = document.querySelector('#print-pdf');

                printPdfButton.addEventListener('click', function() {
                    // Initialize jsPDF
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();

                    // Add content to the PDF
                    doc.text("Employee Performance Summary", 10, 10);

                    // Loop through data and add rows
                    @foreach ($employee as $employeeData)
                        doc.text("Batch: {{ $employeeData->batchName ?? 'N/A' }}", 10, 20);
                        doc.text("Status: {{ $employeeData->status ?? 'N/A' }}", 10, 30);
                        {{--  doc.text(
                            "Created On: {{ \Carbon\Carbon::parse($employeeData->createdAt)->format('d M, Y') }}",
                            10, 40);  --}}

                        @foreach ($employeeData->employees as $employee)
                            doc.text("Name: {{ $employee->employeeName }}", 10, 50);
                            doc.text("Department: {{ $employee->departmentName }}", 10, 60);
                            doc.text("Role: {{ $employee->roleName }}", 10, 70);
                            doc.text("Grade Score: {{ $employee->totalScore->totalKpiScore ?? 'N/A' }}", 10,
                                80);
                            doc.text("Grade: {{ $employee->totalScore->grade ?? 'N/A' }}", 10, 90);
                            doc.text("Grade Remark: {{ $employee->totalScore->remark ?? 'N/A' }}", 10, 100);
                        @endforeach

                        let y = 110;
                        doc.text("Scores Table:", 10, y);
                        y += 10;

                        @foreach ($employeeData->employees[0]->scores as $index => $score)
                            doc.text("#{{ $index + 1 }} KPI: {{ $score->kpiName }}", 10, y);
                            y += 10;
                            doc.text("Question: {{ $score->questionName ?? 'N/A' }}", 10, y);
                            y += 10;
                            doc.text("Supervisor Score: {{ $score->metricSupScore ?? 'N/A' }}", 10, y);
                            y += 10;
                        @endforeach
                    @endforeach

                    // Save the PDF
                    doc.save("Employee_Performance_Summary.pdf");
                });
            });
        </script>
    @endpush

</x-base-layout>
