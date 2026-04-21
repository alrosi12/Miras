<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ $bodyMeasurement->date->toFormattedDateString() }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8 text-sm space-y-2">
        @if (session('status'))<p class="text-emerald-700">{{ session('status') }}</p>@endif
        <p>{{ __('Weight') }}: {{ $bodyMeasurement->weight ?? '—' }}</p>
        <a href="{{ route('body-measurements.edit', $bodyMeasurement) }}" class="text-emerald-700 font-semibold">{{ __('Edit') }}</a>
    </div>
</x-app-layout>
