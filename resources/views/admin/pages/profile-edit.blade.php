<x-admin-app-layout>
    <div class="flex flex-col gap-4 xxl:gap-8">
        <div class="white-box">
            <x-admin::page-header title="{{ __('Edit Profile') }}" :isFilterable="false" />
            <form action="{{ route('admin.profile.update') }}" class="user-information-update flex flex-col gap-4"
                method="POST">
                @csrf
                <div class="flex flex-col md:flex-row items-center gap-5 mb-6">
                    <div class="xl:w-[500px]">
                        <div
                            class="relative bg-primary/5 border border-neutral-30 dark:border-neutral-500 rounded-lg p-8 xl:py-12 flex justify-center mb-7">
                            <div id="avatar_preview">
                                <img src="{{ placeAvatar($user->avatar, $user->full_name) }}" alt="logo light"
                                    width="300" height="300" />
                            </div>
                            <input type="text" class="sr-only" name="avatar" id="avatar_input"
                                value="{{ $user->avatar }}" />
                            <label for="avatar" id="avatar" data-input="avatar_input" data-preview="avatar_preview"
                                class="absolute cursor-pointer size-12 right-5 bottom-0 translate-y-1/2 bg-primary rounded-full text-neutral-0 text-2xl f-center">
                                <i class="ph ph-cloud-arrow-up"></i>
                            </label>
                        </div>
                        <p class="text-neutral-400 text-xs">
                            {{ __('Supported Files: JPG, Png, json. Image will be resized into 300 x 300px') }}
                        </p>
                        @error('image')
                            <span class="input-text-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex-1 flex flex-col gap-4">
                        <div>
                            <x-admin::text-input label="first Name" name="first_name" value="{{ $user->first_name }}" />
                            <span class="input-text" id="first_name"></span>
                        </div>
                        <div>
                            <x-admin::text-input label="last Name" name="last_name" value="{{ $user->last_name }}" />
                            <span class="input-text" id="last_name"></span>
                        </div>
                        <div>
                            <x-admin::text-input label="Email" name="email" value="{{ $user->email }}" />
                            <span class="input-text" id="email"></span>
                        </div>
                        <div>
                            <x-admin::phone-number-input-group label="Phone" value="{{ $user->phone }}" />
                            <span class="input-text" id="phone"></span>
                        </div>
                    </div>
                </div>
                <x-admin::primary-button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </form>
        </div>

        <div class="white-box">
            <x-admin::page-header title="{{ __('Change Password') }}" :isFilterable="false" />
            <form action="{{ route('admin.profile.change-password') }}" class="form-submit-add flex flex-col gap-4"
                method="POST">
                @csrf
                <div class="flex-1 flex flex-col gap-4">

                    <x-admin::text-input-group name="old_password" type="password" label="Old Password"
                        placeholder="Enter your old password" :required="false" />
                    <x-admin::text-input-group name="password" type="password" label="Password"
                        placeholder="Enter your new password" :required="false" />
                    <x-admin::text-input-group name="password_confirmation" type="password" label="Confirm Password"
                        placeholder="Again enter your password" :required="false" />
                </div>
                <x-admin::primary-button type="submit" class="btn btn-primary">
                    {{ __('Change') }}
                </x-admin::primary-button>
            </form>
        </div>
    </div>
    @push('scripts')
        @vite('resources/admin/js/admin-user/profile-edit.js')
    @endpush
</x-admin-app-layout>
