<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800">{{ __('Friends') }}</h2>
            <a href="{{ route('friendships.create') }}" class="text-sm font-semibold text-emerald-700">{{ __('Add friend') }}</a>
        </div>
    </x-slot>
    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-3">
        @if (session('status'))<p class="text-sm text-emerald-700">{{ session('status') }}</p>@endif
        <div class="bg-white shadow-sm sm:rounded-lg divide-y text-sm">
            @forelse ($friendships as $f)
                <div class="px-4 py-3 flex justify-between items-center gap-3">
                    <a href="{{ route('friendships.show', $f) }}" class="text-slate-800 hover:underline">
                        {{ $f->user_id === auth()->id() ? $f->friend->name : $f->user->name }}
                        <span class="text-slate-500">({{ $f->status->value }})</span>
                    </a>
                    @if ($f->friend_id === auth()->id() && $f->status === \App\Enums\FriendshipStatus::Pending)
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('friendships.accept', $f) }}">@csrf<button class="text-emerald-700 font-semibold">{{ __('Accept') }}</button></form>
                            <form method="POST" action="{{ route('friendships.reject', $f) }}">@csrf<button class="text-red-600 font-semibold">{{ __('Decline') }}</button></form>
                        </div>
                    @endif
                </div>
            @empty
                <p class="p-6 text-slate-600">{{ __('No friendships yet.') }}</p>
            @endforelse
        </div>
        {{ $friendships->links() }}
    </div>
</x-app-layout>
