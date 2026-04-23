@extends('layouts.app')

@section('title', __('Friends'))

@section('header')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold text-slate-900">{{ __('Friends') }}</h1>
        <div class="flex flex-wrap gap-2 text-sm font-semibold">
            <a href="{{ route('friends.requests') }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-slate-800 ring-1 ring-slate-200 hover:bg-slate-200">{{ __('Requests') }}</a>
            <a href="{{ route('friends.feed') }}" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-emerald-800 ring-1 ring-emerald-100 hover:bg-emerald-100">{{ __('Activity feed') }}</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-10">
        <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-slate-900">{{ __('Find users') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Search by name or email, then send a request from the results.') }}</p>
            <form method="get" action="{{ route('friends.index') }}" class="mt-4 flex flex-wrap items-end gap-3">
                <input type="search" name="q" value="{{ request('q') }}" class="min-w-[12rem] flex-1 rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="{{ __('Search…') }}" />
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Search') }}</button>
            </form>
            @if (request()->filled('q'))
                <ul class="mt-4 divide-y divide-slate-100 rounded-xl border border-slate-100">
                    @forelse ($searchResults as $u)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($u->avatar)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($u->avatar) }}" alt="" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200" />
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-600">{{ mb_substr($u->name, 0, 1) }}</span>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-900">{{ $u->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $u->email }}</p>
                                </div>
                            </div>
                            <form method="post" action="{{ route('friends.send', $u) }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">{{ __('Add friend') }}</button>
                            </form>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-sm text-slate-600">{{ __('No users found.') }}</li>
                    @endforelse
                </ul>
            @endif
        </section>

        @if ($incoming->isNotEmpty())
            <section class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-6 shadow-sm ring-1 ring-amber-100">
                <h2 class="font-semibold text-amber-950">{{ __('Incoming requests') }}</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($incoming as $req)
                        <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-100 bg-white/80 px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($req->user->avatar)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($req->user->avatar) }}" alt="" class="h-10 w-10 rounded-full object-cover" />
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-900">{{ mb_substr($req->user->name, 0, 1) }}</span>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-900">{{ $req->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $req->created_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <form method="post" action="{{ route('friends.accept', $req) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white">{{ __('Accept') }}</button>
                                </form>
                                <form method="post" action="{{ route('friends.reject', $req) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">{{ __('Decline') }}</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('friends.requests') }}" class="mt-4 inline-block text-sm font-semibold text-amber-900 underline">{{ __('Open requests page') }}</a>
            </section>
        @endif

        <section>
            <h2 class="mb-3 font-semibold text-slate-900">{{ __('Your friends') }}</h2>
            <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                @forelse ($friends as $friend)
                    <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4">
                        <div class="flex items-center gap-3">
                            @if ($friend->avatar)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($friend->avatar) }}" alt="" class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-200" />
                            @else
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-600">{{ mb_substr($friend->name, 0, 1) }}</span>
                            @endif
                            <div>
                                <p class="font-medium text-slate-900">{{ $friend->name }}</p>
                                <p class="text-xs text-slate-500">
                                    @if ($friend->latestWorkoutSession)
                                        {{ __('Last session') }}: {{ $friend->latestWorkoutSession->date->toFormattedDateString() }}
                                        @if ($friend->latestWorkoutSession->workoutPlan)
                                            — {{ $friend->latestWorkoutSession->workoutPlan->name }}
                                        @endif
                                    @else
                                        {{ __('No sessions logged yet.') }}
                                    @endif
                                    · {{ __('This week') }}: {{ $weeklySessionCounts->get($friend->id, 0) }} {{ __('sessions') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 text-sm font-semibold">
                            <a href="{{ route('friends.profile', $friend) }}" class="text-emerald-700 hover:underline">{{ __('Profile') }}</a>
                            <form method="post" action="{{ route('friends.destroy', $friend) }}" onsubmit="return confirm(@json(__('Remove friend?')));">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:underline">{{ __('Remove') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-8 text-center text-slate-600">{{ __('No friends yet. Search above or accept incoming requests.') }}</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
