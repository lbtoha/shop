@php($current = request()->routeIs('shop.account.index'))
<aside class="lg:col-span-1">
    <div class="bg-white border border-neutral-100 rounded p-4">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-neutral-100">
            <div class="w-10 h-10 rounded-full bg-[color:var(--color-brand-light)] text-white flex items-center justify-center font-semibold">
                {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}
            </div>
            <div class="text-sm">
                <div class="font-medium text-ink">{{ auth()->user()->full_name }}</div>
                <div class="text-[color:var(--color-muted)] text-xs">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <nav class="space-y-1 text-sm">
            <a href="{{ route('shop.account.index') }}"
                class="flex items-center gap-2 py-2 px-2 rounded {{ request()->routeIs('shop.account.index') ? 'bg-[color:var(--color-brand)] text-white' : 'hover:bg-neutral-50' }}">
                <i class="ph ph-squares-four"></i> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('shop.account.orders') }}"
                class="flex items-center gap-2 py-2 px-2 rounded {{ request()->routeIs('shop.account.orders') || request()->routeIs('shop.account.order') ? 'bg-[color:var(--color-brand)] text-white' : 'hover:bg-neutral-50' }}">
                <i class="ph ph-package"></i> {{ __('My Orders') }}
            </a>
            <a href="{{ route('shop.account.profile') }}"
                class="flex items-center gap-2 py-2 px-2 rounded {{ request()->routeIs('shop.account.profile') ? 'bg-[color:var(--color-brand)] text-white' : 'hover:bg-neutral-50' }}">
                <i class="ph ph-user"></i> {{ __('Profile Info') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 py-2 px-2 rounded text-red-500 hover:bg-red-50 text-left">
                    <i class="ph ph-sign-out"></i> {{ __('Logout') }}
                </button>
            </form>
        </nav>
    </div>
</aside>
