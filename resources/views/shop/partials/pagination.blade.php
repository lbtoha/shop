@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between border-t border-line pt-6">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider text-muted bg-white border border-line rounded-md cursor-default select-none">
                    {{ __('Previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider text-ink bg-white border border-line rounded-md hover:bg-brand-soft hover:text-brand hover:border-brand-mist transition-all duration-200">
                    {{ __('Previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider text-ink bg-white border border-line rounded-md hover:bg-brand-soft hover:text-brand hover:border-brand-mist transition-all duration-200">
                    {{ __('Next') }}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider text-muted bg-white border border-line rounded-md cursor-default select-none">
                    {{ __('Next') }}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-muted">
                    {!! __('Showing') !!}
                    <span class="font-bold text-ink">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-bold text-ink">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-bold text-ink">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('Previous') }}">
                            <span class="relative inline-flex items-center justify-center w-9 h-9 text-muted bg-white border border-line rounded-md cursor-default select-none">
                                <i class="ph ph-arrow-left text-sm"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('Previous') }}" class="relative inline-flex items-center justify-center w-9 h-9 text-ink bg-white border border-line rounded-md hover:bg-brand-soft hover:text-brand hover:border-brand-mist transition-all duration-200">
                            <i class="ph ph-arrow-left text-sm"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center justify-center w-9 h-9 text-muted bg-white border border-line rounded-md select-none">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-white bg-brand border border-brand rounded-md select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="relative inline-flex items-center justify-center w-9 h-9 text-xs font-medium text-ink bg-white border border-line rounded-md hover:bg-brand-soft hover:text-brand hover:border-brand-mist transition-all duration-200">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('Next') }}" class="relative inline-flex items-center justify-center w-9 h-9 text-ink bg-white border border-line rounded-md hover:bg-brand-soft hover:text-brand hover:border-brand-mist transition-all duration-200">
                            <i class="ph ph-arrow-right text-sm"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('Next') }}">
                            <span class="relative inline-flex items-center justify-center w-9 h-9 text-muted bg-white border border-line rounded-md cursor-default select-none">
                                <i class="ph ph-arrow-right text-sm"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
