<x-admin-guest-layout>
    @php
        $appName = config('application_info.company_info.name', config('app.name'));
        $logo = config('application_info.logo_favicon.logo_light') ?? '/assets/logo.png';
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
                    <h3 class="text-2xl font-bold text-neutral-700 dark:text-neutral-20 mb-1.5">{{ __('Forgot Password') }}</h3>
                    <p class="text-neutral-500 text-sm">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <x-admin::label for="email">{{ __('Email') }}</x-admin::label>
                        <input type="text" name="email" value="{{ old('email') }}" id="email"
                            class="text-input {{ $errors->has('email') ? 'input-error' : '' }}"
                            placeholder="Enter Email" required />
                        <x-admin::input-error :errors="$errors" name="email" />
                    </div>

                    <button type="submit" class="btn-primary w-full">{{ __('Email Password Reset Link') }}</button>
                </form>
            </div>
        </div>
    </main>
</x-admin-guest-layout>
