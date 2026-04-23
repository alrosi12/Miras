@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-900 shadow-sm shadow-rose-900/[0.04]']) }} role="alert">
        <p class="mb-2 font-semibold tracking-tight">{{ __('Please fix the following:') }}</p>
        <ul class="space-y-1.5">
            @foreach ($errors->all() as $error)
                <li class="flex items-start gap-2.5 leading-relaxed">
                    <span class="mt-0.5 shrink-0 text-rose-400">•</span>
                    <span>{{ $error }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-900 shadow-sm shadow-emerald-900/[0.04]']) }} role="status">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-900 shadow-sm shadow-rose-900/[0.04]']) }} role="alert">
        {{ session('error') }}
    </div>
@endif
