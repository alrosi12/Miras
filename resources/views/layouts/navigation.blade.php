<nav x-data="{ open: false }" class="border-b border-gray-100 bg-white shadow-sm shadow-slate-900/[0.02]">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <!-- Logo -->
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="rounded-lg p-1 transition duration-200 ease-out hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-300/60">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:items-center sm:gap-1 lg:gap-2">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('exercises.index')" :active="request()->routeIs('exercises.*')">
                        {{ __('Exercises') }}
                    </x-nav-link>
                    <x-nav-link :href="route('workout-plans.index')" :active="request()->routeIs('workout-plans.*')">
                        {{ __('Plans') }}
                    </x-nav-link>
                    <x-nav-link :href="route('workout-sessions.index')" :active="request()->routeIs('workout-sessions.*')">
                        {{ __('Sessions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">
                        {{ __('Progress') }}
                    </x-nav-link>
                    <x-nav-link :href="route('friends.index')" :active="request()->routeIs('friends.*')">
                        {{ __('Friends') }}
                    </x-nav-link>
                    @if (Auth::user()->is_admin)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-lg border border-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-200 ease-out hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-300/70">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current transition duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-400 transition duration-200 ease-out hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-500 focus-visible:ring-2 focus-visible:ring-gray-300/70">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-0.5 pb-3 pt-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('exercises.index')" :active="request()->routeIs('exercises.*')">
                {{ __('Exercises') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('workout-plans.index')" :active="request()->routeIs('workout-plans.*')">
                {{ __('Plans') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('workout-sessions.index')" :active="request()->routeIs('workout-sessions.*')">
                {{ __('Sessions') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">
                {{ __('Progress') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('friends.index')" :active="request()->routeIs('friends.*')">
                {{ __('Friends') }}
            </x-responsive-nav-link>
            @if (Auth::user()->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    {{ __('Admin') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-gray-200 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
