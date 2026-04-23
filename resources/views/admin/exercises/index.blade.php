@extends('layouts.admin')

@section('title', __('Global exercises'))

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-slate-900">
            {{ __('Global catalog exercises') }}
        </h1>

        <a href="{{ route('admin.exercises.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            {{ __('Add exercise') }}
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">

        <table class="min-w-full text-sm">

            <!-- Head -->
            <thead class="bg-slate-50">
                <tr class="text-left">
                    <th class="px-5 py-3 font-semibold text-slate-700">
                        {{ __('Name') }}
                    </th>
                    <th class="px-5 py-3 font-semibold text-slate-700">
                        {{ __('Muscle') }}
                    </th>
                    <th class="px-5 py-3 font-semibold text-slate-700">
                        {{ __('Type') }}
                    </th>
                    <th class="px-5 py-3 text-end font-semibold text-slate-700">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>

            <!-- Body -->
            <tbody class="divide-y divide-slate-100">

                @foreach ($exercises as $exercise)
                    <tr class="transition hover:bg-slate-50">

                        <!-- Name -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                @if ($exercise->image)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($exercise->image) }}"
                                        alt=""
                                        class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                                    />
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-slate-400 text-sm">
                                        🏋
                                    </div>
                                @endif

                                <span class="font-medium text-slate-900">
                                    {{ $exercise->name }}
                                </span>
                            </div>
                        </td>

                        <!-- Muscle -->
                        <td class="px-5 py-4 text-slate-600">
                            {{ $exercise->muscle_group->name }}
                        </td>

                        <!-- Type -->
                        <td class="px-5 py-4 text-slate-600">
                            {{ $exercise->type->name }}
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-4 text-end">
                            <div class="inline-flex items-center gap-3 text-sm">

                                <a href="{{ route('admin.exercises.edit', $exercise) }}"
                                   class="font-medium text-emerald-700 transition hover:text-emerald-800">
                                    {{ __('Edit') }}
                                </a>

                                <form method="post"
                                      action="{{ route('admin.exercises.destroy', $exercise) }}"
                                      onsubmit="return confirm(@json(__('Delete this exercise?')));">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="font-medium text-red-600 transition hover:text-red-700">
                                        {{ __('Delete') }}
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $exercises->links() }}
    </div>

</div>
@endsection
