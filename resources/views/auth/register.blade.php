<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">{{ __('Start your Miras profile') }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ __('Add a few body stats so we can tailor your dashboard.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Display name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="weight" :value="__('Weight (kg)')" />
                <x-text-input id="weight" class="block mt-1 w-full" type="number" step="0.01" min="20" max="500" name="weight" :value="old('weight')" required />
                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="height" :value="__('Height (cm)')" />
                <x-text-input id="height" class="block mt-1 w-full" type="number" step="0.01" min="50" max="280" name="height" :value="old('height')" required />
                <x-input-error :messages="$errors->get('height')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="birth_date" :value="__('Date of birth')" />
            <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required />
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="goal" :value="__('Primary goal')" />
            <select id="goal" name="goal" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                <option value="" disabled @selected(old('goal') === null)>{{ __('Choose one') }}</option>
                @foreach (\App\Enums\UserGoal::cases() as $goalOption)
                    <option value="{{ $goalOption->value }}" @selected(old('goal') === $goalOption->value)>
                        {{ str_replace('_', ' ', ucfirst($goalOption->name)) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            <a class="text-sm text-slate-600 hover:text-slate-900 font-medium" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Create account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
