@extends('layouts.app')

@section('title', __('Exercises'))

@section('header')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold text-slate-900">{{ __('Exercises') }}</h1>
        <a href="{{ route('exercises.create') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">{{ __('Add exercise') }}</a>
    </div>
@endsection

@section('content')
    <form method="get" action="{{ route('exercises.index') }}" class="mb-8 flex flex-col gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-[12rem] flex-1">
            <label for="q" class="text-xs font-semibold uppercase text-slate-500">{{ __('Search') }}</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Name…') }}" />
        </div>
        <div>
            <label for="muscle_group" class="text-xs font-semibold uppercase text-slate-500">{{ __('Muscle group') }}</label>
            <select id="muscle_group" name="muscle_group" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">{{ __('All') }}</option>
                @foreach (\App\Enums\MuscleGroup::cases() as $g)
                    <option value="{{ $g->value }}" @selected(request('muscle_group') === $g->value)>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="type" class="text-xs font-semibold uppercase text-slate-500">{{ __('Type') }}</label>
            <select id="type" name="type" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">{{ __('All') }}</option>
                @foreach (\App\Enums\ExerciseType::cases() as $t)
                    <option value="{{ $t->value }}" @selected(request('type') === $t->value)>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Filter') }}</button>
        <a href="{{ route('exercises.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Reset') }}</a>
    </form>

    @if ($exercises->isEmpty())
        <p class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center text-slate-600">{{ __('No exercises match your filters.') }}</p>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($exercises as $exercise)
                <x-exercise-card :exercise="$exercise" />
            @endforeach
        </div>
        <div class="mt-8">
            {{ $exercises->links() }}
        </div>
    @endif
@endsection
