<?php

namespace App\Http\Requests;

use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkoutSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $session = $this->route('workout_session');

            return $session instanceof WorkoutSession
                && $this->user()?->can('update', $session) === true;
        }

        return $this->user()?->can('create', WorkoutSession::class) === true;
    }

    /**
     * التحقق من بيانات الجلسة (بدون finish — المدة تُحسب في المتحكم).
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'workout_plan_id' => ['nullable', 'integer', Rule::exists('workout_plans', 'id')],
            'date' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'notes' => ['nullable', 'string', 'max:65535'],
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
