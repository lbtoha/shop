@if (isset($menu['parent_menu']))
    <div class="submenu-item {{ isSubmenuActive($menu) ? 'active' : '' }}">
        @if (Str::is('admin/settings', $menu['parent']))
            <div
                class="flex justify-between items-center submenu-btn hover:bg-primary hover:text-white {{ isSubmenuActive($menu) ? 'bg-primary text-white' : '' }}">
                <a href="{{ route($menu['link']) }}" class="flex items-center justify-start gap-2 w-full px-3 py-2">
                    <i class="{{ $menu['icon'] }} text-[22px]"></i>
                    {{ __($menu['title']) }}
                </a>
                <button type="button"
                    class="submenu-toggle px-3 border-l-1 rtl:border-l-0 rtl:border-r-1 border-gray-300">
                    <i class="ph ph-caret-down"></i>
                </button>
            </div>
        @else
            <button class="submenu-btn parent">
                <div class="flex items-center gap-2">
                    <i class="{{ $menu['icon'] }} text-[22px]"></i>
                    {{ __($menu['title']) }}
                </div>
                <i class="ph ph-caret-down"></i>
            </button>
        @endif
        <div class="submenu-content {{ isSubmenuActive($menu) ? 'block' : 'hidden' }}">
            <ul>
                @foreach ($menu['submenus'] as $sub_menu)
                    @if (isset($sub_menu['submenus']))
                        @include('admin.partials.navigation.submenu', [
                            'menu' => $sub_menu,
                            'top_parent' => $menu['parent'],
                        ])
                    @else
                        <li
                            class="flex sidebar-link py-1.5 text-sm {{ isCurrentUrlMatched($sub_menu['link']) ? 'submenu_active' : '' }}">
                            <a href="{{ route($sub_menu['link']) }}">{{ __($sub_menu['title']) }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@else
    <li class="submenu-item">
        @if (Str::is('admin/settings', value: $top_parent))
        @php
            $parentMenuPrefix = Str::afterLast($top_parent, '/'. $menu['parent']);
        @endphp
            <div class="flex justify-between items-center submenu-btn hover:text-primary">
                <a href="{{ route('admin.settings.index', ['parent' => $menu['parent']]) }}"
                    class="flex items-center justify-start gap-2 w-full px-1 py-2 text-sm {{ isUrlActiveByParentKey($menu['parent'], ) ? 'submenu_active' : '' }}">
                    @if (!empty($menu['icon']))
                        <i class="{{ $menu['icon'] }} text-[22px]"></i>
                    @endif
                    {{ __($menu['title']) }}
                </a>
                <button type="button"
                    class="submenu-toggle ps-3 border-l-1 rtl:border-l-0 rtl:border-r-1 border-gray-300">
                    <i class="ph ph-caret-down"></i>
                </button>
            </div>
        @else
            <button
                class="submenu-btn flex items-center py-1.5 text-sm {{ isSubmenuActive($menu) ? 'submenu_active' : '' }}">
                {{ __($menu['title']) }}
                <i class="ph ph-caret-down ml-2"></i>
            </button>
        @endif

        <ul class="submenu-content {{ isSubmenuActive($menu) ? 'block' : 'hidden' }}">
            @foreach ($menu['submenus'] as $sub_menu)
                @if (isset($sub_menu['submenus']))
                    @include('admin.partials.navigation.submenu', ['menu' => $sub_menu])
                @else
                    <li
                        class="flex sidebar-link py-1.5 text-sm {{ isCurrentUrlMatched($sub_menu['link']) ? 'submenu_active' : '' }}">
                        <a href="{{ route($sub_menu['link']) }}"
                            class="hover:text-primary">{{ __($sub_menu['title']) }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </li>
@endif
