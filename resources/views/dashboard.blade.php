<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Training dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white p-5 rounded-xl shadow-sm ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('Exercises') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['exercises_count'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('Your plans') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['plans_count'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('Sessions this month') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['sessions_this_month'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm ring-1 ring-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('Latest weight') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ optional($stats['latest_measurement'])->weight ?? '—' }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-slate-100">
                <div class="p-6 sm:p-8">
                    <p class="text-slate-700 font-medium">{{ __('Quick links') }}</p>
                    <div class="mt-4 flex flex-wrap gap-4 text-sm font-semibold">
                        <a href="{{ route('exercises.index') }}" class="text-emerald-700">{{ __('Exercises') }}</a>
                        <a href="{{ route('workout-plans.index') }}" class="text-emerald-700">{{ __('Plans') }}</a>
                        <a href="{{ route('workout-sessions.index') }}" class="text-emerald-700">{{ __('Sessions') }}</a>
                        <a href="{{ route('body-measurements.index') }}" class="text-emerald-700">{{ __('Measurements') }}</a>
                        <a href="{{ route('progress.index') }}" class="text-emerald-700">{{ __('Progress') }}</a>
                        <a href="{{ route('friendships.index') }}" class="text-emerald-700">{{ __('Friends') }}</a>
                    </div>
                    @if ($stats['last_session'])
                        <p class="mt-6 text-sm text-slate-600">{{ __('Last session') }}: {{ $stats['last_session']->date->toFormattedDateString() }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
