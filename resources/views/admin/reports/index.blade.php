@extends('layouts.admin')

@section('title', __('Reports'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-slate-900">{{ __('Reports') }}</h1>

        <div class="mb-6">
            <a href="{{ route('admin.reports.export-users', request()->only('q')) }}"
               class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                {{ __('Export users CSV') }}
            </a>
            <p class="mt-2 text-sm text-slate-600">{{ __('Optional: append ?q=search to filter the export by name or email.') }}</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="font-semibold text-slate-900 mb-4">{{ __('User registrations (12 months)') }}</h3>
                <canvas id="chartUsersReport" height="140"></canvas>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h3 class="font-semibold text-slate-900 mb-4">{{ __('Workout sessions (12 months)') }}</h3>
                <canvas id="chartSessionsReport" height="140"></canvas>
            </div>
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const usersUrl = @json(route('admin.reports.users'));
            const sessionsUrl = @json(route('admin.reports.sessions'));

            const [usersPayload, sessionsPayload] = await Promise.all([
                fetch(usersUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()),
                fetch(sessionsUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()),
            ]);

            new Chart(document.getElementById('chartUsersReport'), {
                type: 'bar',
                data: usersPayload,
                options: {
                    responsive: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            });

            new Chart(document.getElementById('chartSessionsReport'), {
                type: 'bar',
                data: sessionsPayload,
                options: {
                    responsive: true,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            });
        });
    </script>
@endpush
