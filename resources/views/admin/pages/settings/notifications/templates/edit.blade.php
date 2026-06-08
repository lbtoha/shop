<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Edit Template') }}" :buttons="$buttons" :isFilterable="false" />
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
                <p class="m-text font-medium">{{ __('Short Codes') }}</p>
            </div>
            <div class="flex flex-col lg:flex-row">

                <ul class="s-text flex-1" x-data="{
                    isOpenId: null,
                    copyShortCode(shortCode) {
                        navigator.clipboard.writeText(shortCode);
                        this.isOpenId = shortCode;
                
                        setTimeout(() => {
                            this.isOpenId = null;
                        }, 1000);
                    }
                }">
                    @foreach ($notificationTemplate->short_codes as $key => $short_code)
                        <li class="border-b border-neutral-30 flex justify-between items-center dark:border-neutral-500">
                            <span @click="copyShortCode('{{ $key }}')" class="p-5 inline-block cursor-pointer"
                                :class="{ 'text-primary': isOpenId == '{{ $key }}' }"
                                x-text="isOpenId == '{{ $key }}' ? 'Copied' : '{{ $key }}'"></span>
                            <span class="p-5 inline-block">{{ $short_code['hint'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
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
                    @php
                        $email_template = $notificationTemplate->bodies()->where('channel', 'email')->first();

                    @endphp
                    <form action="{{ route('admin.settings.notification.templates.update', $email_template->id) }}"
                        method="POST" class="space-y-6 form-submit-edit">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <x-admin::text-input-group name="subject" label="Subject"
                                    value="{{ $email_template->subject }}" />
                            </div>
                            <div>
                                <x-admin::label for="is_active">{{ __('Status') }}</x-admin::label>
                                <x-admin::switch :value="$email_template->is_active" name="is_active" :types="[
                                    ['label' => __('Inactive'), 'value' => 0],
                                    ['label' => __('Active'), 'value' => 1],
                                ]" />
                                <x-admin::input-error name="is_active" />
                            </div>
                        </div>
                        <div>
                            <x-admin::editor label="Email Template Body" id="email_body_editor" name="body"
                                :value="$email_template->body" :errors="$errors" />
                            <x-admin::input-error name="body" />
                        </div>

                        <div class="flex gap-4">
                            <x-admin::primary-button type="submit">
                                {{ __('Save') }}
                            </x-admin::primary-button>
                        </div>
                    </form>
                </div>
                <div class="tab-panel hidden">
                    @php
                        $sms_template = $notificationTemplate->bodies()->where('channel', 'sms')->first();
                    @endphp
                    <form action="{{ route('admin.settings.notification.templates.update', $sms_template->id) }}"
                        method="POST" class="space-y-6 form-submit-edit">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <x-admin::text-input-group name="subject" label="Subject"
                                    value="{{ $sms_template->subject }}" />
                            </div>
                            <div>
                                <x-admin::label for="is_active">{{ __('Status') }}</x-admin::label>
                                <x-admin::switch :value="$sms_template->is_active" name="is_active" :types="[
                                    ['label' => __('Inactive'), 'value' => 0],
                                    ['label' => __('Active'), 'value' => 1],
                                ]" />
                                <x-admin::input-error name="is_active" />
                            </div>
                        </div>

                        <x-admin::textarea-group :value="$sms_template->body" name="body"
                            label="Body"></x-admin::textarea-group>

                        <div class="flex gap-4">
                            <x-admin::primary-button type="submit">
                                {{ __('Save') }}
                            </x-admin::primary-button>
                        </div>
                    </form>
                </div>
                <div class="tab-panel hidden">
                    @php
                        $push_template = $notificationTemplate->bodies()->where('channel', 'push')->first();
                    @endphp
                    <form action="{{ route('admin.settings.notification.templates.update', $push_template->id) }}"
                        method="POST" class="space-y-6 form-submit-edit">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <x-admin::text-input-group name="subject" label="Subject"
                                    value="{{ $push_template->subject }}" />
                            </div>
                            <div>
                                <x-admin::label for="is_active">{{ __('Status') }}</x-admin::label>
                                <x-admin::switch :value="$push_template->is_active" name="is_active" :types="[
                                    ['label' => __('Inactive'), 'value' => 0],
                                    ['label' => __('Active'), 'value' => 1],
                                ]" />
                                <x-admin::input-error name="is_active" />
                            </div>
                        </div>

                        <x-admin::textarea-group :value="$push_template->body" name="body"
                            label="Body"></x-admin::textarea-group>

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
        @vite('resources/admin/js/settings/notifications.js')
    @endpush
</x-admin-app-layout>
