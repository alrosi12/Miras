<?php

namespace App\Policies;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;

class FriendshipPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Friendship $friendship): bool
    {
        return $friendship->user_id === $user->id || $friendship->friend_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Friendship $friendship): bool
    {
        return $this->view($user, $friendship);
    }

    public function delete(User $user, Friendship $friendship): bool
    {
        return $this->view($user, $friendship);
    }

    public function accept(User $user, Friendship $friendship): bool
    {
        return $friendship->friend_id === $user->id
            && $friendship->status === FriendshipStatus::Pending;
    }

    public function reject(User $user, Friendship $friendship): bool
    {
        return $friendship->friend_id === $user->id
            && $friendship->status === FriendshipStatus::Pending;
    }

    public function restore(User $user, Friendship $friendship): bool
    {
        return false;
    }

    public function forceDelete(User $user, Friendship $friendship): bool
    {
        return false;
    }
}
