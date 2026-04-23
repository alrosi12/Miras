<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * لوحة تحكم الإدارة: أرقام ملخّصة + بيانات Chart.js (آخر 6 أشهر).
     */
    public function index(): View
    {
        Gate::authorize('admin');

        $totalUsers = User::query()->count();
        $newUsersThisMonth = User::query()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $totalSessions = WorkoutSession::query()->count();
        $totalGlobalPublicExercises = Exercise::query()
            ->whereNull('user_id')
            ->where('is_public', true)
            ->count();

        $monthLabels = [];
        $newUsersPerMonth = [];
        $sessionsPerMonth = [];

        foreach (range(5, 0) as $i) {
            $month = now()->subMonths($i)->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $monthLabels[] = $month->translatedFormat('M Y');

            $newUsersPerMonth[] = User::query()
                ->whereBetween('created_at', [$month->startOfDay(), $end->endOfDay()])
                ->count();

            $sessionsPerMonth[] = WorkoutSession::query()
                ->whereBetween('date', [$month->toDateString(), $end->toDateString()])
                ->count();
        }

        $newUsersChart = [
            'labels' => $monthLabels,
            'datasets' => [
                [
                    'label' => __('New users'),
                    'data' => $newUsersPerMonth,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.45)',
                ],
            ],
        ];

        $sessionsChart = [
            'labels' => $monthLabels,
            'datasets' => [
                [
                    'label' => __('Sessions'),
                    'data' => $sessionsPerMonth,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.45)',
                ],
            ],
        ];

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'newUsersThisMonth' => $newUsersThisMonth,
            'totalSessions' => $totalSessions,
            'totalGlobalPublicExercises' => $totalGlobalPublicExercises,
            'newUsersChart' => $newUsersChart,
            'sessionsChart' => $sessionsChart,
        ]);
    }
}
