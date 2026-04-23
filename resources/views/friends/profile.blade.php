@extends('layouts.app')

@section('title', $friend->name)

@section('header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-slate-900">{{ $friend->name }}</h1>
        <a href="{{ route('friends.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">{{ __('Back') }}</a>
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <section class="grid gap-4 sm:grid-cols-2">
            <x-stat-card :label="__('Total sessions')" :value="$friend->workout_sessions_count" />
            <x-stat-card :label="__('Total minutes logged')" :value="$totalMinutes" accent="sky" />
        </section>

        <section>
            <h2 class="mb-3 font-semibold text-slate-900">{{ __('Recent sessions') }}</h2>
            <ul class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200/80 bg-white text-sm shadow-sm">
                @forelse ($recentSessions as $s)
                    <li class="flex justify-between gap-2 px-4 py-3">
                        <span>{{ $s->date->toFormattedDateString() }} @if ($s->workoutPlan) — {{ $s->workoutPlan->name }} @endif</span>
                        @if ($s->duration_minutes !== null)
                            <span class="font-medium text-emerald-700">{{ $s->duration_minutes }} {{ __('min') }}</span>
                        @endif
                    </li>
                @empty
                    <li class="p-4 text-slate-600">{{ __('No sessions yet.') }}</li>
                @endforelse
            </ul>
        </section>

        <section>
            <h2 class="mb-3 font-semibold text-slate-900">{{ __('Public workout plans') }}</h2>
            <ul class="space-y-2 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
                @forelse ($publicPlans as $plan)
                    <li>
                        <a href="{{ route('workout-plans.show', $plan) }}" class="font-medium text-emerald-700 hover:underline">{{ $plan->name }}</a>
                    </li>
                @empty
                    <li class="text-sm text-slate-600">{{ __('No public plans.') }}</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
