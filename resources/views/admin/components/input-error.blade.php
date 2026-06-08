@props(['name' => null, 'errors' => null])

<span class="input-text-error" id="{{ $name }}">{{ $errors->first($name) }}</span>
