<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    // Reset Password (link dari email)
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Auth Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard/lead', [DashboardController::class, 'lead'])
        ->name('dashboard.lead')
        ->middleware('role:Lead Engineer');
    
    Route::get('/dashboard/engineer', [DashboardController::class, 'engineer'])
        ->name('dashboard.engineer')
        ->middleware('role:Engineer L1,Engineer L2');

    // Projects - Only Lead Engineer
    Route::prefix('projects')->middleware('role:Lead Engineer')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/data', [ProjectController::class, 'getData'])->name('projects.data');
    });

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store')
            ->middleware('role:Lead Engineer');
        Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')
            ->middleware('role:Lead Engineer');
        Route::get('/kanban-data', [TaskController::class, 'getKanbanData'])->name('tasks.kanban');
    });

    // Schedules
    Route::prefix('schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/', [ScheduleController::class, 'store'])->name('schedules.store')
            ->middleware('role:Lead Engineer');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update')
            ->middleware('role:Lead Engineer');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy')
            ->middleware('role:Lead Engineer');
        Route::get('/calendar-data', [ScheduleController::class, 'getCalendarData'])->name('schedules.calendar');
    });

    // Users - Only Lead Engineer
    Route::prefix('users')->middleware('role:Lead Engineer')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/{user}/approve-certification', [UserController::class, 'approveCertification'])->name('users.approve-certification');
        Route::post('/{user}/reject-certification', [UserController::class, 'rejectCertification'])->name('users.reject-certification');
    });

    // Search 
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Notifications 
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::get('/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
        Route::post('/read-all', [NotificationController::class, 'ajaxMarkAllRead'])->name('notifications.read-all');
        Route::delete('/destroy-all', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('/certification', [ProfileController::class, 'uploadCertification'])->name('profile.certification');
    });
});

// Home redirect
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('Lead Engineer')) {
            return redirect()->route('dashboard.lead');
        }
        return redirect()->route('dashboard.engineer');
    }
    return redirect()->route('login');
});