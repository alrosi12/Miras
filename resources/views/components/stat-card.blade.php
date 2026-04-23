@props([
    'label',
    'value',
    'hint' => null,
    'accent' => 'emerald',
])

@php
    $accents = [
        'emerald' => 'from-emerald-500/10 to-teal-500/5 ring-emerald-200/60',
        'sky' => 'from-sky-500/10 to-blue-500/5 ring-sky-200/60',
        'amber' => 'from-amber-500/10 to-orange-500/5 ring-amber-200/60',
        'violet' => 'from-violet-500/10 to-purple-500/5 ring-violet-200/60',
    ];
    $wrap = $accents[$accent] ?? $accents['emerald'];
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-xl bg-gradient-to-br p-6 shadow-sm shadow-slate-900/[0.03] ring-1 transition duration-200 ease-out hover:shadow-md hover:shadow-slate-900/[0.05] '.$wrap]) }}>
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-2.5 text-xs leading-relaxed text-slate-600">{{ $hint }}</p>
    @endif
</div>
