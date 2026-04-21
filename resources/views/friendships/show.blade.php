<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-slate-800">{{ __('Friendship') }}</h2></x-slot>
    <div class="py-8 max-w-xl mx-auto sm:px-6 lg:px-8 text-sm space-y-2">
        <p>{{ __('Between') }} {{ $friendship->user->name }} ↔ {{ $friendship->friend->name }}</p>
        <p>{{ __('Status') }}: {{ $friendship->status->value }}</p>
        <form method="POST" action="{{ route('friendships.destroy', $friendship) }}" onsubmit="return confirm('{{ __('Remove?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 font-semibold">{{ __('Remove') }}</button></form>
    </div>
</x-app-layout>
