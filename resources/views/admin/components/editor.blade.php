@props([
    'id' => 'editor-id',
    'label' => '',
    'value' => '',
    'name' => 'name',
    'errors' => null,
])
<div {{ $attributes->only('class')->merge(['class' => 'text-editor']) }}>
    @if ($label)
        <x-admin::label :for="$name">
            {{ __($label) }}
        </x-admin::label>
    @endif
    <input type="hidden" id="{{ $id }}_input" name="{{ $name }}" value="{{ $value }}"
        {{ $attributes->whereStartsWith('x-bind') }} {{ $attributes->whereStartsWith(':') }}>
    <div id="{{ $id }}" class="text-editor-content" {{ $attributes->whereStartsWith('x-bind') }}
        {{ $attributes->whereStartsWith(':') }}>
        {!! html_entity_decode($value) !!}
    </div>
    @if ($errors && $errors->has($name))
        <span class="input-text-error text-red-500 text-sm mt-1">{{ $errors->first($name) }}</span>
    @endif
</div>
@push('scripts')
    @vite(['resources/shared/css/editor.css', 'resources/shared/js/editor.js'])
@endpush
