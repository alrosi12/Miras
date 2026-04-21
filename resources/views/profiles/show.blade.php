<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $user->name }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen p-8 text-slate-800">
    <div class="max-w-lg mx-auto bg-white rounded-xl shadow-sm p-8">
        <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
        <p class="text-sm text-slate-500 mt-2">{{ __('Public profile') }}</p>
        <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">{{ __('Plans') }}</dt><dd class="font-semibold">{{ $user->workout_plans_count }}</dd></div>
            <div><dt class="text-slate-500">{{ __('Sessions') }}</dt><dd class="font-semibold">{{ $user->workout_sessions_count }}</dd></div>
        </dl>
    </div>
</body>
</html>
