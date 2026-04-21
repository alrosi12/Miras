<?php

namespace App\Http\Requests\WorkoutSession;

use App\Models\WorkoutPlan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkoutSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'workout_plan_id' => ['nullable', 'integer', Rule::exists('workout_plans', 'id')],
            'date' => ['sometimes', 'required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('workout_plan_id')) {
                return;
            }

            $plan = WorkoutPlan::query()->find($this->input('workout_plan_id'));

            if (! $plan || ! $this->user()->can('view', $plan)) {
                $validator->errors()->add('workout_plan_id', __('You cannot attach this plan.'));
            }
        });
    }
}
