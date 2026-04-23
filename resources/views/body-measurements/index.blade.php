@extends('layouts.app')

@section('title', __('Body measurements'))

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-slate-900">{{ __('Body measurements') }}</h1>
        <a href="{{ route('body-measurements.create') }}" class="text-sm font-semibold text-emerald-700 hover:underline">{{ __('Add entry') }}</a>
    </div>
@endsection

@section('content')
    <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200/80 bg-white text-sm shadow-sm">
        @forelse ($measurements as $m)
            <a href="{{ route('body-measurements.show', $m) }}" class="block px-4 py-3 hover:bg-slate-50">
                {{ $m->date->toFormattedDateString() }} — {{ $m->weight ?? '—' }} kg
            </a>
        @empty
            <p class="p-6 text-slate-600">{{ __('No measurements yet.') }}</p>
        @endforelse
    </div>
    <div class="mt-6">
        {{ $measurements->links() }}
    </div>
@endsection
