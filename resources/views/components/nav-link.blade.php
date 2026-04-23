@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center rounded-t-lg px-2.5 pt-1.5 pb-2.5 border-b-2 border-indigo-400 text-sm font-semibold text-slate-900 transition duration-200 ease-out'
    : 'inline-flex items-center rounded-t-lg px-2.5 pt-1.5 pb-2.5 border-b-2 border-transparent text-sm font-medium text-slate-500 transition duration-200 ease-out hover:border-slate-300 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-200/50';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
