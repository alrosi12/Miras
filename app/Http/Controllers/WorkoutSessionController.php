<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutSession\FinishWorkoutSessionRequest;
use App\Http\Requests\WorkoutSession\StoreWorkoutSessionRequest;
use App\Http\Requests\WorkoutSession\UpdateWorkoutSessionRequest;
use App\Models\WorkoutSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkoutSessionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', WorkoutSession::class);

        $sessions = WorkoutSession::query()
            ->where('user_id', auth()->id())
            ->with('workoutPlan')
            ->latest('date')
            ->paginate(15);

        return view('workout-sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $this->authorize('create', WorkoutSession::class);

        return view('workout-sessions.create');
    }

    public function store(StoreWorkoutSessionRequest $request): RedirectResponse
    {
        $this->authorize('create', WorkoutSession::class);

        $session = WorkoutSession::query()->create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return redirect()->route('workout-sessions.show', $session)->with('status', __('Session logged.'));
    }

    public function show(WorkoutSession $workoutSession): View
    {
        $this->authorize('view', $workoutSession);

        $workoutSession->load(['workoutPlan', 'sessionSets.exercise']);

        return view('workout-sessions.show', compact('workoutSession'));
    }

    public function edit(WorkoutSession $workoutSession): View
    {
        $this->authorize('update', $workoutSession);

        return view('workout-sessions.edit', compact('workoutSession'));
    }

    public function update(UpdateWorkoutSessionRequest $request, WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('update', $workoutSession);

        $workoutSession->update($request->validated());

        return redirect()->route('workout-sessions.show', $workoutSession)->with('status', __('Session updated.'));
    }

    public function destroy(WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('delete', $workoutSession);

        $workoutSession->delete();

        return redirect()->route('workout-sessions.index')->with('status', __('Session deleted.'));
    }

    public function finish(FinishWorkoutSessionRequest $request, WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('finish', $workoutSession);

        $workoutSession->update($request->validated());

        return redirect()->route('workout-sessions.show', $workoutSession)->with('status', __('Session finished.'));
    }
}
