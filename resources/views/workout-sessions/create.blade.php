<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('New session') }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8 text-sm text-slate-600">
        <p>{{ __('Scaffold: form POST to workout-sessions.store') }}</p>
        <a href="{{ route('workout-sessions.index') }}" class="text-emerald-700 font-semibold">{{ __('Back') }}</a>
    </div>
</x-app-layout>
