<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800">{{ __('New exercise') }}</h2>
    </x-slot>

    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8">
        <p class="text-sm text-slate-600 mb-4">{{ __('Build your form here; validation is handled by StoreExerciseRequest.') }}</p>
        <a href="{{ route('exercises.index') }}" class="text-sm text-emerald-700 font-semibold">{{ __('Back to list') }}</a>
    </div>
</x-app-layout>
