<?php

namespace App\Policies;

use App\Models\Exercise;
use App\Models\User;

class ExercisePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * عرض التمرين: عام (is_public) أو مملوك للمستخدم الحالي.
     */
    public function view(User $user, Exercise $exercise): bool
    {
        return (bool) $exercise->is_public
            || (int) $exercise->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * التعديل: فقط صاحب التمرين (لا يمكن تعديل تمارين النظام user_id = null من هنا).
     */
    public function update(User $user, Exercise $exercise): bool
    {
        return $exercise->user_id !== null && (int) $exercise->user_id === (int) $user->id;
    }

    /**
     * الحذف: نفس شروط التعديل.
     */
    public function delete(User $user, Exercise $exercise): bool
    {
        return $this->update($user, $exercise);
    }

    public function restore(User $user, Exercise $exercise): bool
    {
        return false;
    }

    public function forceDelete(User $user, Exercise $exercise): bool
    {
        return false;
    }
}
