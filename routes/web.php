<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Debug route
    Route::get('/debug-tickets', function () {
        $users = \App\Models\User::all(['id', 'name', 'email', 'role']);
        $tickets = \App\Models\Complaint::all(['id', 'ticket_number', 'captured_by', 'assigned_to', 'status', 'full_name']);
        return response()->json([
            'users' => $users,
            'tickets' => $tickets,
            'current_user' => auth()->user(),
        ]);
    });

    // Complaint/Ticket Management
    Route::resource('complaints', ComplaintController::class);
    Route::post('complaints/{complaint}/assign', [ComplaintController::class, 'assign'])->name('complaints.assign');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Analytics Dashboard - Manager & Super Admin only
    Route::middleware(['role:super_admin,manager'])->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    });

    // User Management - Only Super Admin
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
