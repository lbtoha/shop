
@props([
    'disabled' => false,
    'required' => false,
    'type' => 'text',
    'name' => 'name',
    'label' => '',
    'errors' => null,
    'value' => null,
    'class' => '',
    'placeholder' => __('Enter Text'),
])

<div class="input-group">
    <x-admin::label :for="$name">{{ __($label) }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </x-admin::label>
    <x-admin::text-input :name="$name" :disabled="$disabled" :type="$type" :value="$value" :placeholder="__($placeholder)" :class="$class" />
    <x-admin::input-error :errors="$errors" :name="$name" />
</div>
