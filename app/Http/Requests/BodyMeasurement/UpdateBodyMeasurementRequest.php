<?php

namespace App\Http\Requests\BodyMeasurement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBodyMeasurementRequest extends FormRequest
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
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'body_fat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'arms' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'date' => ['sometimes', 'required', 'date'],
        ];
    }
}
