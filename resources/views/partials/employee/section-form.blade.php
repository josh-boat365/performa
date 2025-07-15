<form action="{{ route('self.rating') }}" method="POST" class="ajax-eval-form section-form">
    @csrf
    <div class="d-flex gap-3">
        <div class="col-md-2">
            <input class="form-control mb-3 score-input" type="number" name="sectionEmpScore" required
                placeholder="Enter Score" min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                max="{{ $section->sectionScore }}"
                title="The Score cannot be more than the section score {{ $section->sectionScore }}"
                @disabled(in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])) value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
        </div>
        <div class="col-md-9">
            <textarea class="form-control mb-3 comment-input" type="text" name="employeeComment" required
                placeholder="Enter your comments" @disabled(in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])) rows="3">{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</textarea>
        </div>

        @if ($kpiStatus === 'PENDING')
            <input type="hidden" name="kpiType" value="{{ $kpi->kpi->kpiType }}">
            <input type="hidden" name="sectionEmpScoreId" value="{{ $section->sectionEmpScore->id ?? '' }}">
            <input type="hidden" name="sectionId" value="{{ $section->sectionId }}">
            <input type="hidden" name="kpiId" value="{{ $kpi->kpi->kpiId }}">
            <button type="submit" style="height: fit-content" class="btn btn-success">
                Save
            </button>
        @else
            <div></div>
        @endif
        {{--  @if (in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
            <input type="hidden" name="kpiType" value="{{ $kpi->kpi->kpiType }}">
            <input type="hidden" name="sectionEmpScoreId" value="{{ $section->sectionEmpScore->id ?? '' }}">
            <input type="hidden" name="sectionId" value="{{ $section->sectionId }}">
            <input type="hidden" name="kpiId" value="{{ $kpi->kpi->kpiId }}">
            <button type="submit" style="height: fit-content" class="btn btn-success">
                Save
            </button>
        @endif  --}}
    </div>
</form>

@if (isset($section->sectionEmpScore))
    @if (
        ($section->sectionEmpScore->status === 'CONFIRMATION' || $section->sectionEmpScore->status === 'COMPLETED') &&
            $section->sectionEmpScore->prob == false)
        @include('partials.employee.supervisor-review', [
            'score' => optional($section->sectionEmpScore)->sectionSupScore ?? '',
            'comment' => $section->sectionEmpScore->supervisorComment ?? '',
            'label' => 'Supervisor Score and Comment',
        ])
    @elseif(
        ($section->sectionEmpScore->status === 'CONFIRMATION' || $section->sectionEmpScore->status === 'COMPLETED') &&
            $section->sectionEmpScore->prob == true)
        @include('partials.employee.supervisor-review', [
            'score' => optional($section->sectionEmpScore)->sectionSupScore ?? '',
            'comment' => $section->sectionEmpScore->supervisorComment ?? '',
            'label' => 'Supervisor Score and Comment',
        ])

        @include('partials.employee.supervisor-review', [
            'score' => optional($section->sectionEmpScore)->sectionProbScore ?? '',
            'comment' => $section->sectionEmpScore->probComment ?? '',
            'label' => 'Probing Score and Comment',
            'badgeClass' => 'bg-dark',
        ])
    @endif
@endif
