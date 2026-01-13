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
                                                            style="@if($batch->isCurrentBatch) font-size: 1.25rem; @endif">
                                                            {{ $batch->batchName }}
                                                        </h5>
                                                        @if($batch->batchYear)
                                                            <small class="text-muted @if($batch->isCurrentBatch) fw-semibold @endif"
                                                                style="@if($batch->isCurrentBatch) font-size: 0.9rem; @endif">
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
                                                                <small class="text-muted d-block @if($batch->isCurrentBatch) fw-semibold @endif">Score</small>
                                                                <h6 class="@if($batch->isCurrentBatch) fw-bold @else font-weight-bold @endif"
                                                                    style="@if($batch->isCurrentBatch) font-size: 1.5rem; @endif">
                                                                    {{ $batch->kpiScore ?? '---' }}
                                                                </h6>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block @if($batch->isCurrentBatch) fw-semibold @endif">Grade</small>
                                                                <h6 class="@if($batch->isCurrentBatch) fw-bold @else font-weight-bold @endif"
                                                                    style="@if($batch->isCurrentBatch) font-size: 1.5rem; @endif">
                                                                    {{ $batch->grade ?? '---' }}
                                                                </h6>
                                                            </div>
                                                        </div>

                                                        @if($batch->remark)
                                                            <div class="mt-2">
                                                                <small class="text-muted @if($batch->isCurrentBatch) fw-semibold @endif">Remark:</small>
                                                                <p class="mb-0 small @if($batch->isCurrentBatch) fw-semibold @endif">{{ $batch->remark }}</p>
                                                            </div>
                                                        @endif

                                                        @if($batch->recommendation)
                                                            <div class="mt-2">
                                                                <small class="text-muted @if($batch->isCurrentBatch) fw-semibold @endif">Recommendation:</small>
                                                                <p class="mb-0 small @if($batch->isCurrentBatch) fw-semibold @endif">{{ $batch->recommendation }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center py-3">
                                                        <p class="text-muted mb-0">
                                                            <i class="fas fa-hourglass-half"></i> Scores not yet processed
                                                        </p>
                                                        <small class="text-muted">Status: {{ $batch->status }}</small>
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
    @endpush

</x-base-layout>
