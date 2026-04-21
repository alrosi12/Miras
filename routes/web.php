<?php

use App\Http\Controllers\BodyMeasurementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\ProfileCompletionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\SessionSetController;
use App\Http\Controllers\WorkoutPlanController;
use App\Http\Controllers\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/workout-plans/share/{token}', [WorkoutPlanController::class, 'shareShow'])
    ->name('workout-plans.share');

Route::get('/profiles/{user}', [PublicProfileController::class, 'show'])
    ->name('profiles.show');

Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', [ProfileCompletionController::class, 'show'])
        ->name('profile.complete.show');
    Route::patch('/complete-profile', [ProfileCompletionController::class, 'update'])
        ->name('profile.complete.update');
});

Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    Route::post('workout-plans/{workout_plan}/duplicate', [WorkoutPlanController::class, 'duplicate'])
        ->name('workout-plans.duplicate');
    Route::post('workout-plans/{workout_plan}/share', [WorkoutPlanController::class, 'enableShare'])
        ->name('workout-plans.share.enable');
    Route::delete('workout-plans/{workout_plan}/share', [WorkoutPlanController::class, 'disableShare'])
        ->name('workout-plans.share.disable');

    Route::resource('exercises', ExerciseController::class);
    Route::resource('workout-plans', WorkoutPlanController::class);
    Route::resource('workout-sessions', WorkoutSessionController::class);
    Route::post('workout-sessions/{workout_session}/finish', [WorkoutSessionController::class, 'finish'])
        ->name('workout-sessions.finish');
    Route::resource('workout-sessions.session-sets', SessionSetController::class);
    Route::resource('body-measurements', BodyMeasurementController::class);

    Route::post('friendships/{friendship}/accept', [FriendshipController::class, 'accept'])
        ->name('friendships.accept');
    Route::post('friendships/{friendship}/reject', [FriendshipController::class, 'reject'])
        ->name('friendships.reject');
    Route::resource('friendships', FriendshipController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
