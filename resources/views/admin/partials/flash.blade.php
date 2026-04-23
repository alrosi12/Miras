@if (session('status'))
    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-800 shadow-sm shadow-emerald-900/[0.04] ring-1 ring-emerald-100/80">
        {{ session('status') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3.5 text-sm text-red-800 shadow-sm shadow-red-900/[0.04] ring-1 ring-red-100/80">
        {{ session('error') }}
    </div>
@endif
