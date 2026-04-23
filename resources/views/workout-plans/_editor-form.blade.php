@props([
    'workoutPlan' => null,
    'initialDaysPayload',
    'exercises',
])

@php
    $exerciseOptions = $exercises->map(fn ($e) => ['id' => $e->id, 'name' => $e->name])->values()->all();
    $daysForAlpine = old('days', $initialDaysPayload);
@endphp

<form
    method="POST"
    action="{{ $workoutPlan ? route('workout-plans.update', $workoutPlan) : route('workout-plans.store') }}"
    x-data="workoutPlanDays(@js($daysForAlpine), @js($exerciseOptions))"
    @submit="syncOrders()"
    class="space-y-6 max-w-3xl"
>
    @csrf
    @if ($workoutPlan)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', optional($workoutPlan)->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm">{{ old('description', optional($workoutPlan)->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    {{-- خاص/عام: قيمة 0 عند عدم التحديد حتى يصل false للتحقق --}}
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_public" value="0" />
        <input
            id="is_public"
            type="checkbox"
            name="is_public"
            value="1"
            class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
            @checked(old('is_public', optional($workoutPlan)->is_public ?? false))
        />
        <x-input-label for="is_public" :value="__('Public plan (visible in catalog)')" class="!mb-0" />
    </div>
    <x-input-error class="mt-2" :messages="$errors->get('is_public')" />

    <div class="border border-slate-200 rounded-lg p-4 space-y-4 bg-slate-50/50">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="font-semibold text-slate-800">{{ __('Days & exercises') }}</h3>
            <div class="flex flex-1 flex-col gap-2 sm:max-w-xs sm:flex-row sm:items-center">
                <label class="text-xs font-medium text-slate-600">{{ __('Filter exercises') }}</label>
                <input type="search" x-model="exerciseFilter" class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Search by name…') }}" />
            </div>
            <button type="button" @click="addDay()" class="text-sm font-semibold text-emerald-700">{{ __('Add day') }}</button>
        </div>

        <template x-for="(day, dIdx) in days" :key="dIdx">
            <div class="border border-slate-200 rounded-md p-4 bg-white space-y-3">
                <div class="flex justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500" x-text="'{{ __('Day') }} ' + (dIdx + 1)"></span>
                    <button type="button" class="text-xs text-red-600 font-semibold" @click="removeDay(dIdx)">{{ __('Remove day') }}</button>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600" :for="'day_name_'+dIdx">{{ __('Day name') }}</label>
                    <input
                        type="text"
                        class="mt-1 block w-full border-slate-300 rounded-md shadow-sm text-sm"
                        :id="'day_name_'+dIdx"
                        :name="`days[${dIdx}][day_name]`"
                        x-model="day.day_name"
                        required
                    />
                </div>
                <input type="hidden" :name="`days[${dIdx}][order]`" :value="day.order" />

                <div class="space-y-2 pl-2 border-l-2 border-emerald-200">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-600">{{ __('Exercises') }}</span>
                        <button type="button" class="text-xs font-semibold text-emerald-700" @click="addExercise(dIdx)">{{ __('Add exercise') }}</button>
                    </div>
                    <template x-for="(ex, eIdx) in day.exercises" :key="eIdx">
                        <div
                            class="flex flex-wrap gap-2 items-end border border-slate-100 rounded p-2 transition hover:border-emerald-200"
                            draggable="true"
                            @dragstart="dragStart(dIdx, eIdx, $event)"
                            @dragover.prevent="$event.dataTransfer.dropEffect = 'move'"
                            @drop.prevent="dropExercise(dIdx, eIdx)"
                        >
                            <span class="mb-1 cursor-grab select-none text-slate-400" title="{{ __('Drag to reorder') }}">⋮⋮</span>
                            <div class="min-w-[12rem] flex-1">
                                <label class="text-xs text-slate-500">{{ __('Exercise') }}</label>
                                <select
                                    class="mt-1 block w-full border-slate-300 rounded-md shadow-sm text-sm"
                                    :name="`days[${dIdx}][exercises][${eIdx}][exercise_id]`"
                                    x-model="ex.exercise_id"
                                    required
                                >
                                    <option value="">{{ __('Choose') }}</option>
                                    <template x-for="opt in filteredExerciseOptions()" :key="opt.id">
                                        <option :value="String(opt.id)" x-text="opt.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="w-20">
                                <label class="text-xs text-slate-500">{{ __('Sets') }}</label>
                                <input type="number" min="1" class="mt-1 block w-full border-slate-300 rounded-md text-sm" :name="`days[${dIdx}][exercises][${eIdx}][sets]`" x-model.number="ex.sets" />
                            </div>
                            <div class="w-20">
                                <label class="text-xs text-slate-500">{{ __('Reps') }}</label>
                                <input type="number" min="0" class="mt-1 block w-full border-slate-300 rounded-md text-sm" :name="`days[${dIdx}][exercises][${eIdx}][reps]`" x-model.number="ex.reps" />
                            </div>
                            <div class="w-24">
                                <label class="text-xs text-slate-500">{{ __('Rest (s)') }}</label>
                                <input type="number" min="0" class="mt-1 block w-full border-slate-300 rounded-md text-sm" :name="`days[${dIdx}][exercises][${eIdx}][rest_seconds]`" x-model.number="ex.rest_seconds" />
                            </div>
                            <input type="hidden" :name="`days[${dIdx}][exercises][${eIdx}][order]`" :value="ex.order" />
                            <button type="button" class="text-xs text-red-600 mb-1" @click="removeExercise(dIdx, eIdx)">{{ __('Remove') }}</button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    @if ($errors->has('days') || $errors->has('days.*'))
        <p class="text-sm text-red-600">{{ __('Please fix the days / exercises fields.') }}</p>
    @endif

    <div class="flex gap-3">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
        <a href="{{ $workoutPlan ? route('workout-plans.show', $workoutPlan) : route('workout-plans.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-slate-600 font-semibold">{{ __('Cancel') }}</a>
    </div>
</form>
