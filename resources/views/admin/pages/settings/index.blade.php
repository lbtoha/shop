<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('All Settings') }}" :dateFilter="false" />
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 xl:gap-6">
            @foreach ($menus as $menu)
                <div class="primary5-box">
                    <div class="flex justify-between items-center mb-4 xl:mb-6">
                        <span class="size-12 bg-primary/90 text-neutral-0 rounded-full text-3xl f-center">
                            <i class="{{ $menu['icon'] }}"></i>
                        </span>
                    </div>
                    <div class="mb-6 xl:mb-8">
                        <p class="m-text font-medium mb-2">{{ __($menu['title']) }}</p>
                        <p class="text-xs">{{ __($menu['text']) }}</p>
                    </div>
                    <a href="{{ $menu['link'] }}" class="btn-primary outlined w-full"> <i
                            class="ph ph-gear"></i>{{ __('Settings') }}</a>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-app-layout>
