<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutPlanRequest;
use App\Models\Exercise;
use App\Models\WorkoutPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkoutPlanController extends Controller
{
    /**
     * قائمة خطط المستخدم الحالي فقط (لا تُعرض خطط الآخرين حتى لو عامة).
     */
    public function index(): View
    {
        $this->authorize('viewAny', WorkoutPlan::class);

        $plans = WorkoutPlan::query()
            ->where('user_id', auth()->id())
            ->with('user')
            ->withCount(['workoutPlanDays', 'planDayExercises'])
            ->latest()
            ->paginate(12);

        return view('workout-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $this->authorize('create', WorkoutPlan::class);

        $exercises = Exercise::query()
            ->visibleTo((int) auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $initialDaysPayload = [[
            'day_name' => '',
            'order' => 0,
            'exercises' => [[
                'exercise_id' => '',
                'sets' => 3,
                'reps' => 10,
                'rest_seconds' => 60,
                'order' => 0,
            ]],
        ]];

        return view('workout-plans.create', compact('exercises', 'initialDaysPayload'));
    }

    /**
     * إنشاء الخطة مع الأيام والتمارين داخل معاملة واحدة.
     */
    public function store(WorkoutPlanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $days = $validated['days'];
        unset($validated['days']);

        $plan = DB::transaction(function () use ($validated, $days, $request) {
            $plan = WorkoutPlan::query()->create([
                'user_id' => $request->user()->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_public' => (bool) ($validated['is_public'] ?? false),
            ]);
            $this->persistNestedDays($plan, $days);

            return $plan;
        });

        return redirect()
            ->route('workout-plans.show', $plan)
            ->with('status', __('Plan created.'));
    }

    public function show(WorkoutPlan $workoutPlan): View
    {
        $this->authorize('view', $workoutPlan);

        $workoutPlan->load(['workoutPlanDays.planDayExercises.exercise']);

        return view('workout-plans.show', compact('workoutPlan'));
    }

    public function edit(WorkoutPlan $workoutPlan): View
    {
        $this->authorize('update', $workoutPlan);

        $workoutPlan->load(['workoutPlanDays.planDayExercises']);

        $exercises = Exercise::query()
            ->visibleTo((int) auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $initialDaysPayload = $workoutPlan->workoutPlanDays->map(function ($day) {
            return [
                'day_name' => $day->day_name,
                'order' => $day->order,
                'exercises' => $day->planDayExercises->map(fn ($row) => [
                    'exercise_id' => (string) $row->exercise_id,
                    'sets' => $row->sets,
                    'reps' => $row->reps,
                    'rest_seconds' => $row->rest_seconds,
                    'order' => $row->order,
                ])->values()->all(),
            ];
        })->values()->all();

        return view('workout-plans.edit', compact('workoutPlan', 'exercises', 'initialDaysPayload'));
    }

    /**
     * استبدال أيام الخطة وتمارينها بالكامل (حذف القديم ثم إعادة الإدراج) داخل معاملة.
     */
    public function update(WorkoutPlanRequest $request, WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('update', $workoutPlan);

        $validated = $request->validated();
        $days = $validated['days'];
        unset($validated['days']);

        DB::transaction(function () use ($workoutPlan, $validated, $days) {
            $workoutPlan->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_public' => (bool) ($validated['is_public'] ?? $workoutPlan->is_public),
            ]);
            // cascadeOnDelete على plan_day_exercises عند حذف اليوم
            $workoutPlan->workoutPlanDays()->delete();
            $this->persistNestedDays($workoutPlan, $days);
        });

        return redirect()
            ->route('workout-plans.show', $workoutPlan)
            ->with('status', __('Plan updated.'));
    }

    public function destroy(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('delete', $workoutPlan);

        $workoutPlan->delete();

        return redirect()->route('workout-plans.index')->with('status', __('Plan deleted.'));
    }

    /**
     * نسخ كامل: الخطة + الأيام + صفوف plan_day_exercises.
     */
    public function duplicate(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('duplicate', $workoutPlan);

        $workoutPlan->loadMissing(['workoutPlanDays.planDayExercises']);

        $clone = DB::transaction(function () use ($workoutPlan) {
            $newPlan = $workoutPlan->replicate(['share_token']);
            $newPlan->user_id = auth()->id();
            $newPlan->name = $workoutPlan->name.' ('.__('Copy').')';
            $newPlan->share_token = null;
            $newPlan->save();

            foreach ($workoutPlan->workoutPlanDays as $day) {
                $newDay = $day->replicate();
                $newDay->workout_plan_id = $newPlan->id;
                $newDay->save();

                foreach ($day->planDayExercises as $row) {
                    $newRow = $row->replicate();
                    $newRow->workout_plan_day_id = $newDay->id;
                    $newRow->save();
                }
            }

            return $newPlan;
        });

        return redirect()
            ->route('workout-plans.show', $clone)
            ->with('status', __('Routine duplicated.'));
    }

    /**
     * توليد share_token فريد (UUID) لعرض الخطة بدون تسجيل دخول.
     */
    public function share(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('share', $workoutPlan);

        do {
            $token = (string) Str::uuid();
        } while (WorkoutPlan::query()->where('share_token', $token)->exists());

        $workoutPlan->forceFill(['share_token' => $token])->save();

        return redirect()
            ->route('workout-plans.show', $workoutPlan)
            ->with('status', __('Share link enabled.'))
            ->with('share_url', route('workout-plans.public', ['share_token' => $token]));
    }

    public function revokeShare(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('share', $workoutPlan);

        $workoutPlan->forceFill(['share_token' => null])->save();

        return redirect()
            ->route('workout-plans.show', $workoutPlan)
            ->with('status', __('Share link removed.'));
    }

    /**
     * عرض عام بالرابط — بدون middleware auth.
     */
    public function showPublic(string $share_token): View
    {
        $workoutPlan = WorkoutPlan::query()
            ->byShareToken($share_token)
            ->with(['user', 'workoutPlanDays.planDayExercises.exercise'])
            ->firstOrFail();

        return view('workout-plans.public', compact('workoutPlan'));
    }

    /** تبديل حقل is_public (خاص ↔ عام). */
    public function togglePublic(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('update', $workoutPlan);

        $workoutPlan->update([
            'is_public' => ! $workoutPlan->is_public,
        ]);

        return back()->with('status', __('Visibility updated.'));
    }

    /**
     * @param  array<int, array{day_name: string, order?: int, exercises: array<int, array<string, mixed>>}>  $days
     */
    protected function persistNestedDays(WorkoutPlan $plan, array $days): void
    {
        foreach ($days as $dIdx => $dayData) {
            $exercises = $dayData['exercises'] ?? [];
            $day = $plan->workoutPlanDays()->create([
                'day_name' => $dayData['day_name'],
                'order' => $dayData['order'] ?? $dIdx,
            ]);

            foreach ($exercises as $eIdx => $ex) {
                $day->planDayExercises()->create([
                    'exercise_id' => (int) $ex['exercise_id'],
                    'sets' => isset($ex['sets']) ? (int) $ex['sets'] : 3,
                    'reps' => isset($ex['reps']) ? (int) $ex['reps'] : 10,
                    'rest_seconds' => isset($ex['rest_seconds']) ? (int) $ex['rest_seconds'] : 60,
                    'order' => $ex['order'] ?? $eIdx,
                ]);
            }
        }
    }
}
