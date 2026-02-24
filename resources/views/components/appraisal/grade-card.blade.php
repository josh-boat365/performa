{{--
    Grade Card Component
    @props:
        - title: string (badge title)
        - badgeClass: string (e.g., "bg-success", "bg-secondary", "bg-primary")
        - employeeName: string|null (optional employee name)
        - items: array (key-value pairs for Grade/Score/Remark display)
        - badges: array|null (for badge-style display)
        - slot: optional button/link content
--}}

@props([
    'title',
    'badgeClass' => 'bg-secondary',
    'employeeName' => null,
    'items' => [],
    'badges' => null
])

<div class="grade-card">
    <span class="badge {{ $badgeClass }} mb-2">{{ $title }}</span>

    @if($employeeName)
        <p class="mb-2 small text-muted">{{ $employeeName }}</p>
    @endif

    @if(count($items) > 0)
        <div class="mt-2">
            @foreach($items as $label => $value)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">{{ $label }}:</span>
                    <strong>{{ $value }}</strong>
                </div>
            @endforeach
        </div>
    @endif

    @if($badges)
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @foreach($badges as $badge)
                <span class="badge bg-light text-dark">{{ $badge }}</span>
            @endforeach
        </div>
    @endif

    {{ $slot }}
</div>
