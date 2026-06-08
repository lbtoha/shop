<div class="white-box">
    <x-admin::page-header title="{{ __('Templates') }}" :buttons="$template_buttons" />
    <x-admin::table :columns="$columns" :data="$templates" />
</div>
