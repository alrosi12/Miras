<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExerciseController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminUserController;
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

// رابط عام بدون تسجيل دخول (يجب أن يبقى خارج مجموعة auth)
Route::get('/workout-plans/public/{share_token}', [WorkoutPlanController::class, 'showPublic'])
    ->name('workout-plans.public');

Route::get('/profiles/{user}', [PublicProfileController::class, 'show'])
    ->name('profiles.show');

Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', [ProfileCompletionController::class, 'show'])
        ->name('profile.complete.show');
    Route::patch('/complete-profile', [ProfileCompletionController::class, 'update'])
        ->name('profile.complete.update');
});

// لوحة الإدارة: بدون middleware إكمال الملف الشخصي حتى يعمل حساب admin الافتراضي بسلاسة.
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'verified', 'admin'],
], function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::patch('users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])
        ->name('users.toggle-admin');
    Route::resource('users', AdminUserController::class)->except(['create', 'store']);

    Route::resource('exercises', AdminExerciseController::class)->except(['show']);

    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/users', [AdminReportController::class, 'usersReport'])->name('reports.users');
    Route::get('reports/sessions', [AdminReportController::class, 'sessionsReport'])->name('reports.sessions');
    Route::get('reports/export-users', [AdminReportController::class, 'exportUsers'])->name('reports.export-users');
});

Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/progress/compare', [ProgressController::class, 'compare'])->name('progress.compare');
    Route::get('/progress/measurements', [ProgressController::class, 'measurements'])->name('progress.measurements');
    Route::get('/progress/monthly', [ProgressController::class, 'monthly'])->name('progress.monthly');
    Route::get('/progress/exercise/{exercise}', [ProgressController::class, 'exercise'])->name('progress.exercise');
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    Route::post('workout-plans/{workout_plan}/duplicate', [WorkoutPlanController::class, 'duplicate'])
        ->name('workout-plans.duplicate');
    Route::post('workout-plans/{workout_plan}/share', [WorkoutPlanController::class, 'share'])
        ->name('workout-plans.share.enable');
    Route::delete('workout-plans/{workout_plan}/share', [WorkoutPlanController::class, 'revokeShare'])
        ->name('workout-plans.share.disable');
    Route::patch('workout-plans/{workout_plan}/toggle-public', [WorkoutPlanController::class, 'togglePublic'])
        ->name('workout-plans.toggle-public');

    // تمارين: CRUD كامل (صور على disk public) — انظر ExerciseController + ExerciseRequest + ExercisePolicy
    Route::resource('exercises', ExerciseController::class);
    Route::resource('workout-plans', WorkoutPlanController::class);
    Route::get('workout-sessions/today', [WorkoutSessionController::class, 'today'])
        ->name('workout-sessions.today');
    Route::resource('workout-sessions', WorkoutSessionController::class);
    Route::post('workout-sessions/{workout_session}/finish', [WorkoutSessionController::class, 'finish'])
        ->name('workout-sessions.finish');
    Route::post('workout-sessions/{workout_session}/duplicate', [WorkoutSessionController::class, 'duplicate'])
        ->name('workout-sessions.duplicate');
    Route::post('workout-sessions/{workout_session}/session-sets', [SessionSetController::class, 'store'])
        ->name('workout-sessions.session-sets.store');
    Route::patch('workout-sessions/{workout_session}/session-sets/{session_set}', [SessionSetController::class, 'update'])
        ->name('workout-sessions.session-sets.update');
    Route::delete('workout-sessions/{workout_session}/session-sets/{session_set}', [SessionSetController::class, 'destroy'])
        ->name('workout-sessions.session-sets.destroy');
    Route::resource('body-measurements', BodyMeasurementController::class);

    Route::get('/friends/feed', [FriendshipController::class, 'feed'])->name('friends.feed');
    Route::get('/friends/requests', [FriendshipController::class, 'requests'])->name('friends.requests');
    Route::get('/friends', [FriendshipController::class, 'index'])->name('friends.index');
    Route::get('/friends/{user}/profile', [FriendshipController::class, 'profile'])->name('friends.profile');
    Route::patch('/friends/{friendship}/accept', [FriendshipController::class, 'accept'])->name('friends.accept');
    Route::delete('/friends/{friendship}/reject', [FriendshipController::class, 'reject'])->name('friends.reject');
    Route::post('/friends/{user}', [FriendshipController::class, 'send'])->name('friends.send');
    Route::delete('/friends/{user}', [FriendshipController::class, 'destroy'])->name('friends.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
