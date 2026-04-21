<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('Add set') }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8 text-sm text-slate-600">
        <p>{{ __('Scaffold: POST to workout-sessions.session-sets.store') }}</p>
        <a href="{{ route('workout-sessions.session-sets.index', $workoutSession) }}" class="text-emerald-700 font-semibold">{{ __('Back') }}</a>
    </div>
</x-app-layout>
