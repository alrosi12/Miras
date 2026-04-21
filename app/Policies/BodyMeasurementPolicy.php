<?php

namespace App\Policies;

use App\Models\BodyMeasurement;
use App\Models\User;

class BodyMeasurementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BodyMeasurement $bodyMeasurement): bool
    {
        return $bodyMeasurement->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BodyMeasurement $bodyMeasurement): bool
    {
        return $bodyMeasurement->user_id === $user->id;
    }

    public function delete(User $user, BodyMeasurement $bodyMeasurement): bool
    {
        return $this->update($user, $bodyMeasurement);
    }

    public function restore(User $user, BodyMeasurement $bodyMeasurement): bool
    {
        return false;
    }

    public function forceDelete(User $user, BodyMeasurement $bodyMeasurement): bool
    {
        return false;
    }
}
