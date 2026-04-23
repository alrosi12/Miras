<?php

namespace App\Http\Requests;

use App\Models\Exercise;
use App\Models\WorkoutPlan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class WorkoutPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $plan = $this->route('workout_plan');

            return $plan instanceof WorkoutPlan
                && $this->user()?->can('update', $plan) === true;
        }

        return $this->user()?->can('create', WorkoutPlan::class) === true;
    }

    /**
     * التحقق من الروتين + أيامه (days) + تمارين كل يوم (exercises) كمصفوفات متداخلة.
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],

            'days' => ['required', 'array', 'min:1'],
            'days.*.day_name' => ['required', 'string', 'max:255'],
            'days.*.order' => ['nullable', 'integer', 'min:0', 'max:32767'],
            'days.*.exercises' => ['required', 'array', 'min:1'],
            'days.*.exercises.*.exercise_id' => ['required', 'integer', Rule::exists('exercises', 'id')],
            'days.*.exercises.*.sets' => ['nullable', 'integer', 'min:1', 'max:999'],
            'days.*.exercises.*.reps' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'days.*.exercises.*.rest_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'days.*.exercises.*.order' => ['nullable', 'integer', 'min:0', 'max:32767'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_public')) {
            $this->merge([
                'is_public' => $this->boolean('is_public'),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $user = $this->user();
            if ($user === null) {
                return;
            }

            foreach ($this->input('days', []) as $dIndex => $day) {
                foreach ($day['exercises'] ?? [] as $eIndex => $row) {
                    $id = $row['exercise_id'] ?? null;
                    if ($id === null) {
                        continue;
                    }
                    $exercise = Exercise::query()->find($id);
                    if (! $exercise || ! Gate::forUser($user)->allows('view', $exercise)) {
                        $validator->errors()->add(
                            "days.{$dIndex}.exercises.{$eIndex}.exercise_id",
                            __('You cannot use this exercise in a plan.')
                        );
                    }
                }
            }
        });
    }
}
