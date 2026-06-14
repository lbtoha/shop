<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Home Sections') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.home-sections.store') }}" method="POST" class="form-submit-edit">
            @csrf

            {{-- ── Featured products section toggle ──────────────── --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6">
                <h3 class="text-sm font-bold mb-3 flex items-center gap-2">
                    <i class="ph ph-star text-primary"></i>{{ __('Featured Products Section') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin::text-input-group name="featured_title" :value="$featuredTitle"
                        label="Section Title" placeholder="{{ __('Featured Products') }}" />

                    <div class="input-group">
                        <x-admin::label for="featured_enabled">{{ __('Visibility') }}</x-admin::label>
                        <select name="featured_enabled" id="featured_enabled" class="text-input">
                            <option value="1" @selected($featuredEnabled === 1)>{{ __('Shown') }}</option>
                            <option value="0" @selected($featuredEnabled === 0)>{{ __('Hidden') }}</option>
                        </select>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    {{ __('Shows products you marked as "Featured". Toggle the whole section on or off.') }}
                </p>
            </div>

            {{-- ── All Products section toggle ────────────────────── --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6">
                <h3 class="text-sm font-bold mb-3 flex items-center gap-2">
                    <i class="ph ph-stack text-primary"></i>{{ __('All Products Section') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-admin::text-input-group name="all_title" :value="$allTitle"
                        label="Section Title" placeholder="{{ __('All Products') }}" />

                    <div class="input-group">
                        <x-admin::label for="all_limit">{{ __('Products to show') }}</x-admin::label>
                        <input type="number" name="all_limit" id="all_limit" class="text-input"
                            value="{{ $allLimit }}" min="1" max="48">
                    </div>

                    <div class="input-group">
                        <x-admin::label for="all_enabled">{{ __('Visibility') }}</x-admin::label>
                        <select name="all_enabled" id="all_enabled" class="text-input">
                            <option value="1" @selected($allEnabled === 1)>{{ __('Shown') }}</option>
                            <option value="0" @selected($allEnabled === 0)>{{ __('Hidden') }}</option>
                        </select>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    {{ __('Shows the latest products from every category combined. Toggle on or off.') }}
                </p>
            </div>

            {{-- ── Category sections repeater ─────────────────────── --}}
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="ph ph-squares-four text-primary"></i>{{ __('Category Sections') }}
                </h3>
                <button type="button" id="add-section"
                    class="inline-flex items-center gap-1.5 bg-primary/10 hover:bg-primary hover:text-white text-primary text-xs font-bold px-3 py-2 rounded-lg transition-colors">
                    <i class="ph ph-plus"></i>{{ __('Add Section') }}
                </button>
            </div>

            <p class="text-xs text-gray-500 mb-4">
                {{ __('Each row becomes a section on the home page (e.g. Toys, Food, Footwear). Row order = display order. Empty / out-of-stock categories are skipped automatically.') }}
            </p>

            <div id="sections-wrapper" class="space-y-3"></div>

            <div id="sections-empty" class="text-center text-sm text-gray-400 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl py-8 {{ empty($sections) ? '' : 'hidden' }}">
                {{ __('No sections yet. Click "Add Section" to feature a category on the home page.') }}
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>

    {{-- ── Row template ───────────────────────────────────────── --}}
    <template id="section-row-template">
        <div class="section-row border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                <div class="md:col-span-4 input-group">
                    <x-admin::label>{{ __('Category') }} <span class="text-danger">*</span></x-admin::label>
                    <select name="sections[__INDEX__][category_slug]" class="text-input" required>
                        <option value="">{{ __('Select category') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 input-group">
                    <x-admin::label>{{ __('Title') }}</x-admin::label>
                    <input type="text" name="sections[__INDEX__][title]" class="text-input"
                        placeholder="{{ __('Defaults to category name') }}">
                </div>

                <div class="md:col-span-2 input-group">
                    <x-admin::label>{{ __('Layout') }}</x-admin::label>
                    <select name="sections[__INDEX__][layout]" class="text-input">
                        <option value="grid">{{ __('Grid') }}</option>
                        <option value="slider">{{ __('Slider') }}</option>
                    </select>
                </div>

                <div class="md:col-span-1 input-group">
                    <x-admin::label>{{ __('Limit') }}</x-admin::label>
                    <input type="number" name="sections[__INDEX__][limit]" class="text-input" value="8" min="1" max="24">
                </div>

                <div class="md:col-span-1 input-group">
                    <x-admin::label>{{ __('Show') }}</x-admin::label>
                    <select name="sections[__INDEX__][enabled]" class="text-input">
                        <option value="1">{{ __('On') }}</option>
                        <option value="0">{{ __('Off') }}</option>
                    </select>
                </div>

                <div class="md:col-span-1 flex md:justify-center md:pt-7">
                    <button type="button"
                        class="remove-section w-full md:w-10 h-10 flex items-center justify-center rounded-lg bg-error/10 hover:bg-error hover:text-white text-error transition-colors"
                        title="{{ __('Remove') }}">
                        <i class="ph ph-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
        (function () {
            const wrapper  = document.getElementById('sections-wrapper');
            const template = document.getElementById('section-row-template');
            const addBtn   = document.getElementById('add-section');
            const emptyMsg = document.getElementById('sections-empty');
            let index = 0;

            function addRow(data) {
                const html = template.innerHTML.replace(/__INDEX__/g, index++);
                const frag = document.createElement('div');
                frag.innerHTML = html.trim();
                const row = frag.firstElementChild;

                if (data) {
                    const set = (name, val) => {
                        const el = row.querySelector(`[name$="[${name}]"]`);
                        if (el != null && val != null) el.value = val;
                    };
                    set('category_slug', data.category_slug);
                    set('title', data.title ?? '');
                    set('layout', data.layout ?? 'grid');
                    set('limit', data.limit ?? 8);
                    set('enabled', (data.enabled ?? 1).toString());
                }

                wrapper.appendChild(row);
                if (emptyMsg) emptyMsg.classList.add('hidden');
            }

            addBtn.addEventListener('click', () => addRow());

            wrapper.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-section');
                if (!btn) return;
                btn.closest('.section-row').remove();
                if (!wrapper.children.length && emptyMsg) emptyMsg.classList.remove('hidden');
            });

            // Hydrate saved rows.
            const saved = @json($sections);
            if (Array.isArray(saved) && saved.length) {
                saved.forEach(addRow);
            }
        })();
    </script>
    @endpush
</x-admin-app-layout>
