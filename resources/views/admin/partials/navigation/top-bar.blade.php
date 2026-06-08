<nav class="topbar">
    <div class="flex justify-between items-center">
        <div class="flex gap-4 xxl:gap-6 items-center">
            <button class="sidebar-toggle-btn"><i class="ph ph-list text-2xl"></i></button>
            <div class="relative dropdown">
                <form
                    class="max-w-[357px] dropdown-toggle bg-neutral-0 dark:bg-neutral-904 max-md:hidden rounded-lg border focus-within:border-primary border-neutral-30 dark:border-neutral-500 p-1 flex items-center">
                    <input type="text" class="px-4 w-full bg-transparent text-sm" placeholder="{{ __('Search...') }}" />
                    <span class="size-8 shrink-0 rounded-full f-center">
                        <i class="ph ph-magnifying-glass text-xl"></i>
                    </span>
                </form>
                <div class="absolute hidden dropdown-menu top-[105%] left-0 w-full">
                    <div class="white-box shadow-xl !p-1.5 space-y-1 max-h-[300px] overflow-y-auto custom-scrollbar">
                        @foreach (array_map(
                                fn($menu) => [
                                    'title' => $menu['title'],
                                    'link' => $menu['link'],
                                ],
                                getMenuCaps(authorizedMenus(config('menu.admin.menu'), auth('admin')->user())),
                            ) as $key => $menu)
                            <a href="{{ route($menu['link']) }}"
                                class="px-3 py-2.5 duration-300 rounded-md block hover:bg-primary/10">
                                <span class="m-text block font-medium mb-0.5">{{ $menu['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-3 xxl:gap-4 items-center">
            <!-- full screen toggle btn -->
            <button title="Toggle Fullscreen" id="fullscreenButton" class="topbar-btn max-sm:hidden cursor-pointer">
                <i class="ph ph-corners-out text-xl full-screen-icon"></i>
            </button>
            <!-- Frontend toggle btn -->
            <a href="{{ config('application_info.frontend_url') }}" target="_blank" class="topbar-btn cursor-pointer">
                <i class="ph ph-globe text-xl"></i>
            </a>

            <!-- Dark ligth switch -->
            <button title="Toggle Theme" class="topbar-btn mode-switcher cursor-pointer">
                <i class="ph ph-sun"></i>
            </button>
            <!-- Language switch -->
            <div class="relative dropdown">
                <button title="Change Language" class="topbar-btn dropdown-toggle cursor-pointer">
                    <i class="ph ph-translate"></i>
                </button>
                <div
                    class="absolute hidden dropdown-menu w-[150px] z-20 bg-neutral-0 border border-neutral-30 dark:border-neutral-500 dark:bg-neutral-904 top-full max-h-[300px] overflow-y-auto right-0 shadow-lg rtl:left-0 p-2 rounded-lg">
                    <ul class="flex flex-col gap-1 max-w-">
                        @php
                            $languages = \App\Models\Language::active()->get(); // Retrieve active languages
                            $currentLocale = session('locale', config('app.locale')); // Fallback to default app locale
                        @endphp
                        @foreach ($languages as $key => $lang)
                            <li>
                                <a href="{{ route('admin.change-language', $lang->code) }}"
                                    class="flex {{ $currentLocale == $lang->code ? 'bg-primary text-neutral-0' : '' }} cursor-pointer duration-300 hover:text-primary rounded-md px-4 py-1.5 hover:bg-primary/20">
                                    {{ $lang->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Notification switch -->
            <div class="relative dropdown">
                <span id="notification-count"
                    class="size-4 text-xs absolute -top-1 -right-1 f-center text-neutral-0 bg-primary rounded-full">
                    {{ getNotifications()['count'] }}
                </span>
                <button title="Notifications" class="topbar-btn dropdown-toggle cursor-pointer">
                    <i class="ph ph-bell"></i>
                </button>
                <div
                    class="absolute hidden dropdown-menu top-full z-10 origin-[60%_0] border border-neutral-30 dark:border-neutral-500 rounded-md bg-neutral-0 shadow-[0px_6px_30px_0px_rgba(0,0,0,0.08)] duration-300 dark:bg-neutral-904 -right-[110px] sm:right-0 sm:origin-top-right rtl:right-auto rtl:-left-[120px] sm:rtl:left-0 sm:rtl:origin-top-left">
                    <div class="flex items-center justify-between border-b p-3 dark:border-neutral-500 lg:px-4">
                        <h5 class="h5">{{ __('Notifications') }}</h5>
                        <a href="{{ route('admin.notifications.index') }}"
                            class="text-xs text-primary">{{ __('View All') }}</a>
                    </div>
                    <ul id="notification-list-container"
                        class="flex w-[300px] flex-col gap-2 p-4 max-h-[320px] overflow-y-auto custom-scrollbar">
                        @foreach (getNotifications()['list'] as $key => $notification)
                            <a href="{{ route('admin.notifications.show', $notification->id) }}"
                                class="flex cursor-pointer gap-2 rounded-md p-2 duration-300 bg-primary/5 hover:bg-primary/10 dark:hover:bg-primary/20">
                                <div class="text-sm">
                                    <div class="flex gap-1">
                                        <span>{{ $notification->data }}</span>
                                    </div>
                                    <span
                                        class="text-xs text-n100 dark:text-n50">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </ul>
                </div>
            </div>
            @php
                $user = auth('admin')->user();
            @endphp
            <!-- user profile -->
            <div class="relative dropdown shrink-0">
                <div title="User Profile" class="size-9 cursor-pointer dropdown-toggle">
                    <img src="{{ placeAvatar($user?->avatar, $user->full_name) }}" class="rounded-full"
                        alt="profile img" />
                </div>

                <div
                    class="absolute hidden dropdown-menu top-full z-20 rounded-md bg-neutral-0 shadow-[0px_6px_30px_0px_rgba(0,0,0,0.08)] duration-300 dark:bg-neutral-904 border border-neutral-30 dark:border-neutral-500 right-0 rtl:right-auto origin-top-right rtl:left-0 rtl:origin-top-left">
                    <div
                        class="flex flex-col items-center border-b border-neutral-30 p-3 text-center dark:border-neutral-500 lg:p-4">
                        <img src="{{ placeAvatar($user->avatar, $user->full_name) }}" width="60" height="60"
                            class="rounded-full" alt="profile img" />
                        <h6 class="h6 mt-2">{{ $user->full_name }}</h6>
                        <span class="text-sm">{{ $user->email }}</span>
                    </div>
                    <ul class="flex w-[250px] flex-col p-4">
                        <li>
                            <a href="{{ route('admin.profile.index') }}"
                                class="flex items-center gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary/10 hover:text-primary">
                                <span>
                                    <i class="ph ph-user text-xl"></i>
                                </span>
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}"
                                class="flex items-center gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary/10 hover:text-primary">
                                <span>
                                    <i class="ph ph-gear text-xl"></i>
                                </span>
                                {{ __('Settings') }}
                            </a>
                        </li>
                        <li>
                            <button id="logout" type="button"
                                class="w-full flex items-center gap-2 rounded-md px-2 py-1.5 duration-300 hover:bg-primary/10 hover:text-primary">
                                <span>
                                    <i class="ph ph-sign-out text-xl"></i>
                                </span>
                                {{ __('Log Out') }}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <form method="POST" id="logout-form" action="{{ route('admin.logout') }}">
        @csrf

    </form>
</nav>

@push('scripts')
    @vite('resources/admin/js/firebase-notification.js')
    <script>
        const lang = '{{ session('locale', config('app.locale')) }}';
        if (lang == 'ar') {
            document.documentElement.setAttribute('dir', 'rtl');
        }
    </script>
@endpush
