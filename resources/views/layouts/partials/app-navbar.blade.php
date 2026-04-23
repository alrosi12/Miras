@php
    $user = auth()->user();
@endphp

<nav x-data="{ mobileOpen: false }" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 shadow-sm shadow-slate-900/[0.02] backdrop-blur transition-colors duration-200 supports-[backdrop-filter]:bg-white/80">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:h-[4.25rem] sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-6">
            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2.5 font-semibold tracking-tight text-slate-900 transition-opacity duration-200 hover:opacity-90">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-sm ring-1 ring-emerald-700/20 transition duration-200 hover:shadow-md">M</span>
                <span class="hidden sm:inline">{{ config('app.name', 'Miras') }}</span>
            </a>

            <div class="hidden min-w-0 items-center gap-0.5 md:flex">
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition duration-200 ease-out {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">{{ __('Dashboard') }}</a>
                <a href="{{ route('exercises.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition duration-200 ease-out {{ request()->routeIs('exercises.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">{{ __('Exercises') }}</a>
                <a href="{{ route('workout-plans.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition duration-200 ease-out {{ request()->routeIs('workout-plans.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">{{ __('Routine') }}</a>
                <a href="{{ route('progress.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition duration-200 ease-out {{ request()->routeIs('progress.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">{{ __('Progress') }}</a>
                <a href="{{ route('friends.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition duration-200 ease-out {{ request()->routeIs('friends.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">{{ __('Friends') }}</a>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
            <a href="{{ route('friends.requests') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-600 ring-1 ring-slate-200/80 transition duration-200 ease-out hover:bg-slate-50 hover:text-slate-900 hover:shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30" title="{{ __('Friend requests') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if (($pendingFriendRequestsCount ?? 0) > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white shadow-sm">{{ $pendingFriendRequestsCount > 9 ? '9+' : $pendingFriendRequestsCount }}</span>
                @endif
            </a>

            <div class="relative hidden sm:block" x-data="{ open: false }" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full py-1.5 pl-1 pr-3 text-left text-sm font-medium text-slate-700 ring-1 ring-slate-200/80 transition duration-200 ease-out hover:bg-slate-50 hover:shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/25" aria-expanded="false" :aria-expanded="open">
                    @if (! empty($user->avatar))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-white" />
                    @else
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">{{ mb_substr($user->name, 0, 1) }}</span>
                    @endif
                    <span class="max-w-[8rem] truncate">{{ $user->name }}</span>
                    <svg class="h-4 w-4 shrink-0 text-slate-400 transition duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" @click.outside="open = false" class="absolute end-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg shadow-slate-900/10 ring-1 ring-slate-900/5" style="display: none;">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-slate-700 transition duration-150 ease-out hover:bg-slate-50">{{ __('Profile') }}</a>
                    <a href="{{ route('profile.edit') }}#password" class="block px-4 py-2.5 text-sm text-slate-700 transition duration-150 ease-out hover:bg-slate-50">{{ __('Settings') }}</a>
                    @if ($user->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-amber-800 transition duration-150 ease-out hover:bg-amber-50">{{ __('Admin') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2.5 text-start text-sm text-rose-700 transition duration-150 ease-out hover:bg-rose-50">{{ __('Log Out') }}</button>
                    </form>
                </div>
            </div>

            <button type="button" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 transition duration-200 ease-out hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/25 md:hidden" @click="mobileOpen = !mobileOpen" aria-label="{{ __('Menu') }}">
                <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>

    <div x-show="mobileOpen" x-transition.opacity.duration.200ms class="border-t border-slate-100 md:hidden" x-cloak style="display: none;">
        <div class="space-y-0.5 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">{{ __('Dashboard') }}</a>
            <a href="{{ route('exercises.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">{{ __('Exercises') }}</a>
            <a href="{{ route('workout-plans.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">{{ __('Routine') }}</a>
            <a href="{{ route('progress.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">{{ __('Progress') }}</a>
            <a href="{{ route('friends.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">{{ __('Friends') }}</a>
            <a href="{{ route('friends.requests') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">
                {{ __('Friend requests') }}
                @if (($pendingFriendRequestsCount ?? 0) > 0)
                    <span class="rounded-full bg-rose-600 px-2 py-0.5 text-xs font-bold text-white shadow-sm">{{ $pendingFriendRequestsCount }}</span>
                @endif
            </a>
            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 transition duration-150 ease-out hover:bg-slate-50">{{ __('Profile') }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full rounded-lg px-3 py-2.5 text-start text-sm font-medium text-rose-700 transition duration-150 ease-out hover:bg-rose-50">{{ __('Log Out') }}</button>
            </form>
        </div>
    </div>
</nav>
