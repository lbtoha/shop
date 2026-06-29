@props([
    'class' => '',
    'label' => '',
    'name' => 'name',
    'errors' => null,
    'value' => null,
    'id' => $name, // Default to $name if no id is provided
    'types' => [
        [
            'label' => 'Inactive',
            'value' => 'inactive',
        ],
        [
            'label' => 'Active',
            'value' => 'active',
        ],
    ],
    'uniqueId' => uniqid(), // Generate a unique ID for each instance
])

<div class="relative {{ $class }}">
    <label class="toggle-label" for="{{ $name }}-{{ $uniqueId }}">
        <!-- Checkbox input for the toggle -->
        <input id="{{ $name }}-{{ $uniqueId }}"
               type="checkbox"
               name="{{ $name }}"
               class="sr-only peer"
               value="{{ $types[1]['value'] }}"
               {{ $value == $types[1]['value'] ? 'checked' : '' }} />

        <!-- Hidden input for the inactive state -->
        <input type="hidden"
               name="{{ $name }}"
               value="{{ $types[0]['value'] }}" />

        <!-- Toggle background -->
        <div class="bg peer-checked:!bg-primary/10"></div>

        <!-- Toggle circle -->
        <span class="text-bg peer-checked:!bg-primary peer-checked:translate-x-full"></span>

        <!-- levels for each state -->
        @foreach ($types as $key => $type)
            @if ($key == 0)
                <span class="text flex left-0 text-neutral-0 peer-checked:text-neutral-500 dark:peer-checked:text-neutral-400">
                    <i class="ph ph-x-circle"></i>
                    {{ $type['label'] }}
                </span>
            @else
                <span class="text flex right-0 text-neutral-500 dark:text-neutral-400 peer-checked:text-neutral-0">
                    <i class="ph ph-check-circle"></i>
                    {{ $type['label'] }}
                </span>
            @endif
        @endforeach
    </label>
</div>
