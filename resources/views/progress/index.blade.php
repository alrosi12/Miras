<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('Progress') }}</h2></x-slot>
    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <section>
            <h3 class="font-semibold text-slate-900 mb-3">{{ __('Weight trend (last entries)') }}</h3>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="text-left border-b"><th class="p-3">{{ __('Date') }}</th><th class="p-3">{{ __('Weight') }}</th><th class="p-3">{{ __('Waist') }}</th></tr></thead>
                    <tbody>
                        @forelse ($measurements as $m)
                            <tr class="border-b border-slate-100">
                                <td class="p-3">{{ $m->date->toDateString() }}</td>
                                <td class="p-3">{{ $m->weight ?? '—' }}</td>
                                <td class="p-3">{{ $m->waist ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="p-4 text-slate-600">{{ __('No measurement data.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section>
            <h3 class="font-semibold text-slate-900 mb-3">{{ __('Sessions per week (last 12 weeks)') }}</h3>
            <ul class="text-sm text-slate-700 space-y-1">
                @forelse ($sessionsByWeek as $week => $count)
                    <li><span class="font-mono text-xs">{{ $week }}</span> — {{ $count }} {{ __('sessions') }}</li>
                @empty
                    <li>{{ __('No session data in range.') }}</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-app-layout>
