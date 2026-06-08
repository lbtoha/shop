@props([
    'name' => 'name',
    'level' => '',
    'errors' => null,
    'value' => null,
    'multiple' => false,
])

<label class="flex flex-col input-group">
    <div
        class="w-full single-file-upload rounded-lg border border-neutral-30 dark:border-neutral-500 justify-between items-center inline-flex">
        <span class="px-4 file-name">{{ $value ? $value : __('Choose File') }}</span>
        <input type="file" name="{{ $name }}" class="{{ $errors->has($name) ? 'input-error' : '' }}"
            @if ($multiple) multiple @endif hidden />
        <div
            class="flex w-28 h-11 px-2 flex-col bg-primary rounded-r-lg shadow text-white text-xs font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">
            {{ __('Choose File')  }}
        </div>
    </div>
</label>
