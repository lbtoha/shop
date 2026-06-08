<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Social Login') }}" :dateFilter="false" />
        <x-admin::table :columns="$columns" :data="$socials" :is_pagination="false" />
        <x-admin::modal modalId="social_edit_modal_id" title="Edit Social Login Credentials">
            <form class="form-submit-edit" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="client_id" label="Client Id" />
                    <x-admin::text-input-group name="client_secret" label="Client Secret" />
                    <x-admin::text-input-group name="redirect" label="Redirect" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="social_help_modal_id" title="Help">
            <div>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('How to get client id and secret key') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('To get the client id and secret key, you can follow the following steps:') }}
                    </p>
                </div>
            </div>
        </x-admin::modal>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/social-login.js')
    @endpush
</x-admin-app-layout>
