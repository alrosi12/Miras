<?php

namespace App\Http\Requests;

use App\Models\Exercise;
use App\Models\SessionSet;
use App\Models\WorkoutSession;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SessionSetRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            $session = $this->route('workout_session');

            return $session instanceof WorkoutSession
                && $this->user()?->can('manageSessionSets', $session) === true;
        }

        $set = $this->route('session_set');

        return $set instanceof SessionSet
            && $this->user()?->can('update', $set) === true;
    }

    /**
     * POST: إضافة set — PATCH: تعديل reps / weight / is_completed فقط.
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            return [
                'reps' => ['nullable', 'integer', 'min:0', 'max:9999'],
                'weight' => ['nullable', 'numeric', 'min:0', 'max:9999'],
                'is_completed' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'exercise_id' => ['required', 'integer', Rule::exists('exercises', 'id')],
            'set_number' => ['nullable', 'integer', 'min:1', 'max:500'],
            'reps' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_completed')) {
            $this->merge([
                'is_completed' => $this->boolean('is_completed'),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        if (! $this->isMethod('POST') || ! $this->filled('exercise_id')) {
            return;
        }

        $validator->after(function (Validator $validator): void {
            $exercise = Exercise::query()->find($this->input('exercise_id'));

            if (! $exercise || ! $this->user()->can('view', $exercise)) {
                $validator->errors()->add('exercise_id', __('Invalid exercise.'));
            }
        });
    }
}
