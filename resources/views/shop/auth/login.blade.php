@extends('shop.layouts.app')

@section('title', __('Login') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-md mx-auto px-4 py-12">
        <div class="bg-white border border-neutral-100 rounded-md p-8">
            <h1 class="text-2xl font-bold text-ink mb-1">{{ __('Welcome back') }}</h1>
            <p class="text-sm text-[color:var(--color-muted)] mb-6">{{ __('Sign in to your account') }}</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-3 py-2 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Email or Phone') }}</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus
                        placeholder="{{ __('you@example.com or 01XXXXXXXXX') }}"
                        class="w-full border border-neutral-200 rounded-md py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Password') }}</label>
                    <input type="password" name="password" required
                        class="w-full border border-neutral-200 rounded-md py-2 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>
                <label class="flex items-center gap-2 text-sm text-[color:var(--color-muted)]">
                    <input type="checkbox" name="remember" class="accent-[color:var(--color-brand)]"> {{ __('Remember me') }}
                </label>
                <button type="submit"
                    class="w-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-2.5 rounded-md transition">
                    {{ __('Sign In') }}
                </button>
            </form>

            <p class="text-sm text-center text-[color:var(--color-muted)] mt-5">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="text-[color:var(--color-brand)] font-medium hover:underline">{{ __('Create one') }}</a>
            </p>
        </div>
    </div>
@endsection
