<div class="js-cookie-consent cookie-consent fixed bottom-4 right-4 z-50 p-4">
    <div class="w-80 bg-white border border-gray-200 shadow-xl rounded-xl p-6 text-center space-y-4">
        <!-- Icon -->
        <div class="flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#4f46e5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Message -->
        <p class="text-sm text-gray-800 cookie-consent__message">
            {!! __('cookie-consent::texts.message') !!}
        </p>

        <!-- Buttons -->
        <div class="flex justify-center gap-3">
            <button
                class="js-cookie-consent-agree cookie-consent__agree inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-white bg-[#4f46e5] hover:bg-indigo-600 rounded-md transition duration-200"
            >
                {{ __('cookie-consent::texts.agree') }}
            </button>
            <button
                class="js-cookie-consent-disagree cookie-consent__disagree inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition duration-200"
            >
                {{ __('cookie-consent::texts.disagree') }}
            </button>
        </div>
    </div>
</div>
