<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function index(): View
    {
        Gate::authorize('admin');

        return view('admin.reports.index');
    }

    /**
     * تقرير المستخدمين: تسجيلات جديدة لكل شهر (آخر 12 شهراً) — JSON لـ Chart.js.
     */
    public function usersReport(): JsonResponse
    {
        Gate::authorize('admin');

        $labels = [];
        $data = [];

        foreach (range(11, 0) as $i) {
            $month = now()->subMonths($i)->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $labels[] = $month->format('Y-m');
            $data[] = User::query()
                ->whereBetween('created_at', [$month->startOfDay(), $end->endOfDay()])
                ->count();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('New user registrations'),
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                ],
            ],
        ]);
    }

    /**
     * تقرير الجلسات: عدد الجلسات حسب شهر التمرين (آخر 12 شهراً).
     */
    public function sessionsReport(): JsonResponse
    {
        Gate::authorize('admin');

        $labels = [];
        $data = [];

        foreach (range(11, 0) as $i) {
            $month = now()->subMonths($i)->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $labels[] = $month->format('Y-m');
            $data[] = WorkoutSession::query()
                ->whereBetween('date', [$month->toDateString(), $end->toDateString()])
                ->count();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('Workout sessions'),
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                ],
            ],
        ]);
    }

    /**
     * تصدير قائمة المستخدمين إلى CSV (استجابة Laravel / Symfony stream).
     */
    public function exportUsers(Request $request): StreamedResponse
    {
        Gate::authorize('admin');

        $filename = 'users-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($request): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'name', 'email', 'is_admin', 'email_verified_at', 'created_at']);

            $query = User::query()->orderBy('id');

            if ($request->filled('q')) {
                $term = '%'.$request->query('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
                });
            }

            $query->chunk(500, function ($users) use ($out): void {
                foreach ($users as $u) {
                    fputcsv($out, [
                        $u->id,
                        $u->name,
                        $u->email,
                        $u->is_admin ? '1' : '0',
                        $u->email_verified_at?->toIso8601String() ?? '',
                        $u->created_at?->toIso8601String() ?? '',
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
