{{--
    Score Input Form Component
    @props:
        - action: string (form action URL)
        - scoreName: string (input name for score, e.g., "sectionEmpScore", "metricEmpScore")
        - scoreValue: mixed (current score value)
        - maxScore: number (maximum allowed score)
        - comment: string (current comment value)
        - disabled: bool (whether inputs are disabled)
        - isSaved: bool (whether the form has been saved)
        - hiddenFields: array (key-value pairs for hidden inputs)
--}}

@props([
    'action',
    'scoreName',
    'scoreValue' => '',
    'maxScore',
    'comment' => '',
    'disabled' => false,
    'isSaved' => false,
    'hiddenFields' => []
])

<form action="{{ $action }}" method="POST" class="ajax-eval-form">
    @csrf
    <div class="d-flex gap-3 p-4">
        @foreach($hiddenFields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach

        <div class="col-md-2">
            <input class="form-control mb-3 score-input" type="number"
                name="{{ $scoreName }}" required placeholder="Enter Score"
                min="0" step="0.01" pattern="\d+(\.\d{1,2})?"
                max="{{ $maxScore }}"
                title="Score cannot exceed {{ $maxScore }}"
                value="{{ $scoreValue }}"
                @disabled($disabled)>
        </div>
        <div class="col-md-9">
            <textarea class="form-control mb-3 comment-input" name="employeeComment"
                placeholder="Enter your comments" rows="3"
                @disabled($disabled)>{{ $comment }}</textarea>
        </div>
        @unless($disabled)
            <button type="submit"
                class="btn btn-success btn-save {{ $isSaved ? 'btn-saved' : '' }}"
                style="height: fit-content; min-width: 85px;">
                @if($isSaved)
                    <i class="bx bx-check me-1"></i>Saved
                @else
                    Save
                @endif
            </button>
        @endunless
    </div>
</form>
