<?php

namespace App\Http\Controllers;

use App\Models\BodyMeasurement;
use App\Models\Exercise;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $stats = [
            'exercises_count' => Exercise::query()->visibleTo($user->id)->count(),
            'plans_count' => WorkoutPlan::query()->where('user_id', $user->id)->count(),
            'sessions_this_month' => WorkoutSession::query()
                ->where('user_id', $user->id)
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->count(),
            'last_session' => WorkoutSession::query()
                ->where('user_id', $user->id)
                ->latest('date')
                ->first(),
            'latest_measurement' => BodyMeasurement::query()
                ->where('user_id', $user->id)
                ->latest('date')
                ->first(),
        ];

        return view('dashboard', compact('stats'));
    }
}
