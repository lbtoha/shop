<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Send Notification To User') }}" :isFilterable="false" />
        <div class="white-box tabs">
            <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
                <p class="m-text font-medium">{{ __('Enter Template Information') }}</p>
                <div
                    class="flex flex-wrap gap-3 p-1 rounded-lg md:rounded-full border border-neutral-30 dark:border-neutral-500 relative">
                    <button
                        class="tab-link active px-4 relative z-[3] py-2 flex items-center gap-2 text-sm rounded-full">
                        <i class="ph ph-envelope-simple"></i>{{ __('Email Template') }}</button>
                    <button class="tab-link px-4 relative z-[3] py-2 flex items-center gap-2 text-sm rounded-full">
                        <i class="ph ph-device-mobile"></i>{{ __('SMS Template') }}</button>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-panel">
                    <form action="{{ route('admin.buyers.notifications.store') }}" method="POST"
                        class="space-y-6 form-submit-add">
                        @csrf
                        <input type="hidden" name="type" value="email">
                        <div>
                            <x-admin::label for="user_send_type1">{{ __('Being Sent To') }}</x-admin::label>
                            <x-admin::select-option id="user_send_type2" name="user_send_type" label="Subject">
                                @foreach ($send_types as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </x-admin::select-option>
                            <x-admin::input-error name="user_send_type" />
                        </div>
                        <div>
                            <x-admin::label for="template_id1">{{ __('Select a Template') }}</x-admin::label>
                            <x-admin::select-option id="template_id2" name="template_id" label="Subject">
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </x-admin::select-option>
                            <x-admin::input-error name="template_id" />
                        </div>
                        <x-admin::text-input-group id="subject" name="subject" label="Subject" />
                        <x-admin::editor label="Email Template Body" name="body" id="email_body_editor"
                            name="body" :errors="$errors" />
                        <div class="flex gap-4">
                            <x-admin::primary-button type="submit">
                                {{ __('Save') }}
                            </x-admin::primary-button>
                        </div>
                    </form>
                </div>
                <div class="tab-panel hidden">

                    <form action="{{ route('admin.buyers.notifications.store') }}" method="POST"
                        class="space-y-6 form-submit-add">
                        @csrf
                        <input type="hidden" name="type" value="sms">
                        <div class="flex flex-col gap-4">
                            <x-admin::label for="user_send_type1">{{ __('Being Sent To') }}</x-admin::label>
                            <x-admin::select-option id="user_send_type1" name="user_send_type" label="Subject">
                                @foreach ($send_types as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </x-admin::select-option>
                            <x-admin::input-error name="user_send_type" />
                        </div>
                        <div class="flex flex-col gap-4">
                            <x-admin::label for="template_id1">{{ __('Select a Template') }}</x-admin::label>
                            <x-admin::select-option id="template_id1" name="template_id" label="Subject">
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </x-admin::select-option>
                            <x-admin::input-error name="template_id" />
                        </div>
                        <x-admin::text-input-group id="subject" name="subject" label="Subject" />

                        <x-admin::textarea-group name="body" label="Body"></x-admin::textarea-group>

                        <div class="flex gap-4">
                            <x-admin::primary-button type="submit">
                                {{ __('Save') }}
                            </x-admin::primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @vite('resources/admin/js/manage-user/send-notification.js')
    @endpush
</x-admin-app-layout>
