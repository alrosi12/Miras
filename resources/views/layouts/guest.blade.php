<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Miras') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-10 sm:pt-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900">
            <div class="mb-6 text-center">
                <a href="/" class="inline-flex flex-col items-center gap-2 group">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500/20 ring-1 ring-emerald-400/40 text-emerald-300 font-bold text-xl tracking-tight group-hover:bg-emerald-500/30 transition">
                        M
                    </span>
                    <span class="text-sm font-semibold text-emerald-100/90 tracking-wide uppercase">{{ config('app.name', 'Miras') }}</span>
                    <span class="text-xs text-emerald-200/70">Fitness tracker</span>
                </a>
            </div>

            <div class="w-full sm:max-w-lg mt-2 px-6 py-8 bg-white/95 shadow-xl shadow-emerald-950/20 ring-1 ring-white/60 overflow-hidden sm:rounded-2xl backdrop-blur">
                {{ $slot }}
            </div>

            <p class="mt-8 text-center text-xs text-emerald-200/50">
                {{ __('Log workouts, plans, and progress in one place.') }}
            </p>
        </div>
    </body>
</html>
