<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutPlan\StoreWorkoutPlanRequest;
use App\Http\Requests\WorkoutPlan\UpdateWorkoutPlanRequest;
use App\Models\WorkoutPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkoutPlanController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', WorkoutPlan::class);

        $plans = WorkoutPlan::query()
            ->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('is_public', true);
            })
            ->with('user')
            ->latest()
            ->paginate(12);

        return view('workout-plans.index', compact('plans'));
    }

    public function create(): View
    {
        $this->authorize('create', WorkoutPlan::class);

        return view('workout-plans.create');
    }

    public function store(StoreWorkoutPlanRequest $request): RedirectResponse
    {
        $this->authorize('create', WorkoutPlan::class);

        $plan = WorkoutPlan::query()->create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return redirect()->route('workout-plans.show', $plan)->with('status', __('Plan created.'));
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

        return view('workout-plans.edit', compact('workoutPlan'));
    }

    public function update(UpdateWorkoutPlanRequest $request, WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('update', $workoutPlan);

        $workoutPlan->update($request->validated());

        return redirect()->route('workout-plans.show', $workoutPlan)->with('status', __('Plan updated.'));
    }

    public function destroy(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('delete', $workoutPlan);

        $workoutPlan->delete();

        return redirect()->route('workout-plans.index')->with('status', __('Plan deleted.'));
    }

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

        return redirect()->route('workout-plans.show', $clone)->with('status', __('Routine duplicated.'));
    }

    public function enableShare(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('manageShare', $workoutPlan);

        do {
            $token = Str::random(40);
        } while (WorkoutPlan::query()->where('share_token', $token)->exists());

        $workoutPlan->forceFill(['share_token' => $token])->save();

        return redirect()
            ->route('workout-plans.show', $workoutPlan)
            ->with('status', __('Share link enabled.'))
            ->with('share_url', route('workout-plans.share', ['token' => $token]));
    }

    public function disableShare(WorkoutPlan $workoutPlan): RedirectResponse
    {
        $this->authorize('manageShare', $workoutPlan);

        $workoutPlan->forceFill(['share_token' => null])->save();

        return redirect()->route('workout-plans.show', $workoutPlan)->with('status', __('Share link removed.'));
    }

    public function shareShow(string $token): View
    {
        $workoutPlan = WorkoutPlan::query()
            ->byShareToken($token)
            ->with(['user', 'workoutPlanDays.planDayExercises.exercise'])
            ->firstOrFail();

        return view('workout-plans.share', compact('workoutPlan'));
    }
}
