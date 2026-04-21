<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-900">{{ __('Complete your profile') }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ __('We need your body metrics and goal to unlock the app.') }}</p>
    </div>

    <form method="POST" action="{{ route('profile.complete.update') }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="weight" :value="__('Weight (kg)')" />
                <x-text-input id="weight" class="block mt-1 w-full" type="number" step="0.01" min="20" max="500" name="weight" :value="old('weight', auth()->user()?->weight)" required />
                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="height" :value="__('Height (cm)')" />
                <x-text-input id="height" class="block mt-1 w-full" type="number" step="0.01" min="50" max="280" name="height" :value="old('height', auth()->user()?->height)" required />
                <x-input-error :messages="$errors->get('height')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="birth_date" :value="__('Date of birth')" />
            <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date', optional(auth()->user()?->birth_date)->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="goal" :value="__('Primary goal')" />
            <select id="goal" name="goal" required class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                <option value="" disabled @selected(old('goal') === null && ! auth()->user()?->goal)>{{ __('Choose one') }}</option>
                @foreach (\App\Enums\UserGoal::cases() as $goalOption)
                    <option value="{{ $goalOption->value }}" @selected(old('goal', auth()->user()?->goal?->value) === $goalOption->value)>
                        {{ str_replace('_', ' ', ucfirst($goalOption->name)) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center">
                {{ __('Save and continue') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
