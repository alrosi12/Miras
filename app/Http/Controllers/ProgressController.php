<?php

namespace App\Http\Controllers;

use App\Models\BodyMeasurement;
use App\Models\WorkoutSession;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $measurements = BodyMeasurement::query()
            ->where('user_id', $userId)
            ->orderBy('date')
            ->take(90)
            ->get(['date', 'weight', 'body_fat', 'waist']);

        $sessionsByWeek = WorkoutSession::query()
            ->where('user_id', $userId)
            ->where('date', '>=', now()->subWeeks(12)->toDateString())
            ->orderBy('date')
            ->get(['date'])
            ->groupBy(fn (WorkoutSession $s) => $s->date->copy()->startOfWeek()->toDateString())
            ->map->count();

        return view('progress.index', compact('measurements', 'sessionsByWeek'));
    }
}
