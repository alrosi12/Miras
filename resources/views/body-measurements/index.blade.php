<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Body measurements') }}</h2>
            <a href="{{ route('body-measurements.create') }}" class="text-sm font-semibold text-emerald-700">{{ __('Add entry') }}</a>
        </div>
    </x-slot>
    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg divide-y text-sm">
            @forelse ($measurements as $m)
                <a href="{{ route('body-measurements.show', $m) }}" class="block px-4 py-3 hover:bg-slate-50">
                    {{ $m->date->toFormattedDateString() }} — {{ $m->weight ?? '—' }} kg
                </a>
            @empty
                <p class="p-6 text-slate-600">{{ __('No measurements yet.') }}</p>
            @endforelse
        </div>
        {{ $measurements->links() }}
    </div>
</x-app-layout>
