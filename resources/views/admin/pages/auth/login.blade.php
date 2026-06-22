<x-admin-guest-layout>
    @php
        $appName = config('application_info.company_info.name', config('app.name'));
        $logo = config('application_info.logo_favicon.logo_light') ?? '/assets/admin/images/logo-light.png';
    @endphp

    <main class="relative min-h-screen overflow-hidden f-center bg-neutral-10 dark:bg-neutral-904 p-4 sm:p-6">
        {{-- Ambient background blobs --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-8 -left-8 lg:-top-32 lg:-left-40 size-40 lg:size-[340px] rounded-full bg-primary opacity-[0.18] blur-[100px]"></div>
            <div class="absolute -top-8 -right-8 lg:-top-32 lg:-right-40 size-40 lg:size-[340px] rounded-full bg-secondary-300 opacity-[0.18] blur-[100px]"></div>
            <div class="absolute -right-8 -bottom-8 lg:-right-40 lg:-bottom-28 size-40 lg:size-[340px] rounded-full bg-info-300 opacity-[0.15] blur-[100px]"></div>
            <div class="absolute -left-8 -bottom-8 lg:-left-40 lg:-bottom-28 size-40 lg:size-[340px] rounded-full bg-warning-300 opacity-[0.15] blur-[100px]"></div>
        </div>

        {{-- Auth card --}}
        <div class="relative z-[4] w-full max-w-5xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden bg-neutral-0 dark:bg-neutral-900 border border-neutral-30 dark:border-neutral-700"
                style="box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">

                {{-- Left: brand panel --}}
                <div class="relative hidden lg:flex flex-col justify-between text-white overflow-hidden"
                    style="padding: 2.75rem; background-image: linear-gradient(135deg, var(--color-primary), rgb(var(--secondary-color)));">
                    <div class="absolute inset-0 opacity-20"
                        style="background-image: radial-gradient(circle at 1px 1px, #fff 1px, transparent 0); background-size: 22px 22px;"></div>

                    <div class="relative z-[2] flex items-center gap-3">
                        <img src="{{ $logo }}" alt="{{ $appName }}" class="h-9 w-auto" style="filter: brightness(0) invert(1);" />
                    </div>

                    <div class="relative z-[2]">
                        <h2 class="text-3xl font-bold mb-3" style="line-height: 1.15;">{{ __('Welcome back') }} 👋</h2>
                        <p class="text-base" style="color: rgba(255,255,255,0.82); max-width: 24rem;">
                            {{ __('Manage your products, orders and customers — all from one place.') }}
                        </p>

                        <ul class="mt-8 space-y-3 text-sm" style="color: rgba(255,255,255,0.92);">
                            <li class="flex items-center gap-3">
                                <span class="f-center size-7 rounded-full shrink-0" style="background: rgba(255,255,255,0.15);"><i class="ph ph-package"></i></span>
                                {{ __('Products & inventory') }}
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="f-center size-7 rounded-full shrink-0" style="background: rgba(255,255,255,0.15);"><i class="ph ph-shopping-bag-open"></i></span>
                                {{ __('Orders & checkout') }}
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="f-center size-7 rounded-full shrink-0" style="background: rgba(255,255,255,0.15);"><i class="ph ph-chart-line-up"></i></span>
                                {{ __('Sales insights') }}
                            </li>
                        </ul>
                    </div>

                    <p class="relative z-[2] text-xs" style="color: rgba(255,255,255,0.6);">&copy; {{ now()->year }} {{ $appName }}</p>
                </div>

                {{-- Right: login form --}}
                <div class="flex flex-col justify-center text-neutral-700 dark:text-neutral-20" style="padding: 2.5rem;">
                    {{-- Mobile logo --}}
                    <div class="lg:hidden mb-8 f-center">
                        <img src="{{ $logo }}" alt="{{ $appName }}" class="h-10 w-auto application-logo" />
                    </div>

                    <div class="mb-8">
                        <h3 class="text-2xl font-bold mb-2">{{ __('Sign in') }}</h3>
                        <p class="text-neutral-500 text-sm">{{ __('Enter your credentials to access the dashboard.') }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-admin::label for="email">{{ __('Email') }}</x-admin::label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none">
                                    <i class="ph ph-envelope-simple text-xl"></i>
                                </span>
                                <input type="text" name="email" value="{{ old('email') }}" id="email"
                                    class="text-input {{ $errors->has('email') ? 'input-error' : '' }}"
                                    style="padding-left: 2.75rem;"
                                    placeholder="{{ __('Enter your email') }}" required autofocus />
                            </div>
                            <x-admin::input-error :errors="$errors" name="email" />
                        </div>

                        <div>
                            <x-admin::label for="pass2">{{ __('Password') }}</x-admin::label>
                            <div id="password-field" class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none">
                                    <i class="ph ph-lock-simple text-xl"></i>
                                </span>
                                <input name="password" required value="{{ old('password') }}" id="pass2" type="password"
                                    class="text-input {{ $errors->has('password') ? 'input-error' : '' }}"
                                    style="padding-left: 2.75rem; padding-right: 2.75rem;"
                                    placeholder="{{ __('Enter your password') }}" />
                                <span class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 flex size-8 cursor-pointer items-center justify-center rounded-full duration-300 hover:bg-neutral-40 dark:hover:bg-neutral-700">
                                    <i class="toggle-password-eye ph ph-eye text-xl"></i>
                                    <i class="toggle-password-eye-close ph ph-eye-slash text-xl"></i>
                                </span>
                            </div>
                            <x-admin::input-error :errors="$errors" name="password" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-neutral-500 select-none">
                                <input type="checkbox" name="remember" class="size-4 rounded border-neutral-40"
                                    style="accent-color: var(--color-primary);" />
                                {{ __('Remember me') }}
                            </label>
                            <a href="{{ route('admin.password.request') }}" class="text-sm text-secondary-300 hover:underline">
                                {{ __('Forgot password?') }}
                            </a>
                        </div>

                        <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                            <i class="ph ph-sign-in text-lg"></i>
                            <span>{{ __('Login') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-admin-guest-layout>
