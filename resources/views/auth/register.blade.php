@extends('layouts.guest')

@section('title', __('Register'))

@section('content')
    <div class="mb-8">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ __('Start your Miras profile') }}</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ __('Add a few body stats so we can tailor your dashboard.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div class="space-y-1.5">
            <x-input-label for="name" :value="__('Display name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-4">
            <div class="space-y-1.5">
                <x-input-label for="weight" :value="__('Weight (kg)')" />
                <x-text-input id="weight" class="block mt-1 w-full" type="number" step="0.01" min="20" max="500" name="weight" :value="old('weight')" required />
                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
            </div>
            <div class="space-y-1.5">
                <x-input-label for="height" :value="__('Height (cm)')" />
                <x-text-input id="height" class="block mt-1 w-full" type="number" step="0.01" min="50" max="280" name="height" :value="old('height')" required />
                <x-input-error :messages="$errors->get('height')" class="mt-2" />
            </div>
        </div>

        <div class="space-y-1.5">
            <x-input-label for="birth_date" :value="__('Date of birth')" />
            <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required />
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="goal" :value="__('Primary goal')" />
            <select id="goal" name="goal" required class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm shadow-slate-900/[0.02] transition duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                <option value="" disabled @selected(old('goal') === null)>{{ __('Choose one') }}</option>
                @foreach (\App\Enums\UserGoal::cases() as $goalOption)
                    <option value="{{ $goalOption->value }}" @selected(old('goal') === $goalOption->value)>
                        {{ str_replace('_', ' ', ucfirst($goalOption->name)) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row sm:items-center sm:justify-between">
            <a class="text-center text-sm font-medium text-slate-600 transition duration-200 hover:text-slate-900 sm:text-start" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="w-full justify-center sm:w-auto">
                {{ __('Create account') }}
            </x-primary-button>
        </div>
    </form>
@endsection
