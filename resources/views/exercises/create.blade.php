@extends('layouts.app')

@section('title', __('New exercise'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('New exercise') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-2xl" x-data="{ preview: null }">
        <form method="post" action="{{ route('exercises.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="name" class="text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="description" class="text-sm font-medium text-slate-700">{{ __('Description') }}</label>
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="muscle_group" class="text-sm font-medium text-slate-700">{{ __('Muscle group') }}</label>
                    <select id="muscle_group" name="muscle_group" required class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach (\App\Enums\MuscleGroup::cases() as $g)
                            <option value="{{ $g->value }}" @selected(old('muscle_group') === $g->value)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('muscle_group')" />
                </div>
                <div>
                    <label for="type" class="text-sm font-medium text-slate-700">{{ __('Type') }}</label>
                    <select id="type" name="type" required class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach (\App\Enums\ExerciseType::cases() as $t)
                            <option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                </div>
            </div>

            <div>
                <label for="is_public" class="text-sm font-medium text-slate-700">{{ __('Visible to others (catalog)') }}</label>
                <select id="is_public" name="is_public" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="1" @selected(old('is_public', '1') == '1')>{{ __('Yes') }}</option>
                    <option value="0" @selected(old('is_public') === '0')>{{ __('No') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('is_public')" />
            </div>

            <div>
                <label for="image" class="text-sm font-medium text-slate-700">{{ __('Image (JPEG/PNG, max 2MB)') }}</label>
                <input
                    id="image"
                    name="image"
                    type="file"
                    accept="image/jpeg,image/png,.jpg,.jpeg,.png"
                    class="mt-1 block w-full text-sm text-slate-600"
                    @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                />
                <template x-if="preview">
                    <img :src="preview" alt="" class="mt-3 h-40 w-full max-w-xs rounded-lg border border-slate-200 object-cover" />
                </template>
                <x-input-error class="mt-2" :messages="$errors->get('image')" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('exercises.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">{{ __('Cancel') }}</a>
                <button type="submit" class="inline-flex rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
@endsection
