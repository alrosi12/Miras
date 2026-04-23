@extends('layouts.app')

@section('title', __('Edit session'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('Edit session') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-xl space-y-4 text-slate-700">
        <form method="POST" action="{{ route('workout-sessions.update', $workoutSession) }}" class="space-y-4 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <div>
                <x-input-label for="date" :value="__('Date')" />
                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', $workoutSession->date->toDateString())" required />
                <x-input-error class="mt-2" :messages="$errors->get('date')" />
            </div>
            <div>
                <x-input-label for="workout_plan_id" :value="__('Workout plan (optional)')" />
                <select id="workout_plan_id" name="workout_plan_id" class="mt-1 block w-full border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('workout_plan_id', $workoutSession->workout_plan_id) == $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('workout_plan_id')" />
            </div>
            <div>
                <x-input-label for="duration_minutes" :value="__('Duration (minutes)')" />
                <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" name="duration_minutes" min="0" max="1440" :value="old('duration_minutes', $workoutSession->duration_minutes)" />
                <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
            </div>
            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-slate-300 rounded-md shadow-sm text-sm">{{ old('notes', $workoutSession->notes) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
            </div>
            <div class="flex gap-3">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('workout-sessions.show', $workoutSession) }}" class="text-sm font-semibold text-slate-600 py-2">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

