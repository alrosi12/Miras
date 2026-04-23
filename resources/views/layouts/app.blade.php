<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') — {{ config('app.name', 'Miras') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    @auth
        @include('layouts.partials.app-navbar')
    @endauth

    @hasSection('header')
        <header class="border-b border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.02]">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>
    @endif

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-alert />
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
