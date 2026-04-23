@extends('layouts.app')

@section('title', __('Profile'))

@section('header')
    <h1 class="text-xl font-semibold text-slate-900">{{ __('Profile') }}</h1>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
