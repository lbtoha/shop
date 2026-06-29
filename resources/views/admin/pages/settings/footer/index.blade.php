<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('Footer Setting') }}" :buttons="$buttons" :dateFilter="false" />
        
        <form action="{{ route('admin.settings.footer.store') }}" method="POST"
            class="grid grid-cols-2 gap-4 xl:gap-6 form-submit-edit">
            @csrf
            <div class="col-span-2 md:col-span-1">
                <x-admin::text-input-group name="footer_text" value="{{ $settings['footer_text'] ?? '' }}"
                    label="Footer Text" />
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <x-admin::label for="footer_menu_id">
                    {{ __('Footer Menu') }}
                </x-admin::label>
                <x-admin::select-option id="footer_menu_id" name="footer_menu_id">
                    <option value="">{{ __('Select Menu (Fallback to default)') }}</option>
                    @foreach ($menus as $menu)
                        <option value="{{ $menu->id }}"
                            {{ (isset($settings['footer_menu_id']) && $settings['footer_menu_id'] == $menu->id) ? 'selected' : '' }}>
                            {{ $menu->name }}
                        </option>
                    @endforeach
                </x-admin::select-option>
            </div>

            <div class="col-span-2 flex gap-3">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
    @push('scripts')
        @vite('resources/admin/js/settings/general.js')
    @endpush
</x-admin-app-layout>
