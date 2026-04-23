<?php

namespace Database\Seeders;

use App\Enums\UserGoal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(DemoUsersSeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::query()->updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Gmail',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_admin' => false,
                'weight' => 78,
                'height' => 175,
                'birth_date' => now()->subYears(28)->startOfMonth()->toDateString(),
                'goal' => UserGoal::Maintain,
            ],
        );

        $this->call(TrainingDashboardDemoSeeder::class);
    }
}
