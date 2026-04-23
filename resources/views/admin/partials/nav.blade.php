{{-- شريط تنقل فرعي داخل لوحة الإدارة --}}
<nav class="mb-8 flex flex-wrap gap-2 border-b border-slate-200 pb-5 text-sm font-medium">
    <a href="{{ route('admin.dashboard') }}"
       class="rounded-lg px-3.5 py-2 transition duration-200 ease-out @if (request()->routeIs('admin.dashboard')) bg-slate-900 text-white shadow-sm @else text-slate-700 hover:bg-slate-100 @endif">
        {{ __('Admin dashboard') }}
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="rounded-lg px-3.5 py-2 transition duration-200 ease-out @if (request()->routeIs('admin.users.*')) bg-slate-900 text-white shadow-sm @else text-slate-700 hover:bg-slate-100 @endif">
        {{ __('Users') }}
    </a>
    <a href="{{ route('admin.exercises.index') }}"
       class="rounded-lg px-3.5 py-2 transition duration-200 ease-out @if (request()->routeIs('admin.exercises.*')) bg-slate-900 text-white shadow-sm @else text-slate-700 hover:bg-slate-100 @endif">
        {{ __('Global exercises') }}
    </a>
    <a href="{{ route('admin.reports.index') }}"
       class="rounded-lg px-3.5 py-2 transition duration-200 ease-out @if (request()->routeIs('admin.reports.*')) bg-slate-900 text-white shadow-sm @else text-slate-700 hover:bg-slate-100 @endif">
        {{ __('Reports') }}
    </a>
    <a href="{{ route('dashboard') }}" class="rounded-lg px-3.5 py-2 text-emerald-700 transition duration-200 ease-out hover:bg-emerald-50">
        {{ __('App dashboard') }}
    </a>
</nav>
