<span class="mb-2 badge rounded-pill {{ $badgeClass ?? 'bg-success' }}">
    <strong>{{ $label }}</strong>
</span>
<div class="d-flex gap-3">
    <div class="col-md-2">
        <input class="form-control mb-3" type="number" readonly name="metricSupScore" placeholder="Enter Score" required
            value="{{ $score }}">
    </div>
    <div class="col-md-9">
        <textarea class="form-control mb-3" type="text" readonly name="supervisorComment" placeholder="Enter your comments"
            required rows="3">{{ $comment }}</textarea>
    </div>
</div>
