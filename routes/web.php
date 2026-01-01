<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\TwilioSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/activity', [DashboardController::class, 'getActivity'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.activity');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Complaint/Ticket Management
    Route::resource('complaints', ComplaintController::class)->except(['destroy']);

    // Allow support agents to assign/takeover tickets
    Route::middleware(['role:super_admin,manager,support_agent'])->group(function () {
        Route::post('complaints/{complaint}/assign', [ComplaintController::class, 'assign'])->name('complaints.assign');
    });

    // Note: Ticket deletion has been disabled for audit and compliance purposes
    // All tickets are retained permanently and can only be closed, not deleted

    // Export & Reporting Routes
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('export/excel', [ComplaintExportController::class, 'exportExcel'])->name('export.excel');
        Route::get('export/pdf', [ComplaintExportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('{complaint}/print', [ComplaintExportController::class, 'print'])->name('print');
        Route::get('{complaint}/export-pdf', [ComplaintExportController::class, 'exportTicketPdf'])->name('export-ticket-pdf');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('wizard', [ReportController::class, 'index'])->name('wizard');
        Route::post('generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('monthly', [ComplaintExportController::class, 'monthlyReport'])->name('monthly');
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Analytics Dashboard - Manager & Super Admin only
    Route::middleware(['role:super_admin,manager'])->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    });

    // User Management & Audit Logs - Only Super Admin
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('branches', BranchController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('employers', EmployerController::class);
        Route::resource('payment-methods', PaymentMethodController::class);

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

        // Twilio WhatsApp Settings
        Route::get('/settings/twilio', [TwilioSettingsController::class, 'index'])->name('settings.twilio');
        Route::put('/settings/twilio', [TwilioSettingsController::class, 'update'])->name('settings.twilio.update');
        Route::post('/settings/twilio/test', [TwilioSettingsController::class, 'testConnection'])->name('settings.twilio.test');
    });
});

require __DIR__.'/auth.php';
