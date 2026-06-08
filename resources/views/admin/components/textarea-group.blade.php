@props([
    'name' => 'name',
    'label' => '',
    'required' => false,
    'errors' => null,
    'value' => null,
    'id' => null,
    'placeholder' => __('Enter Text'),
])

<div class="input-group">
    <x-admin::label :for="$name">{{ __($label) }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </x-admin::label>
    <x-admin::textarea :name="$name" :value="$value" :id="$id ?? $name" :placeholder="$placeholder" />
    <x-admin::input-error :errors="$errors" :name="$name" />
</div>
