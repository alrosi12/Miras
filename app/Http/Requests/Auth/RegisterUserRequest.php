<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserGoal;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'weight' => ['required', 'numeric', 'min:20', 'max:500'],
            'height' => ['required', 'numeric', 'min:50', 'max:280'],
            'birth_date' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'goal' => ['required', Rule::enum(UserGoal::class)],
        ];
    }
}
