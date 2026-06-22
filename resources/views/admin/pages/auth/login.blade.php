<x-admin-guest-layout>
    @php
        $appName = config('application_info.company_info.name', config('app.name'));
        $logo = config('application_info.logo_favicon.logo_light') ?? '/assets/admin/images/logo-light.png';
    @endphp

    <main class="relative min-h-screen overflow-hidden f-center bg-neutral-10 dark:bg-neutral-904 p-4 sm:p-6">
        {{-- Subtle ambient glow --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 size-[420px] rounded-full bg-primary opacity-[0.12] blur-[120px]"></div>
            <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 size-[420px] rounded-full bg-secondary-300 opacity-[0.10] blur-[120px]"></div>
        </div>

        {{-- Centered auth card --}}
        <div class="relative z-[4] w-full max-w-[420px]">
            {{-- Brand --}}
            <div class="mb-8 f-center">
                <img src="{{ $logo }}" alt="{{ $appName }}" class="h-10 w-auto application-logo" />
            </div>

            <div class="rounded-2xl bg-neutral-0 dark:bg-neutral-900 border border-neutral-30 dark:border-neutral-700 p-7 sm:p-9"
                style="box-shadow: 0 10px 40px -12px rgba(0,0,0,0.12);">

                <div class="mb-7 text-center">
                    <h3 class="text-2xl font-bold text-neutral-700 dark:text-neutral-20 mb-1.5">{{ __('Sign in') }}</h3>
                    <p class="text-neutral-500 text-sm">{{ __('Enter your credentials to access the dashboard.') }}</p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
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

            <p class="mt-6 text-center text-xs text-neutral-400">
                &copy; {{ now()->year }} {{ $appName }}
            </p>
        </div>
    </main>
</x-admin-guest-layout>
