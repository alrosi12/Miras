<?php

namespace App\Http\Requests\Exercise;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExerciseRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'muscle_group' => ['required', Rule::enum(MuscleGroup::class)],
            'type' => ['required', Rule::enum(ExerciseType::class)],
            'image' => ['nullable', 'string', 'max:2048'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }
}
