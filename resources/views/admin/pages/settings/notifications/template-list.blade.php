<div>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Templates') }}" />
        <x-admin::table :columns="$columns" :data="$templates" />
    </div>
</div>
