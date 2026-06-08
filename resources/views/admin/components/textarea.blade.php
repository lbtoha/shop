@props([
    'disabled' => false,
    'name' => 'name',
    'errors' => null,
    'id' => null,
    'class' => '',
    'value' => null,
    'placeholder' => __('Enter Text'),
    'rows' => '4',
])

<textarea name="{{ $name }}" rows="{{ $rows }}"
    class="{{ $errors->has($name) ? 'input-error' : '' }} {{ $class }} text-input" id="{{ $id }}"
    placeholder="{{ $placeholder }}" id="{{ $id ?? $name }}">{{ $value }}</textarea>
