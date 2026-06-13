@extends('shop.layouts.app')

@section('title', __('Profile Information') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="shop-container py-8">
        {{-- Breadcrumb --}}
        <div class="text-xs text-[color:var(--color-muted)] mb-4 flex items-center gap-1">
            <a href="{{ route('home') }}" class="hover:text-[color:var(--color-brand)]">{{ __('Home') }}</a>
            <span>»</span>
            <span class="text-ink font-medium">{{ __('Customer Profile') }}</span>
        </div>

        <h1 class="text-2xl font-bold text-ink mb-6">{{ __('Profile Information') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            @include('shop.account.partials.sidebar')

            <div class="lg:col-span-3 space-y-6">
                {{-- Success Banner --}}
                @if (session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-sm flex items-center gap-3">
                        <i class="ph ph-check-circle text-xl text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Profile Info Card --}}
                <div class="bg-white border border-neutral-200/60 rounded-3xl p-6 sm:p-8 shadow-sm">
                    {{-- User Profile Header --}}
                    <div class="flex items-center gap-4 mb-8 pb-6 border-b border-neutral-100">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[color:var(--color-brand)] to-[color:var(--color-brand-dark)] text-white flex items-center justify-center font-bold text-2xl shadow-md">
                            {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-ink">{{ $user->full_name }}</h2>
                            <p class="text-xs text-[color:var(--color-muted)]">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('shop.account.profile.update') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <h3 class="font-bold text-sm text-ink mb-4 uppercase tracking-wider text-neutral-400">{{ __('Account information') }}</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Name --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">{{ __('Name') }}</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                    class="w-full border border-[color:var(--color-line)] rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-[color:var(--color-brand)] focus:ring-1 focus:ring-[color:var(--color-brand)] transition-colors">
                                @error('first_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Email --}}
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">{{ __('Email') }}</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full border border-[color:var(--color-line)] rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-[color:var(--color-brand)] focus:ring-1 focus:ring-[color:var(--color-brand)] transition-colors">
                                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Phone number (Read-Only) --}}
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">{{ __('Phone number') }}</label>
                                <input type="text" value="{{ $user->phone ?? __('N/A') }}" readonly
                                    class="w-full border border-[color:var(--color-line)] bg-neutral-50 rounded-xl py-3 px-4 text-sm text-neutral-500 cursor-not-allowed outline-none select-none">
                            </div>

                            {{-- New password --}}
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">{{ __('New password') }}</label>
                                <div class="relative">
                                    <input type="password" id="new_password" name="password"
                                        class="w-full border border-[color:var(--color-line)] rounded-xl py-3 pl-4 pr-11 text-sm focus:outline-none focus:border-[color:var(--color-brand)] focus:ring-1 focus:ring-[color:var(--color-brand)] transition-colors">
                                    <button type="button" onclick="togglePasswordVisibility('new_password', this)"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-[color:var(--color-brand)] p-1 transition-colors">
                                        <i class="ph ph-eye text-lg"></i>
                                    </button>
                                </div>
                                @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Confirm password --}}
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-2">{{ __('Confirm password') }}</label>
                                <div class="relative">
                                    <input type="password" id="confirm_password" name="password_confirmation"
                                        class="w-full border border-[color:var(--color-line)] rounded-xl py-3 pl-4 pr-11 text-sm focus:outline-none focus:border-[color:var(--color-brand)] focus:ring-1 focus:ring-[color:var(--color-brand)] transition-colors">
                                    <button type="button" onclick="togglePasswordVisibility('confirm_password', this)"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-[color:var(--color-brand)] p-1 transition-colors">
                                        <i class="ph ph-eye text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Update Button --}}
                        <div class="pt-4">
                            <button type="submit" class="bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-bold text-sm py-3 px-8 rounded-full shadow-lg shadow-brand/10 transition-all hover:shadow-xl hover:shadow-brand/20">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }
    </script>
@endsection
