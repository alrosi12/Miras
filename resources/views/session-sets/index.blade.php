<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Session sets') }}</h2>
            <a href="{{ route('workout-sessions.session-sets.create', $workoutSession) }}" class="text-sm font-semibold text-emerald-700">{{ __('Add set') }}</a>
        </div>
    </x-slot>
    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <p class="text-sm text-slate-500 mb-4"><a href="{{ route('workout-sessions.show', $workoutSession) }}" class="text-emerald-700 font-semibold">{{ __('← Session') }}</a></p>
        <div class="bg-white shadow-sm sm:rounded-lg divide-y">
            @forelse ($sessionSets as $set)
                <a href="{{ route('workout-sessions.session-sets.show', [$workoutSession, $set]) }}" class="block px-4 py-3 hover:bg-slate-50 text-sm">
                    {{ $set->exercise->name }} — {{ __('Set') }} {{ $set->set_number }}
                </a>
            @empty
                <p class="p-6 text-slate-600">{{ __('No sets logged.') }}</p>
            @endforelse
        </div>
        {{ $sessionSets->links() }}
    </div>
</x-app-layout>
