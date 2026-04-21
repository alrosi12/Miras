<?php

namespace App\Http\Requests\SessionSet;

use App\Models\Exercise;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionSetRequest extends FormRequest
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
            'exercise_id' => ['sometimes', 'required', 'integer', Rule::exists('exercises', 'id')],
            'set_number' => ['sometimes', 'required', 'integer', 'min:1', 'max:500'],
            'reps' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('exercise_id')) {
                return;
            }

            $exercise = Exercise::query()->find($this->input('exercise_id'));

            if (! $exercise || ! $this->user()->can('view', $exercise)) {
                $validator->errors()->add('exercise_id', __('Invalid exercise.'));
            }
        });
    }
}
