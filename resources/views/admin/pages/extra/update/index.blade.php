<x-admin-app-layout>
    @section('title', __('Update - ' . config('application_info.company_info.name')))
    <div class="white-box">
        <x-admin::page-header title="{{ __('System Update') }}" :isFilterable="false" />
        @if ($response['is_update_available'] ?? false)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow mb-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">

                    <!-- Left Content -->
                    <div class="w-full">
                        <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">
                            {{ __('A new update is available!') }} ({{ $response['version'] ?? '' }})
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">
                            {{ __('Please update to the latest version. You are currently using v' . config('app.version')) }}
                            <br>
                            {{ __('It will take a few moments to download and install the update.') }}
                        </p>

                        <!-- Buttons -->
                        @if (isset($response['standalone_client']) || isset($response['static_client']))
                            <div class="flex flex-wrap mt-3 gap-3">
                                @if (isset($response['standalone_client']))
                                    <a href="{{ $response['standalone_client'] ?? '#' }}" target="_blank"
                                        class="btn btn-primary w-full sm:w-auto text-center">
                                        {{ __('Download Standalone Client') }}
                                    </a>
                                @endif
                                @if (isset($response['static_client']))
                                    <a href="{{ $response['static_client'] ?? '#' }}" target="_blank"
                                        class="btn btn-primary w-full sm:w-auto text-center">
                                        {{ __('Download Static Client') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Right Form -->
                    <div class="w-full lg:w-auto">
                        <form action="{{ route('admin.extras.check-for-update') }}" method="POST"
                            class="form-submit-add">
                            @csrf
                            <input type="hidden" name="file_url" value="{{ $response['file_path'] ?? '' }}">

                            <div class="space-y-4">
                                <!-- Force Update -->
                                <div class="flex flex-col">
                                    <x-admin::label for="is_forced">
                                        {{ __('Enable Force Update (It will overwrite your current files)') }}
                                    </x-admin::label>
                                    <div class="mt-1">
                                        <x-admin::switch name="is_forced" :types="[
                                            ['label' => __('No'), 'value' => 0],
                                            ['label' => __('Yes'), 'value' => 1],
                                        ]" />
                                    </div>
                                    <x-admin::input-error name="is_forced" />
                                </div>

                                <!-- New Tracking File -->
                                <div class="flex flex-col">
                                    <x-admin::label for="new_tracking">
                                        {{ __('Generate new tracking file.') }}
                                    </x-admin::label>
                                    <div class="mt-1">
                                        <x-admin::switch name="new_tracking" :types="[
                                            ['label' => __('No'), 'value' => 0],
                                            ['label' => __('Yes'), 'value' => 1],
                                        ]" />
                                    </div>
                                    <x-admin::input-error name="new_tracking" />
                                </div>
                            </div>

                            <!-- Update Button -->
                            <div class="mt-4">
                                <x-admin::primary-button class="w-full lg:w-auto">
                                    {{ __('Update') }}
                                </x-admin::primary-button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        @else
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow mb-6">
                <div class="flex items-start">
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">
                            {{ __('You are up to date!') }}
                        </h3>
                        <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                            {{ __('No new updates available at this moment.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <div class="lg:max-w-3xl xxl:max-w-4xl 3xl:max-w-5xl mx-auto">
            <!-- single version -->
            @forelse (array_reverse($response['update_logs'] ?? []) as $log)
                <div class="flex flex-col md:flex-row justify-between relative mb-6">
                    <div class="update-timeline">
                        <span class="status n10 bg-neutral-10 dark:bg-neutral-900 text-black dark:text-white">
                            v{{ $log['client_version'] }}
                        </span>
                        <p class="text-xs mt-4 text-neutral-700 dark:text-neutral-300">
                            Released on {{ \Carbon\Carbon::parse($log['created_at'])->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="max-w-[572px] w-full">
                        <div class="relative overflow-hidden text-sm font-normal flex flex-col gap-4 lg:gap-6 pb-6">
                            <div
                                class="cursor-pointer group bg-primary/5 dark:bg-primary/10 rounded-xl border border-neutral-200 dark:border-neutral-700 transition-colors">
                                <div>
                                    <div class="px-4 py-4 md:px-5 xl:px-6  text-neutral-800 dark:text-neutral-100">
                                        {!! $log['changelog'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="white-box bg-white dark:bg-neutral-900 text-center py-10 rounded-lg shadow-sm">
                    <p class="text-neutral-600 dark:text-neutral-300 text-sm">{{ __('No updates found') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-admin-app-layout>
