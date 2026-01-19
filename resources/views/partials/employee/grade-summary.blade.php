@php
    if (!function_exists('getBadgeDetails')) {
        function getBadgeDetails($status)
        {
            return match ($status) {
                'PENDING' => ['class' => 'bg-dark', 'text' => 'PENDING'],
                'REVIEW' => ['class' => 'bg-warning', 'text' => 'REVIEW'],
                'CONFIRMATION' => ['class' => 'bg-primary', 'text' => 'CONFIRMATION'],
                'COMPLETED' => ['class' => 'bg-success', 'text' => 'COMPLETED'],
                'PROBLEM' => ['class' => 'bg-danger', 'text' => 'PROBE'],
                default => ['class' => 'bg-secondary', 'text' => 'PENDING'],
            };
        }
    }
    $badgeDetails = getBadgeDetails($gradeDetails['status'] ?? null);
@endphp

<div class="col-md-12">
    <div class="card card-body">
        <div class="d-flex justify-content-between">
            <div>
                <h4>Appraisal Grade</h4>
            </div>
        </div>

        <div class="mt-3">
            <div class="d-flex flex-wrap gap-5">
                <h5>Grade: <b>{{ $gradeDetails['grade'] ?? '___' }}</b></h5>
                <h5>Score: <b>{{ $gradeDetails['kpiScore'] ?? '___' }}</b></h5>
                <h5>Remark: <b>{{ $gradeDetails['remark'] ?? '___' }}</b></h5>
                <h5>Status:
                    <b>
                        <span class="badge rounded-pill {{ $badgeDetails['class'] }}">
                            {{ $badgeDetails['text'] }}
                        </span>
                    </b>
                </h5>
            </div>
        </div>
    </div>
</div>