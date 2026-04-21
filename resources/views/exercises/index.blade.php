<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Exercises') }}</h2>
            <a href="{{ route('exercises.create') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">{{ __('Add exercise') }}</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
        @if (session('status'))
            <p class="text-sm text-emerald-700">{{ session('status') }}</p>
        @endif
        <div class="bg-white shadow-sm sm:rounded-lg divide-y divide-slate-100">
            @forelse ($exercises as $exercise)
                <a href="{{ route('exercises.show', $exercise) }}" class="block px-4 py-3 hover:bg-slate-50">
                    <div class="font-medium text-slate-900">{{ $exercise->name }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $exercise->muscle_group->value }} · {{ $exercise->type->value }}</div>
                </a>
            @empty
                <p class="p-6 text-slate-600">{{ __('No exercises yet.') }}</p>
            @endforelse
        </div>
        {{ $exercises->links() }}
    </div>
</x-app-layout>
