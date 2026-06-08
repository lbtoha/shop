<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('System Configuration') }}" :buttons="$buttons" :isFilterable="false" />
        <form action="{{ route('admin.settings.system-configurations.index') }}" method="POST" class="form-submit-edit">
            @csrf
            <div class="flex flex-col lg:flex-row">
                @php
                    $full_config = config('extra_service.system_config');
                    $count = count($full_config);
                    $half = ceil($count / 2);
                    $half_count = $count - $half;
                    $left_config = array_slice($full_config, 0, $half);
                    $right_config = array_slice($full_config, $half, $half_count);
                @endphp
                <ul class="s-text flex-1">
                    @foreach ($left_config as $key => $config)
                        <li
                            class="border-y border-l border-neutral-30 flex justify-between gap-3 items-center dark:border-neutral-500 md:border-r p-3 xxl:p-5">
                            <div>
                                <p class="s-text font-medium mb-1">{{ __($config['title']) }}</p>
                                <span class="text-xs text-neutral-300 text-wrap">{{ __($config['description']) }}</span>
                            </div>
                            <label class="toggle-label">
                                <input type="checkbox" class="sr-only peer" name="{{ $key }}"
                                    {{ $config['is_enabled'] ? 'checked' : '' }} value="1" />
                                <input type="hidden" name="{{ $key }}" value="0">
                                <div class="bg peer-checked:!bg-primary/10"></div>
                                <span class="text-bg peer-checked:!bg-primary peer-checked:translate-x-full"></span>
                                <span class="text flex opacity-100 peer-checked:opacity-0 left-0 text-xs md:text-lg"> <i
                                        class="ph ph-x-circle"></i>{{ __('Disabled') }}</span>
                                <span
                                    class="text opacity-0 peer-checked:opacity-100 flex right-0 text-xs md:text-lg shrink-0"><i
                                        class="ph ph-check-circle"></i>{{ __('Enabled') }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                <ul class="s-text flex-1">
                    @foreach ($right_config as $key => $config)
                        <li
                            class="border-y border-neutral-30 flex justify-between gap-3 items-center dark:border-neutral-500 md:border-r p-3 xxl:p-5">
                            <div>
                                <p class="s-text font-medium mb-1">{{ __($config['title']) }}</p>
                                <span
                                    class="text-xs text-neutral-300 text-wrap">{{ __($config['description']) }}</span>
                            </div>
                            <label class="toggle-label">
                                <input type="checkbox" class="sr-only peer" name="{{ $key }}"
                                    {{ $config['is_enabled'] ? 'checked' : '' }} value="1" />
                                <input type="hidden" name="{{ $key }}" value="0">
                                <div class="bg peer-checked:!bg-primary/10"></div>
                                <span class="text-bg peer-checked:!bg-primary peer-checked:translate-x-full"></span>
                                <span class="text flex opacity-100 peer-checked:opacity-0 left-0"> <i
                                        class="ph ph-x-circle"></i>{{ __('Disabled') }}</span>
                                <span class="text opacity-0 peer-checked:opacity-100 flex right-0"><i
                                        class="ph ph-check-circle"></i>{{ __('Enabled') }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
