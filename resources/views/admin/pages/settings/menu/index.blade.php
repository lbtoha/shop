<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Main Menus') }}" :buttons="$buttons" :dateFilter="false" />
        <x-admin::table :columns="$columns" :data="$menus" />
        <x-admin::modal :modalId="$menu_add_modal_id" title="Add New Menu">
            <form title="Add New Menu" class="form-submit-add" method="POST">
                @csrf
                <x-admin::text-input-group name="name" label="Name" placeholder="Enter menu title" />
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button type="submit">
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal :modalId="$menu_edit_modal_id" title="Edit Menu">
            <form class="form-submit-edit" method="POST">
                @csrf
                <x-admin::text-input-group name="name" label="Name" placeholder="Enter menu title" />

                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
    </div>
</x-admin-app-layout>
