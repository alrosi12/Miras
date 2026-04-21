<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionSet\StoreSessionSetRequest;
use App\Http\Requests\SessionSet\UpdateSessionSetRequest;
use App\Models\SessionSet;
use App\Models\WorkoutSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionSetController extends Controller
{
    public function index(WorkoutSession $workoutSession): View
    {
        $this->authorize('manageSessionSets', $workoutSession);

        $sessionSets = $workoutSession->sessionSets()
            ->with('exercise')
            ->orderBy('set_number')
            ->paginate(30);

        return view('session-sets.index', compact('workoutSession', 'sessionSets'));
    }

    public function create(WorkoutSession $workoutSession): View
    {
        $this->authorize('manageSessionSets', $workoutSession);

        return view('session-sets.create', compact('workoutSession'));
    }

    public function store(StoreSessionSetRequest $request, WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('manageSessionSets', $workoutSession);

        $workoutSession->sessionSets()->create($request->validated());

        return redirect()
            ->route('workout-sessions.session-sets.index', $workoutSession)
            ->with('status', __('Set added.'));
    }

    public function show(WorkoutSession $workoutSession, SessionSet $sessionSet): View
    {
        $this->authorize('manageSessionSets', $workoutSession);
        $this->authorize('view', $sessionSet);

        $sessionSet->load('exercise');

        return view('session-sets.show', compact('workoutSession', 'sessionSet'));
    }

    public function edit(WorkoutSession $workoutSession, SessionSet $sessionSet): View
    {
        $this->authorize('manageSessionSets', $workoutSession);
        $this->authorize('update', $sessionSet);

        return view('session-sets.edit', compact('workoutSession', 'sessionSet'));
    }

    public function update(UpdateSessionSetRequest $request, WorkoutSession $workoutSession, SessionSet $sessionSet): RedirectResponse
    {
        $this->authorize('manageSessionSets', $workoutSession);
        $this->authorize('update', $sessionSet);

        $sessionSet->update($request->validated());

        return redirect()
            ->route('workout-sessions.session-sets.show', [$workoutSession, $sessionSet])
            ->with('status', __('Set updated.'));
    }

    public function destroy(WorkoutSession $workoutSession, SessionSet $sessionSet): RedirectResponse
    {
        $this->authorize('manageSessionSets', $workoutSession);
        $this->authorize('delete', $sessionSet);

        $sessionSet->delete();

        return redirect()
            ->route('workout-sessions.session-sets.index', $workoutSession)
            ->with('status', __('Set removed.'));
    }
}
