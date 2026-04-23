<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg border border-transparent bg-emerald-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-white shadow-sm shadow-emerald-900/10 transition duration-200 ease-out hover:bg-emerald-700 hover:shadow-md focus:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 active:scale-[0.98] active:bg-emerald-800 disabled:pointer-events-none disabled:opacity-60']) }}>
    {{ $slot }}
</button>
