<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ $exercise->name }}</h2>
            <div class="flex gap-3 text-sm">
                <a href="{{ route('exercises.edit', $exercise) }}" class="text-emerald-700 font-semibold">{{ __('Edit') }}</a>
                <form method="POST" action="{{ route('exercises.destroy', $exercise) }}" onsubmit="return confirm('{{ __('Delete?') }}');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 font-semibold">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-2 text-slate-700">
        @if (session('status'))
            <p class="text-sm text-emerald-700">{{ session('status') }}</p>
        @endif
        <p class="text-sm">{{ $exercise->description ?? __('No description.') }}</p>
        <p class="text-xs text-slate-500">
            @if ($exercise->user_id === null)
                {{ __('Global exercise') }}
            @elseif ($exercise->user_id === auth()->id())
                {{ __('Your exercise') }}
            @else
                {{ __('User exercise #') }}{{ $exercise->user_id }}
            @endif
        </p>
    </div>
</x-app-layout>
