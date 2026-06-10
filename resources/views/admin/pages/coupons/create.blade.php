<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Create New Coupon') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.coupons.store') }}" class="form-submit-edit" method="POST">
            @csrf
            @include('admin.pages.coupons._form', ['coupon' => null])
        </form>
    </div>
</x-admin-app-layout>
