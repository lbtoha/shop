@props([
    'modalId' => 'modalId',
    'title' => 'title',
])

<template x-teleport="body">
    <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[black]/60 dark:bg-neutral-40/80"
        :class="isOpen("{{ $modalId }}") && '!block'">
        <div class="flex min-h-screen items-center justify-center px-4 text-neutral-700 dark:text-neutral-20"
            @click.self="closeModal({{ $modalId }})">
            <div x-show="isOpen({{ $modalId }})"
                class="panel my-8 w-full max-w-3xl overflow-hidden rounded-lg border-0 bg-neutral-0 p-3 dark:bg-neutral-904 sm:p-4 md:p-6 lg:p-8">
                <div class="mb-4 flex items-center justify-between">
                    <h4>{{ $title }}</h4>
                    <i class="ph ph-x cursor-pointer text-xl" @click="closeModal({{ $modalId }})"></i>
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</template>
