@props([
    'select_name' => 'name',
    'number_name' => 'name',
    'select_options' => [['value' => 'fixed', 'label' => 'Fixed'], ['value' => 'percentage', 'label' => 'Percentage']],
    'label' => '',
    'errors' => null,
    'select_value' => null,
    'number_value' => null,
    'placeholder' => '000.00',
])

<div class="input-group">
    <x-admin::label for="{{ $number_value }}">{{ $label }}</x-admin::label>
    <div
        class="group-input flex justify-between w-full border gap-2 border-neutral-30 dark:border-neutral-500 text-sm rounded-lg">
        <div class="flex gap-3">
            <select name="{{ $select_name }}" id="{{ $select_name }}" value="{{ $select_value }}"
                class="number-select bg-transparent focus:outline-none px-3 py-2 xl:py-2.5">
                @foreach ($select_options as $option)
                    <option value="{{ $option['value'] }}" @selected($option['value'] == $select_value)>{{ $option['label'] }}</option>
                @endforeach
            </select>
            <input type="number" id="{{ $number_name }}" name="{{ $number_name }}" value="{{ $number_value }}"
                class="py-2 xl:py-2.5 bg-transparent w-full" placeholder="{{ $placeholder }}"
                autocomplete="transaction-amount">
        </div>
        <span id="symbol" class="icon bg-neutral-20 f-center  size-10 dark:bg-neutral-600 rounded-e-lg">$</span>
    </div>
    <x-admin::input-error :errors="$errors" :name="$number_name" />
</div>
