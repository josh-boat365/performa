<x-base-layout>

    <div class="container-fluid px-1">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Overview of Your Appraisal</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>


        <!-- Performance Over Time Chart & Grading Scheme -->
        <div class="row">
            <!-- Performance Chart - Left Side -->
            <div class="col-xl-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Performance Over Time</h4>
                        <div id="performanceChart" style="height: 320px;"></div>
                    </div>
                </div>
            </div>

            <!-- Grading Scheme - Right Side -->
            <div class="col-xl-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Grading Scheme</h4>
                        @if($grades && count($grades) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size: 1rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="align-middle text-center fs-6">Grade</th>
                                            <th class="align-middle text-center fs-6">Score Range</th>
                                            <th class="align-middle fs-6">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grades as $grade)
                                            <tr>
                                                <td class="text-center fw-bold">
                                                    <span class="badge bg-primary"
                                                        style="font-size: 0.95rem;">{{ $grade['grade'] ?? '---' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    {{ $grade['minScore'] ?? '0' }} - {{ $grade['maxScore'] ?? '100' }}
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $grade['remark'] ?? '---' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="fas fa-info-circle"></i> Grading scheme not available.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Appraisal Batches & Scores</h4>

                        @if($batchScores && $batchScores->count() > 0)
                            <div class="row">
                                @foreach($batchScores->sortByDesc('isCurrentBatch') as $batch)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border @if($batch->isCurrentBatch) border-primary @else border-success @endif"
                                            style="border-radius: 8px; height: 100%;">
                                            <div class="card-body"
                                                style="@if($batch->isCurrentBatch) background-color: #3b76e10d; @else background-color: #0000ff0d; @endif">
                                                <!-- Batch Header -->
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h5 class="card-title mb-1 @if($batch->isCurrentBatch) fw-bold @endif"
                                                            style="@if($batch->isCurrentBatch) font-size: 1.25rem; @endif font-size: 0.95rem;">
                                                            {{ $batch->batchName }}
                                                        </h5>
                                                        @if($batch->batchYear)
                                                            <small class="text-muted @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                style="font-size: 0.8rem;">
                                                                Year: {{ $batch->batchYear }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    @if($batch->isCurrentBatch)
                                                        <span class="badge bg-primary">Current</span>
                                                    @endif
                                                </div>

                                                <!-- Score Details -->
                                                @if($batch->status === 'COMPLETED' && ($batch->kpiScore !== null || $batch->grade !== null))
                                                    <div class="score-details">
                                                        <div class="row text-center mb-2">
                                                            <div class="col-6">
                                                                <small
                                                                    class="text-muted d-block @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                    style="font-size: 0.8rem;">Score</small>
                                                                <h6 class="@if($batch->isCurrentBatch) fw-bold @else font-weight-bold @endif"
                                                                    style="@if($batch->isCurrentBatch) font-size: 1.5rem; @endif font-size: 1.3rem;">
                                                                    {{ $batch->kpiScore ?? '---' }}
                                                                </h6>
                                                            </div>
                                                            <div class="col-6">
                                                                <small
                                                                    class="text-muted d-block @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                    style="font-size: 0.8rem;">Grade</small>
                                                                <h6 class="@if($batch->isCurrentBatch) fw-bold @else font-weight-bold @endif"
                                                                    style="@if($batch->isCurrentBatch) font-size: 1.5rem; @endif font-size: 1.3rem;">
                                                                    {{ $batch->grade ?? '---' }}
                                                                </h6>
                                                            </div>
                                                        </div>

                                                        @if($batch->remark)
                                                            <div class="mt-2">
                                                                <small class="text-muted @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                    style="font-size: 0.8rem;">Remark:</small>
                                                                <p class="mb-0 @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                    style="font-size: 0.8rem;">{{ $batch->remark }}</p>
                                                            </div>
                                                        @endif

                                                        @if($batch->recommendation)
                                                            <div class="mt-2">
                                                                <small class="text-muted @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                    style="font-size: 0.8rem;">Recommendation:</small>
                                                                <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#recommendationModal_{{ $batch->batchId }}">
                                                                    View Recommendation
                                                                </button>
                                                                <!-- Modal -->
                                                                <div class="modal fade" id="recommendationModal_{{ $batch->batchId }}"
                                                                    tabindex="-1"
                                                                    aria-labelledby="recommendationModalLabel_{{ $batch->batchId }}"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="recommendationModalLabel_{{ $batch->batchId }}">
                                                                                    Recommendation for {{ $batch->batchName }}</h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body"
                                                                                style="word-break: break-word; white-space: pre-line; max-height: 60vh; overflow-y: auto;">
                                                                                {{ $batch->recommendation }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center py-3">
                                                        <p class="text-muted mb-0" style="font-size: 0.8rem;">
                                                            <i class="fas fa-hourglass-half"></i> Scores not yet processed
                                                        </p>
                                                        <small class="text-muted" style="font-size: 0.8rem;">Status:
                                                            {{ $batch->status }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i> No batches available. Please wait for appraisals to be
                                created.
                            </div>
                        @endif
                    </div>
                </div><!--end card-->
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var batchLabels = @json($batchScores->pluck('batchName'));
                var batchScores = @json($batchScores->pluck('kpiScore')->map(fn($v) => $v === null ? 0 : $v));

                // Performance Over Time Chart
                var perfOptions = {
                    chart: { type: 'line', height: 320 },
                    series: [{
                        name: 'Score',
                        data: batchScores
                    }],
                    xaxis: {
                        categories: batchLabels,
                        title: { text: 'Batch' }
                    },
                    yaxis: {
                        title: { text: 'Score' }
                    },
                    stroke: { curve: 'smooth' },
                    markers: { size: 5 }
                };
                var perfChart = new ApexCharts(document.querySelector('#performanceChart'), perfOptions);
                perfChart.render();

            });
        </script>
    @endpush

</x-base-layout>