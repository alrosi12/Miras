@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge([
        'class' => 'mt-1.5 space-y-1 text-sm text-red-600'
    ]) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-1.5 leading-relaxed">
                <span class="mt-0.5 shrink-0">•</span>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
