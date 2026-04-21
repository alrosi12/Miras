<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Workout plans') }}</h2>
            <a href="{{ route('workout-plans.create') }}" class="text-sm font-semibold text-emerald-700">{{ __('New plan') }}</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
        <div class="bg-white shadow-sm sm:rounded-lg divide-y divide-slate-100">
            @forelse ($plans as $plan)
                <div class="px-4 py-3 flex justify-between gap-4">
                    <a href="{{ route('workout-plans.show', $plan) }}" class="hover:bg-slate-50 flex-1">
                        <div class="font-medium text-slate-900">{{ $plan->name }}</div>
                        <div class="text-xs text-slate-500">{{ $plan->user_id === auth()->id() ? __('Yours') : __('Public') }} · {{ $plan->user->name }}</div>
                    </a>
                    @can('duplicate', $plan)
                        <form method="POST" action="{{ route('workout-plans.duplicate', $plan) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-emerald-700">{{ __('Duplicate') }}</button>
                        </form>
                    @endcan
                </div>
            @empty
                <p class="p-6 text-slate-600">{{ __('No plans found.') }}</p>
            @endforelse
        </div>
        {{ $plans->links() }}
    </div>
</x-app-layout>
