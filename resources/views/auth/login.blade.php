@extends('layouts.guest')

@section('title', __('Log in'))

@section('content')
<div class="mx-auto max-w-md">

    <!-- Header -->
    <div class="mb-10 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ __('Welcome back') }}
        </h1>
        <p class="mt-3 text-sm leading-relaxed text-slate-600">
            {{ __('Sign in to continue your training log.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div class="space-y-1.5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input
                id="password"
                class="block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-slate-300 text-emerald-600 shadow-sm transition duration-200 focus:ring-emerald-500"
                    name="remember"
                >
                <span class="text-sm text-slate-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3 pt-1">
            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>

            @if (Route::has('password.request'))
                <a
                    class="text-center text-sm font-medium text-emerald-700 underline-offset-4 transition duration-200 hover:text-emerald-800 hover:underline"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>
    </form>

    <!-- Register -->
    <p class="mt-10 text-center text-sm text-slate-600">
        {{ __('New here?') }}
        <a href="{{ route('register') }}" class="font-semibold text-emerald-700 transition duration-200 hover:text-emerald-800">
            {{ __('Create an account') }}
        </a>
    </p>

</div>
@endsection
