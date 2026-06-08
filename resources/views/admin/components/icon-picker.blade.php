@push('styles')
    @vite('resources/shared/css/icon-picker.css')
@endpush
@props([
    'name' => 'name',
    'value' => null,
    'id' => 'thumbnail',
])
<div class="icon-picker">
    <!-- Input to display the selected icon -->
    <input type="text" name="{{ $name }}" value="{{ $value }}" class="text-input icon-input w-full"
        id="{{ $name }}" placeholder="Select an icon">

    <!-- Modal to display the list of icons -->
    <div class="icon-picker-modal dark:bg-neutral-800">
        <div class="icon-list">
            <!-- Icons will be dynamically inserted here -->
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/shared/js/primary-dashboard/icon-picker.js')
@endpush
