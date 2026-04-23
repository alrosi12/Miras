@extends('layouts.app')

@section('title', $workoutPlan->name)

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ $workoutPlan->name }}</h1>
            @if ($workoutPlan->description)
                <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $workoutPlan->description }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                @if ($workoutPlan->is_public)
                    <span class="rounded-full bg-emerald-50 px-2 py-1 font-medium text-emerald-800 ring-1 ring-emerald-100">{{ __('Public') }}</span>
                @else
                    <span class="rounded-full bg-slate-100 px-2 py-1 font-medium text-slate-700 ring-1 ring-slate-200">{{ __('Private') }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('update', $workoutPlan)
                <form method="post" action="{{ route('workout-plans.toggle-public', $workoutPlan) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">{{ __('Toggle visibility') }}</button>
                </form>
            @endcan
            <a href="{{ route('workout-plans.edit', $workoutPlan) }}" class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Edit') }}</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 shadow-sm ring-1 ring-emerald-100">
        <h2 class="text-sm font-semibold text-emerald-900">{{ __('Start session from this routine') }}</h2>
        <p class="mt-1 text-xs text-emerald-800/90">{{ __('Creates a session for today with sets from the matching plan day.') }}</p>
        <form method="post" action="{{ route('workout-sessions.store') }}" class="mt-4 flex flex-wrap items-end gap-3">
            @csrf
            <input type="hidden" name="workout_plan_id" value="{{ $workoutPlan->id }}" />
            <input type="hidden" name="date" value="{{ now()->toDateString() }}" />
            <button type="submit" class="inline-flex rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-emerald-700">{{ __('Start session') }}</button>
            <a href="{{ route('workout-sessions.create') }}?workout_plan_id={{ $workoutPlan->id }}" class="text-sm font-semibold text-emerald-900 underline">{{ __('Advanced options') }}</a>
        </form>
    </div>

    @if (session('share_url'))
        <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
            <p class="font-semibold">{{ __('Share URL') }}</p>
            <p class="mt-2 break-all font-mono text-xs">{{ session('share_url') }}</p>
        </div>
    @endif

    <div class="space-y-6">
        @foreach ($workoutPlan->workoutPlanDays as $day)
            <section class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-5 py-3">
                    <h3 class="font-semibold text-slate-900">{{ $day->day_name }}</h3>
                    <span class="text-xs text-slate-500">{{ __('Order') }}: {{ $day->order }}</span>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($day->planDayExercises as $row)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 text-sm">
                            <span class="font-medium text-slate-800">{{ $row->exercise->name }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $row->sets }}×{{ $row->reps }} · {{ $row->rest_seconds }}s</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
@endsection
