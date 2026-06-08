<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('GDPR Cookie') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.gdpr-cookies.store') }}" method="POST" class="form-submit-add">
            @csrf
            <div class="flex flex-col gap-4 xxl:gap-6">
                <x-admin::text-input-group name="title" label="{{ __('Title') }}" :value="isset($gdpr_cookies['title']) ? $gdpr_cookies['title'] : 'Cookie Policy'" />
                <x-admin::textarea-group name="description" :value="isset($gdpr_cookies['description']) ? $gdpr_cookies['description'] : ''" label="Description" />
                <div>
                    <x-admin::label for="is_enabled">{{ __('Enable') }}</x-admin::label>
                    <x-admin::switch label="Enable" name="is_enabled" :value="isset($gdpr_cookies['is_enabled']) ? $gdpr_cookies['is_enabled'] : 0" :types="[['label' => __('No'), 'value' => 0], ['label' => __('Yes'), 'value' => 1]]" />
                    <x-admin::input-error name="is_enabled" />
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button>
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
