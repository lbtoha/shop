<x-admin-app-layout>
    <div class="flex justify-between items-center gap-3 flex-wrap mb-6">
        <p class="l-text font-medium">{{ __('Notification Settings') }}</p>
        <div
            class="flex flex-wrap gap-3 p-1 rounded-lg lg:rounded-full border border-neutral-30 dark:border-neutral-500 relative">
            @foreach ($buttons as $tab_button)
                <a href="{{ $tab_button['link'] }}"
                    class="px-4 relative z-[3] py-2 flex items-center gap-2 text-sm rounded-full {{ request()->fullUrl() == $tab_button['link'] ? 'text-neutral-0 bg-primary' : '' }}">{{ $tab_button['title'] }}</a>
            @endforeach
        </div>
    </div>
    @switch($service)
        @case('global-email')
            @include('admin.pages.settings.notifications.template-edit', [
                'default_template' => $defaultTemplate,
            ])
        @break

        @case('mail')
            @include('admin.pages.settings.notifications.email-setting')
        @break

        @case('sms')
            @include('admin.pages.settings.notifications.sms-setting')
        @break

        @case('push')
            @include('admin.pages.settings.notifications.push-setting')
        @break

        @case('template')
            @include('admin.pages.settings.notifications.templates.list', [
                'columns' => $columns,
                'templates' => $templates,
                'template_buttons' => $template_buttons,
            ])
        @break

        @default
            @include('admin.pages.settings.notifications.template-edit', [
                'default_template' => $defaultTemplate,
            ])
    @endswitch
    @push('scripts')
        @vite('resources/admin/js/settings/notifications.js')
    @endpush
</x-admin-app-layout>
