@props([
    'disabled' => false,
    'name' => 'phone',
    'label' => '',
    'errors' => null,
    'value' => null,
    'placeholder' => '000.00',
    'with_currencySymbol' => true,
])

<div class="input-group">
    <x-admin::label for="{{ $name }}">{{ $label }}</x-admin::label>
    <div
        class="group-input flex items-center w-full pl-3 border dark:bg-neutral-904 border-neutral-30 dark:border-neutral-500 text-sm rounded-lg">
        <input type="number" id="amount-two" name="{{ $name }}" value="{{ $value }}"
            class="w-full py-2 xl:py-2.5 bg-transparent" placeholder="{{ $placeholder }}">
        @if ($with_currencySymbol)
            <span class="bg-neutral-20 f-center  size-10 dark:bg-neutral-600 rounded-e-lg">{{ currencySymbol() }}</span>
        @endif
    </div>
    <x-admin::input-error :errors="$errors" :name="$name" />
</div>
