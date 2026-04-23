<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseRequest;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * إدارة تمارين الكتالوج العامة (user_id = null، is_public = true).
 */
class AdminExerciseController extends Controller
{
    public function index(): View
    {
        Gate::authorize('admin');

        $exercises = Exercise::query()
            ->whereNull('user_id')
            ->where('is_public', true)
            ->latest()
            ->paginate(15);

        return view('admin.exercises.index', compact('exercises'));
    }

    public function create(): View
    {
        Gate::authorize('admin');

        return view('admin.exercises.create');
    }

    public function store(ExerciseRequest $request): RedirectResponse
    {
        Gate::authorize('admin');

        $data = collect($request->validated())->except(['image'])->all();
        $data['user_id'] = null;
        $data['is_public'] = true;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('exercises', 'public');
        }

        $exercise = Exercise::query()->create($data);

        return redirect()
            ->route('admin.exercises.edit', $exercise)
            ->with('status', __('Global exercise created.'));
    }

    public function edit(Exercise $exercise): View|RedirectResponse
    {
        Gate::authorize('admin');

        if ($exercise->user_id !== null) {
            return redirect()
                ->route('admin.exercises.index')
                ->with('error', __('Only catalog (global) exercises can be edited here.'));
        }

        return view('admin.exercises.edit', compact('exercise'));
    }

    public function update(ExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        Gate::authorize('admin');

        if ($exercise->user_id !== null) {
            return redirect()
                ->route('admin.exercises.index')
                ->with('error', __('Only catalog exercises can be updated here.'));
        }

        $data = collect($request->validated())->except(['image'])->all();
        $data['is_public'] = true;

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($exercise->image);
            $data['image'] = $request->file('image')->store('exercises', 'public');
        }

        $exercise->update($data);

        return redirect()
            ->route('admin.exercises.edit', $exercise)
            ->with('status', __('Exercise updated.'));
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        Gate::authorize('admin');

        if ($exercise->user_id !== null) {
            return redirect()
                ->route('admin.exercises.index')
                ->with('error', __('Only catalog exercises can be deleted here.'));
        }

        $this->deleteStoredImage($exercise->image);
        $exercise->delete();

        return redirect()
            ->route('admin.exercises.index')
            ->with('status', __('Exercise deleted.'));
    }

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
