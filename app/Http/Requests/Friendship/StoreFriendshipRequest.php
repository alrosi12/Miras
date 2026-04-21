<?php

namespace App\Http\Requests\Friendship;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFriendshipRequest extends FormRequest
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
            'friend_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                Rule::notIn([$this->user()->id]),
                Rule::unique('friendships', 'friend_id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('friend_id')) {
                return;
            }

            $reverse = Friendship::query()
                ->where('user_id', $this->input('friend_id'))
                ->where('friend_id', $this->user()->id)
                ->where('status', FriendshipStatus::Accepted)
                ->exists();

            if ($reverse) {
                $validator->errors()->add('friend_id', __('You are already friends with this user.'));
            }
        });
    }
}
