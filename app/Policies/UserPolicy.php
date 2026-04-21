<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Public profile page (basic info) — allowed for guests and authenticated users.
     */
    public function viewPublicProfile(?User $user, User $model): bool
    {
        return true;
    }
}
