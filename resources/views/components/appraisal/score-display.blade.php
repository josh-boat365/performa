{{--
    Score Display Component
    @props:
        - label: string (e.g., "Supervisor Score and Comment", "Probing Score and Comment")
        - badgeClass: string (e.g., "bg-success", "bg-dark")
        - score: mixed (the score value)
        - comment: string (the comment text)
--}}

@props([
    'label' => 'Score and Comment',
    'badgeClass' => 'bg-success',
    'score' => '',
    'comment' => ''
])

<div class="mt-3">
    <span class="mb-2 badge rounded-pill {{ $badgeClass }}">
        <strong>{{ $label }}</strong>
    </span>
    <div class="d-flex gap-3 p-4">
        <div class="col-md-2">
            <input class="form-control mb-3" type="number" readonly
                placeholder="Score" value="{{ $score }}">
        </div>
        <div class="col-md-9">
            <textarea class="form-control mb-3" readonly rows="3"
                placeholder="Comments">{{ $comment }}</textarea>
        </div>
    </div>
</div>
