<?php

namespace App\Enums;

enum UserGoal: string
{
    case LoseWeight = 'lose_weight';
    case GainMuscle = 'gain_muscle';
    case Maintain = 'maintain';
}
