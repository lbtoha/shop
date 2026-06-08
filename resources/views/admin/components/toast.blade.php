@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if (session($type))
        <div id="session-toast" data-type="{{ $type }}" data-message="{{ session($type) }}">
        </div>
    @endif
@endforeach
