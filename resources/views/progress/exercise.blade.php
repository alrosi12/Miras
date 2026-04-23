@extends('layouts.app')

@section('title', __('Exercise progress').' — '.$exercise->name)

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0 space-y-1.5">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ __('Exercise progress') }}</p>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ $exercise->name }}</h1>
            <p class="text-sm leading-relaxed text-slate-500">{{ __('Up to the last :n sessions that include this exercise.', ['n' => 30]) }}</p>
        </div>
        <a href="{{ route('progress.index') }}" class="inline-flex shrink-0 items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60 lg:self-auto">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            {{ __('Back') }}
        </a>
    </div>
@endsection

@section('content')
    @php
        $exerciseChartConfig = [
            'type' => 'line',
            'data' => $exerciseChart,
            'options' => [
                'responsive' => true,
                'interaction' => ['mode' => 'index', 'intersect' => false],
                'scales' => [
                    'y' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'left',
                        'title' => ['display' => true, 'text' => __('Weight')],
                    ],
                    'y1' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'right',
                        'title' => ['display' => true, 'text' => __('Reps')],
                        'grid' => ['drawOnChartArea' => false],
                    ],
                ],
            ],
        ];
    @endphp

    <div class="mx-auto max-w-5xl space-y-10">
        @if ($sessions->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 ring-1 ring-slate-200/80">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                <p class="mt-5 text-sm font-medium text-slate-800">{{ __('No sessions logged for this exercise yet.') }}</p>
                <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-slate-500">{{ __('Log a workout that includes this exercise to see your chart here.') }}</p>
            </div>
        @else
            <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                <div class="h-1 bg-gradient-to-r from-violet-500/70 via-emerald-500/80 to-teal-500/70"></div>
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                    <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Max weight & max reps per session') }}</h2>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="h-80 w-full min-h-[16rem]">
                        <canvas id="chartExercise" class="max-h-full w-full" aria-label="{{ __('Exercise progress chart') }}"></canvas>
                    </div>
                </div>
            </section>
        @endif

        @if ($sessions->isNotEmpty())
            <section>
                <div class="mb-4">
                    <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Sessions in chart') }}</h2>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('Jump to the full session log.') }}</p>
                </div>
                <ul class="grid gap-2 sm:grid-cols-2">
                    @foreach ($sessions as $session)
                        <li>
                            <a href="{{ route('workout-sessions.show', $session) }}" class="flex items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-sm shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/25">
                                <span class="font-medium text-slate-900">{{ $session->date->toFormattedDateString() }}</span>
                                <span class="flex items-center gap-2 text-xs text-slate-500">
                                    @if ($session->duration_minutes !== null)
                                        <span>{{ $session->duration_minutes }} {{ __('min') }}</span>
                                    @endif
                                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
@endsection

@if ($sessions->isNotEmpty())
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('chartExercise');
                if (el && typeof Chart !== 'undefined') {
                    new Chart(el, @json($exerciseChartConfig));
                }
            });
        </script>
    @endpush
@endif
