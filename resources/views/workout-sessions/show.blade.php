@extends('layouts.app')

@section('title', __('Session').' — '.$workoutSession->date->toDateString())

@section('header')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ __('Session') }} — {{ $workoutSession->date->translatedFormat('l j F Y') }}</h1>
            <div class="mt-2 flex flex-wrap gap-3 text-sm text-slate-600">
                @if ($workoutSession->workoutPlan)
                    <span>{{ __('Plan') }}: <strong class="text-slate-900">{{ $workoutSession->workoutPlan->name }}</strong></span>
                @endif
                @if ($workoutSession->duration_minutes !== null)
                    <span>{{ __('Duration') }}: <strong class="text-slate-900">{{ $workoutSession->duration_minutes }} {{ __('min') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('workout-sessions.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('List') }}</a>
            @can('update', $workoutSession)
                <a href="{{ route('workout-sessions.edit', $workoutSession) }}" class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Edit') }}</a>
            @endcan
            @can('duplicate', $workoutSession)
                <form method="post" action="{{ route('workout-sessions.duplicate', $workoutSession) }}">@csrf<button type="submit" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">{{ __('Duplicate') }}</button></form>
            @endcan
            @can('delete', $workoutSession)
                <form method="post" action="{{ route('workout-sessions.destroy', $workoutSession) }}" onsubmit="return confirm(@json(__('Delete session?')));">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-xl border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50">{{ __('Delete') }}</button>
                </form>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    @if ($workoutSession->notes)
        <p class="mb-6 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-700">{{ $workoutSession->notes }}</p>
    @endif

    <div class="space-y-6" x-data="sessionSetsLive(@js($sessionSetsPayload), @json($sessionSetPatchUrls), @json($sessionSetDestroyUrls))">
        <div class="flex min-h-6 flex-wrap items-center gap-3">
            <p x-show="message" x-transition class="text-sm font-medium text-emerald-700" x-text="message"></p>
            <p x-show="error" x-transition class="text-sm font-medium text-rose-700" x-text="error"></p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-3">
                <h2 class="font-semibold text-slate-900">{{ __('Sets') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Exercise') }}</th>
                            <th class="px-4 py-3">{{ __('Set') }}</th>
                            <th class="px-4 py-3">{{ __('Reps') }}</th>
                            <th class="px-4 py-3">{{ __('Weight') }}</th>
                            <th class="px-4 py-3">{{ __('Done') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="row in rows" :key="row.id">
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-2 font-medium text-slate-800" x-text="row.exercise_name"></td>
                                <td class="px-4 py-2 text-slate-600" x-text="row.set_number"></td>
                                <td class="px-4 py-2">
                                    <input type="number" min="0" class="w-24 rounded-lg border-slate-300 text-sm" x-model.number="row.reps" @change.debounce.500ms="patchRow(row)" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" min="0" class="w-28 rounded-lg border-slate-300 text-sm" x-model.number="row.weight" @change.debounce.500ms="patchRow(row)" />
                                </td>
                                <td class="px-4 py-2">
                                    <input type="checkbox" class="rounded border-slate-300 text-emerald-600" x-model="row.is_completed" @change="patchRow(row)" />
                                </td>
                                <td class="px-4 py-2 text-end">
                                    <button type="button" class="text-xs font-semibold text-rose-600 hover:underline" @click="removeRow(row)">{{ __('Remove') }}</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <template x-if="rows.length === 0">
                <p class="p-6 text-sm text-slate-600">{{ __('No sets yet.') }}</p>
            </template>
        </div>
    </div>

    @can('manageSessionSets', $workoutSession)
        <section class="mt-10 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">{{ __('Add set') }}</h3>
            <form method="post" action="{{ route('workout-sessions.session-sets.store', $workoutSession) }}" class="mt-4 flex flex-wrap items-end gap-4">
                @csrf
                <div>
                    <label for="exercise_id" class="text-xs font-semibold text-slate-500">{{ __('Exercise') }}</label>
                    <select id="exercise_id" name="exercise_id" required class="mt-1 block w-56 rounded-lg border-slate-300 text-sm">
                        @foreach ($exercises as $ex)
                            <option value="{{ $ex->id }}">{{ $ex->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="set_number" class="text-xs font-semibold text-slate-500">{{ __('Set #') }}</label>
                    <input id="set_number" type="number" name="set_number" min="1" value="{{ $nextSetNumber }}" class="mt-1 w-24 rounded-lg border-slate-300 text-sm" />
                </div>
                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">{{ __('Add') }}</button>
            </form>
        </section>
    @endcan

    @can('finish', $workoutSession)
        <section class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-6 shadow-sm ring-1 ring-emerald-100">
            <h3 class="font-semibold text-emerald-900">{{ __('Finish session') }}</h3>
            <p class="mt-1 text-xs text-emerald-800/90">{{ __('Duration will be set from session start to now.') }}</p>
            <form method="post" action="{{ route('workout-sessions.finish', $workoutSession) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="flex-1">
                    <label for="notes" class="text-xs font-semibold text-emerald-900">{{ __('Notes (optional)') }}</label>
                    <textarea id="notes" name="notes" rows="2" class="mt-1 w-full max-w-md rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes', $workoutSession->notes) }}</textarea>
                </div>
                <button type="submit" class="inline-flex rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-emerald-700">{{ __('Finish') }}</button>
            </form>
        </section>
    @endcan
@endsection
