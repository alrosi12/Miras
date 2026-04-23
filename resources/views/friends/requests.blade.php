@extends('layouts.app')

@section('title', __('Friend requests'))

@section('header')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-slate-900">{{ __('Friend requests') }}</h1>
        <a href="{{ route('friends.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">{{ __('Back') }}</a>
    </div>
@endsection

@section('content')
    <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        @forelse ($incoming as $row)
            <div class="flex flex-wrap items-center justify-between gap-4 p-4">
                <div class="flex items-center gap-3">
                    @if ($row->user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($row->user->avatar) }}" alt="" class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-200" />
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-600">{{ mb_substr($row->user->name, 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="font-medium text-slate-900">{{ $row->user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $row->created_at->toFormattedDateString() }}</p>
                    </div>
                </div>
                <div class="flex gap-2 text-sm font-semibold">
                    <form method="post" action="{{ route('friends.accept', $row) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">{{ __('Accept') }}</button>
                    </form>
                    <form method="post" action="{{ route('friends.reject', $row) }}" onsubmit="return confirm(@json(__('Decline this request?')));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">{{ __('Decline') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-slate-600">{{ __('No pending requests.') }}</p>
        @endforelse
    </div>
@endsection
