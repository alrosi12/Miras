<?php

namespace App\Http\Controllers;

use App\Http\Requests\BodyMeasurement\StoreBodyMeasurementRequest;
use App\Http\Requests\BodyMeasurement\UpdateBodyMeasurementRequest;
use App\Models\BodyMeasurement;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BodyMeasurementController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', BodyMeasurement::class);

        $measurements = BodyMeasurement::query()
            ->where('user_id', auth()->id())
            ->latest('date')
            ->paginate(20);

        return view('body-measurements.index', compact('measurements'));
    }

    public function create(): View
    {
        $this->authorize('create', BodyMeasurement::class);

        return view('body-measurements.create');
    }

    public function store(StoreBodyMeasurementRequest $request): RedirectResponse
    {
        $this->authorize('create', BodyMeasurement::class);

        $measurement = BodyMeasurement::query()->create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return redirect()->route('body-measurements.show', $measurement)->with('status', __('Measurement saved.'));
    }

    public function show(BodyMeasurement $bodyMeasurement): View
    {
        $this->authorize('view', $bodyMeasurement);

        return view('body-measurements.show', compact('bodyMeasurement'));
    }

    public function edit(BodyMeasurement $bodyMeasurement): View
    {
        $this->authorize('update', $bodyMeasurement);

        return view('body-measurements.edit', compact('bodyMeasurement'));
    }

    public function update(UpdateBodyMeasurementRequest $request, BodyMeasurement $bodyMeasurement): RedirectResponse
    {
        $this->authorize('update', $bodyMeasurement);

        $bodyMeasurement->update($request->validated());

        return redirect()->route('body-measurements.show', $bodyMeasurement)->with('status', __('Measurement updated.'));
    }

    public function destroy(BodyMeasurement $bodyMeasurement): RedirectResponse
    {
        $this->authorize('delete', $bodyMeasurement);

        $bodyMeasurement->delete();

        return redirect()->route('body-measurements.index')->with('status', __('Measurement deleted.'));
    }
}
