<?php

namespace App\Http\Controllers;

use App\Enums\FriendshipStatus;
use App\Http\Requests\Friendship\StoreFriendshipRequest;
use App\Models\Friendship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FriendshipController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Friendship::class);

        $userId = auth()->id();

        $friendships = Friendship::query()
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->with(['user', 'friend'])
            ->latest()
            ->paginate(20);

        return view('friendships.index', compact('friendships'));
    }

    public function create(): View
    {
        $this->authorize('create', Friendship::class);

        return view('friendships.create');
    }

    public function store(StoreFriendshipRequest $request): RedirectResponse
    {
        $this->authorize('create', Friendship::class);

        Friendship::query()->create([
            'user_id' => $request->user()->id,
            'friend_id' => $request->validated('friend_id'),
            'status' => FriendshipStatus::Pending,
        ]);

        return redirect()->route('friendships.index')->with('status', __('Friend request sent.'));
    }

    public function show(Friendship $friendship): View
    {
        $this->authorize('view', $friendship);

        $friendship->load(['user', 'friend']);

        return view('friendships.show', compact('friendship'));
    }

    public function edit(Friendship $friendship): RedirectResponse
    {
        $this->authorize('view', $friendship);

        return redirect()->route('friendships.show', $friendship);
    }

    public function update(Request $_request, Friendship $friendship): RedirectResponse
    {
        abort(405);
    }

    public function destroy(Friendship $friendship): RedirectResponse
    {
        $this->authorize('delete', $friendship);

        $friendship->delete();

        return redirect()->route('friendships.index')->with('status', __('Friendship removed.'));
    }

    public function accept(Friendship $friendship): RedirectResponse
    {
        $this->authorize('accept', $friendship);

        $friendship->update(['status' => FriendshipStatus::Accepted]);

        return redirect()->route('friendships.index')->with('status', __('Friend request accepted.'));
    }

    public function reject(Friendship $friendship): RedirectResponse
    {
        $this->authorize('reject', $friendship);

        $friendship->delete();

        return redirect()->route('friendships.index')->with('status', __('Friend request declined.'));
    }
}
