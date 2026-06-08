<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Create Template') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.notification.templates.store') }}" method="POST"
            class="space-y-6 form-submit-add">
            @csrf
            <x-admin::text-input-group name="name" label="Title" placeholder="{{ __('Enter Title') }}" />
            <div class="tabs">
                <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
                    <p class="m-text font-medium">{{ __('Enter Template Information') }}</p>
                    <div
                        class="flex flex-wrap gap-3 p-1 rounded-lg md:rounded-full border border-neutral-30 dark:border-neutral-500 relative">
                        <button
                            class="tab-link active px-4 relative z-[3] py-2 flex items-center gap-2 text-sm rounded-full">
                            <i class="ph ph-envelope-simple"></i>{{ __('Email Template') }}</button>
                        <button class="tab-link px-4 relative z-[3] py-2 flex items-center gap-2 text-sm rounded-full">
                            <i class="ph ph-device-mobile"></i>{{ __('SMS Template') }}</button>
                        <button class="tab-link px-4 relative z-[3] py-2 flex items-center gap-2 text-sm rounded-full">
                            <i class="ph ph-bell-ringing"></i>{{ __('Push Notification') }}</button>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-panel">
                        <input type="hidden" value="email" name="bodies[0][channel]">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <x-admin::text-input-group name="bodies[0][subject]" label="Subject" />
                            </div>
                            <div>
                                <x-admin::label for="bodies[0][is_active]">{{ __('Status') }}</x-admin::label>
                                <x-admin::switch name="bodies[0][is_active]" :types="[
                                    ['label' => __('Inactive'), 'value' => 0],
                                    ['label' => __('Active'), 'value' => 1],
                                ]" />
                                <x-admin::input-error name="bodies[0][is_active]" />
                            </div>
                        </div>
                        <div>
                            <x-admin::editor label="Email Template Body" id="email_body_editor" name="bodies[0][body]"
                                :errors="$errors" />
                            <x-admin::input-error name="bodies[0][body]" />
                        </div>
                    </div>
                    <div class="tab-panel hidden">
                        <input type="hidden" value="sms" name="bodies[1][channel]">
                        <div class="flex flex-col gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="col-span-3">
                                    <x-admin::text-input-group name="bodies[1][subject]" label="Subject" />
                                </div>
                                <div>
                                    <x-admin::label for="bodies[1][is_active]">{{ __('Status') }}</x-admin::label>
                                    <x-admin::switch name="bodies[1][is_active]" :types="[
                                        ['label' => __('Inactive'), 'value' => 0],
                                        ['label' => __('Active'), 'value' => 1],
                                    ]" />
                                    <x-admin::input-error name="bodies[1][is_active]" />
                                </div>
                            </div>
                            <x-admin::textarea-group name="bodies[1][body]" label="Body"></x-admin::textarea-group>
                        </div>
                    </div>
                    <div class="tab-panel hidden">
                        <input type="hidden" value="push" name="bodies[2][channel]">
                        <div class="flex flex-col gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="col-span-3">
                                    <x-admin::text-input-group name="bodies[2][subject]" label="Subject" />
                                </div>
                                <div>
                                    <x-admin::label for="bodies[2][is_active]">{{ __('Status') }}</x-admin::label>
                                    <x-admin::switch name="bodies[2][is_active]" :types="[
                                        ['label' => __('Inactive'), 'value' => 0],
                                        ['label' => __('Active'), 'value' => 1],
                                    ]" />
                                    <x-admin::input-error name="bodies[2][is_active]" />
                                </div>
                            </div>
                            <x-admin::textarea-group name="bodies[2][body]" label="Body"></x-admin::textarea-group>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/notifications.js')
    @endpush
</x-admin-app-layout>
