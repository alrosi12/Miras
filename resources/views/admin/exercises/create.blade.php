@extends('layouts.admin')

@section('title', __('New global exercise'))

@section('content')
    <h1 class="mb-2 text-2xl font-bold text-slate-900">{{ __('New global exercise') }}</h1>
        <p class="mb-6 text-sm text-slate-600">{{ __('This exercise will be saved as public catalog (no owner).') }}</p>

        <form method="post" action="{{ route('admin.exercises.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="muscle_group" :value="__('Muscle group')" />
                    <select id="muscle_group" name="muscle_group" required class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach (\App\Enums\MuscleGroup::cases() as $g)
                            <option value="{{ $g->value }}" @selected(old('muscle_group') === $g->value)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('muscle_group')" />
                </div>
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" required class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach (\App\Enums\ExerciseType::cases() as $t)
                            <option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                </div>
            </div>

            <div>
                <x-input-label for="image" :value="__('Image (JPEG/PNG, max 2MB)')" />
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-slate-600" />
                <x-input-error class="mt-2" :messages="$errors->get('image')" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.exercises.index') }}" class="py-2 text-sm text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
@endsection
