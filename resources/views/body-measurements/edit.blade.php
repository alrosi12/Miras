<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('Edit measurement') }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('body-measurements.show', $bodyMeasurement) }}" class="text-sm text-emerald-700 font-semibold">{{ __('View') }}</a>
    </div>
</x-app-layout>
