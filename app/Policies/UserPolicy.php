<?php

namespace App\Policies;

use App\Models\Friendship;
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

    /**
     * إرسال طلب صداقة: ليس لنفسه، وليسا أصدقاء، ولا يوجد طلب معلّق في أي اتجاه.
     */
    public function sendFriendRequest(User $auth, User $recipient): bool
    {
        if ($auth->id === $recipient->id) {
            return false;
        }

        if ($this->hasAcceptedFriendship($auth, $recipient)) {
            return false;
        }

        return ! $this->hasPendingFriendshipEitherWay($auth, $recipient);
    }

    /**
     * عرض ملف صديق (جلسات + إحصائيات): يجب أن تكون صداقة مقبولة بين الطرفين.
     */
    public function viewFriendProfile(User $viewer, User $friend): bool
    {
        if ($viewer->id === $friend->id) {
            return true;
        }

        return $this->isAcceptedFriend($viewer, $friend);
    }

    /**
     * إزالة صديق (حذف صف الصداقة): صداقة مقبولة فقط بين المستخدمين.
     */
    public function unfriend(User $auth, User $friend): bool
    {
        if ($auth->id === $friend->id) {
            return false;
        }

        return $this->isAcceptedFriend($auth, $friend);
    }

    protected function isAcceptedFriend(User $a, User $b): bool
    {
        return User::query()
            ->whereKey($b->id)
            ->acceptedFriendsOf($a)
            ->exists();
    }

    protected function hasAcceptedFriendship(User $a, User $b): bool
    {
        return Friendship::query()
            ->accepted()
            ->where(function ($q) use ($a, $b) {
                $q->where(fn ($x) => $x->where('user_id', $a->id)->where('friend_id', $b->id))
                    ->orWhere(fn ($x) => $x->where('user_id', $b->id)->where('friend_id', $a->id));
            })
            ->exists();
    }

    protected function hasPendingFriendshipEitherWay(User $a, User $b): bool
    {
        return Friendship::query()
            ->pending()
            ->where(function ($q) use ($a, $b) {
                $q->where(fn ($x) => $x->where('user_id', $a->id)->where('friend_id', $b->id))
                    ->orWhere(fn ($x) => $x->where('user_id', $b->id)->where('friend_id', $a->id));
            })
            ->exists();
    }
}
