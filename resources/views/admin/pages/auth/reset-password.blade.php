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
                    <h3 class="text-2xl font-bold text-neutral-700 dark:text-neutral-20 mb-1.5">{{ __('Reset Password') }}</h3>
                    <p class="text-neutral-500 text-sm">{{ __('Enter your new password details below.') }}</p>
                </div>

                <form method="POST" action="{{ route('admin.password.store') }}" class="space-y-5">
                    @csrf
                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <x-admin::label for="email">{{ __('Email') }}</x-admin::label>
                        <input type="text" name="email" value="{{ old('email') }}" id="email"
                            class="text-input {{ $errors->has('email') ? 'input-error' : '' }}"
                            placeholder="Enter Email" required />
                        <x-admin::input-error :errors="$errors" name="email" />
                    </div>

                    <div>
                        <x-admin::label for="password">{{ __('Password') }}</x-admin::label>
                        <div id="password-field" class="rounded-3xl relative">
                            <input name="password" required value="{{ old('password') }}" id="pass2"
                                type="password"
                                class="text-input {{ $errors->has('password') ? 'input-error' : '' }}"
                                placeholder="Enter Password" />
                            <span
                                class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 flex size-8  cursor-pointer items-center justify-center rounded-full duration-300 hover:bg-neutral-40 dark:hover:bg-neutral-700">
                                <i class="toggle-password-eye ph ph-eye text-xl"></i>
                                <i class="toggle-password-eye-close ph ph-eye-slash text-xl"></i>
                            </span>
                        </div>
                        <x-admin::input-error :errors="$errors" name="password" />
                    </div>

                    <div>
                        <x-admin::label for="password_confirmation">{{ __('Confirm Password') }}</x-admin::label>
                        <div id="password-field" class="rounded-3xl relative">
                            <input name="password_confirmation" required value="{{ old('password_confirmation') }}"
                                id="pass2_confirm" type="password"
                                class="text-input {{ $errors->has('password_confirmation') ? 'input-error' : '' }}"
                                placeholder="Confirm Password" />
                            <span
                                class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 flex size-8  cursor-pointer items-center justify-center rounded-full duration-300 hover:bg-neutral-40 dark:hover:bg-neutral-700">
                                <i class="toggle-password-eye ph ph-eye text-xl"></i>
                                <i class="toggle-password-eye-close ph ph-eye-slash text-xl"></i>
                            </span>
                        </div>
                        <x-admin::input-error :errors="$errors" name="password_confirmation" />
                    </div>

                    <button type="submit" class="btn-primary w-full">{{ __('Reset Password') }}</button>
                </form>
            </div>
        </div>
    </main>
</x-admin-guest-layout>
