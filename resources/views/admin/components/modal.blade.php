@props([
    'modalId' => 'modalId',
    'title' => 'title',
])

<div id="{{ $modalId }}" class="fixed inset-0 items-center justify-center overflow-y-auto hidden">
    <div class="flex min-h-screen items-center justify-center px-4 text-neutral-700 dark:text-neutral-20 modal-inner">
        <div
            class="panel my-8 w-full max-w-3xl overflow-hidden rounded-lg border-0 bg-neutral-0 p-3 dark:bg-neutral-904 sm:p-4 xl:p-6">
            <div>
                <div class="mb-4 flex items-center justify-between bb-dashed-n30">
                    <h4>{{ __($title) }}</h4>
                    <i class="ph ph-x cursor-pointer text-xl" data-modal-close="{{ $modalId }}"></i>
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
