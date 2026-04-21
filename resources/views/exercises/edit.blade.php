<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800">{{ __('Edit exercise') }}</h2>
    </x-slot>

    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8">
        <p class="text-slate-700 font-medium">{{ $exercise->name }}</p>
        <a href="{{ route('exercises.show', $exercise) }}" class="text-sm text-emerald-700 font-semibold mt-4 inline-block">{{ __('View') }}</a>
    </div>
</x-app-layout>
