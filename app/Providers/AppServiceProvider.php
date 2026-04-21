<?php

namespace App\Providers;

use App\Models\BodyMeasurement;
use App\Models\Friendship;
use App\Models\SessionSet;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('workout_session', function (string $value) {
            return WorkoutSession::query()
                ->where('user_id', auth()->id())
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('session_set', function (string $value, \Illuminate\Routing\Route $route) {
            $workoutSession = $route->parameter('workout_session');

            return SessionSet::query()
                ->where('workout_session_id', $workoutSession->id)
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('body_measurement', function (string $value) {
            return BodyMeasurement::query()
                ->where('user_id', auth()->id())
                ->whereKey($value)
                ->firstOrFail();
        });

        Route::bind('friendship', function (string $value) {
            $userId = auth()->id();

            return Friendship::query()
                ->whereKey($value)
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhere('friend_id', $userId);
                })
                ->firstOrFail();
        });
    }
}
