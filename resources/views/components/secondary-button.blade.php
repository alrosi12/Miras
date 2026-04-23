<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-white shadow-sm shadow-red-900/10 transition duration-200 ease-out hover:bg-red-500 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 focus-visible:ring-offset-2 active:scale-[0.98] active:bg-red-700 disabled:pointer-events-none disabled:opacity-60'
]) }}>
    {{ $slot }}
</button>
