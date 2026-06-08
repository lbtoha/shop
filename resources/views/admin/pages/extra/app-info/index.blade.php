<x-admin-app-layout>
    @section('title', __('Extra - ' . config('application_info.company_info.name')))
    <div class="space-y-4 xl:space-y-6">
        <div class="white-box">
            <x-admin::page-header title="{{ __('Application Info') }}" :isFilterable="false" />
            <div class="flex flex-col lg:flex-row">
                @foreach ($application_info as $row)
                    <ul class="s-text flex-1">
                        @foreach ($row as $key => $value)
                            <li
                                class="border-y border-neutral-30 flex justify-between items-center dark:border-neutral-500 md:border-r">
                                <span class="p-5 inline-block">{{ __(ucwords(str_replace('_', ' ', $key))) }}</span>
                                <span class="p-5 inline-block">{{ $value }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
        <div class="white-box">
            <p class="m-text font-medium mb-5">{{ __('Server Information') }}</p>
            <div class="flex flex-col lg:flex-row">
                @foreach ($server_info as $row)
                    <ul class="s-text flex-1">
                        @foreach ($row as $key => $value)
                            <li
                                class="border-y border-neutral-30 flex justify-between items-center dark:border-neutral-500 md:border-r">
                                <span class="p-5 inline-block">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                <span class="p-5 inline-block">{{ $value }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
        <div class="white-box">
            <div class="flex justify-between items-center gap-4 flex-wrap mb-5">
                <p class="m-text font-medium">{{ __('Clear System Cache') }}</p>
                <a href="{{ route('admin.extras.clear-all') }}" class="btn-error !py-1.5">{{ __('Clear All') }}</a>
            </div>
            <div class="flex flex-col lg:flex-row">
                <ul class="s-text flex-1">
                    <li
                        class="border-y border-neutral-30 px-3 flex justify-between items-center dark:border-neutral-500 md:border-r">
                        <span class="p-5 inline-flex items-center gap-2">
                            <span class="size-5 text-success">
                                <svg fill="currentColor" width="18" height="18"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                    <rect width="256" height="256" fill="none" />
                                    <path
                                        d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z" />
                                </svg>
                            </span>
                            {{ __('Compiled views will be cleared') }}</span>
                        <a href="{{ route('admin.extras.cache', 'view:clear') }}"
                            class="btn-error !py-1.5 outlined">{{ __('Clear') }}</a>
                    </li>
                    <li
                        class="border-b px-3 border-neutral-30 flex justify-between items-center dark:border-neutral-500 md:border-r">
                        <span class="p-5 inline-flex items-center gap-2">
                            <span class="size-5 text-success">
                                <svg fill="currentColor" width="18" height="18"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                    <rect width="256" height="256" fill="none" />
                                    <path
                                        d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z" />
                                </svg>
                            </span>
                            {{ __('Route cache will be cleared') }}</span>
                        <a href="{{ route('admin.extras.cache', 'route:clear') }}"
                            class="btn-error !py-1.5 outlined">{{ __('Clear') }}</a>
                    </li>

                </ul>
                <ul class="s-text flex-1">
                    <li
                        class="border-y border-neutral-30 px-3 flex justify-between items-center dark:border-neutral-500 ">
                        <span class="p-5 inline-flex items-center gap-2">
                            <span class="size-5 text-success">
                                <svg fill="currentColor" width="18" height="18"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                    <rect width="256" height="256" fill="none" />
                                    <path
                                        d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z" />
                                </svg>
                            </span>
                            {{ __('Config cache will be cleared') }}</span>
                        <a href="{{ route('admin.extras.cache', 'config:clear') }}"
                            class="btn-error !py-1.5 outlined">{{ __('Clear') }}</a>
                    </li>
                    <li
                        class="border-b px-3 border-neutral-30 flex justify-between items-center dark:border-neutral-500">
                        <span class="p-5 inline-flex items-center gap-2">
                            <span class="size-5 text-success">
                                <svg fill="currentColor" width="18" height="18"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                    <rect width="256" height="256" fill="none" />
                                    <path
                                        d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z" />
                                </svg>
                            </span>
                            {{ __('Caches will be cleared') }}</span>
                        <a href="{{ route('admin.extras.cache', 'cache:clear') }}"
                            class="btn-error !py-1.5 outlined">{{ __('Clear') }}</a>
                    </li>
                </ul>

            </div>
            <div class="border-b px-3 border-neutral-30 flex justify-between items-center dark:border-neutral-500">
                <span class="p-5 inline-flex items-center gap-2">
                    <span class="size-5 text-success">
                        <svg fill="currentColor" width="18" height="18" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 256 256">
                            <rect width="256" height="256" fill="none" />
                            <path
                                d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm45.66,85.66-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32Z" />
                        </svg>
                    </span>
                    {{ __('Frontend cache will be cleared. It will effect on frontend which is created by Next.js, where you deployed on vercel or vps on any hosting provider') }}</span>
                <a href="{{ route('admin.extras.clear-frontend-cache') }}"
                    class="btn-error !py-1.5 outlined">{{ __('Clear') }}</a>
            </div>
        </div>
    </div>
</x-admin-app-layout>
