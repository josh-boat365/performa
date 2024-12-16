<x-base-layout>

    <div class="container-fluid px-1">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Overview of Batches of KPIs</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Your Appraisal Grades</h4>

                        <div id="column_chart_datalabel" data-colors='["--bs-primary"]' class="apex-charts"
                            dir="ltr"></div>
                    </div>
                </div><!--end card-->
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Participated Appraisals</h4>

                        <span
                            class="badge rounded-pill bg-primary py-1">{{ $employeeKpi['batch_name'] ?? 'N/A' }}</span>

                        <div class="card border border-success" style="border-radius: 8px;">
                            <div class="card-body" style="background-color: #0000ff0d;">
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between">
                                        <h5>Grade: <b>{{ $gradeDetails['grade'] ?? '___' }}</b></h5>
                                        <h5>Score: <b>{{ $gradeDetails['kpiScore'] ?? '___' }}</b></h5>
                                        <h5>Remark: <b>{{ $gradeDetails['remark'] ?? '___' }}</b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--end card-->
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Check if kpiScore and batch_name are available
                var kpiScore = {{ $gradeDetails['kpiScore'] ?? 0 }};
                var batchName = "{{ $employeeKpi['batch_name'] ?? 'N/A' }}";

                // Ensure the chart will only render if there is valid data
                if (kpiScore > 0 && batchName !== 'N/A') {
                    // Retrieve the color from the data-colors attribute
                    var chartColors = document.getElementById("column_chart_datalabel").getAttribute("data-colors");
                    chartColors = chartColors ? JSON.parse(chartColors).map(function(value) {
                        var newColor = getComputedStyle(document.documentElement).getPropertyValue(value
                    .trim());
                        return newColor || value;
                    }) : ['#3b76e1'];

                    // Chart options
                    var options = {
                        series: [{
                            name: "Appraisal Batch",
                            data: [kpiScore]
                        }],
                        chart: {
                            type: "bar",
                            height: 350,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: "55%",
                                endingShape: "rounded"
                            }
                        },
                        dataLabels: {
                            enabled: true
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ["transparent"]
                        },
                        xaxis: {
                            categories: [batchName, "", "", "", "", "", "", "", ""]
                        },
                        yaxis: {
                            title: {
                                text: "Grades"
                            }
                        },
                        fill: {
                            opacity: 1,
                            colors: chartColors
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return "Grade " + "{{ $gradeDetails['grade'] }}" + " - " + val;
                                }
                            }
                        }
                    };

                    try {
                        var chart = new ApexCharts(
                            document.querySelector("#column_chart_datalabel"),
                            options
                        );
                        chart.render();
                    } catch (error) {
                        console.error("Error rendering chart:", error);
                    }
                } else {
                    console.warn("No valid data available for the chart.");
                }
            });
        </script>
    @endpush

</x-base-layout>
