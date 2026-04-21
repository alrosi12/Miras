<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\CompleteProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileCompletionController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user !== null && $user->hasCompletedProfile()) {
            return redirect()->route('dashboard');
        }

        return view('profile.complete');
    }

    public function update(CompleteProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
