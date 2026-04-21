<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserGoal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteProfileRequest extends FormRequest
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
            'weight' => ['required', 'numeric', 'min:20', 'max:500'],
            'height' => ['required', 'numeric', 'min:50', 'max:280'],
            'birth_date' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'goal' => ['required', Rule::enum(UserGoal::class)],
        ];
    }
}
