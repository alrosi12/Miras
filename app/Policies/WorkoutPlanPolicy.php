<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutPlan;

class WorkoutPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $workoutPlan->user_id === $user->id
            || $workoutPlan->is_public === true
            || $user->is_admin === true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $workoutPlan->user_id === $user->id;
    }

    public function delete(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $this->update($user, $workoutPlan);
    }

    public function duplicate(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $workoutPlan->user_id === $user->id || $workoutPlan->is_public === true;
    }

    /** توليد/إلغاء رابط المشاركة — صاحب الخطة فقط. */
    public function share(User $user, WorkoutPlan $workoutPlan): bool
    {
        return $workoutPlan->user_id === $user->id;
    }

    public function restore(User $user, WorkoutPlan $workoutPlan): bool
    {
        return false;
    }

    public function forceDelete(User $user, WorkoutPlan $workoutPlan): bool
    {
        return false;
    }
}
