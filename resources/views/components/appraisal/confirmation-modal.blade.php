{{--
    Confirmation Modal Component
    @props:
        - id: string (modal target class without the dot)
        - title: string
        - icon: string (boxicons class)
        - iconColor: string (e.g., "text-warning", "text-success")
        - headerClass: string (e.g., "bg-success text-white", "bg-warning")
        - message: string
        - description: string
        - action: string (form action URL)
        - buttonText: string
        - buttonClass: string (e.g., "btn-success", "btn-warning")
        - buttonIcon: string (boxicons class)
        - hiddenFields: array (key-value pairs for hidden inputs)
--}}

@props([
    'id',
    'title',
    'icon' => 'bx-question-mark',
    'iconColor' => 'text-warning',
    'headerClass' => 'bg-success text-white',
    'message',
    'description' => '',
    'action',
    'buttonText',
    'buttonClass' => 'btn-success',
    'buttonIcon' => 'bx-check',
    'hiddenFields' => []
])

<div class="modal fade {{ $id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header {{ $headerClass }}">
                <h5 class="modal-title" id="modalLabel">
                    <i class="bx {{ $icon }} me-2"></i>{{ $title }}
                </h5>
                <button type="button" class="btn-close {{ str_contains($headerClass, 'text-white') ? 'btn-close-white' : '' }}"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bx {{ $icon }} {{ $iconColor }}" style="font-size: 48px;"></i>
                <h5 class="mt-3">{{ $message }}</h5>
                @if($description)
                    <p class="text-muted">{{ $description }}</p>
                @endif

                <form action="{{ $action }}" method="POST">
                    @csrf
                    @foreach($hiddenFields as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn {{ $buttonClass }} btn-lg">
                            <i class="bx {{ $buttonIcon }} me-1"></i>{{ $buttonText }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
