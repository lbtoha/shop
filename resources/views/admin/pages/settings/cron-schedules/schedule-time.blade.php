<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Schedule Time') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$schedule_times" />
        <x-admin::modal modalId="schedule_time_create_modal" title="Add New Time">
            <form class="form-submit-add" method="POST">
                @csrf
                <x-admin::text-input-group name="name" label="Name" />

                <x-admin::text-input-group id="interval" type="text" name="interval" label="Interval" />

                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button type="submit">
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="schedule_time_edit_modal" title="Edit Time">
            <form class="form-submit-edit" method="POST">
                @csrf
                <x-admin::text-input-group name="name" label="Name" />

                <x-admin::text-input-group id="interval" type="text" name="interval" label="Interval" />

                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
    </div>
</x-admin-app-layout>
