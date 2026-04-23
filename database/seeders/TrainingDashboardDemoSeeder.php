<?php

namespace Database\Seeders;

use App\Enums\ExerciseType;
use App\Enums\FriendshipStatus;
use App\Enums\MuscleGroup;
use App\Enums\UserGoal;
use App\Models\BodyMeasurement;
use App\Models\Exercise;
use App\Models\Friendship;
use App\Models\PlanDayExercise;
use App\Models\SessionSet;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanDay;
use App\Models\WorkoutSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * بيانات تجريبية واقعية للوحة التدريب (لقطات شاشة): ~4 جلسات أسبوعياً، سلسلة أيام، ثلاثية كبرى، مخطط 7 أيام، أصدقاء.
 */
class TrainingDashboardDemoSeeder extends Seeder
{
    private const SEED_NOTE = 'fittrack_screenshot_demo';

    /** @var list<string> */
    private const TARGET_EMAILS = ['test@example.com', 'admin@fittrack.com', 'user@gmail.com'];

    public function run(): void
    {
        $exercises = $this->ensureCatalogExercises();

        foreach (self::TARGET_EMAILS as $email) {
            $user = User::query()->where('email', $email)->first();
            if ($user === null) {
                continue;
            }

            $this->purgeDemoForUser($user);
            $this->seedFriendProfilesAndActivity($user);
            $this->seedBodyMeasurements($user);
            $this->seedLiftHistory($user, $exercises);
            $this->seedStreakAndRollingDays($user, $exercises);
            $this->seedIsoWeekSessions($user, $exercises);
            $this->seedTodaysPlan($user, $exercises);
            $this->ensureMinSessionsThisWeek($user, $exercises);
            $this->seedLastFiveShowcase($user, $exercises);
        }
    }

    /**
     * @param  array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}  $ex
     */
    private function ensureMinSessionsThisWeek(User $user, array $ex): void
    {
        $start = now()->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = now()->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $count = WorkoutSession::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->count();

        if ($count >= 3) {
            return;
        }

        $plan = $this->demoPlan($user, 'FitTrack Demo — Week fill', 'fill');

        for ($d = 0; $d < 7 && $count < 3; $d++) {
            $day = $start->copy()->addDays($d)->startOfDay();
            if ($day->gt(now()->startOfDay())) {
                break;
            }

            $this->createSessionWithSets(
                $user,
                $plan,
                $day,
                28 + $d,
                [
                    [$ex['ohp']->id, 1, 12, 30.0 + $d],
                    [$ex['row']->id, 1, 15, 40.0],
                ],
            );
            $count++;
        }
    }

    /**
     * @return array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}
     */
    private function ensureCatalogExercises(): array
    {
        $defs = [
            'bench' => ['name' => 'Bench Press', 'muscle' => MuscleGroup::Chest],
            'squat' => ['name' => 'Squat', 'muscle' => MuscleGroup::Legs],
            'deadlift' => ['name' => 'Deadlift', 'muscle' => MuscleGroup::Back],
            'row' => ['name' => 'Barbell Row', 'muscle' => MuscleGroup::Back],
            'ohp' => ['name' => 'Overhead Press', 'muscle' => MuscleGroup::Shoulders],
        ];

        $out = [];
        foreach ($defs as $key => $def) {
            $out[$key] = Exercise::query()->firstOrCreate(
                [
                    'user_id' => null,
                    'name' => $def['name'],
                    'is_public' => true,
                ],
                [
                    'description' => null,
                    'muscle_group' => $def['muscle'],
                    'type' => ExerciseType::Strength,
                ],
            );
        }

        return $out;
    }

