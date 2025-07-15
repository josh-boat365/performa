<div class="modal fade bs-delete-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Confirm Appraisal Submit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="text-center mb-4">
                    Are you sure you want to <b>Submit</b> your <b>Appraisal</b> to your
                    <b>Supervisor</b> for <b>Review?</b>
                </h4>
                <form action="{{ route('submit.appraisal') }}" method="POST" id="appraisalForm">
                    @csrf
                    <input type="hidden" name="kpiId" value="{{ $kpi->kpi->kpiId }}">
                    <input type="hidden" name="batchId" value="{{ $kpi->kpi->batchId }}">
                    <input type="hidden" name="status" value="REVIEW">
                    <div class="d-grid">
                        <button type="submit" id="submitReviewButton" class="btn btn-success">
                            Submit Appraisal For Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
