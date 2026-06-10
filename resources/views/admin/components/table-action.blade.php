@props(['action_buttons' => []])
<div class="relative" id="dot-action">
    <button type="button" class="popover-button">
        <i class="ph ph-dots-three-vertical text-2xl"></i>
    </button>
    <ul class="popover-content">
        @foreach ($action_buttons as $button)
            <li>
                @if ($button['type'] == 'link')
                    <a href="{{ $button['href'] }}">
                        <i class="ph ph-{{ $button['icon'] }} text-lg"></i>
                        {{ __($button['label']) }}
                    </a>
                @elseif ($button['type'] == 'modal')
                    <button type="button" class="set-edit-modal-form-data" data-row="{{ $button['row'] }}"
                        data-action="{{ $button['href'] }}" data-modal-target="{{ $button['id'] }}"><i
                            class="{{ $button['icon'] }} text-lg"></i>
                        {{ __($button['label']) }}
                    </button>
                @elseif ($button['type'] == 'delete')
                    <button type="button" class="delete-item-action" data-action="{{ $button['href'] }}"><i
                            class="{{ $button['icon'] }} text-lg"></i>
                        {{ __($button['label']) }}
                    </button>
                @elseif ($button['type'] == 'action-confirm')
                    <button type="button" class="action-confirm-btn"
                        action="{{ $button['href'] }}"
                        method="{{ $button['method'] ?? 'POST' }}"
                        title="{{ $button['title'] ?? __('Are you sure?') }}"
                        text="{{ $button['text'] ?? '' }}"><i
                            class="{{ $button['icon'] }} text-lg"></i>
                        {{ __($button['label']) }}
                    </button>
                @endif
            </li>
        @endforeach
    </ul>
</div>
