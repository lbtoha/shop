<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Coupon') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" class="form-submit-edit" method="POST">
            @csrf @method('PUT')
            @include('admin.pages.coupons._form', ['coupon' => $coupon])
        </form>
    </div>
</x-admin-app-layout>
