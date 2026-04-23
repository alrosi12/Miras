@extends('layouts.app')

@section('title', __('Edit measurement'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('Edit measurement') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ route('body-measurements.show', $bodyMeasurement) }}" class="text-sm font-semibold text-emerald-700 hover:underline">{{ __('View') }}</a>
    </div>
@endsection
