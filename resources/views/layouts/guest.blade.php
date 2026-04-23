<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Miras'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 px-4 py-12 sm:pt-0">
        <div class="mb-8 text-center">
            <a href="/" class="group inline-flex flex-col items-center gap-2.5">
                <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-500/20 text-xl font-bold tracking-tight text-emerald-300 ring-1 ring-emerald-400/40 transition duration-200 ease-out group-hover:bg-emerald-500/30 group-hover:shadow-md group-hover:shadow-emerald-950/20">M</span>
                <span class="text-sm font-semibold uppercase tracking-wide text-emerald-100/90">{{ config('app.name', 'Miras') }}</span>
                <span class="text-xs text-emerald-200/70">Fitness tracker</span>
            </a>
        </div>

        <div class="w-full overflow-hidden bg-white/95 px-6 py-10 shadow-xl shadow-emerald-950/20 ring-1 ring-white/60 backdrop-blur transition duration-200 sm:max-w-lg sm:rounded-xl">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </div>

        <p class="mt-10 max-w-sm text-center text-xs leading-relaxed text-emerald-200/50">
            {{ __('Log workouts, plans, and progress in one place.') }}
        </p>
    </div>
</body>
</html>
