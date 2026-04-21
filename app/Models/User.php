<?php

namespace App\Models;

use App\Enums\FriendshipStatus;
use App\Enums\UserGoal;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'weight',
        'height',
        'birth_date',
        'goal',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'goal' => UserGoal::class,
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
        ];
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function workoutPlans(): HasMany
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }

    /**
     * كل المجموعات المسجّلة في الجلسات (عبر جلسات التمرين).
     */
    public function sessionSets(): HasManyThrough
    {
        return $this->hasManyThrough(
            SessionSet::class,
            WorkoutSession::class,
            'user_id',
            'workout_session_id',
            'id',
            'id',
        );
    }

    public function bodyMeasurements(): HasMany
    {
        return $this->hasMany(BodyMeasurement::class);
    }

    /**
     * طلبات/صداقات أنا منشئها (user_id = أنا).
     */
    public function friendshipsInitiated(): HasMany
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * طلبات موجّهة إليّ (friend_id = أنا).
     */
    public function friendshipsReceived(): HasMany
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    /**
     * المستخدمون الذين قبلتُ أنا طلب الصداقة لهم (أنا user_id والحالة accepted).
     */
    public function friendsAcceptedAsRequester(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->withPivot('status')
            ->wherePivot('status', FriendshipStatus::Accepted->value)
            ->withTimestamps();
    }

    /**
     * المستخدمون الذين أرسلوا لي طلباً وقُبل (أنا friend_id والحالة accepted).
     */
    public function friendsAcceptedAsAddressee(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->withPivot('status')
            ->wherePivot('status', FriendshipStatus::Accepted->value)
            ->withTimestamps();
    }

    /**
     * المستخدمون الذين لهم صداقة مقبولة مع المستخدم المعطى (في أي اتجاه في جدول friendships).
     */
    public function scopeAcceptedFriendsOf(Builder $query, User $viewer): Builder
    {
        return $query->where(function (Builder $q) use ($viewer) {
            $q->whereIn('id', function ($sub) use ($viewer) {
                $sub->select('friend_id')
                    ->from('friendships')
                    ->where('user_id', $viewer->id)
                    ->where('status', FriendshipStatus::Accepted->value);
            })->orWhereIn('id', function ($sub) use ($viewer) {
                $sub->select('user_id')
                    ->from('friendships')
                    ->where('friend_id', $viewer->id)
                    ->where('status', FriendshipStatus::Accepted->value);
            });
        });
    }
}
