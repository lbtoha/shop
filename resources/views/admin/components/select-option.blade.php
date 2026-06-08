@props([
    'name' => 'name',
    'id' => '',
    'errors' => null,
    'value' => null,
    'options' => [],
    'multiple' => false,
    'class' => 'select-2',
    'url' => null,
    'placeholder' => null,
])
<select {{ $attributes->merge(['class' => $class, 'name' => $name, 'id' => $id]) }}
    @if ($multiple) multiple="multiple" @endif
    @if ($placeholder) data-placeholder="{{ $placeholder }}" @endif
    @if ($url) data-url="{{ $url }}" @endif>
    {{ $slot }}
</select>
