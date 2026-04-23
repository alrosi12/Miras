<?php

namespace App\Http\Controllers;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use App\Http\Requests\ExerciseRequest;
use App\Models\Exercise;
use App\Models\SessionSet;
use App\Models\WorkoutSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    /**
     * قائمة التمارين: العامة (is_public) + تمارين المستخدم الحالي.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Exercise::class);

        $query = Exercise::query()
            ->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('is_public', true);
            });

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where('name', 'like', $term);
        }

        if ($request->filled('muscle_group') && MuscleGroup::tryFrom((string) $request->query('muscle_group'))) {
            $query->where('muscle_group', $request->query('muscle_group'));
        }

        if ($request->filled('type') && ExerciseType::tryFrom((string) $request->query('type'))) {
            $query->where('type', $request->query('type'));
        }

        $exercises = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('exercises.index', compact('exercises'));
    }

    /** نموذج إنشاء تمرين جديد. */
    public function create(): View
    {
        $this->authorize('create', Exercise::class);

        return view('exercises.create');
    }

    /**
     * حفظ تمرين جديد مع رفع الصورة (اختياري) على قرص public.
     */
    public function store(ExerciseRequest $request): RedirectResponse
    {
        $this->authorize('create', Exercise::class);

        $data = $this->payloadFromRequest($request);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('exercises', 'public');
        }

        $exercise = Exercise::query()->create($data);

        return redirect()
            ->route('exercises.show', $exercise)
            ->with('status', __('Exercise created.'));
    }

    /**
     * تفاصيل التمرين + تاريخ استخدامه في الجلسات (آخر 50 سجل).
     */
    public function show(Exercise $exercise): View
    {
        $this->authorize('view', $exercise);

        $usageHistory = $exercise->sessionSets()
            ->with(['workoutSession:id,user_id,date'])
            ->latest('id')
            ->limit(50)
            ->get();

        $weightChart = $this->buildExerciseWeightChart(auth()->id(), $exercise);

        return view('exercises.show', compact('exercise', 'usageHistory', 'weightChart'));
    }

    /**
     * بيانات Chart.js: أقصى وزن مسجّل لكل جلسة (آخر 25 جلسة تحتوي هذا التمرين).
     *
     * @return array{labels: array<int, string>, datasets: array<int, array<string, mixed>>}
     */
    protected function buildExerciseWeightChart(int $userId, Exercise $exercise): array
    {
        $sessions = WorkoutSession::query()
            ->where('user_id', $userId)
            ->whereHas('sessionSets', fn ($q) => $q->where('exercise_id', $exercise->id))
            ->with([
                'sessionSets' => fn ($q) => $q->where('exercise_id', $exercise->id)->orderBy('set_number'),
            ])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(25)
            ->get()
            ->sortBy(fn (WorkoutSession $s) => [$s->date->timestamp, $s->id])
            ->values();

        $labels = $sessions->map(fn (WorkoutSession $s) => $s->date->toDateString())->all();

        $weights = $sessions->map(function (WorkoutSession $s) use ($exercise) {
            $max = $s->sessionSets
                ->where('exercise_id', $exercise->id)
                ->filter(fn (SessionSet $st) => $st->weight !== null)
                ->max('weight');

            return $max !== null ? (float) $max : null;
        })->all();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('Max weight (per session)'),
                    'data' => $weights,
                    'fill' => false,
                    'tension' => 0.25,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
        ];
    }

    /** نموذج تعديل التمرين. */
    public function edit(Exercise $exercise): View
    {
        $this->authorize('update', $exercise);

        return view('exercises.edit', compact('exercise'));
    }

    /**
     * تحديث التمرين؛ إن وُجدت صورة جديدة تُستبدل القديمة وتُحذف من التخزين.
     */
    public function update(ExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $this->authorize('update', $exercise);

        $data = $this->payloadFromRequest($request);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($exercise->image);
            $data['image'] = $request->file('image')->store('exercises', 'public');
        }

        $exercise->update($data);

        return redirect()
            ->route('exercises.show', $exercise)
            ->with('status', __('Exercise updated.'));
    }

    /**
     * حذف التمرين مع حذف ملف الصورة من storage/app/public.
     */
    public function destroy(Exercise $exercise): RedirectResponse
    {
        $this->authorize('delete', $exercise);

        $this->deleteStoredImage($exercise->image);
        $exercise->delete();

        return redirect()
            ->route('exercises.index')
            ->with('status', __('Exercise deleted.'));
    }

    /**
     * الحقول المناسبة للنموذج (بدون ملف الصورة؛ يُعالج ملفياً في store/update).
     *
     * @return array<string, mixed>
     */
    private function payloadFromRequest(ExerciseRequest $request): array
    {
        return collect($request->validated())->except(['image'])->all();
    }

    /** حذف صورة مخزّنة على القرص public إن وُجدت. */
    private function deleteStoredImage(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
