@extends('layouts.admin')

@section('title', __('Admin dashboard'))

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">
            {{ __('Admin dashboard') }}
        </h1>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card
            :label="__('Total users')"
            :value="number_format($totalUsers)"
            accent="sky"
        />
        <x-stat-card
            :label="__('New users this month')"
            :value="number_format($newUsersThisMonth)"
        />
        <x-stat-card
            :label="__('Total sessions')"
            :value="number_format($totalSessions)"
            accent="violet"
        />
        <x-stat-card
            :label="__('Public catalog exercises')"
            :value="number_format($totalGlobalPublicExercises)"
            accent="amber"
        />
    </div>

    <!-- Charts -->
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="h-[320px]">
            <x-chart-card
                :title="__('New users (last 6 months)')"
                chart-id="chartNewUsers"
            />
        </div>

        <div class="h-[320px]">
            <x-chart-card
                :title="__('Sessions (last 6 months)')"
                chart-id="chartSessions"
            />
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const newUsersData = @json($newUsersChart);
        const sessionsData = @json($sessionsChart);

        new Chart(document.getElementById('chartNewUsers'), {
            type: 'line',
            data: newUsersData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: { padding: 12 }
                    }
                },
                layout: {
                    padding: 10
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
            },
        });

        new Chart(document.getElementById('chartSessions'), {
            type: 'bar',
            data: sessionsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: { padding: 12 }
                    }
                },
                layout: {
                    padding: 10
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
            },
        });

    });
</script>
@endpush
