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
                    <h3 class="mb-4 xl:mb-6">{{ __('Reset Password') }}</h3>
                    <p class="mb-7 xl:mb-10">{{ __('Reset your password') }}</p>
                    <form method="POST" action="{{ route('admin.password.store') }}">
                        @csrf
                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div>
                            <div class="mb-4 xl:mb-6">
                                <x-admin::label for="email">{{ __('Email') }}</x-admin::label>
                                <input type="text" name="email" value="{{ old('email') }}" id="email"
                                    class="text-input {{ $errors->has('email') ? 'input-error' : '' }}"
                                    placeholder="Enter Email" required />
                                <x-admin::input-error :errors="$errors" name="email" />
                            </div>
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
                        <div class="my-4">
                            <x-admin::label for="password_confirmation">{{ __('Confirm Password') }}</x-admin::label>
                            <div id="password-field" class="rounded-3xl relative">
                                <input name="password_confirmation" required value="{{ old('password_confirmation') }}"
                                    id="pass2" type="password"
                                    class="text-input {{ $errors->has('password_confirmation') ? 'input-error' : '' }}"
                                    placeholder="Enter Password" />
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
