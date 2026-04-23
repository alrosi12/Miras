@extends('layouts.app')

@section('title', __('Routine'))

@section('header')
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl space-y-1.5">
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">{{ __('My routines') }}</h1>
            <p class="text-sm leading-relaxed text-slate-500">{{ __('Build weekly splits, share links, and duplicate plans in one place.') }}</p>
        </div>
        <a href="{{ route('workout-plans.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-900/15 transition duration-200 ease-out hover:bg-emerald-700 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:self-auto">
            <svg class="h-4 w-4 shrink-0 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('New routine') }}
        </a>
    </div>
@endsection

@section('content')
    @if ($plans->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-200/90 bg-white px-6 py-16 text-center shadow-sm shadow-slate-900/[0.02] sm:px-10 sm:py-20">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-100 text-slate-400 ring-1 ring-slate-200/80">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <h2 class="mt-6 text-base font-semibold tracking-tight text-slate-900">{{ __('No routines yet') }}</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-slate-500">{{ __('Create your first plan to see it here.') }}</p>
            <a href="{{ route('workout-plans.create') }}" class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('New routine') }}
            </a>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
            @foreach ($plans as $plan)
                <article class="group flex min-h-[280px] flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03] transition duration-200 ease-out hover:border-slate-300/90 hover:shadow-md hover:shadow-slate-900/[0.06]">
                    <div class="h-1 shrink-0 bg-gradient-to-r from-emerald-500/90 via-emerald-600/80 to-teal-500/70 opacity-90 transition duration-200 group-hover:opacity-100"></div>
                    <div class="flex flex-1 flex-col p-5 sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="min-w-0 text-lg font-semibold leading-snug tracking-tight text-slate-900">
                                <a href="{{ route('workout-plans.show', $plan) }}" class="transition duration-200 hover:text-emerald-800 focus:outline-none focus-visible:text-emerald-800">{{ $plan->name }}</a>
                            </h2>
                            <a href="{{ route('workout-plans.show', $plan) }}" class="shrink-0 rounded-lg p-1.5 text-slate-400 transition duration-200 hover:bg-slate-50 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/30" title="{{ __('Open') }}">
                                <span class="sr-only">{{ __('Open') }}</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </div>

                        @if ($plan->description)
                            <p class="mt-2.5 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $plan->description }}</p>
                        @else
                            <p class="mt-2.5 text-sm italic text-slate-400">{{ __('No description') }}</p>
                        @endif

                        <dl class="mt-4 flex flex-wrap gap-x-4 gap-y-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <dt class="font-medium text-slate-400">{{ __('Days') }}</dt>
                                <dd class="font-semibold tabular-nums text-slate-700">{{ $plan->workout_plan_days_count }}</dd>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <dt class="font-medium text-slate-400">{{ __('Exercises') }}</dt>
                                <dd class="font-semibold tabular-nums text-slate-700">{{ $plan->plan_day_exercises_count }}</dd>
                            </div>
                            <div class="w-full text-[11px] text-slate-400 sm:w-auto sm:ms-auto">
                                {{ __('Updated') }} {{ $plan->updated_at->diffForHumans() }}
                            </div>
                        </dl>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($plan->is_public)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800 ring-1 ring-emerald-100">{{ __('Public') }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200/80">{{ __('Private') }}</span>
                            @endif
                            @if ($plan->share_token)
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-800 ring-1 ring-sky-100">{{ __('Shared link on') }}</span>
                            @endif
                        </div>

                        <div class="mt-auto flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                            <form method="post" action="{{ route('workout-plans.duplicate', $plan) }}" class="contents">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60">{{ __('Duplicate') }}</button>
                            </form>
                            <form method="post" action="{{ route('workout-plans.share.enable', $plan) }}" class="contents">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60">{{ __('Share') }}</button>
                            </form>
                            <a href="{{ route('workout-plans.edit', $plan) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300/60">{{ __('Edit') }}</a>
                            <a href="{{ route('workout-plans.show', $plan) }}" class="inline-flex flex-1 items-center justify-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition duration-200 hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 sm:flex-none sm:min-w-[5.5rem]">{{ __('View') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($plans->hasPages())
            <div class="mt-10 flex justify-center border-t border-slate-200/80 pt-8">
                <div class="rounded-xl border border-slate-200/80 bg-white px-2 py-2 shadow-sm">
                    {{ $plans->links() }}
                </div>
            </div>
        @endif
    @endif
@endsection
