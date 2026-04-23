@extends('layouts.app')

@section('title', __('Monthly report'))

@section('header')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl space-y-1.5">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ __('Monthly report') }}</h1>
            <p class="text-sm leading-relaxed text-slate-500">{{ __('Weekly volume inside the month you pick.') }}</p>
        </div>
        <a href="{{ route('progress.index') }}" class="inline-flex shrink-0 items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60 sm:self-auto">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            {{ __('Back') }}
        </a>
    </div>
@endsection

@section('content')
    @php
        $sessionsChartConfig = [
            'type' => 'bar',
            'data' => $weeklySessionsChart,
            'options' => [
                'responsive' => true,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['y' => ['beginAtZero' => true]],
            ],
        ];
        $minutesChartConfig = [
            'type' => 'bar',
            'data' => $weeklyMinutesChart,
            'options' => [
                'responsive' => true,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['y' => ['beginAtZero' => true]],
            ],
        ];
        $muscleChartConfig = [
            'type' => 'pie',
            'data' => $muscleFrequencyChart,
            'options' => [
                'responsive' => true,
                'plugins' => ['legend' => ['position' => 'bottom']],
            ],
        ];
    @endphp

    <div class="mx-auto max-w-6xl space-y-8">
        <form method="GET" action="{{ route('progress.monthly') }}" class="flex flex-col gap-4 rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-900/[0.03] sm:flex-row sm:flex-wrap sm:items-end sm:gap-5 sm:p-6">
            <div class="min-w-[12rem] flex-1">
                <label for="month" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Month') }}</label>
                <input id="month" type="month" name="month" value="{{ $monthStart->format('Y-m') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm transition duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" />
            </div>
            <div class="flex shrink-0 items-center gap-3">
                <x-primary-button type="submit">{{ __('Show') }}</x-primary-button>
            </div>
        </form>

        <div class="inline-flex flex-wrap items-center gap-x-3 gap-y-1 rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm">
            <span class="font-medium text-slate-900">{{ __('Period') }}:</span>
            <span class="tabular-nums">{{ $monthStart->toFormattedDateString() }} — {{ $monthEnd->toFormattedDateString() }}</span>
            <span class="hidden text-slate-300 sm:inline" aria-hidden="true">·</span>
            <span class="font-semibold tabular-nums text-emerald-800">{{ $sessions->count() }} {{ __('sessions') }}</span>
        </div>

        @if (count($weeklySessionsChart['labels']) > 0)
            <div class="grid gap-6 lg:grid-cols-2">
                <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Sessions per week') }}</h2>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="h-64 w-full">
                            <canvas id="chartSessionsWeek" class="max-h-full w-full" aria-label="{{ __('Sessions per week') }}"></canvas>
                        </div>
                    </div>
                </section>
                <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Minutes per week') }}</h2>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div class="h-64 w-full">
                            <canvas id="chartMinutesWeek" class="max-h-full w-full" aria-label="{{ __('Minutes per week') }}"></canvas>
                        </div>
                    </div>
                </section>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center shadow-sm">
                <p class="text-sm font-medium text-slate-700">{{ __('No sessions in this month.') }}</p>
                <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-slate-500">{{ __('Pick another month or log a workout session for this period.') }}</p>
            </div>
        @endif

        @if (count($muscleFrequencyChart['labels']) > 0)
            <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Muscle groups (set count)') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('Share of completed sets by muscle group.') }}</p>
                </div>
                <div class="flex justify-center p-5 sm:p-6">
                    <div class="h-72 w-full max-w-2xl">
                        <canvas id="chartMuscles" class="max-h-full w-full" aria-label="{{ __('Muscle groups distribution') }}"></canvas>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;
            @if (count($weeklySessionsChart['labels']) > 0)
                const c1 = document.getElementById('chartSessionsWeek');
                const c2 = document.getElementById('chartMinutesWeek');
                if (c1) new Chart(c1, @json($sessionsChartConfig));
                if (c2) new Chart(c2, @json($minutesChartConfig));
            @endif
            @if (count($muscleFrequencyChart['labels']) > 0)
                const c3 = document.getElementById('chartMuscles');
                if (c3) new Chart(c3, @json($muscleChartConfig));
            @endif
        });
    </script>
@endpush
