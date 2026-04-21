<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weight',
        'body_fat',
        'chest',
        'waist',
        'arms',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'body_fat' => 'decimal:2',
            'chest' => 'decimal:2',
            'waist' => 'decimal:2',
            'arms' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
