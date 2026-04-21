<?php

namespace App\Policies;

use App\Models\SessionSet;
use App\Models\User;
use App\Models\WorkoutSession;

class SessionSetPolicy
{
    /**
     * قائمة المجموعات داخل جلسة — يُصرَّح عبر الجلسة في المتحكم أيضاً.
     */
    public function viewAny(User $user, WorkoutSession $workoutSession): bool
    {
        return $workoutSession->user_id === $user->id;
    }

    public function view(User $user, SessionSet $sessionSet): bool
    {
        return $sessionSet->workoutSession->user_id === $user->id;
    }

    public function create(User $user, WorkoutSession $workoutSession): bool
    {
        return $workoutSession->user_id === $user->id;
    }

    public function update(User $user, SessionSet $sessionSet): bool
    {
        return $sessionSet->workoutSession->user_id === $user->id;
    }

    public function delete(User $user, SessionSet $sessionSet): bool
    {
        return $this->update($user, $sessionSet);
    }
}
