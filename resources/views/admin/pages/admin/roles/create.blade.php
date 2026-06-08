<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Create New Admin User') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.admin-roles.store') }}" class="form-submit-edit" method="POST">
            @csrf
            <x-admin::text-input-group name="name" label="Name" placeholder="Enter Name" />
            <div class="my-4 flex flex-col gap-4 xxl:gap-6">
                <label class="">{{ __('Permissions') }}</label>
                <div class="flex flex-wrap gap-4 xxl:gap-6">
                    @foreach (config('caps.permissions') as $key => $cap)
                        <label for="{{ $cap }}" class="option max-lg:justify-end">
                            <input type="checkbox" id="{{ $cap }}" name="caps[{{ $loop->index }}]"
                                aria-checked="false" value="{{ $key }}" />
                            <span class="checkbox"></span>
                            <span class="uppercase">{{ $cap }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="my-4 flex flex-col gap-4 xxl:gap-6">
                <label class="">{{ __('Admin Menu Permission') }}</label>
                <!-- HTML Structure -->
                <div class="flex flex-wrap gap-4 xxl:gap-6">
                    @foreach (config('menu.admin.menu') as $key => $menu)
                        @include('admin.pages.admin.roles.dynamic-link-checkbox', [
                            'menu' => $menu,
                            'name' => 'module_caps',
                        ])
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/admin-user/roles.js')
    @endpush
</x-admin-app-layout>
