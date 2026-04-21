<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Exercise::class);

        $exercises = Exercise::query()
            ->visibleTo(auth()->id())
            ->latest()
            ->paginate(15);

        return view('exercises.index', compact('exercises'));
    }

    public function create(): View
    {
        $this->authorize('create', Exercise::class);

        return view('exercises.create');
    }

    public function store(StoreExerciseRequest $request): RedirectResponse
    {
        $this->authorize('create', Exercise::class);

        Exercise::query()->create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return redirect()->route('exercises.index')->with('status', __('Exercise created.'));
    }

    public function show(Exercise $exercise): View
    {
        $this->authorize('view', $exercise);

        return view('exercises.show', compact('exercise'));
    }

    public function edit(Exercise $exercise): View
    {
        $this->authorize('update', $exercise);

        return view('exercises.edit', compact('exercise'));
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $this->authorize('update', $exercise);

        $exercise->update($request->validated());

        return redirect()->route('exercises.show', $exercise)->with('status', __('Exercise updated.'));
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        $this->authorize('delete', $exercise);

        $exercise->delete();

        return redirect()->route('exercises.index')->with('status', __('Exercise deleted.'));
    }
}
