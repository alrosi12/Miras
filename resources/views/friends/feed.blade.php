@extends('layouts.app')

@section('title', __('Friends activity'))

@section('header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-slate-900">{{ __('Friends activity') }}</h1>
        <a href="{{ route('friends.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">{{ __('Back') }}</a>
    </div>
@endsection

@section('content')
    <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        @forelse ($feed as $session)
            <div class="flex flex-wrap items-center justify-between gap-4 p-4 text-sm">
                <div class="flex items-center gap-3">
                    @if ($session->user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($session->user->avatar) }}" alt="" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200" />
                    @else
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600">{{ mb_substr($session->user->name, 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="font-medium text-slate-900">{{ $session->user->name }}</p>
                        @if ($session->workoutPlan)
                            <p class="text-xs text-slate-600">{{ $session->workoutPlan->name }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-end text-slate-500">
                    <p class="text-sm">{{ $session->date->toFormattedDateString() }}</p>
                    @if ($session->duration_minutes !== null)
                        <p class="text-xs font-medium text-emerald-700">{{ $session->duration_minutes }} {{ __('min') }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-slate-600">{{ __('No friend activity yet. Add friends to see their sessions here.') }}</p>
        @endforelse
    </div>
@endsection
