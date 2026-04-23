@extends('layouts.app')

@section('title', __('Training dashboard'))

@section('header')
    <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ __('Training dashboard') }}</h1>
@endsection

@section('content')
    <div class="space-y-10">
        {{-- أيام الأسبوع الحالي مع إكمال الجلسة --}}
        <section class="rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-900/[0.03]">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('This week') }}</h2>
            <div class="mt-5 grid grid-cols-7 gap-2 text-center sm:gap-3">
                @foreach ($weekCompletion as $day)
                    <div class="flex flex-col items-center gap-2 rounded-xl border px-1 py-3.5 transition duration-200 sm:px-2 {{ $day['completed'] ? 'border-emerald-200 bg-emerald-50 shadow-sm shadow-emerald-900/[0.04]' : 'border-slate-100 bg-slate-50/80 hover:border-slate-200/80' }}">
                        <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $day['label'] }}</span>
                        <span class="text-lg sm:text-xl" aria-label="{{ $day['completed'] ? __('Session logged') : __('No session') }}">{{ $day['completed'] ? '✓' : '·' }}</span>
                        <span class="hidden text-[10px] text-slate-400 sm:block">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('j') }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('This week vs last week') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <x-stat-card
                    :label="__('Sessions')"
                    :value="$weekThis['sessions_count']"
                    :hint="__('Last week').': '.$weekPrev['sessions_count'].' · '.($weekCompare['sessions_pct'] === null ? __('New vs last week') : (number_format($weekCompare['sessions_pct'], 1).'%'))"
                    accent="emerald"
                />
                <x-stat-card
                    :label="__('Minutes')"
                    :value="$weekThis['total_minutes']"
                    :hint="__('Last week').': '.$weekPrev['total_minutes']"
                    accent="sky"
                />
                <x-stat-card
                    :label="__('Distinct exercises')"
                    :value="$weekThis['distinct_exercises_count']"
                    :hint="__('Last week').': '.$weekPrev['distinct_exercises_count']"
                    accent="violet"
                />
            </div>
            <p class="mt-3 text-xs text-slate-400">
                {{ __('Week') }}: {{ $weekThis['week_start']->toFormattedDateString() }} — {{ $weekThis['week_end']->toFormattedDateString() }}
            </p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card :label="__('Current streak')" :value="$streakCurrent" :hint="__('Consecutive days with a logged session.')" />
            <x-stat-card :label="__('Best streak')" :value="$streakBest" :hint="__('Longest run of consecutive training days.')" accent="amber" />
            <x-stat-card
                :label="__('Latest body weight')"
                :value="$latestMeasurement ? $latestMeasurement->weight : '—'"
                :hint="$bodyWeightMonth && $bodyWeightMonth['delta_kg'] !== null
                    ? __('vs ~4 weeks ago') . ': ' . ($bodyWeightMonth['delta_kg'] > 0 ? '+' : '') . $bodyWeightMonth['delta_kg'] . ' ' . __('kg') . ' · ' . ($latestMeasurement ? $latestMeasurement->date->toFormattedDateString() : '')
                    : ($latestMeasurement ? $latestMeasurement->date->toFormattedDateString() : __('No measurements yet.'))"
            />
        </section>

        @if (collect($bigThreeProgress)->contains(fn ($r) => $r['current_kg'] !== null || $r['previous_kg'] !== null))
            <section>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('Main lifts') }} (1RM / top set)</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($bigThreeProgress as $lift)
                        <x-stat-card
                            :label="$lift['label']"
                            :value="$lift['current_kg'] !== null ? $lift['current_kg'].' '.__('kg') : '—'"
                            :hint="$lift['delta_kg'] !== null
                                ? __('vs last month') . ': ' . ($lift['delta_kg'] > 0 ? '+' : '') . $lift['delta_kg'] . ' ' . __('kg')
                                : ($lift['previous_kg'] !== null ? __('Last month') . ': ' . $lift['previous_kg'] . ' ' . __('kg') : '')"
                            accent="emerald"
                        />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-900/[0.03] sm:p-7">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Last 7 days') }}</h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-slate-500">{{ __('Sessions and total minutes per day') }}</p>
                </div>
            </div>
            <div class="relative h-64 w-full min-w-0">
                <canvas id="dashboardActivity7d" aria-label="{{ __('Last 7 days activity chart') }}"></canvas>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                <div class="border-b border-slate-100 px-6 py-4 sm:px-7 sm:py-5">
                    <h3 class="font-semibold tracking-tight text-slate-900">{{ __('Last 5 sessions') }}</h3>
                </div>
                <ul class="space-y-2 p-3 sm:p-4">
                    @forelse ($lastSessions as $session)
                        <li>
                            <x-session-card :session="$session" />
                        </li>
                    @empty
                        <li class="px-4 py-8 text-sm leading-relaxed text-slate-600 sm:px-5">{{ __('No sessions yet.') }}</li>
                    @endforelse
                </ul>
            </section>

            <div class="space-y-6">
                <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                    <div class="border-b border-slate-100 px-6 py-4 sm:px-7 sm:py-5">
                        <h3 class="font-semibold tracking-tight text-slate-900">{{ __("Today's routine") }}</h3>
                    </div>
                    <div class="p-6 sm:p-7">
                        @if ($todaysRoutine)
                            <p class="font-medium text-slate-800">{{ $todaysRoutine['plan']->name }}</p>
                            <p class="mt-2 text-sm text-emerald-800">{{ $todaysRoutine['day']->day_name }}</p>
                            <ul class="mt-4 list-inside list-disc space-y-1.5 text-sm leading-relaxed text-slate-600">
                                @foreach ($todaysRoutine['day']->planDayExercises as $row)
                                    <li>{{ $row->exercise->name }} — {{ $row->sets }}×{{ $row->reps }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('workout-plans.show', $todaysRoutine['plan']) }}" class="mt-5 inline-block text-sm font-semibold text-emerald-700 transition duration-200 hover:text-emerald-800">{{ __('View plan') }}</a>
                        @else
                            <p class="text-sm leading-relaxed text-slate-600">{{ __('No matching plan day for today.') }}</p>
                            <a href="{{ route('workout-plans.create') }}" class="mt-3 inline-block text-sm font-semibold text-emerald-700 transition duration-200 hover:text-emerald-800">{{ __('Create a plan') }}</a>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
            <div class="border-b border-slate-100 px-6 py-4 sm:px-7 sm:py-5">
                <h3 class="font-semibold tracking-tight text-slate-900">{{ __('Friends activity') }}</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-slate-500">{{ __('Latest session from each friend') }}</p>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($friendsSessions as $fs)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 text-sm transition duration-150 hover:bg-slate-50/80 sm:px-7 sm:py-5">
                        <div class="flex min-w-0 items-center gap-3">
                            @if (! empty($fs->user->avatar))
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($fs->user->avatar) }}" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover ring-1 ring-slate-200" />
                            @else
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800">{{ mb_substr($fs->user->name, 0, 1) }}</span>
                            @endif
                            <div class="min-w-0">
                                <span class="font-medium text-slate-900">{{ $fs->user->name }}</span>
                                <span class="text-slate-500"> · {{ $fs->date->toFormattedDateString() }}</span>
                                <span class="text-xs text-slate-400">({{ $fs->date->diffForHumans() }})</span>
                                @if ($fs->workoutPlan)
                                    <p class="text-xs text-slate-600">{{ $fs->workoutPlan->name }}</p>
                                @endif
                                @if ($fs->duration_minutes)
                                    <p class="text-xs text-emerald-700">{{ $fs->duration_minutes }} {{ __('min') }}</p>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-8 text-sm leading-relaxed text-slate-600 sm:px-7">{{ __('No friend sessions to show yet.') }}</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-900/[0.03] sm:p-7">
            <p class="font-medium tracking-tight text-slate-800">{{ __('Quick links') }}</p>
            <div class="mt-5 flex flex-wrap gap-2 text-sm font-semibold sm:gap-3">
                <a href="{{ route('exercises.index') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Exercises') }}</a>
                <a href="{{ route('workout-plans.index') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Routine') }}</a>
                <a href="{{ route('workout-sessions.index') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Sessions') }}</a>
                <a href="{{ route('workout-sessions.today') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Today session') }}</a>
                <a href="{{ route('body-measurements.index') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Measurements') }}</a>
                <a href="{{ route('progress.index') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Progress') }}</a>
                <a href="{{ route('friends.index') }}" class="rounded-lg bg-emerald-50 px-3.5 py-2 text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100">{{ __('Friends') }}</a>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('dashboardActivity7d');
                if (!el || typeof Chart === 'undefined') return;
                var raw = @json($chartLast7Days);
                var labels = raw.map(function (r) { return r.label; });
                var sessions = raw.map(function (r) { return r.sessions_count; });
                var minutes = raw.map(function (r) { return r.total_minutes; });
                var maxSessions = Math.max.apply(null, sessions.concat([0]));
                var maxMinutes = Math.max.apply(null, minutes.concat([0]));
                new Chart(el, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: @json(__('Sessions')),
                                data: sessions,
                                yAxisID: 'y',
                                backgroundColor: 'rgba(16, 185, 129, 0.55)',
                                borderColor: 'rgb(5, 150, 105)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: @json(__('Minutes')),
                                data: minutes,
                                type: 'line',
                                yAxisID: 'y1',
                                borderColor: 'rgb(14, 165, 233)',
                                backgroundColor: 'rgba(14, 165, 233, 0.15)',
                                tension: 0.25,
                                fill: true,
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                title: { display: true, text: @json(__('Sessions')) },
                                ticks: { stepSize: 1, precision: 0 },
                                suggestedMax: Math.max(2, maxSessions + 1),
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                title: { display: true, text: @json(__('Minutes')) },
                                suggestedMax: Math.max(30, maxMinutes + 15),
                            },
                        },
                    },
                });
            });
        </script>
    @endpush
@endsection
