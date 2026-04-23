@extends('layouts.app')

@section('title', __('Body measurements'))

@section('header')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl space-y-1.5">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ __('Body measurements') }}</h1>
            <p class="text-sm leading-relaxed text-slate-500">{{ __('Track weight, body fat, and circumferences over time.') }}</p>
        </div>
        <a href="{{ route('progress.index') }}" class="inline-flex shrink-0 items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60 sm:self-auto">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            {{ __('Back') }}
        </a>
    </div>
@endsection

@section('content')
    @php
        $measurementsChartConfig = [
            'type' => 'line',
            'data' => $measurementsChart,
            'options' => [
                'responsive' => true,
                'interaction' => ['mode' => 'index', 'intersect' => false],
                'plugins' => ['legend' => ['position' => 'bottom']],
                'scales' => [
                    'y' => ['beginAtZero' => false],
                ],
            ],
        ];
    @endphp

    <div class="mx-auto max-w-6xl space-y-10">
        <section class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03]">
            <div class="h-1 bg-gradient-to-r from-sky-500/80 via-emerald-500/80 to-teal-500/70"></div>
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
                <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Trend (Chart.js)') }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ __('All logged series on one timeline.') }}</p>
            </div>
            <div class="p-5 sm:p-6">
                <div class="h-96 w-full min-h-[16rem]">
                    <canvas id="chartMeasurements" class="max-h-full w-full" aria-label="{{ __('Body measurements trend') }}"></canvas>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h2 class="text-base font-semibold tracking-tight text-slate-900">{{ __('Full history') }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('Every saved measurement, newest first.') }}</p>
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
                            @forelse ($measurements as $m)
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
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('chartMeasurements');
            if (el && typeof Chart !== 'undefined') {
                new Chart(el, @json($measurementsChartConfig));
            }
        });
    </script>
@endpush
