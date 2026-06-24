@extends('shop.layouts.app')

@section('title', __('Verify your email') . ' — ' . config('application_info.company_info.name'))

@section('content')
    <div class="max-w-md mx-auto px-4 py-12">
        <div class="bg-white border border-neutral-100 rounded-md p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-full bg-[color:var(--color-brand-soft)] flex items-center justify-center mx-auto mb-3">
                    <i class="ph {{ $channel === 'phone' ? 'ph-chat-circle-text' : 'ph-envelope-open' }} text-2xl text-[color:var(--color-brand)]"></i>
                </div>
                <h1 class="text-2xl font-bold text-ink mb-1">
                    {{ $channel === 'phone' ? __('Verify your phone') : __('Verify your email') }}
                </h1>
                <p class="text-sm text-[color:var(--color-muted)]">
                    {{ __('Enter the code we sent to') }}
                    <span class="font-semibold text-ink">{{ $destination }}</span>
                </p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-3 py-2 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-md px-3 py-2 mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.otp.verify') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Verification Code') }}</label>
                    <input type="text" name="otp" inputmode="numeric" autocomplete="one-time-code" required autofocus
                        placeholder="{{ __('Enter the code') }}"
                        class="w-full text-center tracking-[0.5em] text-lg font-bold border border-neutral-200 rounded-md py-3 px-3 focus:outline-none focus:border-[color:var(--color-brand)]">
                </div>

                <button type="submit"
                    class="w-full bg-[color:var(--color-brand)] hover:bg-[color:var(--color-brand-dark)] text-white font-medium py-2.5 rounded-md transition">
                    {{ __('Verify & Create Account') }}
                </button>
            </form>

            <div class="flex items-center justify-between mt-5 text-sm">
                <form method="POST" action="{{ route('register.otp.resend') }}">
                    @csrf
                    <button type="submit" class="text-[color:var(--color-brand)] font-medium hover:underline">
                        {{ __('Resend code') }}
                    </button>
                </form>
                <a href="{{ route('register') }}" class="text-[color:var(--color-muted)] hover:underline">
                    {{ __('Start over') }}
                </a>
            </div>
        </div>
    </div>
@endsection
