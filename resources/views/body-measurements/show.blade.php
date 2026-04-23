@extends('layouts.app')

@section('title', $bodyMeasurement->date->toDateString())

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ $bodyMeasurement->date->toFormattedDateString() }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-xl space-y-2 rounded-2xl border border-slate-200/80 bg-white p-6 text-sm shadow-sm">
        <p>{{ __('Weight') }}: {{ $bodyMeasurement->weight ?? '—' }}</p>
        <a href="{{ route('body-measurements.edit', $bodyMeasurement) }}" class="font-semibold text-emerald-700 hover:underline">{{ __('Edit') }}</a>
    </div>
@endsection
