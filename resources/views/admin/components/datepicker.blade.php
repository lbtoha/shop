@props([
    'disabled' => false,
    'type' => 'text',
    'level' => '',
    'id' => '',
    'name' => 'name',
    'errors' => null,
    'value' => null,
])

<div class="form-input">
    <input type="{{ $type }}" @disabled($disabled) id="{{ $id }}" value="{{ $value }}"
        name="{{ $name }}" class="{{ $errors->has($name) ? 'input-error' : '' }}"
        placeholder="{{ __('Enter Text') }}" />
    <label for="name">{{ $level }}</label>
</div>
