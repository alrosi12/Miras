<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') — {{ __('Admin') }} — {{ config('app.name', 'Miras') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        <aside class="hidden w-64 shrink-0 border-e border-slate-800/80 bg-slate-900 text-slate-100 lg:block">
            <div class="flex h-16 items-center border-b border-slate-700/80 px-5">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold tracking-tight text-white transition duration-200 hover:text-white/90">{{ __('Admin panel') }}</a>
            </div>
            <nav class="space-y-0.5 px-3 py-5 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2.5 transition duration-200 ease-out {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">{{ __('Dashboard') }}</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-3 py-2.5 transition duration-200 ease-out {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">{{ __('Users') }}</a>
                <a href="{{ route('admin.exercises.index') }}" class="block rounded-lg px-3 py-2.5 transition duration-200 ease-out {{ request()->routeIs('admin.exercises.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">{{ __('Exercises') }}</a>
                <a href="{{ route('admin.reports.index') }}" class="block rounded-lg px-3 py-2.5 transition duration-200 ease-out {{ request()->routeIs('admin.reports.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">{{ __('Reports') }}</a>
                <a href="{{ route('dashboard') }}" class="mt-6 block rounded-lg px-3 py-2.5 text-emerald-300 transition duration-200 ease-out hover:bg-slate-800">{{ __('App dashboard') }}</a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white lg:hidden">
                <div class="flex flex-wrap gap-2 px-4 py-3 text-xs font-medium">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-800 transition duration-200 hover:bg-slate-200/80">{{ __('Dashboard') }}</a>
                    <a href="{{ route('admin.users.index') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-800 transition duration-200 hover:bg-slate-200/80">{{ __('Users') }}</a>
                    <a href="{{ route('admin.exercises.index') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-800 transition duration-200 hover:bg-slate-200/80">{{ __('Exercises') }}</a>
                    <a href="{{ route('admin.reports.index') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-slate-800 transition duration-200 hover:bg-slate-200/80">{{ __('Reports') }}</a>
                </div>
            </header>

            <header class="sticky top-0 z-30 flex h-14 items-center justify-between gap-4 border-b border-slate-200 bg-white/95 px-4 shadow-sm shadow-slate-900/[0.02] backdrop-blur sm:px-6">
                <span class="text-sm font-medium text-slate-500">{{ __('Admin') }}</span>
                <div class="min-w-0 text-end">
                    <p class="truncate text-xs text-slate-500">{{ __('Signed in as') }}</p>
                    <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                </div>
            </header>

            <main class="flex-1 px-4 py-8 sm:px-6 sm:py-10">
                @include('admin.partials.flash')
                <x-alert />
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
