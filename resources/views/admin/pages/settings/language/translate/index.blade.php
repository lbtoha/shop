<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Translations') }}" :buttons="$buttons" :dateFilter="false" />
        <x-admin::table :columns="$columns" :data="$translations" />
        <x-admin::modal title="Add New Key" modalId="translate_key_add_modal">
            <form class="form-submit-add" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="key" label="Key" placeholder="Enter Your Name" />
                    <x-admin::text-input-group name="value" label="Value" placeholder="Enter Your Name" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal title="Edit Key Value" modalId="translate_key_edit_modal">
            <form class="form-submit-edit" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="key" label="Key" placeholder="Enter Your Name" />
                    <x-admin::text-input-group name="value" label="Value" placeholder="Enter Your Name" />
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
