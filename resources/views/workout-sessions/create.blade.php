@extends('layouts.app')

@section('title', __('New session'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('New session') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-xl space-y-4 text-slate-700">

        @php
            $defaultPlanId = $suggestedRoutine ? $suggestedRoutine['plan']->id : null;
        @endphp

        @if ($suggestedRoutine)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-4 text-sm">
                <p class="font-semibold text-emerald-900">{{ __('Today\'s routine match') }}</p>
                <p class="mt-1 text-slate-700">
                    {{ $suggestedRoutine['plan']->name }} — <span class="font-medium">{{ $suggestedRoutine['day']->day_name }}</span>
                </p>
                <p class="mt-2 text-xs text-slate-600">{{ __('Choosing this plan below will pre-fill empty sets for the session date that matches this weekday.') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('workout-sessions.store') }}" class="space-y-4 bg-white shadow-sm sm:rounded-lg p-6">
            @csrf
            <div>
                <x-input-label for="date" :value="__('Date')" />
                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', now()->toDateString())" required />
                <x-input-error class="mt-2" :messages="$errors->get('date')" />
            </div>
            <div>
                <x-input-label for="workout_plan_id" :value="__('Workout plan (optional)')" />
                <select id="workout_plan_id" name="workout_plan_id" class="mt-1 block w-full border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('workout_plan_id', $defaultPlanId) == $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('workout_plan_id')" />
            </div>
            <div>
                <x-input-label for="duration_minutes" :value="__('Duration (minutes, optional)')" />
                <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" min="0" max="1440" :value="old('duration_minutes')" />
                <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
            </div>
            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-slate-300 rounded-md shadow-sm text-sm">{{ old('notes') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
            </div>
            <div class="flex gap-3">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('workout-sessions.index') }}" class="text-sm font-semibold text-slate-600 py-2">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

