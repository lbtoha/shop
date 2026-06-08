@php
    $hasSelectedSubmenu = function ($items) use (&$hasSelectedSubmenu) {
        return false;
    };
    $isSelected = false;
    if (isset($adminRole)) {
        $hasSelectedSubmenu = function ($items) use ($adminRole, &$hasSelectedSubmenu) {
            foreach ($items as $item) {
                if (
                    !empty($item['link']) &&
                    $adminRole->module_caps &&
                    in_array($item['link'], $adminRole->module_caps)
                ) {
                    return true;
                }
                if (!empty($item['submenus']) && $hasSelectedSubmenu($item['submenus'])) {
                    return true;
                }
            }
            return false;
        };

        $isSelected =
            $adminRole->module_caps && !empty($menu['link']) && in_array($menu['link'], $adminRole->module_caps);
    }

    $hasSubmenus = !empty($menu['submenus']) && is_array($menu['submenus']);

@endphp

@if ($hasSubmenus)
    <div class="w-full">
        <div
            class="option parent-menu flex items-center justify-between p-3 rounded-lg border border-gray-300 bg-gray-50 hover:bg-gray-100 transition">
            <label for="{{ \Illuminate\Support\Str::slug($menu['title']) }}"
                class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" id="{{ \Illuminate\Support\Str::slug($menu['title']) }}"
                    value="{{ $menu['parent'] ?? ($menu['link'] ?? '') }}" class="parent-checkbox w-5 h-5 accent-primary"
                    @checked($hasSelectedSubmenu($menu['submenus'])) />
                <span class="checkbox"></span>
                <span class="font-semibold text-gray-800 uppercase tracking-wide">
                    {{ $menu['title'] }}
                </span>
                <span class="text-xs text-gray-500 ml-1">
                    {{ __('(Select all in this category)') }}
                </span>
            </label>

            <button type="button"
                class="toggle-btn flex items-center gap-1 px-2 py-1 text-gray-600 hover:text-gray-900 rounded transition cursor-pointer hover:border border-gray-300"
                title="Show/Hide submenus">
                <svg class="w-4 h-4 transform transition-transform duration-200" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="submenu ml-6 hidden mt-2 space-y-2">
            @foreach ($menu['submenus'] as $submenuIndex => $submenu)
                @include('admin.pages.admin.roles.dynamic-link-checkbox', [
                    'menu' => $submenu,
                    'adminRole' => isset($adminRole) ? $adminRole : null,
                    'name' => "{$name}[{$loop->parent->index}][submenus][{$submenuIndex}]",
                ])
            @endforeach
        </div>
    </div>
@else
    <label for="{{ \Illuminate\Support\Str::slug($menu['title']) }}" class="option max-lg:justify-end">
        <input type="checkbox" id="{{ \Illuminate\Support\Str::slug($menu['title']) }}"
            name="{{ $name }}[{{ $loop->index }}]" value="{{ $menu['link'] ?? '' }}"
            @checked($isSelected) class="child-checkbox" data-parent="{{ $menu['title'] }}" />
        <span class="checkbox"></span>
        <span class="uppercase">{{ $menu['title'] }}</span>
    </label>
@endif
