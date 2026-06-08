@props([
    'disabled' => false,
    'name' => 'phone',
    'label' => '',
    'select_option_name' => 'symbol',
    'errors' => null,
    'value' => null,
    'placeholder' => __('Enter Text'),
])
@push('styles')
    @vite('resources/shared/css/phone.css')
@endpush

<div class="input-group">
    <x-admin::label for="{{ $name }}">{{ $label }}</x-admin::label>
    <input id="phone_number" value="{{ $value }}" class="text-input bg-transparent w-full" autocomplete="cc-number"
        type="tel">
    <x-admin::input-error :errors="$errors" :name="$name" />
</div>

@push('scripts')
    @vite('resources/shared/js/phone.js')
@endpush
