@extends('layouts.admin')

@section('title', $user->name)

@section('content')
        <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h1>
            <div class="flex gap-3 text-sm">
                <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-emerald-700 hover:underline">{{ __('Edit') }}</a>
                <a href="{{ route('admin.users.index') }}" class="text-slate-600 hover:underline">{{ __('Back to list') }}</a>
            </div>
        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Email') }}</p>
                <p class="mt-1 text-slate-900">{{ $user->email }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Sessions (total)') }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($sessionsCount) }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
                <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Workout plans (total)') }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($plansCount) }}</p>
            </div>
        </div>

        <div class="mb-2 flex flex-wrap items-center gap-3">
            @if ($user->is_admin)
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-900">{{ __('Administrator') }}</span>
            @endif
            <form method="post" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline">
                @csrf
                @method('PATCH')
                <x-secondary-button type="submit">{{ $user->is_admin ? __('Toggle off admin') : __('Toggle on admin') }}</x-secondary-button>
            </form>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-900">{{ __('Recent sessions') }} ({{ __('up to 30') }})</h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($user->workoutSessions as $session)
                        <li class="px-6 py-4">
                            <a href="{{ route('workout-sessions.show', $session) }}" class="block hover:bg-slate-50/50 -mx-2 px-2 py-1 rounded">
                                <p class="font-medium text-slate-900">{{ $session->date->toFormattedDateString() }}</p>
                                @if ($session->workoutPlan)
                                    <p class="text-sm text-slate-600">{{ $session->workoutPlan->name }}</p>
                                @endif
                                @if ($session->sessionSets->isNotEmpty())
                                    <p class="mt-1 text-xs text-slate-500 line-clamp-2">
                                        {{ $session->sessionSets->pluck('exercise.name')->filter()->unique()->implode(', ') }}
                                    </p>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-sm text-slate-600">{{ __('No sessions.') }}</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-900">{{ __('Workout plans') }} ({{ __('up to 20') }})</h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($user->workoutPlans as $plan)
                        <li class="px-6 py-4">
                            <a href="{{ route('workout-plans.show', $plan) }}" class="font-medium text-emerald-800 hover:underline">{{ $plan->name }}</a>
                            <p class="text-xs text-slate-500 mt-1">{{ __('Updated') }}: {{ $plan->updated_at?->toFormattedDateString() }}</p>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-sm text-slate-600">{{ __('No plans.') }}</li>
                    @endforelse
                </ul>
            </section>
        </div>
@endsection
