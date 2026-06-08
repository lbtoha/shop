<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Extensions') }}" :isFilterable="false" />
        <x-admin::table :columns="$columns" :data="$extensions" :is_pagination="false" />
        <x-admin::modal modalId="recaptcha" title="{{ __('Edit Recaptcha Credentials') }}">
            <form class="form-submit-edit" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="site_key" label="{{ __('Site Key') }}" />
                    <x-admin::text-input-group name="secret_key" label="{{ __('Secret Key') }}" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="google_analytics" title="Edit Google Analytics Credentials">
            <form class="form-submit-edit" method="POST">
                @csrf
                <x-admin::text-input-group name="measurement_id" label="Measurement Id" />
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="tawk_to" title="{{ __('Edit Tawk.to Credentials') }}">
            <form class="form-submit-edit" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="property_id" label="Property Id" />
                    <x-admin::text-input-group name="widget_id" label="Widget Id" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
    </div>
</x-admin-app-layout>
