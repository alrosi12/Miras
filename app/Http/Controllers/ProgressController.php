<?php

namespace App\Http\Controllers;

use App\Enums\MuscleGroup;
use App\Models\BodyMeasurement;
use App\Models\Exercise;
use App\Models\SessionSet;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProgressController extends Controller
{
    /**
     * صفحة التقدم: PRs، آخر قياسات، ومقارنة شهر/شهر بصيغة Chart.js.
     */
    public function index(): View
    {
        $userId = (int) auth()->id();

        $personalRecords = $this->aggregatePersonalRecords($userId);

        $latestMeasurements = BodyMeasurement::query()
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $monthCompareChart = $this->monthComparisonChart($userId, now()->startOfMonth());

        return view('progress.index', [
            'personalRecords' => $personalRecords,
            'latestMeasurements' => $latestMeasurements,
            'monthCompareChart' => $monthCompareChart,
        ]);
    }

    /**
     * تقدم تمرين محدد: أعلى وزن لكل جلسة + أعلى عدد تكرارات؛ آخر 30 جلسة؛ بيانات Chart.js.
     */
    public function exercise(Exercise $exercise): View
    {
        $this->authorize('view', $exercise);

        $userId = (int) auth()->id();

        $sessions = WorkoutSession::query()
            ->where('user_id', $userId)
            ->whereHas('sessionSets', fn ($q) => $q->where('exercise_id', $exercise->id))
            ->with([
                'sessionSets' => fn ($q) => $q->where('exercise_id', $exercise->id)->orderBy('set_number'),
            ])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->sortBy(fn (WorkoutSession $s) => [$s->date->timestamp, $s->id])
            ->values();

        $labels = $sessions->map(fn (WorkoutSession $s) => $s->date->toDateString())->all();

        $maxWeightPerSession = $sessions->map(function (WorkoutSession $s) use ($exercise) {
            $max = $s->sessionSets
                ->where('exercise_id', $exercise->id)
                ->filter(fn (SessionSet $st) => $st->weight !== null)
                ->max('weight');

            return $max !== null ? (float) $max : null;
        })->all();

        $maxRepsPerSession = $sessions->map(function (WorkoutSession $s) use ($exercise) {
            $max = $s->sessionSets
                ->where('exercise_id', $exercise->id)
                ->filter(fn (SessionSet $st) => $st->reps !== null)
                ->max('reps');

            return $max !== null ? (int) $max : null;
        })->all();

        $exerciseChart = $this->chartPayload(
            $labels,
            [
                [
                    'label' => __('Max weight (per session)'),
                    'data' => $maxWeightPerSession,
                    'fill' => false,
                    'tension' => 0.2,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => __('Max reps (per session)'),
                    'data' => $maxRepsPerSession,
                    'fill' => false,
                    'tension' => 0.2,
                    'yAxisID' => 'y1',
                ],
            ]
        );

        return view('progress.exercise', [
            'exercise' => $exercise,
            'sessions' => $sessions,
            'exerciseChart' => $exerciseChart,
        ]);
    }

    /**
     * تاريخ القياسات الكامل + بيانات Chart.js (weight, body_fat, chest, waist, arms).
     */
    public function measurements(): View
    {
        $userId = (int) auth()->id();

        $measurements = BodyMeasurement::query()
            ->where('user_id', $userId)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $measurementsChart = $this->measurementsChartPayload($measurements);

        return view('progress.measurements', [
            'measurements' => $measurements,
            'measurementsChart' => $measurementsChart,
        ]);
    }

    /**
     * تقرير شهري: جلسات ودقائق لكل أسبوع داخل الشهر + تكرار العضلات؛ جاهز لـ Chart.js.
     */
    public function monthly(Request $request): View
    {
        $userId = (int) auth()->id();

        $monthInput = $request->query('month', now()->format('Y-m'));
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Throwable) {
            $monthStart = now()->copy()->startOfMonth();
        }

        $monthEnd = $monthStart->copy()->endOfMonth();

        $sessions = WorkoutSession::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->with(['sessionSets.exercise:id,muscle_group'])
            ->orderBy('date')
            ->get();

        $byWeek = $sessions->groupBy(
            fn (WorkoutSession $s) => $s->date->copy()->startOfWeek(Carbon::MONDAY)->toDateString()
        )->sortKeys();

        $weekLabels = $byWeek->keys()->values()->map(function (string $weekStart) {
            $d = Carbon::parse($weekStart);

            return $d->translatedFormat('M j').' – '.$d->copy()->endOfWeek(Carbon::SUNDAY)->translatedFormat('M j');
        })->all();

        $sessionsPerWeek = $byWeek->map->count()->values()->all();
        $minutesPerWeek = $byWeek->map(fn (Collection $weekSessions) => (int) $weekSessions->sum(
            fn (WorkoutSession $s) => $s->duration_minutes ?? 0
        ))->values()->all();

        $weeklySessionsChart = $this->chartPayload(
            $weekLabels,
            [
                [
                    'label' => __('Sessions'),
                    'data' => $sessionsPerWeek,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.45)',
                ],
            ]
        );

        $weeklyMinutesChart = $this->chartPayload(
            $weekLabels,
            [
                [
                    'label' => __('Minutes'),
                    'data' => $minutesPerWeek,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.45)',
                ],
            ]
        );

        $muscleCounts = $sessions
            ->pluck('sessionSets')
            ->flatten()
            ->filter(fn (SessionSet $set) => $set->relationLoaded('exercise') && $set->exercise !== null && $set->exercise->muscle_group !== null)
            ->groupBy(fn (SessionSet $set) => $set->exercise->muscle_group->value)
            ->map->count()
            ->sortDesc();

        $muscleLabels = $muscleCounts->keys()->map(function (string $key) {
            $enum = MuscleGroup::tryFrom($key);

            return $enum !== null ? $enum->name : $key;
        })->values()->all();
        $muscleData = $muscleCounts->values()->all();

        $muscleFrequencyChart = $this->chartPayload(
            $muscleLabels,
            [
                [
                    'label' => __('Sets per muscle group'),
                    'data' => $muscleData,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.55)',
                ],
            ]
        );

        return view('progress.monthly', [
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'sessions' => $sessions,
            'weeklySessionsChart' => $weeklySessionsChart,
            'weeklyMinutesChart' => $weeklyMinutesChart,
            'muscleFrequencyChart' => $muscleFrequencyChart,
            'muscleCounts' => $muscleCounts,
        ]);
    }

    /**
     * مقارنة شهرين (month1, month2) — JSON فقط للـ AJAX.
     */
    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month1' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'month2' => ['required', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        try {
            $m1 = Carbon::createFromFormat('Y-m', $validated['month1'])->startOfMonth();
            $m2 = Carbon::createFromFormat('Y-m', $validated['month2'])->startOfMonth();
        } catch (\Throwable) {
            return response()->json(['message' => __('Invalid month format.')], 422);
        }

        $userId = (int) auth()->id();

        $stats1 = $this->monthStatsFromSessions($userId, $m1);
        $stats2 = $this->monthStatsFromSessions($userId, $m2);

        $label1 = $m1->translatedFormat('F Y');
        $label2 = $m2->translatedFormat('F Y');

        $chart = $this->chartPayload(
            [$label1, $label2],
            [
                ['label' => __('Sessions'), 'data' => [$stats1['sessions_count'], $stats2['sessions_count']], 'backgroundColor' => 'rgba(16, 185, 129, 0.5)'],
                ['label' => __('Minutes'), 'data' => [$stats1['total_minutes'], $stats2['total_minutes']], 'backgroundColor' => 'rgba(59, 130, 246, 0.5)'],
                ['label' => __('Distinct exercises'), 'data' => [$stats1['distinct_exercises'], $stats2['distinct_exercises']], 'backgroundColor' => 'rgba(245, 158, 11, 0.5)'],
            ]
        );

        return response()->json([
            'chart' => $chart,
            'month1' => array_merge(['label' => $label1, 'key' => $m1->format('Y-m')], $stats1),
            'month2' => array_merge(['label' => $label2, 'key' => $m2->format('Y-m')], $stats2),
        ]);
    }

    /**
     * @return Collection<int, object{
     *     exercise: Exercise,
     *     max_weight: float|null,
     *     max_reps: int|null
     * }>
     */
    protected function aggregatePersonalRecords(int $userId): Collection
    {
        $sets = SessionSet::query()
            ->whereHas('workoutSession', fn ($q) => $q->where('user_id', $userId))
            ->with('exercise:id,name,muscle_group')
            ->get(['exercise_id', 'weight', 'reps']);

        return $sets
            ->groupBy('exercise_id')
            ->map(function (Collection $group) {
                /** @var Exercise|null $exercise */
                $exercise = $group->first()?->exercise;
                if ($exercise === null) {
                    return null;
                }

                $maxWeight = $group->filter(fn (SessionSet $s) => $s->weight !== null)->max('weight');
                $maxReps = $group->filter(fn (SessionSet $s) => $s->reps !== null)->max('reps');

                return (object) [
                    'exercise' => $exercise,
                    'max_weight' => $maxWeight !== null ? (float) $maxWeight : null,
                    'max_reps' => $maxReps !== null ? (int) $maxReps : null,
                ];
            })
            ->filter()
            ->sortByDesc(fn ($row) => $row->max_weight ?? 0)
            ->values();
    }

    /**
     * @return array{labels: array<int, string>, datasets: array<int, array<string, mixed>>}
     */
    protected function monthComparisonChart(int $userId, Carbon $thisMonthStart): array
    {
        $thisMonth = $thisMonthStart->copy()->startOfMonth();
        $prevMonth = $thisMonth->copy()->subMonth()->startOfMonth();

        $thisStats = $this->monthStatsFromSessions($userId, $thisMonth);
        $prevStats = $this->monthStatsFromSessions($userId, $prevMonth);

        $labelThis = $thisMonth->translatedFormat('F Y');
        $labelPrev = $prevMonth->translatedFormat('F Y');

        return $this->chartPayload(
            [$labelThis, $labelPrev],
            [
                [
                    'label' => __('Sessions'),
                    'data' => [$thisStats['sessions_count'], $prevStats['sessions_count']],
                    'backgroundColor' => 'rgba(16, 185, 129, 0.45)',
                ],
                [
                    'label' => __('Minutes'),
                    'data' => [$thisStats['total_minutes'], $prevStats['total_minutes']],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.45)',
                ],
                [
                    'label' => __('Distinct exercises'),
                    'data' => [$thisStats['distinct_exercises'], $prevStats['distinct_exercises']],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.45)',
                ],
            ]
        );
    }

    /**
     * @return array{sessions_count: int, total_minutes: int, distinct_exercises: int}
     */
    protected function monthStatsFromSessions(int $userId, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $sessions = WorkoutSession::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->with('sessionSets:id,workout_session_id,exercise_id')
            ->get();

        $distinctExercises = $sessions
            ->pluck('sessionSets')
            ->flatten()
            ->pluck('exercise_id')
            ->filter()
            ->unique()
            ->count();

        return [
            'sessions_count' => $sessions->count(),
            'total_minutes' => (int) $sessions->sum(fn (WorkoutSession $s) => $s->duration_minutes ?? 0),
            'distinct_exercises' => $distinctExercises,
        ];
    }

    /**
     * @param  Collection<int, BodyMeasurement>  $measurements
     * @return array{labels: array<int, string>, datasets: array<int, array<string, mixed>>}
     */
    protected function measurementsChartPayload(Collection $measurements): array
    {
        $labels = $measurements->map(fn (BodyMeasurement $m) => $m->date->toDateString())->all();

        $metric = function (string $attr) use ($measurements) {
            return $measurements->map(fn (BodyMeasurement $m) => $m->{$attr} !== null ? (float) $m->{$attr} : null)->all();
        };

        return $this->chartPayload(
            $labels,
            [
                ['label' => __('Weight'), 'data' => $metric('weight'), 'fill' => false, 'tension' => 0.2],
                ['label' => __('Body fat %'), 'data' => $metric('body_fat'), 'fill' => false, 'tension' => 0.2],
                ['label' => __('Chest'), 'data' => $metric('chest'), 'fill' => false, 'tension' => 0.2],
                ['label' => __('Waist'), 'data' => $metric('waist'), 'fill' => false, 'tension' => 0.2],
                ['label' => __('Arms'), 'data' => $metric('arms'), 'fill' => false, 'tension' => 0.2],
            ]
        );
    }

    /**
     * هيكل موحّد لـ Chart.js (labels + datasets).
     *
     * @param  array<int, string|float|int|null>  $labels
     * @param  array<int, array<string, mixed>>  $datasets
     * @return array{labels: array<int, string|float|int|null>, datasets: array<int, array<string, mixed>>}
     */
    protected function chartPayload(array $labels, array $datasets): array
    {
        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
