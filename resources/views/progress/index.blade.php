@extends('layouts.app')

@section('title', __('Progress'))

@section('header')
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-2xl space-y-1.5">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ __('Progress') }}</h1>
            <p class="text-sm leading-relaxed text-slate-500">{{ __('Personal records, body measurements, and month-over-month training volume.') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 sm:gap-3">
            <a href="{{ route('progress.measurements') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-800 ring-1 ring-emerald-100 transition duration-200 hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30">
                <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v1.5m-18 0A2.25 2.25 0 005.25 9h13.5A2.25 2.25 0 0021 7.5m-18 0v8.25A2.25 2.25 0 005.25 18h13.5A2.25 2.25 0 0021 15.75V7.5"/></svg>
                {{ __('Measurements') }}
            </a>
            <a href="{{ route('progress.monthly') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60">
                <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                {{ __('Monthly report') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $monthCompareConfig = [
            'type' => 'bar',
            'data' => $monthCompareChart,
            'options' => [
                'responsive' => true,
                'plugins' => ['legend' => ['position' => 'bottom']],
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];
    @endphp

    <div class="mx-auto max-w-6xl space-y-10">
        <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
            <div class="h-1 bg-gradient-to-r from-emerald-500/90 via-emerald-600/80 to-teal-500/70"></div>
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('This month vs last month') }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ __('Compare session counts and total minutes between the current and previous month.') }}</p>
            </div>
            <div class="p-5 sm:p-6">
                <div class="mx-auto h-72 max-w-3xl">
                    <canvas id="chartMonthCompare" class="max-h-full w-full" aria-label="{{ __('This month vs last month') }}"></canvas>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Personal records (by exercise)') }}</h2>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('Best logged weight and reps per exercise.') }}</p>
                </div>
            </div>
            <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/90 text-left">
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Exercise') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Max weight') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Max reps') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500"><span class="sr-only">{{ __('Actions') }}</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($personalRecords as $row)
                                <tr class="transition duration-150 hover:bg-slate-50/80">
                                    <td class="px-4 py-3.5 font-medium text-slate-900">{{ $row->exercise->name }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $row->max_weight !== null ? $row->max_weight : '—' }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $row->max_reps !== null ? $row->max_reps : '—' }}</td>
                                    <td class="px-4 py-3.5 text-end">
                                        <a href="{{ route('progress.exercise', $row->exercise) }}" class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition duration-200 hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-1">{{ __('Chart') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-sm leading-relaxed text-slate-600">{{ __('Log sessions with sets to see personal records.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Latest body measurements') }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('Most recent entries (up to eight).') }}</p>
            </div>
            <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/90 text-left">
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Date') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Weight') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Body fat') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Chest') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Waist') }}</th>
                                <th class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Arms') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($latestMeasurements as $m)
                                <tr class="transition duration-150 hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-4 py-3.5 font-medium text-slate-800">{{ $m->date->toDateString() }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $m->weight ?? '—' }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $m->body_fat ?? '—' }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $m->chest ?? '—' }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $m->waist ?? '—' }}</td>
                                    <td class="px-4 py-3.5 tabular-nums text-slate-700">{{ $m->arms ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-sm leading-relaxed text-slate-600">{{ __('No measurements yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($latestMeasurements->isNotEmpty())
                <p class="mt-3 text-end text-xs">
                    <a href="{{ route('progress.measurements') }}" class="font-semibold text-emerald-700 transition duration-200 hover:text-emerald-800">{{ __('View full history') }} →</a>
                </p>
            @endif
        </section>

        <section class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 px-5 py-5 sm:px-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('AJAX compare (example)') }}</p>
            <code class="mt-3 block break-all rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-[11px] leading-relaxed text-slate-700 shadow-inner">GET {{ url('/progress/compare') }}?month1=2026-03&amp;month2=2026-04</code>
            <p class="mt-3 text-xs leading-relaxed text-slate-600">{{ __('Returns JSON with chart.labels, chart.datasets, and month stats.') }}</p>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartMonthCompare');
            if (ctx && typeof Chart !== 'undefined') {
                new Chart(ctx, @json($monthCompareConfig));
            }
        });
    </script>
@endpush
