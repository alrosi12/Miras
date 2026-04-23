@extends('layouts.admin')

@section('title', __('Users'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-slate-900">{{ __('Users') }}</h1>

        <form method="get" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-wrap items-end gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
            <div class="min-w-[200px] flex-1">
                <x-input-label for="q" :value="__('Search (name or email)')" />
                <x-text-input id="q" class="mt-1 block w-full" type="text" name="q" :value="request('q')" />
            </div>
            <div>
                <x-input-label for="admin" :value="__('Admin filter')" />
                <select id="admin" name="admin" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">{{ __('All') }}</option>
                    <option value="1" @selected(request('admin') === '1')>{{ __('Admins only') }}</option>
                    <option value="0" @selected(request('admin') === '0')>{{ __('Non-admins') }}</option>
                </select>
            </div>
            <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:text-slate-900 py-2">{{ __('Reset') }}</a>
            <a href="{{ route('admin.reports.export-users', array_filter(['q' => request('q')])) }}"
               class="self-center text-sm font-semibold text-slate-800 underline decoration-slate-300 hover:decoration-slate-800">
                {{ __('Export users (CSV)') }}
            </a>
        </form>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-100">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold text-slate-700">ID</th>
                        <th class="px-4 py-3 text-start font-semibold text-slate-700">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-start font-semibold text-slate-700">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-start font-semibold text-slate-700">{{ __('Admin') }}</th>
                        <th class="px-4 py-3 text-start font-semibold text-slate-700">{{ __('Registered') }}</th>
                        <th class="px-4 py-3 text-end font-semibold text-slate-700">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $u)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-600">{{ $u->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                @if ($u->is_admin)
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-900">{{ __('Yes') }}</span>
                                @else
                                    <span class="text-slate-500">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $u->created_at?->toFormattedDateString() }}</td>
                            <td class="px-4 py-3 text-end">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $u) }}" class="text-emerald-700 hover:underline">{{ __('View') }}</a>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="text-slate-700 hover:underline">{{ __('Edit') }}</a>
                                    <form method="post" action="{{ route('admin.users.toggle-admin', $u) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-amber-800 hover:underline">{{ $u->is_admin ? __('Revoke admin') : __('Make admin') }}</button>
                                    </form>
                                    @if (! $u->is(auth()->user()))
                                        <form method="post" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm(@json(__('Delete this user and all related data?')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
@endsection
