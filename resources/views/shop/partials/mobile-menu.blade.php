@php
    $wishlistCount = app(\App\Services\Ecommerce\Wishlist::class)->count();
    $navCategories = \App\Models\Category::active()
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->get();
@endphp

{{-- Menu Overlay --}}
<div data-menu-overlay class="fixed inset-0 bg-black/50 z-[100] opacity-0 invisible transition-opacity duration-300"></div>

{{-- ── Mobile drawer menu ───────────────────────────────── --}}
<nav data-mobile-menu
    class="fixed top-0 left-0 h-full w-[80vw] max-w-[300px] bg-white z-[101] shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-in-out">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 h-16 border-b border-line-soft shrink-0">
        <span class="font-bold text-ink uppercase tracking-wider text-xs">{{ __('Menu') }}</span>
        <button type="button" data-menu-close class="text-2xl text-muted hover:text-ink cursor-pointer" aria-label="{{ __('Close') }}">
            <i class="ph ph-x"></i>
        </button>
    </div>

    {{-- Content Wrapper --}}
    <div class="flex-1 overflow-y-auto pb-safe">
        {{-- Search --}}
        <div class="p-4 border-b border-line-soft">
            <form action="{{ route('shop.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search products…') }}"
                        class="w-full border border-line rounded-full py-2.5 pl-5 pr-12 text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/15 bg-canvas">
                    <button type="submit"
                        class="absolute right-1.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-brand hover:bg-brand-dark text-white flex items-center justify-center transition-colors">
                        <i class="ph-bold ph-magnifying-glass text-sm"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Nav links --}}
        <div class="px-4 py-3 flex flex-col gap-0.5 text-sm font-semibold">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-house text-base text-brand/60"></i>{{ __('Home') }}
            </a>
            <a href="{{ route('shop.index') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-storefront text-base text-brand/60"></i>{{ __('Shop') }}
            </a>
            <a href="{{ route('shop.wishlist.index') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-heart text-base text-brand/60"></i>{{ __('Wishlist') }}
               <span data-wishlist-count class="{{ $wishlistCount ? '' : 'hidden' }} ml-auto bg-accent text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                   {{ $wishlistCount }}
               </span>
            </a>
            <a href="{{ route('shop.track') }}"
               class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
               <i class="ph ph-hash text-base text-brand/60"></i>{{ __('Track Order') }}
            </a>

            @if ($navCategories->isNotEmpty())
                <details class="group w-full">
                    <summary class="w-full flex items-center justify-between py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors text-left font-semibold cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                        <span class="flex items-center gap-3">
                            <i class="ph ph-squares-four text-base text-brand/60"></i>
                            {{ __('Categories') }}
                        </span>
                        <i class="ph ph-caret-down transition-transform duration-200 text-xs group-open:rotate-180"></i>
                    </summary>
                    <div class="pl-4 mt-1 flex flex-col gap-0.5 border-l border-line-soft ml-5">
                        @foreach ($navCategories as $cat)
                            <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                               class="flex items-center gap-3 py-2 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors text-body text-sm font-medium">
                               <i class="ph ph-tag text-sm text-brand/40"></i>{{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </details>
            @endif

            {{-- Auth Options for Mobile --}}
            <div class="mt-2 pt-3 border-t border-line-soft flex flex-col gap-0.5">
                @auth
                    <a href="{{ route('shop.account.index') }}"
                       class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
                       <i class="ph ph-user-circle text-base text-brand/60"></i>{{ __('My Account') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors text-left font-semibold cursor-pointer">
                            <i class="ph ph-sign-out text-base text-brand/60"></i>{{ __('Logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
                       <i class="ph ph-sign-in text-base text-brand/60"></i>{{ __('Sign In') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="flex items-center gap-3 py-2.5 px-3 rounded-md hover:bg-brand-soft hover:text-brand transition-colors">
                       <i class="ph ph-user-plus text-base text-brand/60"></i>{{ __('Sign Up') }}
                    </a>
                @endauth
            </div>

            {{-- Language --}}
            <div class="mt-2 pt-3 border-t border-line-soft flex items-center gap-3 px-3 pb-1">
                <i class="ph ph-globe text-brand/60"></i>
                <a href="{{ route('shop.language', 'en') }}"
                   class="text-sm {{ app()->getLocale() === 'en' ? 'text-brand font-semibold' : 'text-muted' }} hover:text-brand transition-colors">EN</a>
                <span class="text-subtle">|</span>
                <a href="{{ route('shop.language', 'bn') }}"
                   class="text-sm {{ app()->getLocale() === 'bn' ? 'text-brand font-semibold' : 'text-muted' }} hover:text-brand transition-colors">বাংলা</a>
            </div>

            {{-- Social Medias --}}
            <div class="mt-2 pt-3 border-t border-line-soft px-3 flex items-center gap-4.5 pb-2">
                @foreach (config('application_info.social_medias', []) as $social)
                    @if(!empty($social['link']) && $social['link'] !== '#')
                        <a href="{{ $social['link'] }}" target="_blank" rel="noopener" 
                           class="text-ink/60 hover:text-brand transition-colors flex items-center" 
                           title="{{ __($social['name']) }}">
                            <i class="{{ $social['icon'] }} text-lg"></i>
                        </a>
                    @endif
                @endforeach
                @php
                    $companyTemp = config('application_info.company_info');
                    $waNumberTemp = preg_replace('/\D/', '', ((int) getOption('whatsapp_enabled', 0) === 1 ? getOption('whatsapp_number') : null) ?: ($companyTemp['phone'] ?? ''));
                @endphp
                @if(!empty($waNumberTemp))
                    <a href="https://wa.me/{{ $waNumberTemp }}" target="_blank" rel="noopener" 
                       class="text-ink/60 hover:text-brand transition-colors flex items-center" 
                       title="WhatsApp">
                        <i class="ph ph-whatsapp-logo text-lg"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>
