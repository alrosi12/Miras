@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full px-4 py-2.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm placeholder:text-slate-400 transition duration-200 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-60 disabled:cursor-not-allowed'
    ]) }}
>
