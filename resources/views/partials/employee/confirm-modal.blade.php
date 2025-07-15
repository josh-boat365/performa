<div class="modal fade bs-confirm-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Confirm Supervisor Score</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="text-center mb-4">
                    Are you sure you want to <b>Accept</b> this scores from your <b>Supervisor?</b>
                </h4>
                <form action="{{ route('submit.appraisal') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kpiId" value="{{ $kpi->kpi->kpiId }}">
                    <input type="hidden" name="batchId" value="{{ $kpi->kpi->batchId }}">
                    <input type="hidden" name="status" value="COMPLETED">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Yes, Accept</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
