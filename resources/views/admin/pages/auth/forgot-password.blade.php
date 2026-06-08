<x-admin-guest-layout>
    <!-- Session Status -->
    <main class="relative min-h-screen overflow-x-hidden f-center bg-neutral-0 dark:bg-neutral-904">
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-8 -left-8 lg:-top-32 lg:-left-40 size-40 lg:size-[340px] rounded-full bg-secondary-300 opacity-[0.2] blur-[100px]">
            </div>
            <div
                class="absolute -top-8 -right-8 lg:-top-32 lg:-right-40 size-40 lg:size-[340px] rounded-full bg-error-300 opacity-[0.2] blur-[100px]">
            </div>
            <div
                class="absolute -right-8 -bottom-8 lg:-right-40 lg:-bottom-28 size-40 lg:size-[340px] rounded-full bg-info-300 opacity-[0.15] blur-[100px]">
            </div>
            <div
                class="absolute -left-8 -bottom-8 lg:-left-40 lg:-bottom-28 size-40 lg:size-[340px] rounded-full bg-warning-300 opacity-[0.15] blur-[100px]">
            </div>
        </div>

        <div class="container overflow-y-auto">
            <div
                class="grid grid-cols-12 gap-4 xxl:gap-6 items-center relative z-[4] text-neutral-700 dark:text-neutral-20 py-12">
                <div class="col-span-12 lg:col-span-6 xxl:col-span-5">
                    <h3 class="mb-4 xl:mb-6">{{ __('Forgot Password') }}</h3>
                    <p class="mb-7 xl:mb-10">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </p>
                    <form method="POST" action="{{ route('admin.password.email') }}">
                        @csrf
                        <div>
                            <div class="mb-4 xl:mb-6">
                                <x-admin::label for="email">Email</x-admin::label>
                                <input type="text" name="email" value="{{ old('email') }}" id="email"
                                    class="text-input {{ $errors->has('email') ? 'input-error' : '' }}"
                                    placeholder="Enter Email" required />
                                <x-admin::input-error :errors="$errors" name="email" />
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full">{{ __('Email Password Reset Link') }}</button>
                    </form>
                </div>
                <div class="col-span-12 lg:col-span-6 xxl:col-start-7 flex justify-center">
                    <div
                        class="size-72 sm:size-[450px] xxl:size-[636px] rounded-full bg-neutral-30 dark:bg-neutral-700 f-center">
                        <img src="/assets/admin/images/login-1.png" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </main>

</x-admin-guest-layout>
