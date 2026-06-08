<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Create New Admin User') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.admins.store') }}" class="user-information-update flex flex-col gap-4"
            method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-admin::text-input-group name="first_name" label="First Name"
                    placeholder="{{ __('Enter Your First Name') }}" />
                <x-admin::text-input-group name="last_name" label="Last Name"
                    placeholder="{{ __('Enter Your Last Name') }}" />
                <x-admin::text-input-group name="email" type="email" label="Email"
                    placeholder="{{ __('Enter Your Email') }}" />
                <x-admin::phone-number-input-group name="phone" label="{{ __('Phone') }}"
                    placeholder="{{ __('Enter Your Phone') }}" />

                <div>
                    <x-admin::label for="admin_role_id">{{ __('Select Role') }}</x-admin::label>
                    <x-admin::select-option label="Select Role" id="admin_role_id" name="admin_role_id">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </x-admin::select-option>
                    <x-admin::input-error :errors="$errors" name="admin_role_id" />
                </div>
                <div>
                    <x-admin::label for="email">{{ __('Select Status') }}</x-admin::label>
                    <x-admin::switch label="Status" name="status" />
                    <x-admin::input-error :errors="$errors" name="status" />
                </div>
                <x-admin::text-input-group name="password" label="Password" type="password"
                    placeholder="Enter Your Password" />
                <x-admin::text-input-group name="password_confirmation" label="Confirm Password" type="password"
                    placeholder="Enter Your Password" />
            </div>
            <x-admin::primary-button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </x-admin::primary-button>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/admin-user/admin.js')
    @endpush
</x-admin-app-layout>
