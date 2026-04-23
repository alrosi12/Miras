@props(['title', 'chartId'])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-900/[0.03]']) }}>
    <h3 class="mb-5 text-sm font-semibold tracking-tight text-slate-900">{{ $title }}</h3>
    <div class="relative min-h-[220px] w-full">
        <canvas id="{{ $chartId }}" class="max-h-72 w-full"></canvas>
    </div>
    @isset($footer)
        <div class="mt-5 border-t border-slate-100 pt-4 text-xs leading-relaxed text-slate-500">
            {{ $footer }}
        </div>
    @endisset
</div>
