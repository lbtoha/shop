@extends('shop.layouts.app')

@section('title', __('Create Account') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-md mx-auto px-4 py-12">
        <div class="bg-white border border-neutral-100 rounded p-8">
            <h1 class="text-2xl font-bold text-ink mb-1">{{ __('Create your account') }}</h1>
            <p class="text-sm text-[color:var(--color-muted)] mb-6">{{ __('Track your orders and check out faster') }}</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded px-3 py-2 mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                            class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                    </div>
                </div>
                <p class="text-xs text-[color:var(--color-muted)] -mb-1">{{ __('Sign up with your email, phone, or both.') }}</p>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="{{ __('you@example.com') }}"
                        class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="{{ __('01XXXXXXXXX') }}"
                        class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Password') }}</label>
                    <input type="password" name="password" required
                        class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-neutral-200 rounded py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>
                <button type="submit"
                    class="w-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-2.5 rounded transition">
                    {{ __('Create Account') }}
                </button>
            </form>

            <p class="text-sm text-center text-[color:var(--color-muted)] mt-5">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="text-[color:var(--color-brand)] font-medium hover:underline">{{ __('Sign in') }}</a>
            </p>
        </div>
    </div>
@endsection
