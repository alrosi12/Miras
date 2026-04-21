<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('Edit plan') }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8">
        <p class="text-slate-700 font-medium">{{ $workoutPlan->name }}</p>
        <a href="{{ route('workout-plans.show', $workoutPlan) }}" class="text-sm text-emerald-700 font-semibold mt-4 inline-block">{{ __('View') }}</a>
    </div>
</x-app-layout>
