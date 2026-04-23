<?php

namespace App\Http\Controllers;

use App\Models\BodyMeasurement;
use App\Models\Exercise;
use App\Models\Friendship;
use App\Models\SessionSet;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanDay;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** @var array<int, array{current: int, best: int}> */
    protected array $streakCache = [];

    public function index(): View
    {
        $user = auth()->user();

        $weekAnchor = now();
        $weekThisStart = $weekAnchor->copy()->startOfWeek(Carbon::MONDAY);
        $weekPrevStart = $weekThisStart->copy()->subWeek();

        $weekThis = $this->getWeeklyStats($user, $weekThisStart);
        $weekPrev = $this->getWeeklyStats($user, $weekPrevStart);

        $weekCompare = $this->compareWeeklyStats($weekThis, $weekPrev);

        $streakCurrent = $this->calculateStreak($user);
        $streakBest = $this->calculateBestStreak($user);

        $lastSessions = WorkoutSession::query()
            ->where('user_id', $user->id)
            ->with([
                'workoutPlan:id,name',
                'sessionSets' => fn ($q) => $q->orderBy('set_number'),
                'sessionSets.exercise:id,name',
            ])
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get();

        $chartLast7Days = $this->chartLast7Days($user);

        $bodyWeightMonth = $this->bodyWeightMonthComparison($user);

        $bigThreeProgress = $this->bigThreeProgress($user);

        $todaysRoutine = $this->resolveRoutineForDate((int) $user->id, now());

        $latestMeasurement = BodyMeasurement::query()
            ->where('user_id', $user->id)
            ->whereNotNull('weight')
            ->latest('date')
            ->latest('id')
            ->first();

        $friendsSessions = $this->friendsLatestSessions($user, 3);

        $weekCompletion = $this->weekCompletionDays($user, $weekThisStart);

        return view('dashboard.index', [
            'weekThis' => $weekThis,
            'weekPrev' => $weekPrev,
            'weekCompare' => $weekCompare,
            'streakCurrent' => $streakCurrent,
            'streakBest' => $streakBest,
            'lastSessions' => $lastSessions,
            'todaysRoutine' => $todaysRoutine,
            'latestMeasurement' => $latestMeasurement,
            'friendsSessions' => $friendsSessions,
            'weekCompletion' => $weekCompletion,
            'chartLast7Days' => $chartLast7Days,
            'bodyWeightMonth' => $bodyWeightMonth,
            'bigThreeProgress' => $bigThreeProgress,
        ]);
    }

    /**
     * أيام الأسبوع الحالي (إثنين→أحد) مع علامة إن وُجدت جلسة في ذلك اليوم.
     *
     * @return array<int, array{label: string, date: string, completed: bool}>
     */
    protected function weekCompletionDays(User $user, Carbon $weekMondayStart): array
    {
        $start = $weekMondayStart->copy()->startOfDay();
        $end = $weekMondayStart->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $datesWithSession = WorkoutSession::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->flip();

        $out = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekMondayStart->copy()->addDays($i);
            $ds = $day->toDateString();
            $out[] = [
                'label' => $day->translatedFormat('D'),
                'date' => $ds,
                'completed' => $datesWithSession->has($ds),
            ];
        }

        return $out;
    }

    /**
     * الـ streak الحالي: أيام متتالية من **اليوم** للخلف؛ يوم بدون جلسة = يوم راحة يقطع السلسلة.
     */
    public function calculateStreak(User $user): int
    {
        return $this->streakCounts($user)['current'];
    }

    /**
     * أطول سلسلة أيام متتالية (كل يوم فيها جلسة واحدة على الأقل) في تاريخ المستخدم.
     */
    public function calculateBestStreak(User $user): int
    {
        return $this->streakCounts($user)['best'];
    }

    /**
     * إحصائيات أسبوع تقويمي واحد (إثنين → أحد) يحتوي التاريخ الممرَّر.
     *
     * @return array{
     *     week_start: Carbon,
     *     week_end: Carbon,
     *     sessions_count: int,
     *     total_minutes: int,
     *     distinct_exercises_count: int
     * }
     */
    public function getWeeklyStats(User $user, Carbon $week): array
    {
        $start = $week->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = $week->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $sessionsInWeek = fn () => WorkoutSession::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        $sessionsCount = $sessionsInWeek()->count();
        $totalMinutes = (int) ($sessionsInWeek()->sum('duration_minutes') ?? 0);

        $sessionIds = $sessionsInWeek()->pluck('id');

        $distinctExercises = 0;
        if ($sessionIds->isNotEmpty()) {
            $distinctExercises = SessionSet::query()
                ->whereIn('workout_session_id', $sessionIds)
                ->pluck('exercise_id')
                ->unique()
                ->count();
        }

        return [
            'week_start' => $start,
            'week_end' => $end,
            'sessions_count' => $sessionsCount,
            'total_minutes' => $totalMinutes,
            'distinct_exercises_count' => $distinctExercises,
        ];
    }

    /**
     * @param  array{sessions_count: int, total_minutes: int, distinct_exercises_count: int}  $current
     * @param  array{sessions_count: int, total_minutes: int, distinct_exercises_count: int}  $previous
     * @return array{
     *     sessions_pct: float|null,
     *     minutes_pct: float|null,
     *     exercises_pct: float|null
     * }
     */
    protected function compareWeeklyStats(array $current, array $previous): array
    {
        return [
            'sessions_pct' => $this->percentChange($previous['sessions_count'], $current['sessions_count']),
            'minutes_pct' => $this->percentChange($previous['total_minutes'], $current['total_minutes']),
            'exercises_pct' => $this->percentChange($previous['distinct_exercises_count'], $current['distinct_exercises_count']),
        ];
    }

    /** نسبة التغيير مقارنة بالأسبوع السابق؛ null إذا لا يمكن الحساب (قيمة سابقة = 0 والحالية > 0). */
    protected function percentChange(int|float $previous, int|float $current): ?float
    {
        if ($previous == 0) {
            return $current == 0 ? 0.0 : null;
        }

        return (($current - $previous) / $previous) * 100.0;
    }

    /**
     * @return array{current: int, best: int}
     */
    protected function streakCounts(User $user): array
    {
        $key = (int) $user->id;
        if (isset($this->streakCache[$key])) {
            return $this->streakCache[$key];
        }

        $dates = WorkoutSession::query()
            ->where('user_id', $user->id)
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->sort()
            ->values();

        $lookup = $dates->flip();

        $current = 0;
        $cursor = now()->toDateString();
        while ($lookup->has($cursor)) {
            $current++;
            $cursor = Carbon::parse($cursor)->subDay()->toDateString();
        }

        $best = 0;
        if ($dates->isNotEmpty()) {
            $run = 1;
            $best = 1;
            $prev = Carbon::parse($dates->first())->startOfDay();
            for ($i = 1; $i < $dates->count(); $i++) {
                $day = Carbon::parse($dates[$i])->startOfDay();
                if ($day->equalTo($prev->copy()->addDay())) {
                    $run++;
                } else {
                    $run = 1;
                }
                $best = max($best, $run);
                $prev = $day;
            }
        }

        return $this->streakCache[$key] = [
            'current' => $current,
            'best' => $best,
        ];
    }

    /**
     * آخر جلسة مسجّلة لكل صديق (حد أقصى) — مرتبة من الأحدث للأقدم.
     *
     * @return Collection<int, WorkoutSession>
     */
    protected function friendsLatestSessions(User $user, int $limit = 5): Collection
    {
        $friendIds = Friendship::query()
            ->accepted()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('friend_id', $user->id);
            })
            ->get(['user_id', 'friend_id'])
            ->map(fn (Friendship $f) => $f->user_id === $user->id ? $f->friend_id : $f->user_id)
            ->unique()
            ->values();

        if ($friendIds->isEmpty()) {
            return collect();
        }

        $sessions = collect();
        foreach ($friendIds as $friendId) {
            $session = WorkoutSession::query()
                ->where('user_id', (int) $friendId)
                ->with(['user:id,name,avatar', 'workoutPlan:id,name'])
                ->latest('date')
                ->latest('id')
                ->first();

            if ($session !== null) {
                $sessions->push($session);
            }
        }

        return $sessions
            ->sortByDesc(fn (WorkoutSession $s) => sprintf('%s-%09d', $s->date->toDateString(), $s->id))
            ->take($limit)
            ->values();
    }

    /**
     * آخر 7 أيام (من اليوم للخلف): عدد الجلسات ومجموع الدقائق لكل يوم.
     *
     * @return list<array{date: string, label: string, sessions_count: int, total_minutes: int}>
     */
    protected function chartLast7Days(User $user): array
    {
        $end = now()->startOfDay();
        $start = $end->copy()->subDays(6);

        $rows = WorkoutSession::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date, COUNT(*) as sessions_count, COALESCE(SUM(duration_minutes), 0) as total_minutes')
            ->groupBy('date')
            ->get()
            ->keyBy(fn ($r) => Carbon::parse($r->date)->toDateString());

        $out = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $end->copy()->subDays($i);
            $ds = $day->toDateString();
            $row = $rows->get($ds);
            $out[] = [
                'date' => $ds,
                'label' => $day->translatedFormat('D j'),
                'sessions_count' => $row ? (int) $row->sessions_count : 0,
                'total_minutes' => $row ? (int) $row->total_minutes : 0,
            ];
        }

        return $out;
    }

    /**
     * أحدث وزن مع مقارنة تقريبية لشهر (أقرب قياس قبل ~28 يوماً من تاريخ آخر قياس).
     *
     * @return array{latest: BodyMeasurement, previous: BodyMeasurement|null, delta_kg: float|null}|null
     */
    protected function bodyWeightMonthComparison(User $user): ?array
    {
        $latest = BodyMeasurement::query()
            ->where('user_id', $user->id)
            ->whereNotNull('weight')
            ->latest('date')
            ->latest('id')
            ->first();

        if ($latest === null) {
            return null;
        }

        $anchor = Carbon::parse($latest->date)->subDays(28)->startOfDay();

        $previous = BodyMeasurement::query()
            ->where('user_id', $user->id)
            ->whereNotNull('weight')
            ->where('date', '<=', $anchor->toDateString())
            ->where('id', '<>', $latest->id)
            ->latest('date')
            ->latest('id')
            ->first();

        $delta = null;
        if ($previous !== null) {
            $delta = round((float) $latest->weight - (float) $previous->weight, 1);
        }

        return [
            'latest' => $latest,
            'previous' => $previous,
            'delta_kg' => $delta,
        ];
    }

    /**
     * أقصى وزن مسجّل لتمرين في نطاق تواريخ (جلسات المستخدم).
     */
    protected function maxLiftKgInRange(User $user, int $exerciseId, Carbon $from, Carbon $to): ?float
    {
        $max = SessionSet::query()
            ->where('exercise_id', $exerciseId)
            ->whereNotNull('weight')
            ->whereHas('workoutSession', function ($q) use ($user, $from, $to) {
                $q->where('user_id', $user->id)
                    ->whereBetween('date', [$from->toDateString(), $to->toDateString()]);
            })
            ->max('weight');

        return $max !== null ? (float) $max : null;
    }

    /**
     * Bench / Squat / Deadlift: أقصى وزن هذا الشهر مقابل الشهر التقويمي السابق.
     *
     * @return list<array{key: string, label: string, current_kg: float|null, previous_kg: float|null, delta_kg: float|null}>
     */
    protected function bigThreeProgress(User $user): array
    {
        $defs = [
            ['key' => 'bench', 'label' => 'Bench Press', 'match' => ['Bench Press', 'Barbell Bench Press']],
            ['key' => 'squat', 'label' => 'Squat', 'match' => ['Squat', 'Barbell Back Squat']],
            ['key' => 'deadlift', 'label' => 'Deadlift', 'match' => ['Deadlift', 'Conventional Deadlift']],
        ];

        $thisStart = now()->copy()->startOfMonth()->startOfDay();
        $thisEnd = now()->copy()->endOfMonth()->endOfDay();
        $prevStart = now()->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay();
        $prevEnd = now()->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay();

        $out = [];
        foreach ($defs as $def) {
            $exerciseId = null;
            foreach ($def['match'] as $name) {
                $id = Exercise::query()
                    ->whereNull('user_id')
                    ->where('is_public', true)
                    ->where('name', $name)
                    ->value('id');
                if ($id !== null) {
                    $exerciseId = (int) $id;
                    break;
                }
            }

            if ($exerciseId === null) {
                $out[] = [
                    'key' => $def['key'],
                    'label' => $def['label'],
                    'current_kg' => null,
                    'previous_kg' => null,
                    'delta_kg' => null,
                ];

                continue;
            }

            $current = $this->maxLiftKgInRange($user, $exerciseId, $thisStart, $thisEnd);
            $previous = $this->maxLiftKgInRange($user, $exerciseId, $prevStart, $prevEnd);
            $delta = ($current !== null && $previous !== null) ? round($current - $previous, 1) : null;

            $out[] = [
                'key' => $def['key'],
                'label' => $def['label'],
                'current_kg' => $current,
                'previous_kg' => $previous,
                'delta_kg' => $delta,
            ];
        }

        return $out;
    }

    /**
     * أول خطة للمستخدم فيها يوم يطابق التاريخ (نفس منطق جلسات التمرين).
     *
     * @return array{plan: WorkoutPlan, day: WorkoutPlanDay}|null
     */
    protected function resolveRoutineForDate(int $userId, CarbonInterface $date): ?array
    {
        $plans = WorkoutPlan::query()
            ->where('user_id', $userId)
            ->with([
                'workoutPlanDays.planDayExercises' => fn ($q) => $q->orderBy('order'),
                'workoutPlanDays.planDayExercises.exercise:id,name',
            ])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($plans as $plan) {
            $day = $this->findPlanDayForDate($plan, $date);
            if ($day !== null) {
                return ['plan' => $plan, 'day' => $day];
            }
        }

        return null;
    }

    /**
     * مطابقة يوم الخطة مع يوم الأسبوع: اسم اليوم (EN / locale) أو رقم 1–7 أو ترتيب order.
     */
    protected function findPlanDayForDate(WorkoutPlan $plan, CarbonInterface $date): ?WorkoutPlanDay
    {
        $english = mb_strtolower($date->copy()->locale('en')->translatedFormat('l'));
        $localized = mb_strtolower($date->translatedFormat('l'));
        $iso = (int) $date->dayOfWeekIso;

        foreach ($plan->workoutPlanDays as $day) {
            $name = mb_strtolower(trim($day->day_name));

            if ($name === $english || $name === $localized) {
                return $day;
            }

            if (is_numeric($day->day_name) && (int) $day->day_name === $iso) {
                return $day;
            }

            if ((int) $day->order === $iso) {
                return $day;
            }
        }

        return null;
    }
}
