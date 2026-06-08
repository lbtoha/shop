@props(['href' => null, 'type' => 'submit', 'modalTarget' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-error']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => 'btn-error']) }}
        @if ($modalTarget) data-modal-target="{{ $modalTarget }}" @endif>
        <span class="btn-spinner hidden">
            <i class="ph ph-spinner gap-2 animate-spin"></i> Loading...
        </span>
        <div class="btn-text"> {{ $slot }} </div>
    </button>
@endif
