<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function show(User $user): View
    {
        $this->authorize('viewPublicProfile', $user);

        $user->loadCount(['workoutPlans', 'workoutSessions']);

        $canSendFriendRequest = auth()->check()
            && auth()->id() !== $user->id
            && auth()->user()->can('sendFriendRequest', $user);

        return view('profiles.show', compact('user', 'canSendFriendRequest'));
    }
}
