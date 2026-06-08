<x-admin-app-layout>
    @push('styles')
        @vite('resources/shared/css/icon-picker.css')
    @endpush
    <div class="grid grid-cols-12 gap-4 xxl:gap-6" x-data="menuBar"
        x-init='pages={{ $pages }}, menuItems={{ $items }}, mainMenu={{ $menu }},  itemInputs = menuItems.sort((a, b) => a.order - b.order);'>
        <div class="col-span-12 md:col-span-6 lg:col-span-7 xl:col-span-8 xxl:col-span-9 space-y-4 xxl:space-y-6">
            <div class="white-box">
                <div>
                    <x-admin::label for="name">Name</x-admin::label>
                    <input type="text" id="name"
                        :class="[validationErrors.menu_title && validationErrors.menu_title[0] ? 'input-error' : '']"
                        class="text-input" disabled x-model="mainMenu.name" placeholder="{{ __('Enter Text') }}" />
                    <span x-show="validationErrors.menu_title && validationErrors.menu_title[0]"
                        class="input-text-error"
                        x-text="validationErrors.menu_title ? validationErrors.menu_title[0] : '' "></span>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-4 xxl:gap-6">
                <div class="col-span-12 lg:col-span-6 xxl:col-span-4 space-y-4 xxl:space-y-6">
                    <div x-data="{ opened: true }"
                        class="rounded-md bg-neutral-0 dark:bg-neutral-904 border border-neutral-30 dark:border-neutral-500">
                        <div @click="opened=!opened"
                            class="flex justify-between cursor-pointer items-center px-4 xxl:px-6 py-3 xl:py-5">
                            <span class="m-text">Pages</span>
                            <button class="duration-300" :class="[opened ? 'duration-300' : '']">
                                <i class="ph ph-caret-down"></i>
                            </button>
                        </div>
                        <div x-show="opened" x-collapse>
                            <div
                                class="py-4 px-4 xxl:px-6 xl:py-4 border-t border-neutral-30 dark:border-neutral-500 space-y-3">
                                <template x-for="page in pages">
                                    <label :for="page.slug" :key="page.slug"
                                        class="option max-lg:justify-end">
                                        <input type="checkbox" x-model="selectedPages" :id="page.slug"
                                            aria-checked="false" :value="page.slug" />
                                        <span class="checkbox"></span>
                                        <span x-text="page.title"></span>
                                    </label>
                                </template>
                            </div>
                            <div
                                class="flex justify-end border-t pt-4 border-neutral-30 dark:border-neutral-500 pb-4 px-5">
                                <button class="btn-primary outlined" type="button" @click="addNewMenu()"
                                    id="add-new-menu-from-page"><i
                                        class="ph ph-plus"></i>{{ __('Add to menu') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6 xxl:col-span-8 space-y-4 xxl:space-y-6">
                    <div class="white-box min-h-[400px]">
                        <p class="m-text mb-4 border-b border-neutral-30 dark:border-neutral-500 pb-4">
                            {{ __('Menu Structure') }}
                        </p>

                        <!-- Menu Structure -->
                        <div class="dd">
                            <ol class="dd-list" id="nestable2">
                                <template x-for="(item, index) in menuItems">
                                    <li class="dd-item" :data-position="item.order" :key="index"
                                        :data-id="item.id">
                                        <div class="handle" x-data="{ open: false }">
                                            <div class="dd-container" :class="open ? 'expanded' : ''">
                                                <div class="dd-handle">
                                                    <div class="flex items-center gap-2">
                                                        <i class="ph ph-dots-six-vertical text-lg cursor-pointer"></i>
                                                        <p x-text="item.title"></p>
                                                    </div>
                                                    <span class="text-xs">{{ __('Page') }}</span>
                                                </div>
                                                <button x-on:click="open = !open" class="arrow"><i
                                                        class="ph ph-caret-down"></i></button>
                                            </div>
                                            <div x-show="open" x-collapse>
                                                <div :id="`item-form-${item.id}`"
                                                    class="space-y-3 p-3 xl:p-4 border border-neutral-30 border-t-0 dark:border-neutral-500">
                                                    <input type="hidden" :value="item.page_id||''" name="page_id">
                                                    <div>
                                                        <label class="form-level block mb-2 text-xs">{{ __('Title') }}
                                                        </label>
                                                        <input type="text" :value="item.title" name="title"
                                                            class="px-3 py-2.5 rounded-md border border-neutral-30 dark:border-neutral-500 w-full text-sm"
                                                            placeholder="{{ __('Enter Text') }}" />
                                                    </div>
                                                    <div>
                                                        <label class="form-level block mb-2 text-xs">{{ __('URL') }}
                                                        </label>
                                                        <input type="text" :value="item.url" name="url"
                                                            :disabled="item.is_primary"
                                                            class="px-3 py-2.5 rounded-md border border-neutral-30 dark:border-neutral-500 w-full text-sm"
                                                            placeholder="{{ __('Enter Text') }}" />
                                                    </div>

                                                    <div>
                                                        <label class="form-level block mb-2 text-xs">
                                                            {{ __('Icon') }}</label>
                                                        <div class="icon-picker">
                                                            <input type="text" name="icon" :value="item.icon"
                                                                class="text-input icon-input w-full"
                                                                placeholder="Select an icon">
                                                            <div class="icon-picker-modal dark:bg-neutral-800">
                                                                <div class="icon-list">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="mb-1 text-xs">Target</p>
                                                    <select class="select-2" name="target" :value="item.target">
                                                        <option value="__blank">{{ __('Open Link Directly') }}</option>
                                                        <option value="__self">
                                                            {{ __('Open Link in Direct Tab') }}
                                                        </option>
                                                    </select>

                                                    <div class="flex gap-3 mt-3">
                                                        <span @click="removeMenuItem(item.id)"
                                                            class="inline-flex px-3 py-1.5 cursor-pointer rounded-md items-center gap-1 bg-error text-white button-delete pull-right"
                                                            data-owner-id="1"> <i class="ph ph-x"
                                                                aria-hidden="true"></i>
                                                            {{ __('Delete') }} </span>
                                                        <span @click="saveSingleMenuItem(item.id)"
                                                            class="button-edit cursor-pointer px-3 py-1.5 rounded-md items-center gap-1 bg-primary text-white inline-flex pull-right"
                                                            data-owner-id="1"> <i class="ph ph-pencil-simple"
                                                                aria-hidden="true"></i> {{ __('Save') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ol class="dd-list" x-html="renderChildren(item.children, index)"></ol>
                                    </li>
                                </template>
                            </ol>
                        </div>
                    </div>

                    <div class="white-box">
                        <p class="m-text mb-4 border-b border-neutral-30 dark:border-neutral-500 pb-4">
                            {{ __('Menu Settings') }}
                        </p>
                        <div class="flex flex-wrap items-center gap-5">
                            <p>{{ __('Location') }}</p>
                            <div class="flex items-center gap-3">
                                @foreach (\App\Enums\MenuLocationEnum::cases() as $location)
                                    <label for="location-{{ $location->value }}" class="option max-lg:justify-end">
                                        <input type="radio" id="location-{{ $location->value }}"
                                            name="menu_location" x-model="mainMenu.location"
                                            value="{{ $location->value }}" />
                                        <span class="checkbox"></span>
                                        {{ $location->label() }}
                                    </label>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-6 lg:col-span-5 xl:col-span-4 xxl:col-span-3 space-y-4 xxl:space-y-6">
            <div class="white-box">
                <p class="m-text mb-4 border-b border-neutral-30 dark:border-neutral-500 pb-4">
                    {{ __(key: 'Publish') }}</p>
                <div class="flex gap-3">
                    <button class="btn-primary" type="button" x-on:click="saveMenu()">
                        <span x-show="!isSaving">
                            <i class="ph ph-floppy-disk"></i> {{ __('Save') }}
                        </span>
                        <span x-show="isSaving">
                            <i class="ph ph-spinner ph-spinner-third"></i> {{ __('Loading...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @vite(['resources/admin/js/settings/menu.js', 'resources/shared/js/primary-dashboard/icon-picker.js'])
    @endpush
</x-admin-app-layout>
