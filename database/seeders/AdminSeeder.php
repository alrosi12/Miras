<?php

namespace Database\Seeders;

use App\Enums\UserGoal;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * مستخدم إداري افتراضي للتطوير/الإقلاع الأولي.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@fittrack.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
                // حقول الملف الشخصي حتى يمرّ middleware التطبيق العادي إن زار /dashboard
                'weight' => 75,
                'height' => 175,
                'birth_date' => now()->subYears(25)->startOfDay(),
                'goal' => UserGoal::Maintain,
            ],
        );
    }
}
