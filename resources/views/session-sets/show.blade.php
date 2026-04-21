<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800">{{ $sessionSet->exercise->name }} — {{ __('Set') }} {{ $sessionSet->set_number }}</h2>
    </x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8 text-sm space-y-3">
        @if (session('status'))<p class="text-emerald-700">{{ session('status') }}</p>@endif
        <p>{{ __('Reps') }}: {{ $sessionSet->reps ?? '—' }} · {{ __('Weight') }}: {{ $sessionSet->weight ?? '—' }}</p>
        <div class="flex gap-4">
            <a href="{{ route('workout-sessions.session-sets.edit', [$workoutSession, $sessionSet]) }}" class="text-emerald-700 font-semibold">{{ __('Edit') }}</a>
            <form method="POST" action="{{ route('workout-sessions.session-sets.destroy', [$workoutSession, $sessionSet]) }}" onsubmit="return confirm('{{ __('Delete?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 font-semibold">{{ __('Delete') }}</button></form>
        </div>
    </div>
</x-app-layout>
