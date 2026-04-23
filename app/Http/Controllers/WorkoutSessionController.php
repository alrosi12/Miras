<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutSessionRequest;
use App\Models\Exercise;
use App\Models\SessionSet;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanDay;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkoutSessionController extends Controller
{
    /**
     * قائمة جلسات المستخدم مع فلترة اختيارية بالتاريخ (date_from / date_to).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', WorkoutSession::class);

        $query = WorkoutSession::query()
            ->where('user_id', auth()->id())
            ->with('workoutPlan');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->query('date_to'));
        }

        if ($request->filled('month')) {
            try {
                $m = Carbon::createFromFormat('Y-m', (string) $request->query('month'))->startOfMonth();
                $query->whereYear('date', $m->year)->whereMonth('date', $m->month);
            } catch (\Throwable) {
                // تجاهل قيمة شهر غير صالحة
            }
        }

        $sessions = $query
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $calendarMonth = now();
        if ($request->filled('calendar_month')) {
            try {
                $calendarMonth = Carbon::createFromFormat('Y-m', (string) $request->query('calendar_month'))->startOfMonth();
            } catch (\Throwable) {
                $calendarMonth = now()->startOfMonth();
            }
        } else {
            $calendarMonth = $calendarMonth->copy()->startOfMonth();
        }

        $calendarStart = $calendarMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $sessionCountsByDate = WorkoutSession::query()
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->get(['date'])
            ->groupBy(fn (WorkoutSession $s) => $s->date->toDateString())
            ->map(fn ($rows) => $rows->count());

        return view('workout-sessions.index', [
            'sessions' => $sessions,
            'calendarMonth' => $calendarMonth,
            'calendarStart' => $calendarStart,
            'calendarEnd' => $calendarEnd,
            'sessionCountsByDate' => $sessionCountsByDate,
        ]);
    }

    /**
     * نموذج جلسة جديدة: يقترح روتين «يوم اليوم» الحالي إن وُجد (اسم اليوم في الخطة).
     */
    public function create(): View
    {
        $this->authorize('create', WorkoutSession::class);

        $plans = WorkoutPlan::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $suggestedRoutine = $this->resolveRoutineForDate(auth()->id(), now());

        return view('workout-sessions.create', compact('plans', 'suggestedRoutine'));
    }

    /**
     * حفظ الجلسة + إنشاء صفوف session_sets فارغة من يوم الخطة المطابق لتاريخ الجلسة (داخل معاملة).
     */
    public function store(WorkoutSessionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $session = DB::transaction(function () use ($validated, $request) {
            $session = WorkoutSession::query()->create([
                'user_id' => $request->user()->id,
                'workout_plan_id' => $validated['workout_plan_id'] ?? null,
                'date' => $validated['date'],
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (! empty($validated['workout_plan_id'])) {
                $plan = WorkoutPlan::query()
                    ->with(['workoutPlanDays.planDayExercises'])
                    ->find($validated['workout_plan_id']);

                if ($plan && $request->user()->can('view', $plan)) {
                    $planDay = $this->findPlanDayForDate($plan, Carbon::parse($validated['date']));

                    if ($planDay !== null) {
                        foreach ($planDay->planDayExercises->sortBy('order') as $row) {
                            $session->sessionSets()->create([
                                'exercise_id' => $row->exercise_id,
                                'set_number' => 1,
                                'reps' => null,
                                'weight' => null,
                                'is_completed' => false,
                            ]);
                        }
                    }
                }
            }

            return $session;
        });

        return redirect()
            ->route('workout-sessions.show', $session)
            ->with('status', __('Session logged.'));
    }

    public function show(WorkoutSession $workoutSession): View
    {
        $this->authorize('view', $workoutSession);

        $workoutSession->load(['workoutPlan', 'sessionSets.exercise']);

        $exercises = Exercise::query()
            ->visibleTo((int) auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $nextSetNumber = (int) ($workoutSession->sessionSets()->max('set_number') ?? 0) + 1;

        $sessionSetsPayload = $workoutSession->sessionSets->map(fn (SessionSet $s) => [
            'id' => $s->id,
            'exercise_id' => $s->exercise_id,
            'exercise_name' => $s->exercise->name,
            'set_number' => $s->set_number,
            'reps' => $s->reps,
            'weight' => $s->weight !== null ? (float) $s->weight : null,
            'is_completed' => (bool) $s->is_completed,
        ])->values();

        $sessionSetPatchUrls = $workoutSession->sessionSets->mapWithKeys(fn (SessionSet $s) => [
            $s->id => route('workout-sessions.session-sets.update', [$workoutSession, $s]),
        ])->all();

        $sessionSetDestroyUrls = $workoutSession->sessionSets->mapWithKeys(fn (SessionSet $s) => [
            $s->id => route('workout-sessions.session-sets.destroy', [$workoutSession, $s]),
        ])->all();

        return view('workout-sessions.show', compact(
            'workoutSession',
            'exercises',
            'nextSetNumber',
            'sessionSetsPayload',
            'sessionSetPatchUrls',
            'sessionSetDestroyUrls',
        ));
    }

    public function edit(WorkoutSession $workoutSession): View
    {
        $this->authorize('update', $workoutSession);

        $plans = WorkoutPlan::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('workout-sessions.edit', compact('workoutSession', 'plans'));
    }

    public function update(WorkoutSessionRequest $request, WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('update', $workoutSession);

        $workoutSession->update($request->validated());

        return redirect()
            ->route('workout-sessions.show', $workoutSession)
            ->with('status', __('Session updated.'));
    }

    public function destroy(WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('delete', $workoutSession);

        $workoutSession->delete();

        return redirect()->route('workout-sessions.index')->with('status', __('Session deleted.'));
    }

    /**
     * إنهاء الجلسة: حساب duration_minutes من created_at حتى الآن (دقيقة واحدة على الأقل).
     */
    public function finish(Request $request, WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('finish', $workoutSession);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:65535'],
        ]);

        $minutes = max(1, (int) ceil(abs($workoutSession->created_at->diffInMinutes(now()))));

        $attributes = ['duration_minutes' => $minutes];
        if (array_key_exists('notes', $data)) {
            $attributes['notes'] = $data['notes'];
        }
        $workoutSession->update($attributes);

        return redirect()
            ->route('workout-sessions.show', $workoutSession)
            ->with('status', __('Session finished.'));
    }

    /**
     * نسخ جلسة سابقة: نفس الخطة والتاريخ اليوم، مع نسخ التمارين كـ sets جديدة و is_completed = false.
     */
    public function duplicate(WorkoutSession $workoutSession): RedirectResponse
    {
        $this->authorize('duplicate', $workoutSession);

        $workoutSession->loadMissing('sessionSets');

        $clone = DB::transaction(function () use ($workoutSession) {
            $new = $workoutSession->replicate();
            $new->user_id = auth()->id();
            $new->date = now()->toDateString();
            $new->duration_minutes = null;
            $new->notes = $workoutSession->notes;
            $new->save();

            foreach ($workoutSession->sessionSets as $set) {
                $new->sessionSets()->create([
                    'exercise_id' => $set->exercise_id,
                    'set_number' => $set->set_number,
                    'reps' => $set->reps,
                    'weight' => $set->weight,
                    'is_completed' => false,
                ]);
            }

            return $new;
        });

        return redirect()
            ->route('workout-sessions.show', $clone)
            ->with('status', __('Session duplicated.'));
    }

    /**
     * إن وُجدت جلسة بتاريخ اليوم للمستخدم الحالي → عرضها، وإلا → صفحة الإنشاء مع تنبيه.
     */
    public function today(): RedirectResponse
    {
        $this->authorize('viewAny', WorkoutSession::class);

        $session = WorkoutSession::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->latest('id')
            ->first();

        if ($session) {
            return redirect()->route('workout-sessions.show', $session);
        }

        return redirect()
            ->route('workout-sessions.create')
            ->with('status', __('No session logged for today yet.'));
    }

    /**
     * أول خطة للمستخدم فيها يوم يطابق تاريخ معيّن (للاقتراح في create).
     *
     * @return array{plan: WorkoutPlan, day: WorkoutPlanDay}|null
     */
    protected function resolveRoutineForDate(int $userId, CarbonInterface $date): ?array
    {
        $plans = WorkoutPlan::query()
            ->where('user_id', $userId)
            ->with(['workoutPlanDays.planDayExercises.exercise'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($plans as $plan) {
            $day = $this->findPlanDayForDate($plan, $date);
            if ($day !== null) {
                return ['plan' => $plan, 'day' => $day];
            }
        }

        return null;
    }

    /**
     * مطابقة يوم الخطة مع يوم الأسبوع: اسم اليوم (EN / locale) أو رقم 1–7 مثل dayOfWeekIso أو ترتيب order.
     */
    protected function findPlanDayForDate(WorkoutPlan $plan, CarbonInterface $date): ?WorkoutPlanDay
    {
        $english = mb_strtolower($date->copy()->locale('en')->translatedFormat('l'));
        $localized = mb_strtolower($date->translatedFormat('l'));
        $iso = (int) $date->dayOfWeekIso;

        foreach ($plan->workoutPlanDays as $day) {
            $name = mb_strtolower(trim($day->day_name));

            if ($name === $english || $name === $localized) {
                return $day;
            }

            if (is_numeric($day->day_name) && (int) $day->day_name === $iso) {
                return $day;
            }

            if ((int) $day->order === $iso) {
                return $day;
            }
        }

        return null;
    }
}
