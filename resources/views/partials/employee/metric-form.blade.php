

<div class="card border border-success" style="border-radius: 10px;">
    <div class="card-body metric-card">
        <h5>{{ $metric->metricName }} (<span style="color: #c80f0f">{{ $metric->metricScore }}</span>)</h5>
        <p>{{ $metric->metricDescription }}</p>

        <form action="{{ route('self.rating') }}" method="POST" class="ajax-eval-form metric-form">
            @csrf
            <div class="d-flex gap-3">
                <input type="hidden" name="metricId" value="{{ $metric->metricId }}">
                <input type="hidden" name="kpiType" value="{{ $kpi->kpi->kpiType }}">
                <input type="hidden" name="kpiId" value="{{ $kpi->kpi->kpiId }}">
                <input type="hidden" name="sectionId" value="{{ $section->sectionId }}">

                <div class="col-md-2">
                    <input class="form-control mb-3" type="number" name="metricEmpScore" required
                        placeholder="Enter Score" min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                        max="{{ $metric->metricScore }}"
                        title="The Score cannot be more than the metric score {{ $metric->metricScore }}"
                        @disabled(in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                        value="{{ optional($metric->metricEmpScore)->metricEmpScore ?? '' }}">
                </div>

                <div class="col-md-9">
                    <textarea class="form-control mb-3" type="text" name="employeeComment" required placeholder="Enter your comments"
                        rows="3" @disabled(in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))>
                        {{ optional($metric->metricEmpScore)->employeeComment ?? '' }}
                    </textarea>
                </div>

                @unless (in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                    <button type="submit" style="height: fit-content" class="btn btn-success">
                        Save
                    </button>
                @endunless
            </div>
        </form>

        @if (isset($metric->metricEmpScore))
            @if (
                ($metric->metricEmpScore->status === 'CONFIRMATION' || $metric->metricEmpScore->status === 'COMPLETED') &&
                    $metric->metricEmpScore->prob == false)
                @include('partials.employee.supervisor-review', [
                    'score' => optional($metric->metricEmpScore)->metricSupScore ?? '',
                    'comment' => $metric->metricEmpScore->supervisorComment ?? '',
                    'label' => 'Supervisor Score and Comment',
                ])
            @elseif(
                ($metric->metricEmpScore->status === 'CONFIRMATION' || $metric->metricEmpScore->status === 'COMPLETED') &&
                    $metric->metricEmpScore->prob == true)
                @include('partials.employee.supervisor-review', [
                    'score' => optional($metric->metricEmpScore)->metricSupScore ?? '',
                    'comment' => $metric->metricEmpScore->supervisorComment ?? '',
                    'label' => 'Supervisor Score and Comment',
                ])

                @include('partials.employee.supervisor-review', [
                    'score' => optional($metric->metricEmpScore)->metricProbScore ?? '',
                    'comment' => $metric->metricEmpScore->probComment ?? '',
                    'label' => 'Probing Score and Comment',
                    'badgeClass' => 'bg-dark',
                ])
            @endif
        @endif
    </div>
</div>
