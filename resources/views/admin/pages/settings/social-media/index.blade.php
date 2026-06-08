<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Social Media') }}" :dateFilter="false" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$socials" :is_pagination="false" />
        <x-admin::modal modalId="social_add_modal_id" title="Add New Social Media">
            <form class="form-submit-add" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="name" label="Name" />
                    <x-admin::text-input-group name="link" label="Link" />
                    <div class="input-group">
                        <x-admin::label for="icon">
                            {{ __('Icon') }}
                        </x-admin::label>
                        <x-admin::icon-picker name="icon" label="Icon" />
                        <x-admin::input-error name="icon" />
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="social_edit_modal_id" title="Edit Social Media">
            <form class="form-submit-edit" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="name" label="Name" />
                    <x-admin::text-input-group name="link" label="Link" />
                    <div class="input-group">
                        <x-admin::label for="icon">
                            {{ __('Icon') }}
                        </x-admin::label>
                        <x-admin::icon-picker name="icon" label="Icon" />
                        <x-admin::input-error name="icon" />
                    </div>
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
