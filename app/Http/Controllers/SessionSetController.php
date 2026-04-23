<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionSetRequest;
use App\Models\SessionSet;
use App\Models\WorkoutSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SessionSetController extends Controller
{
    /**
     * إضافة set للجلسة؛ إن لم يُرسل set_number يُحسب تلقائياً (أعلى رقم + 1 ضمن نفس الجلسة).
     */
    public function store(SessionSetRequest $request, WorkoutSession $workoutSession): RedirectResponse
    {
        $data = $request->validated();

        $setNumber = $data['set_number'] ?? null;
        if ($setNumber === null) {
            $setNumber = (int) ($workoutSession->sessionSets()->max('set_number') ?? 0) + 1;
        }

        $workoutSession->sessionSets()->create([
            'exercise_id' => $data['exercise_id'],
            'set_number' => $setNumber,
            'reps' => $data['reps'] ?? null,
            'weight' => $data['weight'] ?? null,
            'is_completed' => (bool) ($data['is_completed'] ?? false),
        ]);

        return redirect()
            ->route('workout-sessions.show', $workoutSession)
            ->with('status', __('Set added.'));
    }

    /**
     * تحديث reps و weight و is_completed فقط.
     */
    public function update(SessionSetRequest $request, WorkoutSession $workoutSession, SessionSet $sessionSet): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $sessionSet);

        $sessionSet->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Set updated.'),
                'set' => $sessionSet->fresh()->load('exercise:id,name'),
            ]);
        }

        return redirect()
            ->route('workout-sessions.show', $workoutSession)
            ->with('status', __('Set updated.'));
    }

    public function destroy(Request $request, WorkoutSession $workoutSession, SessionSet $sessionSet): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $sessionSet);

        $sessionSet->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => __('Set removed.')]);
        }

        return redirect()
            ->route('workout-sessions.show', $workoutSession)
            ->with('status', __('Set removed.'));
    }
}
