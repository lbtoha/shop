{{--
    Floating WhatsApp contact button — shown site-wide on the storefront.
    Toggled from admin Shop Settings (whatsapp_enabled + whatsapp_number).
    Optional $waMessage lets a page (e.g. product detail) pre-fill the chat text.
--}}
@php
    $waEnabled = (int) getOption('whatsapp_enabled', 0) === 1 && filled(getOption('whatsapp_number'));
    $waNumberFloat = preg_replace('/[^0-9]/', '', getOption('whatsapp_number', ''));
    // Pages may set a pre-filled chat message via @section('wa_message').
    $waMessage = trim($__env->yieldContent('wa_message'));
    $waText = filled($waMessage) ? '?text=' . rawurlencode($waMessage) : '';
@endphp

@if ($waEnabled)
    <a href="https://wa.me/{{ $waNumberFloat }}{{ $waText }}" target="_blank" rel="noopener"
        aria-label="{{ __('Contact us on WhatsApp') }}"
        class="fixed left-4 bottom-[76px] lg:bottom-6 z-50 w-14 h-14 rounded-full bg-[#25D366] hover:bg-[#1ebe57] text-white flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
        <i class="ph-fill ph-whatsapp-logo text-3xl"></i>
    </a>
@endif
