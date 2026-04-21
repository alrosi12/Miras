<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PlanDayExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_plan_day_id',
        'exercise_id',
        'sets',
        'reps',
        'rest_seconds',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'sets' => 'integer',
            'reps' => 'integer',
            'rest_seconds' => 'integer',
            'order' => 'integer',
        ];
    }

    public function workoutPlanDay(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlanDay::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * الوصول للخطة الأم عبر يوم التمرين (نمط has-one-through: صف التمرين → اليوم → الخطة).
     */
    public function workoutPlan(): HasOneThrough
    {
        return $this->hasOneThrough(
            WorkoutPlan::class,
            WorkoutPlanDay::class,
            'id',
            'id',
            'workout_plan_day_id',
            'workout_plan_id',
        );
    }
}
