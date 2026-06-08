<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Task Schedules') }}" :buttons="$buttons" />
        <x-admin::table :columns="$columns" :data="$task_schedules" />
        <x-admin::modal modalId="task_schedule_create_modal" title="{{ __('Add New Task Schedule') }}">
            <form class="form-submit-add" method="POST">
                @csrf
                <x-admin::text-input-group name="name" label="Name" />

                <div>
                    <x-admin::label for="schedule_time_id">{{ __('Schedule Time') }}</x-admin::label>
                    <x-admin::select-option class="select-2" name="schedule_time_id" label="Name">
                        @foreach ($task_times as $task_time)
                            <option value="{{ $task_time->id }}">{{ $task_time->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                    <x-admin::input-error name="schedule_time_id" />
                </div>

                <x-admin::text-input-group name="command" label="Command" />

                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button type="submit">
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
        <x-admin::modal modalId="task_schedule_edit_modal" title="Edit Task Schedule">
            <form class="form-submit-edit" method="POST">
                @csrf
                <x-admin::text-input-group name="name" label="Name" />

                <div>
                    <x-admin::label for="schedule_time_id">{{ __('Schedule Time') }}</x-admin::label>
                    <x-admin::select-option class="select-2" name="schedule_time_id" label="Name">
                        @foreach ($task_times as $task_time)
                            <option value="{{ $task_time->id }}">{{ $task_time->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                    <x-admin::input-error name="schedule_time_id" />
                </div>

                <x-admin::text-input-group name="command" label="Command" />

                <div class="flex items-center justify-end mt-4">
                    <x-admin::primary-button>
                        {{ __('Save') }}
                    </x-admin::primary-button>
                </div>
            </form>
        </x-admin::modal>
    </div>
</x-admin-app-layout>
