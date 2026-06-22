<x-admin-app-layout>
    <div class="white-box">
        <x-admin::page-header title="{{ __('AI Settings') }}" :buttons="$buttons" :isFilterable="false" />

        <form action="{{ route('admin.settings.ai.store') }}" method="POST" class="form-submit-edit">
            @csrf

            <div class="flex items-center gap-3 mb-6">
                <i class="ph ph-sparkle text-2xl text-primary"></i>
                <div>
                    <h3 class="font-semibold text-lg">{{ __('AI Virtual Try-On') }}</h3>
                    <p class="text-sm text-neutral-500">{{ __('Let customers upload a photo and preview products on themselves, powered by Google Gemini.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 xxl:gap-6">
                <div class="input-group">
                    <x-admin::label for="ai_tryon_enabled">{{ __('Enable Virtual Try-On') }}</x-admin::label>
                    <x-admin::switch name="ai_tryon_enabled" id="ai_tryon_enabled"
                        :value="(int) $settings['ai_tryon_enabled']"
                        :types="[['label' => __('Disabled'), 'value' => 0], ['label' => __('Enabled'), 'value' => 1]]" />
                </div>

                <x-admin::text-input-group name="ai_tryon_model" :value="$settings['ai_tryon_model']"
                    label="Gemini Model" placeholder="gemini-3.1-flash-image" />

                <div class="md:col-span-2">
                    <x-admin::text-input-group name="ai_tryon_api_key" :value="$settings['ai_tryon_api_key']"
                        label="Gemini API Key" placeholder="AIza…" />
                </div>
            </div>

            <p class="text-sm text-neutral-500 mt-3">
                {{ __('Get a key from Google AI Studio (aistudio.google.com). When enabled, a "Try it On" button appears on every product page. Image generation is billed per request by Google — keep this off if you are not using it. Customer photos are never stored; generated previews are auto-deleted after 24 hours.') }}
            </p>

            <div class="flex items-center justify-end mt-4">
                <x-admin::primary-button type="submit">
                    {{ __('Save') }}
                </x-admin::primary-button>
            </div>
        </form>
    </div>
</x-admin-app-layout>
