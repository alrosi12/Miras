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
 * 10 مستخدمين تجريبيين (demo1…demo10) مع خطط وجلسات وقياسات وصداقات لاختبار الواجهات.
 * كلمة المرور لجميع الحسابات: password
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $catalogExercise = Exercise::query()
            ->whereNull('user_id')
            ->where('is_public', true)
            ->first();

        if ($catalogExercise === null) {
            $catalogExercise = Exercise::query()->create([
                'user_id' => null,
                'name' => 'Seeded Bench Press',
                'description' => 'تمرين كتالوج للبذور التجريبية.',
                'muscle_group' => MuscleGroup::Chest,
                'type' => ExerciseType::Strength,
                'is_public' => true,
            ]);
        }

        $goals = [UserGoal::Maintain, UserGoal::LoseWeight, UserGoal::GainMuscle];
        $users = [];

        for ($i = 1; $i <= 10; $i++) {
            $users[] = User::query()->updateOrCreate(
                ['email' => "demo{$i}@fittrack.test"],
                [
                    'name' => "Demo User {$i}",
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'is_admin' => false,
                    'weight' => 65 + $i,
                    'height' => 168 + ($i % 5),
                    'birth_date' => Carbon::now()->subYears(22 + ($i % 8))->startOfMonth()->toDateString(),
                    'goal' => $goals[$i % 3],
                ],
            );
        }

        foreach ($users as $index => $user) {
            $i = $index + 1;

            // لا نكرر الخطط إن وُجدت مسبقاً (إعادة تشغيل الـ seeder بأمان).
            if ($user->workoutPlans()->exists()) {
                continue;
            }

            $plan = WorkoutPlan::query()->create([
                'user_id' => $user->id,
                'name' => "Plan — {$user->name}",
                'description' => 'خطة تجريبية من DemoUsersSeeder.',
                'is_public' => $i % 3 === 0,
                'share_token' => null,
            ]);

            $dayPush = WorkoutPlanDay::query()->create([
                'workout_plan_id' => $plan->id,
                'day_name' => 'Push',
                'order' => 1,
            ]);

            WorkoutPlanDay::query()->create([
                'workout_plan_id' => $plan->id,
                'day_name' => 'Pull',
                'order' => 2,
            ]);

            PlanDayExercise::query()->create([
                'workout_plan_day_id' => $dayPush->id,
                'exercise_id' => $catalogExercise->id,
                'sets' => 3,
                'reps' => 10,
                'rest_seconds' => 90,
                'order' => 1,
            ]);

            for ($d = 0; $d < 3; $d++) {
                $session = WorkoutSession::query()->create([
                    'user_id' => $user->id,
                    'workout_plan_id' => $plan->id,
                    'date' => now()->subDays($d * 4 + $i)->toDateString(),
                    'duration_minutes' => 40 + $d * 5,
                    'notes' => null,
                ]);

                SessionSet::query()->create([
                    'workout_session_id' => $session->id,
                    'exercise_id' => $catalogExercise->id,
                    'set_number' => 1,
                    'reps' => 8 + $d,
                    'weight' => 15.0 + $i,
                    'is_completed' => true,
                ]);
            }

            BodyMeasurement::query()->create([
                'user_id' => $user->id,
                'weight' => $user->weight,
                'body_fat' => null,
                'chest' => null,
                'waist' => null,
                'arms' => null,
                'date' => now()->subDays(7 + $i)->toDateString(),
            ]);

            if ($i % 2 === 1) {
                Exercise::query()->create([
                    'user_id' => $user->id,
                    'name' => "Custom exercise {$i}",
                    'description' => 'تمرين خاص بالمستخدم (غير عام).',
                    'muscle_group' => MuscleGroup::Arms,
                    'type' => ExerciseType::Strength,
                    'is_public' => false,
                ]);
            }
        }

        $friendSeeds = [
            [0, 1, FriendshipStatus::Accepted],
            [0, 2, FriendshipStatus::Pending],
            [3, 4, FriendshipStatus::Accepted],
            [4, 5, FriendshipStatus::Pending],
            [6, 7, FriendshipStatus::Accepted],
        ];

        foreach ($friendSeeds as [$a, $b, $status]) {
            Friendship::query()->updateOrCreate(
                [
                    'user_id' => $users[$a]->id,
                    'friend_id' => $users[$b]->id,
                ],
                ['status' => $status],
            );
        }
    }
}
