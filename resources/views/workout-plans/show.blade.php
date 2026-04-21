<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between gap-3 items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ $workoutPlan->name }}</h2>
            <div class="flex flex-wrap gap-2 text-sm">
                @can('update', $workoutPlan)
                    <a href="{{ route('workout-plans.edit', $workoutPlan) }}" class="text-emerald-700 font-semibold">{{ __('Edit') }}</a>
                @endcan
                @can('duplicate', $workoutPlan)
                    <form method="POST" action="{{ route('workout-plans.duplicate', $workoutPlan) }}">@csrf<button type="submit" class="text-emerald-700 font-semibold">{{ __('Duplicate') }}</button></form>
                @endcan
                @can('manageShare', $workoutPlan)
                    <form method="POST" action="{{ route('workout-plans.share.enable', $workoutPlan) }}">@csrf<button type="submit" class="text-emerald-700 font-semibold">{{ __('Enable share') }}</button></form>
                    <form method="POST" action="{{ route('workout-plans.share.disable', $workoutPlan) }}">@csrf @method('DELETE')<button type="submit" class="text-slate-600 font-semibold">{{ __('Remove share') }}</button></form>
                @endcan
                @can('delete', $workoutPlan)
                    <form method="POST" action="{{ route('workout-plans.destroy', $workoutPlan) }}" onsubmit="return confirm('{{ __('Delete plan?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 font-semibold">{{ __('Delete') }}</button></form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4 text-slate-700">
        @if (session('status'))<p class="text-sm text-emerald-700">{{ session('status') }}</p>@endif
        @if (session('share_url'))<p class="text-xs break-all text-slate-600">{{ session('share_url') }}</p>@endif
        <p class="text-sm">{{ $workoutPlan->description ?? '' }}</p>
        <div class="space-y-4">
            @foreach ($workoutPlan->workoutPlanDays as $day)
                <div class="border border-slate-200 rounded-lg p-4">
                    <h3 class="font-semibold text-slate-900">{{ $day->day_name }}</h3>
                    <ul class="mt-2 text-sm list-disc list-inside text-slate-600">
                        @foreach ($day->planDayExercises as $row)
                            <li>{{ $row->exercise->name }} — {{ $row->sets }}×{{ $row->reps }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
