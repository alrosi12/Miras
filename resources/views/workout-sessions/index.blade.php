@extends('layouts.app')

@section('title', __('Sessions'))

@section('header')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold text-slate-900">{{ __('Workout sessions') }}</h1>
        <a href="{{ route('workout-sessions.create') }}" class="inline-flex rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700">{{ __('Log session') }}</a>
    </div>
@endsection

@section('content')
    @php
        $cursor = $calendarStart->copy();
        $weeks = [];
        while ($cursor->lte($calendarEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = $cursor->copy();
                $cursor->addDay();
            }
            $weeks[] = $week;
        }
    @endphp

    <div class="mb-8 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-sm font-semibold text-slate-800">{{ __('Calendar') }} — {{ $calendarMonth->translatedFormat('F Y') }}</h2>
            <form method="get" action="{{ route('workout-sessions.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="month" name="calendar_month" value="{{ $calendarMonth->format('Y-m') }}" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                @foreach (request()->except('calendar_month') as $k => $v)
                    @if (is_string($v) && $v !== '')
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
                    @endif
                @endforeach
                <button type="submit" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">{{ __('Go') }}</button>
            </form>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full border-collapse text-center text-xs sm:text-sm">
                <thead>
                    <tr class="text-slate-500">
                        @foreach (['Mo','Tu','We','Th','Fr','Sa','Su'] as $wd)
                            <th class="border border-slate-100 py-2 font-semibold">{{ $wd }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeks as $week)
                        <tr>
                            @foreach ($week as $day)
                                @php
                                    $inMonth = $day->month === $calendarMonth->month;
                                    $ds = $day->toDateString();
                                    $cnt = $sessionCountsByDate->get($ds, 0);
                                @endphp
                                <td class="border border-slate-100 p-1 align-top {{ $inMonth ? 'bg-white' : 'bg-slate-50 text-slate-400' }}">
                                    <div class="min-h-[3.5rem] rounded-lg p-1">
                                        <span class="block font-semibold {{ $cnt ? 'text-emerald-700' : 'text-slate-700' }}">{{ $day->day }}</span>
                                        @if ($cnt)
                                            <span class="mt-1 inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-emerald-100 text-[10px] font-bold text-emerald-900">{{ $cnt }}</span>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form method="get" action="{{ route('workout-sessions.index') }}" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:flex-wrap sm:items-end">
        <input type="hidden" name="calendar_month" value="{{ request('calendar_month', $calendarMonth->format('Y-m')) }}" />
        <div>
            <label for="month" class="text-xs font-semibold text-slate-500">{{ __('Month filter') }}</label>
            <input id="month" type="month" name="month" value="{{ request('month') }}" class="mt-1 block rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
        <div>
            <label for="date_from" class="text-xs font-semibold text-slate-500">{{ __('From') }}</label>
            <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 block rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
        <div>
            <label for="date_to" class="text-xs font-semibold text-slate-500">{{ __('To') }}</label>
            <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 block rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
        </div>
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Filter list') }}</button>
        <a href="{{ route('workout-sessions.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Reset') }}</a>
    </form>

    <div class="space-y-3">
        @forelse ($sessions as $session)
            <x-session-card :session="$session" />
        @empty
            <p class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center text-slate-600">{{ __('No sessions in this range.') }}</p>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $sessions->links() }}
    </div>
@endsection
