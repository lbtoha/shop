@props([
    'name' => 'name',
    'level' => 'Choose File',
    'value' => null,
    'id' => 'thumbnail',
    'image_preview' => true,
])

<div class="input-group">
    <div class="file-manager-container rounded-md">
        <label>
            <div
                class="w-full rounded-lg border border-neutral-30 dark:border-neutral-500 justify-between items-center inline-flex">
                <span class="px-4 shrink-0 file-name">{{ __('Choose File') }}</span>
                <input id="thumbnail-{{ $id }}" class="w-full" readonly name="{{ $name }}"
                    value="{{ $value }}" />
                <button type="button" 
                        class="clear-file-btn {{ empty($value) ? 'hidden' : '' }} p-2 mr-1 text-neutral-400 hover:text-red-500 transition-colors duration-200 cursor-pointer focus:outline-none shrink-0"
                        data-input="thumbnail-{{ $id }}" 
                        data-preview="holder-{{ $id }}"
                        title="{{ __('Remove image') }}">
                    <i class="ph ph-x-circle text-lg"></i>
                </button>
                <div id="{{ $id }}" data-input="thumbnail-{{ $id }}"
                    data-preview="holder-{{ $id }}"
                    class="file-uploader flex shrink-0 w-28 h-11 px-2 flex-col bg-primary rounded-r-lg shadow text-white text-xs font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">
                    {{ __('Choose File') }}
                </div>
            </div>
        </label>

        @if ($image_preview)
            <div id="holder-{{ $id }}" class="preview max-w-[100px] mt-2">
                @if ($value)
                    <img src="{{ $value }}" width="50" height="50" class="img-fluid">
                @endif
            </div>
        @endif
    </div>
</div>
