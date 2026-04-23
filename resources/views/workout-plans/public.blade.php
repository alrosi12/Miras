<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $workoutPlan->name }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen p-8">
    <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-xl p-8">
        <p class="text-xs uppercase tracking-wide text-emerald-700 font-semibold">{{ __('Public routine') }}</p>
        <h1 class="text-2xl font-bold mt-2">{{ $workoutPlan->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('By') }} {{ $workoutPlan->user->name }}</p>
        @if ($workoutPlan->description)
            <p class="mt-4 text-slate-700">{{ $workoutPlan->description }}</p>
        @endif
        <div class="mt-8 space-y-4">
            @foreach ($workoutPlan->workoutPlanDays as $day)
                <div class="border border-slate-200 rounded-lg p-4">
                    <h2 class="font-semibold">{{ $day->day_name }}</h2>
                    <ul class="mt-2 text-sm list-disc list-inside text-slate-600">
                        @foreach ($day->planDayExercises as $row)
                            <li>
                                {{ $row->exercise->name }}
                                — {{ $row->sets }}×{{ $row->reps }}
                                @if ($row->rest_seconds)
                                    · {{ __('Rest') }} {{ $row->rest_seconds }}s
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
