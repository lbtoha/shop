@props([
    'disabled' => false,
    'type' => 'text',
    'name' => 'name',
    'errors' => null,
    'value' => null,
    'placeholder' => __('Enter Text'),
    'class' => '',
])

<input type="{{ $type }}" @disabled($disabled) id="{{ $name }}" value="{{ $value }}"
    name="{{ $name }}" class="{{ $errors->has($name) ? 'input-error' : '' }} text-input {{ $class }}"
    placeholder="{{ $placeholder }}" />
