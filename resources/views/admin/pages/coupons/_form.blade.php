@php($c = $coupon ?? null)
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 xl:gap-6">
    <x-admin::text-input-group name="code" label="Coupon Code" placeholder="SAVE20" :value="$c?->code" :required="true" class="uppercase" />

    <div class="input-group">
        <x-admin::label for="type">{{ __('Discount Type') }} <span class="text-danger">*</span></x-admin::label>
        <x-admin::select-option id="type" name="type">
            <option value="fixed" @selected(($c?->type ?? 'fixed') === 'fixed')>{{ __('Fixed amount') }}</option>
            <option value="percent" @selected($c?->type === 'percent')>{{ __('Percentage') }}</option>
        </x-admin::select-option>
    </div>

    <x-admin::text-input-group name="value" type="number" label="Discount Value" placeholder="10" :value="$c?->value !== null ? (float) $c->value : ''" :required="true" />

    <x-admin::text-input-group name="max_discount" type="number" label="Max Discount (percent only, optional)" placeholder="—" :value="$c?->max_discount !== null ? (float) $c?->max_discount : ''" />

    <x-admin::text-input-group name="min_subtotal" type="number" label="Minimum Order Subtotal" placeholder="0" :value="$c?->min_subtotal !== null ? (float) $c?->min_subtotal : '0'" />

    <x-admin::text-input-group name="usage_limit" type="number" label="Usage Limit (blank = unlimited)" placeholder="—" :value="$c?->usage_limit" />

    <x-admin::text-input-group name="starts_at" type="date" label="Starts On (optional)" :value="$c?->starts_at?->format('Y-m-d')" placeholder="" />

    <x-admin::text-input-group name="expires_at" type="date" label="Expires On (optional)" :value="$c?->expires_at?->format('Y-m-d')" placeholder="" />

    <div class="input-group">
        <x-admin::label for="is_active">{{ __('Active') }}</x-admin::label>
        <x-admin::switch name="is_active" id="is_active" :value="($c?->is_active ?? true) ? 1 : 0" :types="[['label' => __('Inactive'), 'value' => 0], ['label' => __('Active'), 'value' => 1]]" />
    </div>
</div>

<div class="flex items-center justify-end mt-4">
    <x-admin::primary-button type="submit">{{ __('Save') }}</x-admin::primary-button>
</div>
