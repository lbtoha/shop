<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Brand Partners') }}" :dateFilter="false" />
        
        <div class="flex items-start gap-3 rounded-md border border-warning/40 px-4 py-3 bg-warning/5 mb-6">
            <i class="ph ph-lightbulb text-warning text-lg"></i>
            <p class="text-warning s-text">
                {{ __('Specify your brand partners. You can add their names, upload/select their logo images, and add/remove as many slots as you need. If left blank, the storefront will display the default premium brand list.') }}
            </p>
        </div>

        <form action="{{ route('admin.settings.brand-partners.store') }}" method="POST"
            class="form-submit-edit">
            @csrf

            <!-- Toggle Brand Partners Section -->
            <div class="border border-neutral-30 dark:border-neutral-500 rounded-lg p-5 mb-8 bg-neutral-50/50 dark:bg-neutral-800">
                <div class="max-w-xs input-group">
                    <x-admin::label for="show_brand_partners">{{ __('Show Brand Partners on Storefront') }}</x-admin::label>
                    <x-admin::switch name="show_brand_partners" id="show_brand_partners" 
                        :value="getOption('show_brand_partners', 1)" 
                        :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>
            </div>
            
            <!-- Repeater Container -->
            <div id="partners-container" class="space-y-6 mb-8">
                @php
                    $partnersList = count($partners) > 0 ? $partners : [['name' => '', 'logo' => '']];
                @endphp

                @foreach ($partnersList as $index => $partner)
                    <div class="partner-row border border-neutral-30 dark:border-neutral-500 rounded-lg p-5 bg-neutral-50/50 dark:bg-neutral-800 relative" data-index="{{ $index }}">
                        <!-- Remove button -->
                        <button type="button" class="remove-partner-btn absolute top-3 right-3 text-danger hover:text-danger/80 text-xs flex items-center gap-1 font-semibold cursor-pointer transition-colors duration-200">
                            <i class="ph ph-trash text-sm"></i> {{ __('Remove') }}
                        </button>

                        <h4 class="font-bold text-sm text-neutral-800 dark:text-neutral-100 mb-4 border-b pb-2 flex justify-between">
                            <span>{{ __('Brand Partner') }}</span>
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name Input -->
                            <div>
                                <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">{{ __('Brand Name') }}</label>
                                <input type="text" name="partners[{{ $index }}][name]" value="{{ $partner['name'] }}" 
                                    class="w-full px-3 py-2 border rounded-md border-neutral-30 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-sm focus:outline-none focus:border-primary" 
                                    placeholder="e.g. Vogue" />
                            </div>

                            <!-- Logo Input via LFM -->
                            <div>
                                <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">{{ __('Brand Logo') }}</label>
                                <div class="relative border border-neutral-30 dark:border-neutral-600 rounded-lg p-4 flex justify-center bg-white dark:bg-neutral-900 min-h-24">
                                    <div id="partner_logo_{{ $index }}_preview" class="max-h-16 flex items-center justify-center">
                                        @if ($partner['logo'])
                                            <img src="{{ $partner['logo'] }}" alt="Partner logo" class="max-h-16 object-contain" />
                                        @else
                                            <span class="text-neutral-400 text-xs">{{ __('No logo selected') }}</span>
                                        @endif
                                    </div>
                                    <input type="text" class="sr-only" id="partner_logo_{{ $index }}_input" name="partners[{{ $index }}][logo]" value="{{ $partner['logo'] }}" />
                                    <label for="partner_logo_{{ $index }}" id="partner_logo_{{ $index }}" data-input="partner_logo_{{ $index }}_input" data-preview="partner_logo_{{ $index }}_preview" 
                                        class="absolute cursor-pointer size-8 right-3 bottom-0 translate-y-1/2 bg-primary rounded-full text-neutral-0 text-lg f-center">
                                        <i class="ph ph-cloud-arrow-up"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Add & Save Actions -->
            <div class="flex flex-wrap gap-4 items-center justify-between border-t pt-6 border-neutral-30 dark:border-neutral-500">
                <button type="button" id="add-partner-btn" class="flex items-center gap-2 px-4 py-2 border border-primary text-primary hover:bg-primary hover:text-white rounded-md transition-all duration-300 font-semibold cursor-pointer text-sm">
                    <i class="ph ph-plus-circle text-lg"></i> {{ __('Add Brand Partner') }}
                </button>

                <x-admin::primary-button type="submit">
                    {{ __('Save Changes') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>

    <!-- Template for Dynamic Rows -->
    <template id="partner-template">
        <div class="partner-row border border-neutral-30 dark:border-neutral-500 rounded-lg p-5 bg-neutral-50/50 dark:bg-neutral-800 relative" data-index="__INDEX__">
            <button type="button" class="remove-partner-btn absolute top-3 right-3 text-danger hover:text-danger/80 text-xs flex items-center gap-1 font-semibold cursor-pointer transition-colors duration-200">
                <i class="ph ph-trash text-sm"></i> {{ __('Remove') }}
            </button>

            <h4 class="font-bold text-sm text-neutral-800 dark:text-neutral-100 mb-4 border-b pb-2 flex justify-between">
                <span>{{ __('Brand Partner') }}</span>
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">{{ __('Brand Name') }}</label>
                    <input type="text" name="partners[__INDEX__][name]" value="" 
                        class="w-full px-3 py-2 border rounded-md border-neutral-30 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-sm focus:outline-none focus:border-primary" 
                        placeholder="e.g. Vogue" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">{{ __('Brand Logo') }}</label>
                    <div class="relative border border-neutral-30 dark:border-neutral-600 rounded-lg p-4 flex justify-center bg-white dark:bg-neutral-900 min-h-24">
                        <div id="partner_logo___INDEX___preview" class="max-h-16 flex items-center justify-center">
                            <span class="text-neutral-400 text-xs">{{ __('No logo selected') }}</span>
                        </div>
                        <input type="text" class="sr-only" id="partner_logo___INDEX___input" name="partners[__INDEX__][logo]" value="" />
                        <label for="partner_logo___INDEX__" id="partner_logo___INDEX__" data-input="partner_logo___INDEX___input" data-preview="partner_logo___INDEX___preview" 
                            class="absolute cursor-pointer size-8 right-3 bottom-0 translate-y-1/2 bg-primary rounded-full text-neutral-0 text-lg f-center">
                            <i class="ph ph-cloud-arrow-up"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
        @vite('resources/admin/js/settings/general.js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let container = document.getElementById('partners-container');
                let addBtn = document.getElementById('add-partner-btn');
                let template = document.getElementById('partner-template').innerHTML;

                // Unique index generator based on maximum existing index to prevent duplicate inputs
                let maxIndex = -1;
                document.querySelectorAll('.partner-row').forEach(function(row) {
                    let idx = parseInt(row.getAttribute('data-index') || 0);
                    if (idx > maxIndex) {
                        maxIndex = idx;
                    }
                });
                let nextIndex = maxIndex + 1;

                // Handle adding a new row
                addBtn.addEventListener('click', function () {
                    let html = template.replace(/__INDEX__/g, nextIndex);
                    
                    // Append to container
                    container.insertAdjacentHTML('beforeend', html);

                    // Initialize file manager for the new row
                    if (window.fileManagerInit) {
                        window.fileManagerInit('partner_logo_' + nextIndex, 'image');
                    }

                    nextIndex++;
                });

                // Handle removing a row (using delegation)
                container.addEventListener('click', function (e) {
                    if (e.target.closest('.remove-partner-btn')) {
                        let row = e.target.closest('.partner-row');
                        if (row) {
                            row.remove();
                        }
                    }
                });

                // Initialize existing rows
                document.querySelectorAll('.partner-row').forEach(function(row) {
                    let idx = row.getAttribute('data-index');
                    if (window.fileManagerInit) {
                        window.fileManagerInit('partner_logo_' + idx, 'image');
                    }
                });
            });
        </script>
    @endpush
</x-admin-app-layout>
