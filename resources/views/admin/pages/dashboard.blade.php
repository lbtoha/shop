<x-admin-app-layout>
    <div class="grid grid-cols-2 gap-4 xxl:gap-6">

        <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 xxl:grid-cols-4 gap-4 xxl:gap-6">
            @foreach ($state as $item)
                <div class="white-box">
                    <div class="flex justify-between items-center gap-3 mb-1 xxl:mb-5">
                        <div>
                            <p class="s-text mb-1">{{ $item['title'] }}</p>
                            <p class="l-text font-semibold">{{ $item['data'] }}</p>
                        </div>
                        <div class="size-11 rounded-full bg-primary f-center">
                            <i class="{{ $item['icon'] }} text-xl text-white"></i>
                        </div>
                    </div>
                    <a href="{{ $item['url'] }}" class="text-blue font-medium text-xs underline">{{ __('View all') }}</a>
                </div>
            @endforeach
        </div>
        <div class="col-span-2 white-box">
            <div class="flex justify-between items-center gap-4 flex-wrap mb-4 xl:mb-6">
                <p class="m-text font-medium">{{ __('Daily Login Overview (Last 15 days)') }}</p>
            </div>
            <div id="dailyLoginChart"></div>
        </div>
        <div class="col-span-2 lg:col-span-1 white-box">
            <div class="white-box">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                    <p class="m-text font-medium">{{ __('Login By OS') }}</p>
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-xs text-primary underline font-medium">{{ __('View all') }}</a>
                </div>
                <div class="flex justify-center">
                    <div id="osChartRef"></div>
                </div>
            </div>
        </div>
        <div class="col-span-2 lg:col-span-1 white-box">
            <div class="white-box">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-2">
                    <p class="m-text font-medium">{{ __('Login By Browser') }}</p>
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-xs text-primary underline font-medium">{{ __('View all') }}</a>
                </div>
                <div class="flex justify-center">
                    <div id="browserChartRef"></div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @vite('resources/admin/js/dashboard/index.js')
    @endpush
</x-admin-app-layout>