    private function purgeDemoForUser(User $user): void
    {
        WorkoutSession::query()
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('notes', self::SEED_NOTE)
                    ->orWhereHas('workoutPlan', function ($p) {
                        $p->where('name', 'like', 'FitTrack Demo%')
                            ->orWhere('name', 'Dashboard demo plan');
                    });
            })
            ->delete();

        WorkoutPlan::query()
            ->where('user_id', $user->id)
            ->where(function ($p) {
                $p->where('name', 'like', 'FitTrack Demo%')
                    ->orWhere('name', 'Dashboard demo plan');
            })
            ->delete();

        $measurementDates = [
            now()->copy()->subDays(35)->toDateString(),
            now()->copy()->subDays(21)->toDateString(),
            now()->copy()->subDays(7)->toDateString(),
            now()->toDateString(),
        ];
        BodyMeasurement::query()
            ->where('user_id', $user->id)
            ->whereIn('date', $measurementDates)
            ->delete();
    }

    private function seedFriendProfilesAndActivity(User $viewer): void
    {
        $profiles = [
            'demo1@fittrack.test' => ['name' => 'أحمد الزريقي', 'plan' => 'HIIT — تبويب'],
            'demo2@fittrack.test' => ['name' => 'سارة الحسن', 'plan' => 'Pull — ظهر وذراع'],
            'demo3@fittrack.test' => ['name' => 'Omar Khoury', 'plan' => 'Lower — أرجل'],
        ];

        $bench = Exercise::query()->whereNull('user_id')->where('name', 'Bench Press')->where('is_public', true)->first();
        if ($bench === null) {
            return;
        }

        foreach ($profiles as $email => $meta) {
            $friend = User::query()->where('email', $email)->first();
            if ($friend === null || $friend->is($viewer)) {
                continue;
            }

            User::query()->whereKey($friend->id)->update([
                'name' => $meta['name'],
                'goal' => UserGoal::GainMuscle,
            ]);

            $already = Friendship::query()
                ->where('status', FriendshipStatus::Accepted)
                ->where(function ($q) use ($viewer, $friend) {
                    $q->where(fn ($q2) => $q2->where('user_id', $viewer->id)->where('friend_id', $friend->id))
                        ->orWhere(fn ($q2) => $q2->where('user_id', $friend->id)->where('friend_id', $viewer->id));
                })
                ->exists();

            if (! $already) {
                Friendship::query()->create([
                    'user_id' => $viewer->id,
                    'friend_id' => $friend->id,
                    'status' => FriendshipStatus::Accepted,
                ]);
            }

            WorkoutSession::query()
                ->where('user_id', $friend->id)
                ->where('notes', self::SEED_NOTE)
                ->delete();

            $plan = WorkoutPlan::query()->firstOrCreate(
                ['user_id' => $friend->id, 'name' => 'FitTrack Demo — '.$meta['plan']],
                [
                    'description' => 'خطة عرض للوحة التدريب.',
                    'is_public' => false,
                    'share_token' => null,
                ],
            );

            $day = WorkoutPlanDay::query()->firstOrCreate(
                ['workout_plan_id' => $plan->id, 'day_name' => 'push'],
                ['order' => 1],
            );

            PlanDayExercise::query()->firstOrCreate(
                [
                    'workout_plan_day_id' => $day->id,
                    'exercise_id' => $bench->id,
                    'order' => 1,
                ],
                ['sets' => 3, 'reps' => 8, 'rest_seconds' => 120],
            );

            $plan->touch();

            $when = match ($email) {
                'demo1@fittrack.test' => now()->copy()->subDay(),
                'demo2@fittrack.test' => now()->copy()->subDays(3),
                default => now()->copy()->subDays(5),
            };

            $session = WorkoutSession::query()->create([
                'user_id' => $friend->id,
                'workout_plan_id' => $plan->id,
                'date' => $when->toDateString(),
                'duration_minutes' => match ($email) {
                    'demo1@fittrack.test' => 52,
                    'demo2@fittrack.test' => 58,
                    default => 61,
                },
                'notes' => self::SEED_NOTE,
            ]);

            SessionSet::query()->create([
                'workout_session_id' => $session->id,
                'exercise_id' => $bench->id,
                'set_number' => 1,
                'reps' => 8,
                'weight' => match ($email) {
                    'demo1@fittrack.test' => 62.5,
                    'demo2@fittrack.test' => 45.0,
                    default => 72.5,
                },
                'is_completed' => true,
            ]);
        }
    }

    private function seedBodyMeasurements(User $user): void
    {
        $rows = [
            [now()->copy()->subDays(35), 82.4],
            [now()->copy()->subDays(21), 81.6],
            [now()->copy()->subDays(7), 81.0],
            [now()->copy()->startOfDay(), 80.6],
        ];

        foreach ($rows as [$day, $kg]) {
            BodyMeasurement::query()->create([
                'user_id' => $user->id,
                'date' => $day->toDateString(),
                'weight' => $kg,
                'body_fat' => null,
                'chest' => null,
                'waist' => null,
                'arms' => null,
            ]);
        }
    }

    /**
     * @param  array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}  $ex
     */
    private function seedLiftHistory(User $user, array $ex): void
    {
        $prev = now()->copy()->subMonthNoOverflow();
        $prevBench = $prev->copy()->startOfMonth()->addDays(4);
        $prevSquat = $prev->copy()->startOfMonth()->addDays(11);
        $prevDl = $prev->copy()->startOfMonth()->addDays(19);

        $thisMonth = now()->copy()->startOfMonth();
        $thisBench = $thisMonth->copy()->addDays(3);
        $thisSquat = $thisMonth->copy()->addDays(9);
        $thisDl = $thisMonth->copy()->addDays(16);

        $plan = $this->demoPlan($user, 'FitTrack Demo — Strength log', 'strength');

        $this->createSessionIfPast($user, $plan, $prevBench, 50, [
            [$ex['bench']->id, 1, 5, 82.5],
            [$ex['row']->id, 1, 10, 52.5],
        ]);

        $this->createSessionIfPast($user, $plan, $prevSquat, 55, [
            [$ex['squat']->id, 1, 5, 107.5],
            [$ex['bench']->id, 1, 8, 72.5],
        ]);

        $this->createSessionIfPast($user, $plan, $prevDl, 58, [
            [$ex['deadlift']->id, 1, 3, 130.0],
            [$ex['row']->id, 1, 8, 55.0],
        ]);

        $this->createSessionIfPast($user, $plan, $thisBench, 62, [
            [$ex['bench']->id, 1, 5, 85.0],
            [$ex['ohp']->id, 1, 8, 42.5],
        ]);

        $this->createSessionIfPast($user, $plan, $thisSquat, 64, [
            [$ex['squat']->id, 1, 5, 112.5],
            [$ex['deadlift']->id, 1, 3, 132.5],
        ]);

        $this->createSessionIfPast($user, $plan, $thisDl, 66, [
            [$ex['deadlift']->id, 1, 3, 140.0],
            [$ex['squat']->id, 1, 5, 100.0],
        ]);

        $peakBench = now()->copy()->subDays(2)->startOfDay();
        if ($peakBench->greaterThan($thisBench->copy()->startOfDay())) {
            $this->createSessionIfPast($user, $plan, $peakBench, 60, [
                [$ex['bench']->id, 1, 4, 87.5],
                [$ex['row']->id, 1, 10, 57.5],
            ]);
        }

        $peakSquat = now()->copy()->subDays(4)->startOfDay();
        if ($peakSquat->greaterThan($thisSquat->copy()->startOfDay())) {
            $this->createSessionIfPast($user, $plan, $peakSquat, 63, [
                [$ex['squat']->id, 1, 5, 115.0],
                [$ex['ohp']->id, 1, 8, 45.0],
            ]);
        }
    }

    /**
     * @param  array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}  $ex
     */
    private function seedStreakAndRollingDays(User $user, array $ex): void
    {
        $plan = $this->demoPlan($user, 'FitTrack Demo — Full week', 'full');

        for ($i = 0; $i < 4; $i++) {
            $day = now()->copy()->startOfDay()->subDays($i);
            $templates = [
                [
                    [$ex['bench']->id, 1, 8, 80.0],
                    [$ex['ohp']->id, 1, 10, 40.0],
                    [$ex['row']->id, 1, 10, 55.0],
                    [$ex['squat']->id, 1, 6, 95.0],
                ],
                [
                    [$ex['deadlift']->id, 1, 5, 125.0],
                    [$ex['squat']->id, 1, 8, 92.5],
                    [$ex['row']->id, 1, 10, 52.5],
                ],
                [
                    [$ex['bench']->id, 1, 8, 82.5],
                    [$ex['squat']->id, 1, 6, 100.0],
                    [$ex['ohp']->id, 1, 10, 42.5],
                    [$ex['row']->id, 1, 12, 50.0],
                ],
                [
                    [$ex['squat']->id, 1, 5, 102.5],
                    [$ex['deadlift']->id, 1, 4, 128.0],
                    [$ex['bench']->id, 1, 10, 72.5],
                ],
            ];
            $sets = $templates[$i % count($templates)];
            $this->createSessionWithSets($user, $plan, $day, 58 + ($i * 2), $sets);
        }

        $chartExtra = now()->copy()->startOfDay()->subDays(6);
        if (! $chartExtra->isSameDay(now()->copy()->startOfDay()->subDays(0))
            && ! $chartExtra->isSameDay(now()->copy()->startOfDay()->subDays(1))
            && ! $chartExtra->isSameDay(now()->copy()->startOfDay()->subDays(2))
            && ! $chartExtra->isSameDay(now()->copy()->startOfDay()->subDays(3))) {
            $this->createSessionWithSets(
                $user,
                $plan,
                $chartExtra,
                48,
                [
                    [$ex['bench']->id, 1, 10, 70.0],
                    [$ex['row']->id, 1, 12, 47.5],
                ],
            );
        }
    }

    /**
     * @param  array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}  $ex
     */
    private function seedIsoWeekSessions(User $user, array $ex): void
    {
        $weekStart = now()->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $prevStart = $weekStart->copy()->subWeek();

        $planMain = $this->demoPlan($user, 'FitTrack Demo — Block A', 'blocka');

        $prevPattern = [0, 2, 4, 6];
        foreach ($prevPattern as $off) {
            $day = $prevStart->copy()->addDays($off)->startOfDay();
            $this->createSessionWithSets(
                $user,
                $planMain,
                $day,
                52 + ($off % 3) * 4,
                [
                    [$ex['squat']->id, 1, 6, 95.0 + ($off % 2) * 2.5],
                    [$ex['bench']->id, 1, 8, 75.0],
                    [$ex['deadlift']->id, 1, 4, 120.0],
                ],
            );
        }
    }

    /**
     * @param  array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}  $ex
     */
    private function seedLastFiveShowcase(User $user, array $ex): void
    {
        $items = [
            ['name' => 'FitTrack Demo — Push — صدر وكتف', 'slug' => 'push_ar', 'day' => 2, 'min' => 64, 'sets' => [
                [$ex['bench']->id, 1, 8, 82.5],
                [$ex['ohp']->id, 1, 10, 42.5],
                [$ex['row']->id, 1, 10, 55.0],
                [$ex['squat']->id, 1, 6, 90.0],
                [$ex['deadlift']->id, 1, 3, 110.0],
            ]],
            ['name' => 'FitTrack Demo — Pull — ظهر', 'slug' => 'pull_ar', 'day' => 3, 'min' => 58, 'sets' => [
                [$ex['deadlift']->id, 1, 4, 125.0],
                [$ex['row']->id, 1, 10, 57.5],
                [$ex['bench']->id, 1, 10, 65.0],
                [$ex['squat']->id, 1, 8, 85.0],
            ]],
            ['name' => 'FitTrack Demo — Lower — أرجل', 'slug' => 'lower_ar', 'day' => 4, 'min' => 61, 'sets' => [
                [$ex['squat']->id, 1, 6, 105.0],
                [$ex['deadlift']->id, 1, 5, 118.0],
                [$ex['bench']->id, 1, 12, 60.0],
                [$ex['row']->id, 1, 12, 50.0],
            ]],
            ['name' => 'FitTrack Demo — Upper Power', 'slug' => 'upper_en', 'day' => 6, 'min' => 55, 'sets' => [
                [$ex['bench']->id, 1, 5, 80.0],
                [$ex['ohp']->id, 1, 8, 40.0],
                [$ex['row']->id, 1, 8, 52.5],
                [$ex['squat']->id, 1, 5, 95.0],
            ]],
            ['name' => 'FitTrack Demo — Full Body — تعبئة', 'slug' => 'full_ar', 'day' => 8, 'min' => 59, 'sets' => [
                [$ex['squat']->id, 1, 8, 88.0],
                [$ex['bench']->id, 1, 10, 70.0],
                [$ex['deadlift']->id, 1, 4, 115.0],
                [$ex['ohp']->id, 1, 10, 35.0],
                [$ex['row']->id, 1, 12, 47.5],
            ]],
        ];

        foreach ($items as $row) {
            $plan = $this->demoPlan($user, $row['name'], $row['slug']);
            $day = now()->copy()->startOfDay()->subDays((int) $row['day']);
            $this->createSessionWithSets($user, $plan, $day, (int) $row['min'], $row['sets']);
        }
    }

    /**
     * @param  array{bench: Exercise, squat: Exercise, deadlift: Exercise, row: Exercise, ohp: Exercise}  $ex
     */
    private function seedTodaysPlan(User $user, array $ex): void
    {
        $english = mb_strtolower(now()->locale('en')->translatedFormat('l'));

        $plan = WorkoutPlan::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => 'FitTrack Demo — اليوم',
            ],
            [
                'description' => 'يوم يطابق اليوم لعرض «روتين اليوم».',
                'is_public' => false,
                'share_token' => null,
            ],
        );

        $day = WorkoutPlanDay::query()->firstOrCreate(
            [
                'workout_plan_id' => $plan->id,
                'day_name' => $english,
            ],
            ['order' => (int) now()->dayOfWeekIso],
        );

        $rows = [
            [$ex['bench']->id, 4, 6, 0],
            [$ex['squat']->id, 4, 5, 1],
            [$ex['deadlift']->id, 3, 5, 2],
            [$ex['row']->id, 3, 10, 3],
        ];

        foreach ($rows as [$eid, $sets, $reps, $order]) {
            PlanDayExercise::query()->updateOrCreate(
                [
                    'workout_plan_day_id' => $day->id,
                    'exercise_id' => $eid,
                    'order' => $order,
                ],
                [
                    'sets' => $sets,
                    'reps' => $reps,
                    'rest_seconds' => 150,
                ],
            );
        }

        $plan->touch();
    }

    private function demoPlan(User $user, string $name, string $dayToken): WorkoutPlan
    {
        $plan = WorkoutPlan::query()->firstOrCreate(
            ['user_id' => $user->id, 'name' => $name],
            [
                'description' => 'FitTrack demo',
                'is_public' => false,
                'share_token' => null,
            ],
        );

        WorkoutPlanDay::query()->firstOrCreate(
            [
                'workout_plan_id' => $plan->id,
                'day_name' => $dayToken,
            ],
            ['order' => 1],
        );

        return $plan;
    }

    /**
     * @param  list<array{0: int, 1: int, 2: int, 3: float}>  $sets
     */
    private function createSessionIfPast(User $user, WorkoutPlan $plan, Carbon $day, int $minutes, array $sets): void
    {
        if ($day->copy()->startOfDay()->isFuture()) {
            return;
        }

        $this->createSessionWithSets($user, $plan, $day, $minutes, $sets);
    }

    /**
     * @param  list<array{0: int, 1: int, 2: int, 3: float}>  $sets
     */
    private function createSessionWithSets(User $user, WorkoutPlan $plan, Carbon $day, int $minutes, array $sets): WorkoutSession
    {
        $session = WorkoutSession::query()->create([
            'user_id' => $user->id,
            'workout_plan_id' => $plan->id,
            'date' => $day->copy()->startOfDay()->toDateString(),
            'duration_minutes' => $minutes,
            'notes' => self::SEED_NOTE,
        ]);

        foreach ($sets as $row) {
            [$exerciseId, $setNo, $reps, $weight] = $row;
            SessionSet::query()->create([
                'workout_session_id' => $session->id,
                'exercise_id' => $exerciseId,
                'set_number' => $setNo,
                'reps' => $reps,
                'weight' => $weight,
                'is_completed' => true,
            ]);
        }

        return $session;
    }
}
