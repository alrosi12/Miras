<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Workout sessions') }}</h2>
            <a href="{{ route('workout-sessions.create') }}" class="text-sm font-semibold text-emerald-700">{{ __('Log session') }}</a>
        </div>
    </x-slot>
    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg divide-y">
            @forelse ($sessions as $session)
                <a href="{{ route('workout-sessions.show', $session) }}" class="block px-4 py-3 hover:bg-slate-50">
                    <span class="font-medium">{{ $session->date->toFormattedDateString() }}</span>
                    @if ($session->workoutPlan)
                        <span class="text-sm text-slate-500"> — {{ $session->workoutPlan->name }}</span>
                    @endif
                </a>
            @empty
                <p class="p-6 text-slate-600">{{ __('No sessions yet.') }}</p>
            @endforelse
        </div>
        {{ $sessions->links() }}
    </div>
</x-app-layout>
