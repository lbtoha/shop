
@props(['href' => null, 'type' => 'submit'])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-primary outlined']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => 'btn-primary outlined']) }}>
        <span class="btn-spinner hidden">
            <span class="animate-spin"><i class="ph ph-spinner gap-2 "></i></span> Loading...
        </span>
        <div class="btn-text"> {{ $slot }} </div>
    </button>
@endif
