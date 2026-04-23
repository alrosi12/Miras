<a {{ $attributes->merge([
    'class' => 'block w-full rounded-lg px-4 py-2.5 text-start text-sm text-slate-700 transition duration-150 ease-out hover:bg-slate-50 focus:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-slate-200/80'
]) }}>
    {{ $slot }}
</a>
