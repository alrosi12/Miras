<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('New measurement') }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8 text-sm text-slate-600">
        <p>{{ __('Scaffold: POST body-measurements.store') }}</p>
        <a href="{{ route('body-measurements.index') }}" class="text-emerald-700 font-semibold">{{ __('Back') }}</a>
    </div>
</x-app-layout>
