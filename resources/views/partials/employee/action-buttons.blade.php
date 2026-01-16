{{-- Common navigation buttons for all statuses --}}
<div class="float-end">
    <div class="d-flex gap-3 pagination-controls">
        <button id="prev-btn" class="btn btn-dark" disabled>Previous</button>
        <button id="next-btn" class="btn btn-primary">Next</button>

        {{-- Status-specific buttons --}}
        @if ($kpiStatus === 'PENDING')
            <button id="submit-btn" type="button" data-bs-toggle="modal" class="btn btn-success"
                data-bs-target=".bs-delete-modal-lg" id="submitAppraisalButton">
                Submit Appraisal
            </button>
        @endif
    </div>
</div>

{{-- Additional controls for CONFIRMATION status --}}
@if ($kpiStatus === 'CONFIRMATION')
    <br><br><br>
    <div class="float-end">
        <div class="d-flex gap-3">
            <button type="button" data-bs-toggle="modal" class="btn btn-primary" style="width: 8rem; height: fit-content"
                data-bs-target=".bs-confirm-modal-lg">
                Accept
            </button>

            <a href="{{ route('show.employee.probe', ['id' => $kpi->kpi->kpiId, 'batchId' => $kpi->kpi->batchId]) }}"
                class="btn btn-warning" style="width: 8rem; height: fit-content">
                Probe
            </a>
        </div>
    </div>
@endif

{{-- Modals --}}
@if ($kpiStatus === 'PENDING')
    @include('partials.employee.submit-modal', ['kpi' => $kpi])
@endif

@if ($kpiStatus === 'CONFIRMATION')
    @include('partials.employee.confirm-modal', ['kpi' => $kpi])
@endif
