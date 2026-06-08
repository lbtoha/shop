<aside class="sidebar opened">
    <div
        class="px-3 xxl:px-4 py-3 sm:py-4 flex justify-between items-center border-b border-dashed border-neutral-30 dark:border-neutral-500">
        <a href="/admin" class="text-primary flex gap-3 items-start">
            <img class="h-9 application-logo" src="{{ config('application_info.logo_favicon.logo_light') }}" alt="logo"
                data-dark-logo="{{ config('application_info.logo_favicon.logo_dark') }}"
                data-light-logo="{{ config('application_info.logo_favicon.logo_light') }}" />
        </a>
        <button class="xl:hidden text-xl sidebar-close-btn">
            <i class="ph ph-x"></i>
        </button>
    </div>
    <div class="overflow-y-auto h-[90%] px-3 xxl:px-4 pb-12 custom-scrollbar-hovered pt-4 vertical-sidebar">
        <div class="space-y-2">
            @foreach (authorizedMenus(config('menu.admin.menu'), auth('admin')->user()) as $key => $menu)
                @if (!isset($menu['submenus']))
                    <a href="{{ route($menu['link']) }}"
                        class="submenu-btn single-menu px-3 py-2 hover:bg-primary hover:text-white {{ isCurrentUrlMatched($menu['link']) ? 'menu_active' : '' }}">
                        <div class="flex items-center gap-2">
                            <i class="{{ $menu['icon'] }} text-[22px]"></i>
                            {{ __($menu['title']) }}
                        </div>
                    </a>
                @else
                    <!-- Menu Item with Submenu -->
                    @include('admin.partials.navigation.submenu', ['menu' => $menu])
                @endif
            @endforeach
        </div>
    </div>
</aside>
