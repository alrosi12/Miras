<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between gap-3 items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Session') }} — {{ $workoutSession->date->toFormattedDateString() }}</h2>
            <div class="flex flex-wrap gap-3 text-sm">
                <a href="{{ route('workout-sessions.session-sets.index', $workoutSession) }}" class="text-emerald-700 font-semibold">{{ __('Sets') }}</a>
                <a href="{{ route('workout-sessions.edit', $workoutSession) }}" class="text-emerald-700 font-semibold">{{ __('Edit') }}</a>
                <form method="POST" action="{{ route('workout-sessions.finish', $workoutSession) }}">
                    @csrf
                    <input type="hidden" name="duration_minutes" value="45">
                    <button type="submit" class="text-emerald-800 font-semibold">{{ __('Finish (demo 45m)') }}</button>
                </form>
            </div>
        </div>
    </x-slot>
    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
        @if (session('status'))<p class="text-sm text-emerald-700">{{ session('status') }}</p>@endif
        <p class="text-sm text-slate-600">{{ $workoutSession->notes ?? '' }}</p>
        <ul class="text-sm text-slate-700 list-disc list-inside">
            @foreach ($workoutSession->sessionSets as $set)
                <li>{{ $set->exercise->name }} — #{{ $set->set_number }} @if($set->reps){{ $set->reps }} reps @endif</li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
