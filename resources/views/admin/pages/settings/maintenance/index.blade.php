<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Maintenance Mode') }}" :isFilterable="false" />
        <form action="{{ route('admin.settings.maintenance.store') }}" method="POST" class="form-submit-add">
            @csrf
            <div class="flex flex-col gap-4 xxl:gap-6">
                <span class="text-sm text-red-500">
                    {{ __('Please enter secure secret key and save it for later to login to admin from maintenance mode. Do not share it with anyone and keep it safe until maintenance mode is deactivated.') }}
                </span>
                <x-admin::text-input-group name="login_secret_key" :value="isset($maintenance['login_secret_key'])
                    ? $maintenance['login_secret_key']
                    : auth('admin')->user()->email"
                    label="Login Secret Key (Please enter a secret key for admin login enable when maintenance mode is enabled)" />
                <div>
                    <x-admin::label for="image">{{ __('Image') }}</x-admin::label>
                    <x-admin::file-uploader id="maintenance_image" type="text" name="image" :value="isset($maintenance['image']) ? $maintenance['image'] : ''"
                        label="Image" />
                    <x-admin::input-error name="image" />
                </div>
                <x-admin::textarea-group name="description" :value="isset($maintenance['description']) ? $maintenance['description'] : ''" label="Description" />
                <x-admin::text-input-group name="countdown" type="datetime-local" :value="isset($maintenance['countdown']) ? $maintenance['countdown'] : ''" label="Countdown" />
                <div class="input-group">
                    <x-admin::label for="status">{{ __('Status') }}</x-admin::label>
                    <x-admin::switch name="status" id="status" :value="isset($maintenance['status']) ? $maintenance['status'] : 0" :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button>
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/maintenance.js')
    @endpush
</x-admin-app-layout>
