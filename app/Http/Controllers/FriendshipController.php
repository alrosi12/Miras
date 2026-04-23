<?php

namespace App\Http\Controllers;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FriendshipController extends Controller
{
    /**
     * قائمة الأصدقاء المقبولين + آخر جلسة + عدد جلسات هذا الأسبوع لكل صديق.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Friendship::class);

        $auth = auth()->user();

        $friends = User::query()
            ->whereKeyNot($auth->id)
            ->acceptedFriendsOf($auth)
            ->with(['latestWorkoutSession.workoutPlan:id,name'])
            ->orderBy('name')
            ->get();

        $weekStart = now()->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = now()->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $sessionsThisWeek = WorkoutSession::query()
            ->whereIn('user_id', $friends->pluck('id'))
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get(['id', 'user_id']);

        $weeklySessionCounts = $sessionsThisWeek
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->count());

        $incoming = $auth
            ->receivedFriendRequests()
            ->with('user:id,name,email,avatar')
            ->where('status', FriendshipStatus::Pending)
            ->latest()
            ->limit(15)
            ->get();

        $searchResults = collect();
        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $friendIds = $friends->pluck('id')->push($auth->id)->unique();

            $searchResults = User::query()
                ->whereKeyNot($auth->id)
                ->whereNotIn('id', $friendIds)
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)->orWhere('email', 'like', $term);
                })
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name', 'email', 'avatar']);
        }

        return view('friends.index', [
            'friends' => $friends,
            'weeklySessionCounts' => $weeklySessionCounts,
            'incoming' => $incoming,
            'searchResults' => $searchResults,
        ]);
    }

    /**
     * طلبات واردة (pending): المرسل + الصورة + تاريخ الطلب.
     */
    public function requests(): View
    {
        $this->authorize('viewAny', Friendship::class);

        $incoming = auth()->user()
            ->receivedFriendRequests()
            ->with('user:id,name,email,avatar')
            ->where('status', FriendshipStatus::Pending)
            ->latest()
            ->get();

        return view('friends.requests', compact('incoming'));
    }

    /**
     * إرسال طلب صداقة إلى المستخدم (تحقق عبر UserPolicy).
     */
    public function send(User $user): RedirectResponse
    {
        if (! auth()->user()->can('sendFriendRequest', $user)) {
            return redirect()
                ->route('friends.index')
                ->with('error', __('You cannot send a friend request right now (self, already friends, or a pending request exists).'));
        }

        try {
            Friendship::query()->create([
                'user_id' => auth()->id(),
                'friend_id' => $user->id,
                'status' => FriendshipStatus::Pending,
            ]);
        } catch (\Throwable) {
            return redirect()
                ->route('friends.index')
                ->with('error', __('Could not send friend request. Please try again.'));
        }

        return redirect()
            ->route('friends.index')
            ->with('status', __('Friend request sent.'));
    }

    /**
     * قبول طلب: يجب أن يكون المستقبل هو المستخدم الحالي (FriendshipPolicy::accept).
     */
    public function accept(Friendship $friendship): RedirectResponse
    {
        $this->authorize('accept', $friendship);

        $friendship->update(['status' => FriendshipStatus::Accepted]);

        return redirect()
            ->route('friends.requests')
            ->with('status', __('Friend request accepted.'));
    }

    /**
     * رفض طلب وحذف السجل (المستقبل فقط).
     */
    public function reject(Friendship $friendship): RedirectResponse
    {
        $this->authorize('reject', $friendship);

        $friendship->delete();

        return redirect()
            ->route('friends.requests')
            ->with('status', __('Friend request declined.'));
    }

    /**
     * إزالة صداقة مقبولة بين المستخدم الحالي والهدف (من الطرفين).
     */
    public function destroy(User $user): RedirectResponse
    {
        if (! auth()->user()->can('unfriend', $user)) {
            return redirect()
                ->route('friends.index')
                ->with('error', __('You cannot remove this friendship.'));
        }

        $friendship = Friendship::query()
            ->accepted()
            ->where(function ($q) use ($user) {
                $q->where('user_id', auth()->id())->where('friend_id', $user->id)
                    ->orWhere('user_id', $user->id)->where('friend_id', auth()->id());
            })
            ->first();

        if ($friendship === null) {
            return redirect()
                ->route('friends.index')
                ->with('error', __('No accepted friendship found with this user.'));
        }

        $friendship->delete();

        return redirect()
            ->route('friends.index')
            ->with('status', __('Friend removed.'));
    }

    /**
     * ملف صديق: جلسات حديثة، إحصائيات عامة، خطط عامة فقط.
     */
    public function profile(User $user): View|RedirectResponse
    {
        if (! auth()->user()->can('viewFriendProfile', $user)) {
            return redirect()
                ->route('friends.index')
                ->with('error', __('You cannot view this profile.'));
        }

        $user->loadCount('workoutSessions');

        $totalMinutes = (int) $user->workoutSessions()->sum('duration_minutes');

        $recentSessions = $user->workoutSessions()
            ->with(['workoutPlan:id,name'])
            ->latest('date')
            ->latest('id')
            ->limit(15)
            ->get();

        $publicPlans = WorkoutPlan::query()
            ->where('user_id', $user->id)
            ->where('is_public', true)
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get(['id', 'name', 'description', 'updated_at']);

        return view('friends.profile', [
            'friend' => $user,
            'totalMinutes' => $totalMinutes,
            'recentSessions' => $recentSessions,
            'publicPlans' => $publicPlans,
        ]);
    }

    /**
     * نشاط الأصدقاء: آخر 20 جلسة لجميع الأصدقاء (مرتبة بالتاريخ).
     */
    public function feed(): View
    {
        $this->authorize('viewAny', Friendship::class);

        $friendIds = auth()->user()->friends->pluck('id');

        if ($friendIds->isEmpty()) {
            return view('friends.feed', ['feed' => collect()]);
        }

        $feed = WorkoutSession::query()
            ->whereIn('user_id', $friendIds)
            ->with(['user:id,name,avatar', 'workoutPlan:id,name'])
            ->latest('date')
            ->latest('id')
            ->limit(20)
            ->get();

        return view('friends.feed', compact('feed'));
    }
}
