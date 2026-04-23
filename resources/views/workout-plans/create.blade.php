@extends('layouts.app')

@section('title', __('New routine'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('New routine') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl">
        @include('workout-plans._editor-form', ['initialDaysPayload' => $initialDaysPayload, 'exercises' => $exercises])
    </div>
@endsection
