<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class WorkoutPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'share_token',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workoutPlanDays(): HasMany
    {
        return $this->hasMany(WorkoutPlanDay::class)->orderBy('order');
    }

    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }

    /**
     * كل صفوف plan_day_exercises الخاصة بالخطة عبر أيامها.
     */
    public function planDayExercises(): HasManyThrough
    {
        return $this->hasManyThrough(
            PlanDayExercise::class,
            WorkoutPlanDay::class,
            'workout_plan_id',
            'workout_plan_day_id',
            'id',
            'id',
        );
    }

    public function scopeShared(Builder $query): Builder
    {
        return $query->whereNotNull('share_token');
    }

    public function scopeByShareToken(Builder $query, string $token): Builder
    {
        return $query->where('share_token', $token);
    }
}
