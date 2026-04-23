@extends('layouts.app')

@section('title', __('New measurement'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('New measurement') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-xl rounded-2xl border border-slate-200/80 bg-white p-6 text-sm text-slate-600 shadow-sm">
        <p>{{ __('Scaffold: POST body-measurements.store') }}</p>
        <a href="{{ route('body-measurements.index') }}" class="mt-4 inline-block font-semibold text-emerald-700 hover:underline">{{ __('Back') }}</a>
    </div>
@endsection
