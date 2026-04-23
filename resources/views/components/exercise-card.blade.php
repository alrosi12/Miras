@props(['exercise', 'href' => null])

@php
    $url = $href ?? route('exercises.show', $exercise);
@endphp

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/[0.03] transition duration-200 ease-out hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md hover:shadow-slate-900/[0.06]']) }}>
    <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
        @if ($exercise->image)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($exercise->image) }}" alt="" class="h-full w-full object-cover transition duration-300 ease-out group-hover:scale-[1.03]" />
        @else
            <div class="flex h-full w-full items-center justify-center text-4xl text-slate-300">🏋</div>
        @endif
    </div>
    <div class="flex flex-1 flex-col gap-1 p-5">
        <p class="line-clamp-2 font-semibold leading-snug text-slate-900">{{ $exercise->name }}</p>
        <p class="text-xs font-medium uppercase tracking-wide text-emerald-700">{{ $exercise->muscle_group->name }}</p>
        <p class="text-xs text-slate-500">{{ $exercise->type->name }}</p>
    </div>
</a>
