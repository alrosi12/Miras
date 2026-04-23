<?php

namespace App\Http\Requests;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_public')) {
            $this->merge([
                'is_public' => $this->boolean('is_public'),
            ]);
        }
    }

    /**
     * قواعد التحقق لإنشاء وتعديل التمرين (الصورة اختيارية لكن إن وُجدت يجب أن تكون jpeg/png وبحد أقصى 2MB).
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'muscle_group' => ['required', Rule::enum(MuscleGroup::class)],
            'type' => ['required', Rule::enum(ExerciseType::class)],
            'is_public' => ['sometimes', 'boolean'],
            // ملف فقط (ليس image rule لتجنب قبول gif/svg)، حد 2048 كيلوبايت = 2MB
            'image' => ['sometimes', 'nullable', 'file', 'mimes:jpeg,jpg,png', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.mimes' => __('The image must be a JPEG or PNG file.'),
            'image.max' => __('The image must not be larger than 2 MB.'),
        ];
    }
}
