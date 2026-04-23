<?php

namespace App\Models;

use App\Enums\ExerciseType;
use App\Enums\MuscleGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'muscle_group',
        'type',
        'image',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'muscle_group' => MuscleGroup::class,
            'type' => ExerciseType::class,
            'is_public' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** المستخدم صاحب التمرين (نفس علاقة owner). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * أيام الخطط المرتبطة عبر جدول plan_day_exercises.
     */
    public function workoutPlanDays(): BelongsToMany
    {
        return $this->belongsToMany(WorkoutPlanDay::class, 'plan_day_exercises')
            ->withPivot(['sets', 'reps', 'rest_seconds', 'order'])
            ->withTimestamps();
    }

    public function planDayExercises(): HasMany
    {
        return $this->hasMany(PlanDayExercise::class);
    }

    public function sessionSets(): HasMany
    {
        return $this->hasMany(SessionSet::class);
    }

    /**
     * تمارين النظام (غير مملوكة لمستخدم).
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    /**
     * تمارين مُعلَمة كعامة في الكتالوج.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * التمارين الظاهرة لمستخدم: العامة (user_id null) أو التي يملكها.
     */
    public function scopeVisibleTo(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->whereNull('user_id')
                ->orWhere('user_id', $userId);
        });
    }
}
