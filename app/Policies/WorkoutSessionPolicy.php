<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutSession;

class WorkoutSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WorkoutSession $workoutSession): bool
    {
        return $workoutSession->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WorkoutSession $workoutSession): bool
    {
        return $workoutSession->user_id === $user->id;
    }

    public function delete(User $user, WorkoutSession $workoutSession): bool
    {
        return $this->update($user, $workoutSession);
    }

    public function finish(User $user, WorkoutSession $workoutSession): bool
    {
        return $this->update($user, $workoutSession);
    }

    /**
     * إدارة مجموعات التمرين داخل الجلسة.
     */
    public function manageSessionSets(User $user, WorkoutSession $workoutSession): bool
    {
        return $workoutSession->user_id === $user->id;
    }

    public function restore(User $user, WorkoutSession $workoutSession): bool
    {
        return false;
    }

    public function forceDelete(User $user, WorkoutSession $workoutSession): bool
    {
        return false;
    }
}
