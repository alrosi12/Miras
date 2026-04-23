@props(['session', 'href' => null])

@php
    $url = $href ?? route('workout-sessions.show', $session);
@endphp

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'group block rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.03] transition duration-200 ease-out hover:border-emerald-200 hover:shadow-md hover:shadow-slate-900/[0.05]']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <p class="font-semibold tracking-tight text-slate-900">{{ $session->date->translatedFormat('l j M Y') }}</p>
            @if ($session->workoutPlan)
                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $session->workoutPlan->name }}</p>
            @endif
            @if ($session->duration_minutes !== null)
                <p class="mt-2.5 text-xs font-medium text-emerald-700">{{ $session->duration_minutes }} {{ __('min') }}</p>
            @endif
            @if ($session->relationLoaded('sessionSets') && $session->sessionSets->isNotEmpty())
                @php
                    $exerciseCount = $session->sessionSets->pluck('exercise_id')->filter()->unique()->count();
                @endphp
                @if ($exerciseCount > 0)
                    <p class="mt-1 text-xs text-slate-500">{{ __('Exercises') }}: {{ $exerciseCount }}</p>
                @endif
            @endif
        </div>
        <span class="shrink-0 text-slate-400 transition duration-200 group-hover:text-slate-500" aria-hidden="true">→</span>
    </div>
    @if ($session->relationLoaded('sessionSets') && $session->sessionSets->isNotEmpty())
        <p class="mt-3.5 line-clamp-2 text-xs leading-relaxed text-slate-500">
            {{ $session->sessionSets->pluck('exercise.name')->filter()->unique()->implode(', ') }}
        </p>
    @endif
</a>
