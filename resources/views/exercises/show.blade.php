@extends('layouts.app')

@section('title', $exercise->name)

@section('header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ $exercise->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $exercise->muscle_group->name }} · {{ $exercise->type->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('update', $exercise)
                <a href="{{ route('exercises.edit', $exercise) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Edit') }}</a>
            @endcan
            <a href="{{ route('exercises.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('Back') }}</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="space-y-6">
            @if ($exercise->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($exercise->image) }}" alt="" class="w-full max-w-md rounded-2xl border border-slate-200 object-cover shadow-sm" />
            @endif
            @if ($exercise->description)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 text-sm text-slate-700 shadow-sm">
                    {!! nl2br(e($exercise->description)) !!}
                </div>
            @endif
        </div>

        <x-chart-card :title="__('Weight progress (recent sessions)')" chart-id="exerciseWeightChart" />
    </div>

    <section class="mt-10 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="font-semibold text-slate-900">{{ __('Session usage history') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Last 50 logged sets') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Date') }}</th>
                        <th class="px-4 py-3">{{ __('Set') }}</th>
                        <th class="px-4 py-3">{{ __('Reps') }}</th>
                        <th class="px-4 py-3">{{ __('Weight') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($usageHistory as $set)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-700">
                                @if ($set->workoutSession)
                                    <a href="{{ route('workout-sessions.show', $set->workoutSession) }}" class="font-medium text-emerald-700 hover:underline">{{ $set->workoutSession->date->toDateString() }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $set->set_number }}</td>
                            <td class="px-4 py-3">{{ $set->reps ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $set->weight ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-600">{{ __('Not used in sessions yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payload = @json($weightChart);
            const ctx = document.getElementById('exerciseWeightChart');
            if (!ctx || !payload.labels.length) return;
            new Chart(ctx, {
                type: 'line',
                data: payload,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
            });
        });
    </script>
@endpush
